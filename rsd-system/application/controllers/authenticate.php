<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Authenticate extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		$this->load->library('auth/authentication');
		$this->load->library('auth/acl');
	}
	/**
	 * 
	 * Default controller
	 */
	public function index()
	{
		$this->authentication->checkLogin(
			TRUE, 	/* redirect */
			'welcome',		/* success */
			'authenticate/login');	/* fail */
	}
	/**
	 * 
	 * Check login and redirect if logged
	 * If receive login data(submit, $userId, $password)
	 * 	Do login 
	 * Default
	 * 	
	 */
	public function login()
	{
		$this->load->helper("form");
		/* logged in ? */
		if ( $this->authentication->checkLogin() )
		{
			redirect('project','refresh');
		} 
		$data = array();
		/* Check submit data */
		if ( $this->input->post('submit') !== FALSE )
		{
			$userId = $this->input->post('user-name');
			$password = md5($this->input->post('password'));
			
			if ( $this->authentication->doLogin(
				$userId, 
				$password) )
				{
					redirect('welcome');
				}else
				{
					$data['displayAlert'] = TRUE;
				}
		}
		$data['numOfFailed'] = $this
			->authentication
			->getNumOfFailed(); 
		if ( $data['numOfFailed'] > 0 )
		{
			$delay = strtotime("now") - 
				$this->authentication->getLastTried();
			if ( $data['numOfFailed'] >= $this->authentication->getMaxNumOfTried() 
				&& $delay <= $data['numOfFailed']*60 )
				{
					$data['delay'] = $delay;
				}
		}
			
		/* Use template library instead */
		$this->template->write_view(
			'_content', 
			'login_form', 
			$data, 
			TRUE);
		$this->template->render();
	}
	
	public function logout()
	{
		$this->authentication->doLogout();
		redirect('welcome');
	}
	/**
	 * ACL control:
	 */
	public function acl_matrix($matrix = "input")
	{
		$data = array();
		
		$this->acl->UserTableName('users');
		$this->acl->UserInfoTableName('user_info');
		$data['users'] = $this->acl->getUsers();
		$data['roles'] = $this->acl->getRoles();
		if ($matrix == "view")
		{
			/* display matrix */
			/* 
			 * scan all posted user id & role id, 
			 * 	remove from $data list 
			 * 	add to matrix data
			 */
			
			/* build matrix */
			
			/* push to $data */
			
		}
		/* default display input form */
		
		/* render */
		/* Use template library instead */
		$this->template->write_view(
			'_content', 
			'acl_matrix', 
			$data, 
			TRUE);
		$this->template->render();
	}
}