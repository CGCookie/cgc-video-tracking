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
