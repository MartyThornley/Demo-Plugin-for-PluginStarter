<?php
/**
 * @package PluginStarter Demo Plugin
 * @version 1.0.1
 */
/*
Plugin Name: PluginStarter Demo Plugin
Plugin URI: http://martythornley.com
Description: A demo/test plugin that uses plugin starter.
Author: Marty Thornley
Version: 1.0.1
Author URI: http://martythornley.com
*/

	define( 'DEMO_PLUGIN_URL',		plugin_dir_url( __FILE__ ) );
	define( 'DEMO_PLUGIN_DIR',		plugin_dir_path( __FILE__ ) );

	include ( trailingslashit( DEMO_PLUGIN_DIR ) . 'check-for-plugin-starter.php' );
	
	include( trailingslashit( PLUGINSTARTER_PATH ) . 'classes/WP_Dev_Network.php' );
	
	/* 
	 * Create a dev network connection
	 */
	if ( class_exists( 'WP_Dev_Network' ) ) {
		class BlogSite_Plugins_Network extends WP_Dev_Network {
			function definitions() {
				$this->network 				= 'blogsite_plugins_network';
				$this->network_full_name 	= 'BlogSite Plugins';
				$this->server_url 			= 'http://localhost/dev_host/api/info';
				$this->network_logo_url		= 'http://photographyblogsites.com/favicon.ico';
				$this->subscribe_url 		= 'http://photographyblogsites.com';
				$this->network_thumbs_url	= "http://premium.wpmudev.org/wp-content/projects";
				$this->user_agent 			= $this->network . '/' . $this->version;
				$this->minimum_version 		= '3.0.9';
			}
		}
		$BlogSite_Plugins_Network = new BlogSite_Plugins_Network();	
	}
	
?>