<?php

/**
*	Process the incoming ajax call and record progress to table
*
*	@since 5.0
*/
class cgcVideoTrackingProcess implements cgcApiInterface {


	/**
	 * Processing callback for "process_video_progress" action via REST API
	 *
	 * @since 6.0.0
	 *
	 * @param array $data Sanatized data
	 *
	 * @return array|string|WP_Error
	 */
	static public function process( $data, $method ) {
		$user_id 	= is_user_logged_in() ? get_current_user_id() : false;
		$video_id   = $data['video_id'];
		$percent    = $data[ 'percent' ];
		$length    = $data[ 'length' ];

		$has_progress = cgc_video_tracking_get_user_progress( $user_id, $video_id );

		if ( $has_progress ) {

			cgc_video_tracking_update_progress( $video_id, $percent, $length );

		} else {

			cgc_video_tracking_add_progress( $video_id, $percent, $length );
		}

		return true;


	}

	/**
	 * Return a string with the name of the API action/route
	 *
	 * IMPORTANT: This action must match nonce.
	 *
	 *  @since 6.0.0
	 *
	 * @return string
	 */
	static public function action() {
		return 'process_video_progress';
	}

	/**
	 * Define fields for endpoint(s) of route created for this
	 *
	 * @since 6.0.0
	 *
	 * @return array
	 */
	static public function fields() {
		return array(
			'video_id' => array(
				'default'            => 0,
				'type'               => 'integer',
				'sanitize_callback'  => 'cgc_rest_api_safe_string',
			),
			'percent' => array(
				'default'            => 0,
				'type'               => 'integer',
				'sanitize_callback'  => 'cgc_rest_api_ensure_float',
			),
			'length' => array(
				'default'            => 0,
				'type'               => 'integer',
				'sanitize_callback'  => 'cgc_rest_api_ensure_float',
			),

		);
	}

}

