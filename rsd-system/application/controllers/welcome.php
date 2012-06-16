<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		//For dev debug
		//$this->output->enable_profiler(TRUE);
		$this->load->library('auth/authentication');
	}
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');

		$query = 
			$this
				->db
				->get('projects');
		$data['projects'] = Array(); 
		foreach ($query->result() as $project) {
			$item = Array();
			
			$item['id'] =  $project->id;
			$item['name'] =  $project->name;
			$item['description'] =  $project->description;
			
			$data['projects'][] = $item;
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
						"name" => "All Project", 
						"link" => "", 
						"active" => TRUE, 
						"child" => array()
					)
				)
			));
				
		$this->template->write_view(
			'_content',
			'welcome_message',
			$data);
		$this->template->render();
	}
	
	public function test()
	{
		$this->load->library('crudlib/crud');
		$rsl = TRUE;
		/* Attack */
		$this->crud->TableName("projects");
		$this->crud->PageSize(10);
		$this->crud->FirstItemIndex(15);
		$this->crud->ColumnDefine(array(
			'id' => array('display' => FALSE),
			'name' => array(
				'display' => TRUE, 
				'header' => "Name"
			),
			'description' => array(
				'display' => TRUE,
				'header' => "Description"
			)
		));
		echo $this->crud->render_list();
		/* Result */
		if ($rsl)
		{
			echo 'Things are OK.';
		}else
		{
			echo 'Adding failed';
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */