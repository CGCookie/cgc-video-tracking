<?php
/**
 *
 * @package   CGC Video Tracking
 * @author    Nick Haskins <nick@cgcookie.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 *
 * Plugin Name:       CGC Video Tracking
 * Plugin URI:        http://cgcookie.com
 * Description:       Records percentage watched for a video id
 * Version:           5.0.2
 * GitHub Plugin URI: https://github.com/cgcookie/cgc-video-tracking
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Set some constants
define('CGC_VIDEO_TRACKING_VERSION', '5.0.2');

define('CGC_VIDEO_TRACKING_URL', plugins_url( '', __FILE__ ));

register_activation_hook( __FILE__, array( 'cgcVideoTracking', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'cgcVideoTracking', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'cgcVideoTracking', 'get_instance' ) );
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	add_action( 'plugins_loaded', array( 'cgcVideoTrackingAdmin', 'get_instance' ) );

}
