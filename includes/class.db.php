<?php

class CGC_Video_Tracking_DB {


	private $table;
	private $db_version;

	function __construct() {

		global $wpdb;

		$this->table   		= $wpdb->base_prefix . 'cgc_video_tracking';
		$this->db_version 	= CGC_VIDEO_TRACKING_VERSION;

	}

	/**
	*	Record progress
	*
	*	@since 5.0
	*/
	public function add_progress( $args = array() ) {

		global $wpdb;

		$defaults = array(
			'video_id'		=> '',
			'user_id'		=> '',
			'percent'		=> '',
			'length'		=> '', // stores in total seconds
			'created_at' 	=> time()
		);

		$args = wp_parse_args( $args, $defaults );

		$add = $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$this->table} SET
					`video_id`  	= '%s',
					`user_id`  		= '%d',
					`percent`		= '%s',
					`length`		= '%s',
					`created_at`	= '%s'
				;",
				sanitize_text_field( $args['video_id'] ),
				absint( $args['user_id'] ),
				filter_var( $args['percent'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ),
				sanitize_text_field( $args['length'] ), // point rounding
				date_i18n( 'Y-m-d H:i:s', $args['created_at'], true )
			)
		);

		return $add ? $wpdb->insert_id : false;
	}

	/**
	*	Update existing progress
	*
	*	@since 5.0
	*/
	public function update_progress( $args = array() ) {

		global $wpdb;

		$old_progress = cgc_video_tracking_get_user_progress( $args['user_id'], $args['video_id'] );
		$progress 	  = ( (float) $old_progress + (float) $args['percent'] );

		if ( $progress > 95.0 ) {
			return;
		}

		$update = $wpdb->query(
			$wpdb->update(
				$this->table,
				array(
					'percent' => filter_var( $progress, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION )
				),
				array(
					'user_id' => absint( $args['user_id'] ),
					'video_id' => sanitize_text_field( $args['video_id'] )
				)
			)
		);

		return $update ? $update : false;
	}

	/**
	*	Delete a users progress by video and user id
	*	@param $user_id int id of the user todrop the progress for
	*	@param $video_id string if of the video to drop the progress for
	*/
	public function drop_progress( $video_id = 0, $user_id = 0 ) {

		global $wpdb;

		$drop = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$this->table} WHERE
					`video_id`  = '%s' AND
					`user_id`   = '%d'
				;",
				sanitize_text_field( $video_id ),
				absint( $user_id )
			)
		);

		return $drop ? true : false;
	}

	/**
	*	Get the progress of a video by user id
	*	@param $user_id int id of the user to get the progress for
	*	@param $video_id string if of the video to get the progress for
	*/
	public function get_user_progress( $user_id = 0 , $video_id = 0 ) {

		global $wpdb;

		$out = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT percent FROM {$this->table} WHERE user_id='%d' AND video_id='%s';", absint( $user_id ), sanitize_text_field( $video_id )
			)
		);

		return $out ? $out : false;

	}

	/**
	*	Get the length of a wistia video by id
	*	@param $video_id string if of the video to get the length for
	*	@return length in seconds
	*/
	public function get_video_length( $video_id = 0 ) {

		global $wpdb;

		$out = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT length FROM {$this->table} WHERE video_id='%s';", sanitize_text_field( $video_id )
			)
		);

		return $out ? $out : false;
	}

	/**
	*	Get all the videos this user has watched
	*	@param $user_id int id of the user to get the videos for
	*	@return the sum of all lengths of all videos this user has watched returned in seconds
	*/
	public function get_total_watched_length( $user_id = 0, $time = false ) {

		global $wpdb;

		$last_week = true == $time ? 'AND DATEDIFF(NOW(), created_at) <= 7' : null;

		$out = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT length FROM {$this->table} WHERE user_id='%d' %s;", absint( $user_id ), $last_week
			)
		);

		return $out ? $out : false;
	}

	/**
	*	Get the last video this use has recorded progress for
	*	@param $user_id int id of the user to get the info for
	*	@return string video id
	*/
	public function get_last_watched( $user_id = 0 ) {

		global $wpdb;

		$out = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$this->table} WHERE user_id='%d' ORDER BY created_at DESC LIMIT 1;", absint( $user_id )
			)
		);

		return $out ? $out[0] : false;
	}

}