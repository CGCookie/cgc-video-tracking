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

	    	if ( wp_verify_nonce( $_POST['nonce'], 'process-video-progress' ) ) {

	    		$user_id 	= is_user_logged_in() ? get_current_user_id() : false;
	    		$video_id   = isset( $_POST['video_id'] ) ? $_POST['video_id'] : false;
	    		$percent    = isset( $_POST['percent'] ) ? $_POST['percent'] : false;

	    		$progress  = cgc_video_tracking_add_progress( sanitize_text_field( $video_id ), filter_var( $percent, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) );

	    		if ( !is_wp_error( $progress ) ) {

		       		wp_send_json_success();

	    		} else {

	    			wp_send_json_error();

	    		}
		    }

	  	} else {

	  		wp_send_json_error();

	  	}

	}
}
new cgcVideoTrackingProcess();