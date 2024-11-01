<?php
/*
 Plugin Name: Social Feed Gallery Portfolio
 Plugin URI:
 Description: Instagram Gallery Portfolio plugin is an easy way to create responsive Instagram gallery. 
 Author:  wpdiscover
 Author URI: https://profiles.wordpress.org/wpdiscover/
 Version: 1.3 
 License: GPLv2 (or later)
*/
/** Configuration **/

if ( !defined( 'IGP_WP_CURRENT_VERSION' ) ) {
    define( 'IGP_WP_CURRENT_VERSION', '1.0.1' );
}

define( 'IGP_WP_NAME'             , 'igp_wp' );
define( 'IGP_WP_DIR'              , plugin_dir_path(__FILE__) );
define( 'IGP_WP_URL'              , plugin_dir_url(__FILE__) );

define( 'IGP_WP_INCLUDES'         , IGP_WP_DIR        . 'includes'    . DIRECTORY_SEPARATOR );
define( 'IGP_WP_ADMIN'            , IGP_WP_INCLUDES   . 'admin'       . DIRECTORY_SEPARATOR );
define( 'IGP_WP_LIBRARIES'        , IGP_WP_INCLUDES   . 'libraries'   . DIRECTORY_SEPARATOR );

define( 'IGP_WP_ASSETS'           , IGP_WP_URL . 'assets/' );
define( 'IGP_WP_JS'               , IGP_WP_URL . 'assets/js/' );
define( 'IGP_WP_IMAGES'           , IGP_WP_URL . 'assets/images/' );
define( 'IGP_WP_RESOURCES'        , IGP_WP_URL . 'assets/resources/' );


define('IGP_WP_MASONRY'           , IGP_WP_URL . 'includes/public/templates/masonry-animation');
define( 'igp_wp'                  , 'igp_wp' );


/**
* Activating plugin and adding some info
*/
function igp_activate() {
    update_option( "portfolio-wp-v", IGP_WP_CURRENT_VERSION );
    update_option( "portfolio-wp-v", IGP_WP_CURRENT_VERSION );
    update_option("portfolio-wp-type","FREE");
    update_option("portfolio-wp-installDate",date('Y-m-d h:i:s') );

    // require_once IGP_WP_DIR.'default_image.php';
    
}

/**
 * Deactivate the plugin
 */
function igp_deactivate() {
    // Do nothing
} 

// Installation and uninstallation hooks
register_activation_hook(__FILE__, 'igp_activate' );
register_deactivation_hook(__FILE__, 'igp_deactivate' );



add_image_size( 'igp_image_grid',600,600,true);

/**
 * The core plugin class that is used to define admin-specific hooks,
 * internationalization, and public-facing site hooks.
 */

require IGP_WP_INCLUDES . 'class-portfolio-wp.php';


/**
 * Start execution of the plugin.
*/
function igp_wp_run() {
	//instantiate the plugin class
    $portfolioWp = new Portfolio_WP();
} 
igp_wp_run();

?>