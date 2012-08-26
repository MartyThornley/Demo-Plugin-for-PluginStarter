<?php
/**
 * @package PluginStarter Demo Plugin
 * @version 1.0
 */
/*
Plugin Name: PluginStarter Demo Plugin
Plugin URI: http://martythornley.com
Description: A demo/test plugin that uses plugin starter.
Author: Marty Thornley
Version: 1.0
Author URI: http://martythornley.com
*/

	define( 'DEMO_PLUGIN_URL',		plugin_dir_url( __FILE__ ) );
	define( 'DEMO_PLUGIN_DIR',		plugin_dir_path( __FILE__ ) );

	include ( trailingslashit( DEMO_PLUGIN_DIR ) . 'check-for-plugin-starter.php' );

?>