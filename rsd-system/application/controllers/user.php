<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		//For dev debug
		//$this->output->enable_profiler(TRUE);
		$this->load->library('auth/authentication');
	}
	public function index()
	{
		$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');
		$this->template->write_view('_content','user_view');
		$this->template->render();
	}
	/**
	 * Display update form 
	 */
	public function edit()
	{
		$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');
	}
	/**
	 * Perform updating
	 */
	public function update()
	{
		$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');
	}
}
?>
