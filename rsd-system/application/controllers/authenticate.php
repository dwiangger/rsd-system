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
		$this->load->helper('form');
		
		$data = array('matrix' => FALSE );
		
		$this->acl->UserTableName('users');
		$this->acl->UserInfoTableName('user_info');
		$data['users'] = $this->acl->getUsers();
		$data['roles'] = $this->acl->getRoles();
		if ($matrix == "view")
		{
			/* display matrix */
			$selectedUsers = $this->input->post('users');
			$selectedRoles = $this->input->post('roles');
			/* If no input data, just display as $matrix==input */
			if ( is_array($selectedUsers) && count($selectedUsers) > 0
				&& is_array($selectedRoles) && count($selectedRoles) > 0 ) {
					$data['selectedUsers'] = array();
					$data['selectedRoles'] = array();
					/* 
					 * scan all posted user id & role id, 
					 * 	remove from $data list 
					 * 	add to matrix data
					 */
					$data['matrix'] = TRUE;
					/* build matrix */
					foreach ($data['users'] as $id => $user) {
						if( in_array($id,$selectedUsers) )
						{
							$data['selectedUsers'][$id] = 
								$user['name'];
						} 
					}
					foreach ($data['roles'] as $id => $role) {
						if( in_array($id,$selectedRoles) )
						{
							$data['selectedRoles'][$id] = 
								$role['name'];
						} 
					}
					/* push to $data */
					$data['permission'] = $this->acl
						->getUsersRolesMatrix(
							$selectedUsers, $selectedRoles);
			}
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
	/**
	 * 
	 * Update matrix
	 */
	public function update_matrix()
	{
		$userIdList = $this->input->post('userIdList');
		$roleIdList = $this->input->post('roleIdList');
		/* checkout posted data */
		foreach ($userIdList as $userId) {
			foreach ($roleIdList as $roleId) {
				if ($this->input->post('checkbox_'.$userId.'_'.$roleId) !== FALSE 
					&& $this->input->post('checkbox_'.$userId.'_'.$roleId) == 1 )
				{
					$this->acl->updatePermissionById($userId,$roleId,1);
				}else 
				{
					$this->acl->updatePermissionById($userId,$roleId,0);
				}
			}
		}
		/* redirect to somewhere, it's better ti redirect to a matrix with all previous users,roles */
		redirect('authenticate/acl_matrix');
	}
	/**
	 * Create and update role
	 */
	public function role($action = "list", $id = 0)
	{
		$action = strtolower($action);
		/* define for crud */
		$this->load->library('crudlib/crud');
		$this->crud->TableName("acl_roles");
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
		/* handle action */
		switch ($action) {
			case 'view_create':
				$this->crud->Link('create',
					site_url('/authenticate/role/create')
				);
				$data = $this->crud->render_createForm();
			break;
			case 'view_edit':
				$this->crud->ItemId($id);
				$this->crud->Link('update',
					site_url('/authenticate/role/update/{item-index}')
				);
				$data = $this->crud->render_editForm();
				if ($data == NULL)
				{
					show_404($this->uri->uri_string(),FALSE);
				}
			break;
			case 'create':
				$id = $this->crud->action_create();
		
				redirect('/authenticate/role', 'refresh');
			break;
			case 'update':
				$this->crud->ItemId($id);
				$this->crud->action_update();
		
				redirect('/authenticate/role', 'refresh');
			break;
			case "list":
			default:
				/* display role list as default */
				$this->crud->PageIndex($id>0?$id:1); // page-id must > 0
				$this->crud->Link("view_edit",
					site_url('/authenticate/role/view_edit/{item-index}'));
				$data = $this->crud->render_list();
			break;
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
}