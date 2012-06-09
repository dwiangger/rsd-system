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
		
		$query = 
			$this
				->db
				->query('SELECT * FROM `dsr2_teams` WHERE `id`=? LIMIT 1',array($teamId));
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
		
		$query = 
			$this
				->db
				->query('SELECT `dsr2_users`.`user_id`,`dsr2_users`.`id` 
					FROM `dsr2_users`,`dsr2_team_user` 
					WHERE `dsr2_users`.`id`=`dsr2_team_user`.`user_id` 
						AND `dsr2_team_user`.`team_id`=?',array($teamId));
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