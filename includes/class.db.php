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
	public function update_progress( $args = array() ) {

		global $wpdb;

		$defaults = array(
			'video_id'	=> '',
			'user_id'	=> '',
			'percent'	=> ''
		);

		$args = wp_parse_args( $args, $defaults );

		$add = $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$this->table} SET
					`video_id`  = '%s',
					`user_id`  	= '%d',
					`percent`	= '%s'
				;",
				sanitize_text_field( $args['video_id'] ),
				absint( $args['user_id'] ),
				filter_var( $args['percent'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION )
			)
		);

		return $add ? $wpdb->insert_id : false;
	}

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

}