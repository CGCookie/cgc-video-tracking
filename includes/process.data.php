<?php

/**
*	Process the incoming ajax call and record progress to table
*
*	@since 5.0
*/
class cgcVideoTrackingProcess {

	function __construct(){

		add_action('wp_ajax_process_video_progress',				array($this,'add_progress'));

	}

	/**
	*	Process ajax call and add progress
	*
	*	@since 5.0
	*/
	function add_progress(){


		if ( isset( $_POST['action'] ) && $_POST['action'] == 'process_video_progress' ) {

	    	if ( wp_verify_nonce( $_POST['nonce'], 'process_video_progress' ) ) {

	    		$user_id 	= is_user_logged_in() ? get_current_user_id() : false;
	    		$video_id   = isset( $_POST['video_id'] ) ? $_POST['video_id'] : false;
	    		$percent    = isset( $_POST['percent'] ) ? $_POST['percent'] : false;

	    		cgc_video_tracking_add_progress( $video_id, $percent );

		       	wp_send_json_success();

		    }

	  	} else {

	  		wp_send_json_error();

	  	}

	}
}
new cgcVideoTrackingProcess();