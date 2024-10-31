<?php

/*
Plugin Name: Recotrust Integration Plugin
Plugin URI: https://www.recotrust.com
Description: Sends invitation requests to Recotrust and displays Recotrust widgets on ecommerce sites.
Version: 1.0.6
Author: Recotrust, Angry Creative, Elias Chalhoub
Author URI: https://angrycreative.se
License: GPL2
*/

spl_autoload_register(
	function( $classname ) {
			$classname = explode( '\\', $classname );
			$classfile = sprintf(
				'%sincludes/class-%s.php',
				plugin_dir_path( __FILE__ ),
				str_replace( '_', '-', strtolower( end( $classname ) ) )
			);

		if ( file_exists( $classfile ) ) {
			include_once $classfile;
		}
	}
);

\Ac_Reco_Plugin\Plugin::get_instance();
