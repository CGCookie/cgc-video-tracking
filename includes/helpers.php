<?php

/**
*	Drop the progress of a specific video for a specific user
*	@param $args array an array of data to be recorded into the database
*	@since 5.0
*/
function cgc_video_tracking_remove_progress( $video_id = 0 ){

	// bail out if we dont' have a video id or percent
	if ( empty( $video_id ) )
		return;

	$user_id = get_current_user_ID();

	$db = new CGC_Video_Tracking_DB;

	$db->drop_progress( $video_id, $user_id );
}

/**
*	Add the percentage of a video watched for a specific user
*	@param $args array an array of data to be recorded into the database
*	@since 5.0
*/
function cgc_video_tracking_add_progress( $video_id = 0, $percent = 0, $length = 0 ){

	// bail out if we dont' have a video id or percent
	if ( empty( $video_id ) || empty( $percent ) )
		return;

	$user_id = get_current_user_ID();

	$db = new CGC_Video_Tracking_DB;

	$args = array(
		'video_id' 		=> $video_id,
		'user_id'		=> $user_id,
		'percent'		=> $percent,
		'length'		=> $length,
		'created_at' 	=> time()
	);

	$db->drop_progress( $video_id, $user_id );
	$db->add_progress( $args );
}

/**
*	Update the percentage of a video watched for a specific user
*	@param $args array an array of data to be recorded into the database
*	@since 5.0.3
*/
function cgc_video_tracking_update_progress( $video_id = 0, $percent = 0 ){

	// bail out if we dont' have a video id or percent
	if ( empty( $video_id ) || empty( $percent ) )
		return;

	$user_id = get_current_user_ID();

	$db = new CGC_Video_Tracking_DB;

	$args = array(
		'video_id' 		=> $video_id,
		'user_id'		=> $user_id,
		'percent'		=> $percent
	);

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
function cgc_video_tracking_get_total_watched( $user_id = 0, $time = false ) {

	if ( empty( $user_id ) )
		$user_id = get_current_user_ID();

	$db = new CGC_Video_Tracking_DB;


	$out = $db->get_total_watched_length( $user_id, $time );

	return !empty( $out ) ? $out : false;
}

/**
*	Return the last video that a user has watched
*	@param $user_id int id of the user to get the last watched video for
*	@param $count int number to return, 1 by default
*/
function cgc_video_tracking_get_recently_watched( $user_id = 0, $count = 0 ) {

	if ( empty( $user_id ) )
		$user_id = get_current_user_ID();

	$db = new CGC_Video_Tracking_DB;

	$out = $db->get_recently_watched( $user_id, $count );

	return !empty( $out ) ? $out : false;
}
