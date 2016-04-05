<?php

class cgcVideoTrackingDb {


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

		$user_id = 	isset( $args['user_id'] ) ? $args['user_id'] : false;
		$video_id = isset( $args['video_id'] ) ? $args['video_id'] : false;

		$progress = filter_var( $args['percent'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );

		// get the id of the lesson that this video is attahed to
		$lesson_id = function_exists('cgc_get_lesson_by_video_id') ? cgc_get_lesson_by_video_id( $video_id ) : false;

		// get the id of the course that this lesson is a part of
		$course_id = function_exists('cgc_course_get_object_parent') ? cgc_course_get_object_parent( $lesson_id ) : false;

		// get the id of the flow that this course is a part of
		$flow_id = function_exists('cgc_course_get_parent_flow') ? cgc_course_get_parent_flow( $course_id ) : false;

		// purge video progress and recently watched cache for this user before retrieving and updating
		wp_cache_delete( 'cgc_cache--video_progress_'.$user_id.'-'.$video_id );
		wp_cache_delete( 'cgc_cache--video_recently_watched_'.$user_id );

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
				$progress,
				sanitize_text_field( $args['length'] ), // point rounding
				date_i18n( 'Y-m-d H:i:s', $args['created_at'], true )
			)
		);

		do_action('cgc_lesson_progress_added', $user_id, $progress, $lesson_id, $course_id, $flow_id );
		do_action('cgc_lesson_progress_updated', $user_id, $progress, $lesson_id, $course_id, $flow_id );

		return $add ? $wpdb->insert_id : false;
	}

	/**
	*	Update existing progress
	*
	*	@since 5.0
	*
	*/
	public function update_progress( $args = array() ) {

		global $wpdb;

		$user_id = 	isset( $args['user_id'] ) ? $args['user_id'] : false;
		$video_id = isset( $args['video_id'] ) ? $args['video_id'] : false;
		$length = isset( $args['length'] ) ? $args['length'] : false;

		// purge video progress and recently watched cache for this user before retrieving and updating
		wp_cache_delete( 'cgc_cache--video_progress_'.$user_id.'-'.$video_id );
		wp_cache_delete( 'cgc_cache--video_recently_watched_'.$user_id );

		$new_progress = $args['percent'];
		$old_progress = cgc_video_tracking_get_user_progress( $user_id, $video_id );
		$old_progress = $old_progress ? $old_progress[0] : false;

		$update = false;

		// if we're at 100% dont record anymore
		if ( $old_progress > 100.0 ) {
			return false;
		}

		// get the id of the lesson that this video is attahed to
		$lesson_id = function_exists('cgc_get_lesson_by_video_id') ? cgc_get_lesson_by_video_id( $video_id ) : false;

		// get the id of the course that this lesson is a part of
		$course_id = function_exists('cgc_course_get_object_parent') ? cgc_course_get_object_parent( $lesson_id ) : false;

		// get the id of the flow that this course is a part of
		$flow_id = function_exists('cgc_course_get_parent_flow') ? cgc_course_get_parent_flow( $course_id ) : false;

		// if new progress is greater than old progress
		if ( $new_progress >= $old_progress ) {

			$update = $wpdb->update(
				$this->table,
				array(
					'percent' => filter_var( $new_progress, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ),
					'length' => sanitize_text_field( $length )
				),
				array(
					'user_id' => absint( $user_id ),
					'video_id' => sanitize_text_field( $video_id )
				)
			);

			do_action('cgc_lesson_progress_updated', $user_id, $new_progress, $lesson_id, $course_id, $flow_id );
		}

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

		$out =  wp_cache_get( 'cgc_cache--video_progress_'.$user_id.'-'.$video_id );

		if ( false === $out ) {
			$out = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT percent FROM {$this->table} WHERE user_id='%d' AND video_id='%s';", absint( $user_id ), sanitize_text_field( $video_id )
				)
			);
			wp_cache_set( 'cgc_cache--video_progress_'.$user_id.'-'.$video_id, $out, '', 12 * HOUR_IN_SECONDS );
		}

		return $out ? $out : false;

	}

	/**
	*	Get the length of a wistia video by id
	*	@param $video_id string if of the video to get the length for
	*	@return length in seconds
	*/
	public function get_video_length( $video_id = 0 ) {

		global $wpdb;

		$out =  wp_cache_get( 'cgc_cache--video_length' );

		if ( false === $out ) {
			$out = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT length FROM {$this->table} WHERE video_id='%s';", sanitize_text_field( $video_id )
				)
			);
			wp_cache_set( 'cgc_cache--video_length', $out, '', 12 * HOUR_IN_SECONDS );
		}

		return $out ? $out : false;
	}

	/**
	*	Get all the videos this user has watched
	*	@param $user_id int id of the user to get the videos for
	*	@return the sum of all lengths of all videos this user has watched returned in seconds
	*/
	public function get_total_watched_length( $user_id = 0, $time = false ) {

		global $wpdb;

		$last_week = true == $time ? 'AND created_at > DATE_SUB(NOW(), INTERVAL 1 WEEK) ORDER BY created_at DESC' : false;

		$out = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT length FROM {$this->table} WHERE user_id='%d' {$last_week};", absint( $user_id )
			)
		);

		return $out ? $out : false;
	}

	/**
	*	Get the last video this use has recorded progress for
	*	@param $user_id int id of the user to get the info for
	*	@return string video id
	*/
	public function get_recently_watched( $user_id = 0, $count = 0 ) {

		global $wpdb;

		$out =  wp_cache_get( 'cgc_cache--video_recently_watched_'.$user_id );

		if ( false === $out ) {
			$out = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$this->table} WHERE user_id='%d' ORDER BY created_at DESC LIMIT %d;", absint( $user_id ), absint( $count )
				), ARRAY_A
			);
			wp_cache_set( 'cgc_cache--video_recently_watched_'.$user_id, $out, '', 12 * HOUR_IN_SECONDS );
		}

		return $out ? $out : false;
	}

}
