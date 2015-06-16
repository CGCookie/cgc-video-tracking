<?php

/**
*	Update the percentage of a video watched for a specific user
*	@param $args array an array of data to be recorded into the database
*	@since 5.0
*/
function cgc_video_tracking_add_progress( $video_id = 0, $percent = 0 ){

	// bail out if we dont' have a video id or percent
	if ( empty( $video_id ) || empty( $percent ) )
		return;

	$user_id = get_current_user_ID();

	$db = new CGC_Video_Tracking_DB;

	$args = array(
		'video_id' 	=> $video_id,
		'user_id'	=> $user_id,
		'percent'	=> $percent
	);

	$db->drop_progress( $video_id, $user_id );
	$db->update_progress( $args );
}


/**
*	Get the progress of a video by user id
*	@param $user_id int id of the user to get the progress for
*	@param $video_id string if of the video to get the progress for
*/
function cgc_video_tracking_get_user_progress( $user_id = 0, $video_id = 0 ) {

	if ( empty( $user_id ) )
		$user_id = get_current_user_ID();

	if ( empty( $video_id ) )
		return;

	$db = new CGC_Video_Tracking_DB;
	$out = $db->get_user_progress( $user_id, $video_id );

	return !empty( $out ) ? $out : false;
}


/**
*	Return an array of percentages on videos watched for this user
*
*	@param $user_id int id of the user to get the videos for
*	@param $time bool should we return the total watched within the last week
*/
function cgc_video_tracking_get_users_videos( $user_id = 0, $time = false ) {

	if ( empty( $user_id ) )
		$user_id = get_current_user_ID();

	$db = new CGC_Video_Tracking_DB;


	$out = $db->get_total_watched_percent( $user_id, $time );

	return !empty( $out ) ? $out : false;
}