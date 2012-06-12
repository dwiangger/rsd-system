<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		//For dev debug
		//$this->output->enable_profiler(TRUE);
		$this->load->library('auth/authentication');
	}
	/**
	 * Display personal info
	 * @param number/text $user_id user string_id/ number_id 
	 */
	public function index($user_id = 0)
	{
		$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');
		/* Get user_id */
		$this->db->where('id',$user_id);
		$this->db->or_where('user_id',$user_id);
		$query = $this->db->get('users');
		if ( $query->num_rows() != 1 )
		{
			show_404($this->uri->uri_string(),FALSE);
		}
		$user = $query->row();
		$data['userId'] = $user->user_id;
		
		/* Get user_info */
		$this->db->where('id',$user->id);
		$query = $this->db->get('user_info');
		if ( $query->num_rows() == 1 )
		{
			$data['userInfo'] = $query->row();
		}else 
		{
			$data['userInfo'] = array();
		}
		
		$this->template->write_view('_content','user_view',$data);
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
