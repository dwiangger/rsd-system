<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Team extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		//For dev debug
		//$this->output->enable_profiler(TRUE);
		$this->load->library('auth/authentication');
				$this->load->library('crudlib/crud');
		
		$this->crud->TableName("teams");
		
		$this->crud->Link("view_list",
			$this->config->item('base_url')."index.php/team/view_list/{page-index}");
		$this->crud->Link("view_edit",
			$this->config->item('base_url')."index.php/team/view_edit/{item-index}");
		$this->crud->Link("view_confirmDelete",
			$this->config->item('base_url')."index.php/team/view_confirm/{item-index}");
		$this->crud->Option('itemName','Team');
			
		$this->crud->ColumnDefine(array(
			'id' => array(
				'display' => FALSE,
				'primary' => TRUE
			),
			'name' => array(
				'display' => TRUE, 
				'header' => "Name",
				'width' => 240
			),
			'description'=>array(
				'display' => TRUE,
				'header' => "Description",
				'inputType' => 'textarea',
				'class' => 'span5',
				'inputData' => array(
					'value' => 'team desc',
					'description' => 'Team description'
				)
			),
			'project_id'=>array(
				'display' => true,
				'header' => 'Project',
				'ref' => array(
					'displayCol' => 'name',
					'firstChain' => 'projects',
					'lastChain' => 'projects',
					'chain' => array(
						'projects' => array(
							'indexCol' => 'id',
							'refCol' => NULL, 
							'nextChain' => NULL
						)
					)
				)
			)
		));
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
	public function view_list($page = 1)
	{
		//$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');
		$this->crud->PageIndex($page);
				$this->crud->Link("view_detail",
			$this->config->item('base_url')."index.php/team/view_detail/{item-index}");
		
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
	
	public function view_detail($id = 0)
	{
		//$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');
		$this->crud->ItemId($id);
		$data = $this->crud->render_detail();
		
		if ($data == NULL)
		{
			show_404($this->uri->uri_string(),FALSE);
		}
		/* Result */
		$this->template->write_view(
			'_navigation',
			'template/navigation',array());
		$this->template->write(
			'_content',
			$data);
		$this->template->render();
	}
	/**
	 * Display update form 
	 */
	public function view_edit($id = 0)
	{
		//$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');
		$this->crud->ItemId($id);
		$this->crud->Link('update',
			site_url('/team/update/{item-index}')
		);
		$data = $this->crud->render_editForm();
		
		if ($data == NULL)
		{
			show_404($this->uri->uri_string(),FALSE);
		}
		/* Result */
		$this->template->write_view(
			'_navigation',
			'template/navigation',array());
		$this->template->write(
			'_content',
			$data);
		$this->template->render();
	}
	
	public function view_confirm($id = 0)
	{
		//$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');
		$this->crud->ItemId($id);
		$this->crud->Link('delete',
			site_url('/team/delete/{item-index}')
		);		
		$data = $this->crud->render_confirmDelete();
		
		if ($data == NULL)
		{
			show_404($this->uri->uri_string(),FALSE);
		}
		/* Result */
		$this->template->write_view(
			'_navigation',
			'template/navigation',array());
		$this->template->write(
			'_content',
			$data);
		$this->template->render();
	}
	
	public function view_create()
	{
		//$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');
		$this->crud->Link('create',
			site_url('/team/create')
		);
		$data = $this->crud->render_createForm();

		/* Result */
		$this->template->write_view(
			'_navigation',
			'template/navigation',array());
		$this->template->write(
			'_content',
			$data);
		$this->template->render();
	}
	/**
	 * Perform create
	 */
	public function create()
	{
		//$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');
		$id = $this->crud->action_create();

		redirect('/team/view_detail/'.$id, 'refresh');
	}
	/**
	 * Perform updating
	 */
	public function update($id = 0)
	{
		//$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');
		$this->crud->ItemId($id);
		$this->crud->action_update();

		redirect('/team/view_detail/'.$id, 'refresh');

	}
	/**
	 * Perform delete
	 */
	public function delete($id = 0)
	{
		//$this->authentication->checkLogin(TRUE,NULL,'authenticate/login');
		$this->crud->ItemId($id);
		$this->crud->action_delete();
		
		redirect('/team/view_list/', 'refresh');
	}
}
?>