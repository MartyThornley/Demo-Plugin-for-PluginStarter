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
  * Add versiuon number to each function - check_for_plugin_starter_notice_1 - to make sure we are not using old functions?
  * Test against existing pluginstarter version if it exists.
  */

if ( ! defined( 'PLUGIN_STARTER_LATEST_ZIP' ) ) define ( 'PLUGIN_STARTER_LATEST_ZIP' , 'http://pluginstarter.com/latest.zip' );

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
					$msg = 'The PluginStarter is MAYBE installed';
				break;
		}
		if ( $msg )
			echo '<div class="error fade">'.$msg.'</div>';
	}
}

if ( ! function_exists( 'plugin_starter_download' ) ) {
	function plugin_starter_download( $creds = '' ){
		global $wp_filesystem;

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
		
		if ( ! file_exists( WP_CONTENT_DIR . '/plugins/plugin-starter' ) ) { 
			$result = unzip_file ( $tempfname , WP_CONTENT_DIR . '/plugins' );
		}
		unlink ( $tempfname );
		
		if ( $result === true )
			update_option( 'plugin_starter_download' , 'Success' );
	}
}

if ( ! function_exists( 'check_for_plugin_starter' ) ) {
	function check_for_plugin_starter () {
		$exists = 'no';
		$basedir = trailingslashit( WP_CONTENT_DIR ) . 'plugin-starter';

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