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
					`video_id`  = '%d',
					`user_id`  	= '%d',
					`percent`	= '%d'
				;",
				absint( $args['video_id'] ),
				absint( $args['user_id'] ),
				absint( $args['percent'] )
			)
		);

		if ( $add )
			return $wpdb->insert_id;

		return false;
	}

}