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
 * Version:           5.0.1
 * GitHub Plugin URI: https://github.com/cgcookie/cgc-video-tracking
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Set some constants
define('CGC_VIDEO_TRACKING_VERSION', '5.0.1');
define('CGC_VIDEO_TRACKING_DIR', plugin_dir_path( __FILE__ ));
define('CGC_VIDEO_TRACKING_URL', plugins_url( '', __FILE__ ));

require_once( plugin_dir_path( __FILE__ ) . 'public/class-cgc-video-tracking.php' );

register_activation_hook( __FILE__, array( 'CGC_Video_Tracking', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'CGC_Video_Tracking', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'CGC_Video_Tracking', 'get_instance' ) );

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-cgc-video-tracking-admin.php' );
	add_action( 'plugins_loaded', array( 'CGC_Video_Tracking_Admin', 'get_instance' ) );

}
