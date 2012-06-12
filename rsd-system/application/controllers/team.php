<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Team extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		//For dev debug
		//$this->output->enable_profiler(TRUE);
		$this->load->library('auth/authentication');
	}
	public function index($teamId = 0)
	{
		$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');
		
		$this->db->where('id',$teamId);
		$this->db->limit(1);
		$query = $this->db
				->get('teams');
		if ( $query->num_rows() != 1 )
		{
			show_404($this->uri->uri_string(),FALSE);
		}
		$team = $query->row();
		$data['team'] = Array();
		$data['team']['id'] = $team->id;
		$data['team']['name'] = $team->name;
		$data['team']['description'] = $team->description;	
		$query->free_result();
		
		$this->db->from('users');
		$this->db->select($this->db->dbprefix('users').'.user_id, '
			.$this->db->dbprefix('users').'.id');
		$this->db->join('team_user',
			$this->db->dbprefix('users').'.id='
			.$this->db->dbprefix('team_user').'.user_id');
		$this->db->where('team_user.team_id',$teamId);
		$query = $this->db->get();
		
		$data['users'] = Array(); 
		foreach ($query->result() as $user) {
			$item = Array();
			
			$item['id'] =  $user->id;
			$item['user_id'] =  $user->user_id;
			
			$data['users'][] = $item;
		}
		$this->template->write_view(
			'_content','team_view',$data);
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