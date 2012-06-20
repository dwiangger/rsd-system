<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project extends CI_Controller {
	/* mapping for generate CRUD */
	var $_definition;
	
	function __construct()
	{
		parent::__construct();
		//For dev debug
		//$this->output->enable_profiler(TRUE);
		$this->load->library('auth/authentication');
		$this->load->library('crudlib/crud');
		
		$this->crud->TableName("projects");
		
		$this->crud->Option("navLink",
			$this->config->item('base_url')."index.php/project/view_list/{page-index}");
		$this->crud->Option("editLink",
			$this->config->item('base_url')."index.php/project/view_edit/{item-index}");
		$this->crud->Option("confirmDeleteLink",
			$this->config->item('base_url')."index.php/project/view_confirm/{item-index}");
		
		$this->crud->ColumnDefine(array(
			'id' => array(
				'display' => FALSE,
				'primary' => TRUE
			),
			'name' => array(
				'display' => TRUE, 
				'header' => "Name"
			),
			'description'=>array(
				'display' => TRUE,
				'header' => "Description",
				'inputType' => 'textarea',
				'inputData' => array(
					'value' => 'team desc',
					'description' => 'Team description'
				)
			)
		));
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
		
		$teamMenu = array();
		
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
		/* get all project and add to menu */
		$projectMenu = array();
		$query = $this->db
			->get('projects');
		$data['projects'] = Array(); 
		foreach ($query->result() as $project) {
			$projectMenu[] = array(
				"name" => $project->name,
				"link" => "index.php/project/index/".$project->id,
				"active" => FALSE
			);
		}
		$this->template->write_view(
			'_navigation',
			'template/navigation',
			array(
				'nav' => array(
					array(
						"name" => "Home", 
						"link" => "", 
						"active" => FALSE, 
						"child" => array() 
					),
					array(
						"name" => "All projects", 
						"link" => "", 
						"active" => FALSE, 
						"child" => $projectMenu /* get all projects as menu here */
					),
					array(
						"name" => "Teams", 
						"link" => "", 
						"active" => TRUE, 
						"child" => array()
					)
				)
			));
		$this->template->write_view(
			'_content','project_view',$data);
		$this->template->render();
	}

	public function view_list($page = 1)
	{
		//$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');
		$this->crud->PageIndex($page);
		$data = $this->crud->render_list();
		
		/* Result */
		$this->template->write_view(
			'_navigation',
			'template/navigation',array());
		$this->template->write(
			'_content',
			$data);
		$this->template->render();
	}
	
	public function view_detail()
	{
		//$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');
	}
	/**
	 * Display update form 
	 */
	public function view_edit($id)
	{
		//$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');
	}
	
	public function view_confirm($id)
	{
		//$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');
	}
	/**
	 * Perform updating
	 */
	public function update()
	{
		//$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');
	}
}
?>