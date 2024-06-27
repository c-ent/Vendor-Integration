<?php 
/**
* @package Vendor Integration
*/
/*
Plugin Name: Vendor Integration
Plugin URI: 
Description: 
Version: 1.1.4
Author: 
Author URI : 
*/


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ){
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

/* Plugin Activation */
function activate_vendorintegration() {
	Includes\Base\Activate::activate();
}

/* Plugin Deactivation */
function deactivate_vendorintegration() {
	Includes\Base\Deactivate::deactivate();
}

register_activation_hook( __FILE__, 'activate_vendorintegration' );
register_deactivation_hook( __FILE__, 'deactivate_vendorintegration' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
// require plugin_dir_path( __FILE__ ) . 'includes/class-plugin-name.php';

// function run_plugin_name() {

// 	$plugin = new Plugin_Name();
// 	$plugin->run();

// }

// Include the Init folder, Initialize all the core classes of the plugin
if ( class_exists( 'Includes\\Init' ) ) {
	global $getThisTemplates;
	
	Includes\Init::load_template();
	Includes\Init::register_services();
}

