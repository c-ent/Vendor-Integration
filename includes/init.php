<?php 

namespace Includes;

final class Init {
    /**
	 * Store all the classes inside an array
	 * @return array Full list of classes
	 */
	public static function get_services(){
		return [
			Pages\Admin::class,		
		];
	}


    /**
	 * Loop through the classes, initialize them, and call the register() method if it exist
	 * @return
	 */
	public static function register_services() {

		foreach ( self::get_services() as $class ) {
			$service = self::instantiate( $class );
			
			if ( method_exists( $service, 'register' ) ){
				$service->register();
			}
		}
	}


    	/**
	 * Initialize the class
	 * @param class $class class from the services array
	 * @return class instance new instance of the class
	 */
	private static function instantiate( $class ){
		$service = new $class();
		return  $service;
	}
	


    /**
	 * Initialize the temaplate
	
	 */
	public static function load_template(){
		
		global $getThisTemplates;
		
		
		$templates_arr = array();
		$fileList = glob(plugin_dir_path( dirname( __FILE__, 1 ) ) . 'templates/*');
		
		//print_r (plugin_dir_path( dirname( __FILE__, 1 ) ) . 'templates/*');
		foreach($fileList as $filename){
			//Use the is_file function to make sure that it is not a directory.
			
			if(is_file($filename)){
				$info = pathinfo($filename);
				
				if($info['extension'] == 'php' ){
					$templates_arr[$info['filename']] = $info['dirname'].'/'. $info['basename'] ;
					//echo $info['filename'];
					
				}
				
			}  
			if(is_dir($filename)) {
				$nextinfo = pathinfo($filename);
				$load_reccuresive_file_templates =  self::load_reccuresive_file_templates($filename , $nextinfo['basename']);
				
				$templates_arr = array_merge($templates_arr,$load_reccuresive_file_templates);
			}
		} 
				
		

		$getThisTemplates = $templates_arr;
		// print_r($templates);
	}
	
	/**
	 * Initialize the temaplate
	 * @param class path file template folder
	 * @return class array templates
	 */
	public static function load_reccuresive_file_templates($filename , $nextinfo){
		
		$nextFolder = glob($filename."/*");
		$templates_arr = array();
		foreach($nextFolder as $nextfilename){
			if(is_file($nextfilename)){
				$info = pathinfo($nextfilename);
				
				if($info['extension'] == 'php' ){
					$templates_arr[$nextinfo.'/'.$info['filename']] = $info['dirname'].'/'. $info['basename'] ;
					
					
				}
				
			} 
			if(is_dir($nextfilename)) {
				$secondinfo = pathinfo($nextfilename);
				$array =  self::load_reccuresive_file_templates($nextfilename , $nextinfo.'/'.$secondinfo['basename']);
				
				$templates_arr = array_merge($templates_arr,$array);
			}
			
		} 
		
		return $templates_arr;
	}
	

}
