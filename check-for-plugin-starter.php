<?php
/*
 * Checks for an existing correct version of the PluginStarter
 *
 * Inspiration and small bits of code from the following plugins:
 * Developer by Automattic
 * Shadowbox-js
 */

 /*
  * Todos
  *
  * Add version number to each function - check_for_plugin_starter_notice_1 - to make sure we are not using old functions?
  * Test against existing pluginstarter version if it exists.
  */

if ( ! defined( 'PLUGIN_STARTER_LATEST_ZIP' ) ) define ( 'PLUGIN_STARTER_LATEST_ZIP' , 'https://github.com/MartyThornley/PluginStarter/zipball/master' );

add_action( 'admin_init' , 'check_for_plugin_starter' );

if ( ! function_exists( 'check_for_plugin_starter_notice' ) ) {
	function check_for_plugin_starter_notice() {
		$exists = apply_filters( 'check_for_plugin_starter_exists' , '' );
		switch ( $exists ) {
			case 'yes' :
				$msg = 'The PluginStarter is installed';
				break;
			case 'maybe' :
				$msg = 'The PluginStarter is MAYBE installed';
				break ;
			case 'no' :
			 	$msg = get_option( 'plugin_starter_download' );
				if ( $msg == 'Success' )
					$msg = 'The plugin "PluginStarter" should be <a href="'.admin_url( 'plugins.php' ).'">activated</a>.';
				break;
		}
		if ( $msg )
			echo '<div class="error fade"><p>'.$msg.'</p></div>';
	}
}

if ( ! function_exists( 'plugin_starter_download' ) ) {
	function plugin_starter_download( $creds = '' ){
		global $wp_filesystem;
		
		$mu_file = WP_CONTENT_DIR . '/mu-plugins/plugin-starter.php';
		$this_plugin_dir = plugin_dir_path( __FILE__ );
		$core_dir = trailingslashit( $this_plugin_dir ) . 'core';
		$core_file = $core_dir . '/plugin-starter.php';
		
		if ( ! file_exists( $mu_file ) && ! file_exists( $core_file ) ) { 

			if ( empty ( $creds ) )
				$creds = request_filesystem_credentials ( '' );
	
			if ( ! WP_Filesystem ( $creds ) ) {
				update_option( 'plugin_starter_download' , 'Could not access WP FileSystem' );
				return false;
			}
			
			$tempfname = download_url ( PLUGIN_STARTER_LATEST_ZIP );
	
			if ( is_wp_error ( $tempfname ) ) {
				update_option( 'plugin_starter_download' , 'Could not download file' );
				return false;
			}
			
			if ( ! file_exists ( $tempfname ) ) {
				update_option( 'plugin_starter_download' , 'File downloaded but unreadable' );
				return false;
			}
			
			if ( ! file_exists( trailingslashit( $this_plugin_dir ) . 'core' ) )
				mkdir ( trailingslashit( $this_plugin_dir ) . 'core' );
			
			$result = unzip_file ( $tempfname , $core_dir );
			unlink ( $tempfname );
		
		}
		
		if ( $result === true || is_file( $core_file ) )
			update_option( 'plugin_starter_download' , 'Success' );
		else 
			update_option( 'plugin_starter_download' , 'Problem Unzipping file' );

	}
}

if ( ! function_exists( 'check_for_plugin_starter' ) ) {
	function check_for_plugin_starter () {
		$exists = 'no';
		$basedir = trailingslashit( WP_CONTENT_DIR ) . '/plugins/plugin-starter';

		// does the class exist? - check for mu-plugins or other plugin		
		if ( class_exists( 'Plugin_Starter' ) ) {
			$exists = 'yes';
		// is the plugin installed but maybe not activated?
		} elseif ( file_exists ( $basedir ) ) {		
			$exists = 'maybe';
		} else {
			plugin_starter_download();
			$exists = 'no';
		}

		// check version?
		
		add_filter ( 'check_for_plugin_starter_exists' , create_function('', "return $exists;") );				
		add_action ( 'admin_notices' , 'check_for_plugin_starter_notice' );
	}
}

// finally... load the plugin starter if it has downloaded or exists:
if ( file_exists( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'core/plugin-starter.php' ) ) {
	include( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'core/plugin-starter.php' );
}
