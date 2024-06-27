<?php 

/**
* Trigger this file on Plugin install
*
* 
*/

namespace Includes\Base;

class Activate {

	public static function activate(){
		
		flush_rewrite_rules();
		// self::create_plugin_database_table();

	}
	function create_plugin_database_table()
	{
			/* 
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
			dbDelta($sql); */
		
	}

}