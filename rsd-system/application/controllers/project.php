<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		//For dev debug
		//$this->output->enable_profiler(TRUE);
		$this->load->library('auth/authentication');
	}
	public function index($projectId = 0)
	{
		$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');
		
		$this->db->where('id',$projectId);
		$this->db->limit(1);
		$query = $this->db
			->get('projects');
		if ( $query->num_rows() != 1 )
		{
			show_404($this->uri->uri_string(),FALSE);
		}
		$project = $query->row();
		$data['project'] = Array();
		$data['project']['id'] = $project->id;
		$data['project']['name'] = $project->name;
		$data['project']['description'] = $project->description;	
		
		$this->db->where('project_id',$projectId);
		$query = $this->db
			->get('teams');
		$data['teams'] = Array(); 
		foreach ($query->result() as $team) {
			$item = Array();
			
			$item['id'] =  $team->id;
			$item['name'] =  $team->name;
			$item['description'] =  $team->description;
			
			$data['teams'][] = $item;
		}
		$this->template->write_view(
			'_navigation',
			'template/navigation',
			array( 'nav' => array(
				'Home' => array(
					'All project' => '',
					$project->name => 'index.php/project/index/'.$project->id
				)
			)));
		$this->template->write_view(
			'_content','project_view',$data);
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