<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @author thien.an
 *
 */
class CRUD {
	/**
	 * load all needed library
	 */
	public function __construct()
	{
		// Set the super object to a local variable for use later
		$this->CI =& get_instance();

		/* load libraries, helpers manually in case not autoload */
		$this->CI->load->database();

		log_message('debug', "crudlib/CRUD Class Initialized");
	}
	/**
	 * @var instance of CI
	 */
	var $CI;
   
}