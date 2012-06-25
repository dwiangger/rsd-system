<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * Provide functions to :<br/>
 * 	- Manage roles, roles-users <br/> 
 * 	- Query roles-users <br/>
 * 	- Query users by roles <br/>
 * @author gcs_an
 *
 */
class Acl {
	/**
	 * load all needed library
	 */
	public function __construct()
	{
		// Set the super object to a local variable for use later
		$this->CI =& get_instance();

		/* load libraries, helpers manually in case not autoload */
		$this->CI->load->database();

		log_message('debug', "ACL Class Initialized");
	}
	/**
	 * @var instance of CI
	 */
	var $CI;
	/**
	 * Role table name
	 */
	const ROLE_TABLE = 'acl_roles';
	/**
	 * User-Role table name
	 */
	const USER_ROLE_TABLE = 'acl_role_user';
	/**
	 * Role management method set 
	 * A role has only unique [name] as identify and [description] 
	 */
	/**
	 * 
	 * Get all available roles. 
	 */
	public function getRoles()
	{
		$list = array();
		$query = $this->CI->db->get(self::ROLE_TABLE);
		foreach ($query->result() as $row) {
			$item = array();
			
			$item['name'] = $row->name;
			$item['description'] = $row->description;
			
			$list[$row->id] = $item;
		}
		
		return $list;
	}
	/**
	 * 
	 * get all available user as 
	 * array(
	 * 	id => array(
	 * 		'user_id' => user id,
	 * 		'name' => full name // ref to user info table 
	 * 	),
	 * 	...
	 * ) 
	 */
	public function getUsers()
	{
		
	}
	/**
	 * 
	 * get a specific role by name
	 * @param string $roleName
	 */
	public function getRole($roleName)
	{
		$query = $this->CI->db->query(
			"SELECT * FROM `"
			.$this->CI->db->dbprefix(self::ROLE_TABLE)
			."` WHERE `name`=? LIMIT 1",
			$roleName);
		if ( $query->num_rows() != 1)
		{
			return FALSE;
		}
		return $query->row();
	}
	/**
	 * 
	 * add a role to list.  
	 * Return FALSE if role name existed or adding failed 
	 * @param string $name
	 * @param text $description
	 */
	public function addRole($name,$description)
	{
		/* check existing role */
		$query = $this->CI->db->query(
			"SELECT * FROM `"
			.$this->CI->db->dbprefix(self::ROLE_TABLE)
			."` WHERE `name`=?"
			,$name);
		if ( $query->num_rows() >= 1 )
		{
			return FALSE;
		}
		/* Do adding */
		$item = array(
			'name' => $name, 
			'description' => $description
		);
		$this->CI->db->insert(self::ROLE_TABLE,$item);
		return TRUE;
	}
	/**
	 * 
	 * Update role info: description 
	 * @param string $name
	 * @param text $description
	 */
	public function updateRole($name, $description)
	{
		$item = array(
			'description' => $description
		);
		$this->CI->db->where('name',$name);
		$this->CI->db->update(self::ROLE_TABLE,$item);
		if ($this->CI->db->affected_rows() >= 1)
		{
			return TRUE;
		}
		return FALSE;
	}
	/**
	 * 
	 * Delete a specific role by name
	 * @param string $name
	 */
	public function deleteRole($name)
	{
		$this->CI->db->where('name',$name);
		$this->CI->db->delete(self::ROLE_TABLE);
		if ($this->CI->db->affected_rows() >= 1)
		{
			return TRUE;
		}
		return FALSE;
	}
	/**
	 * Method to manage user-role
	 */
	/**
	 * 
	 * Get user-roles as matrix. 
	 * user-role has not permitted with have value 0 or not display in matrix. 
	 * $result[user][role] = 1/0/not exist 
	 */
	public function getUsersRolesMatrix()
	{
		$userTableName = $this->CI->authentication->getUserTableName(); 
		$fullUserTableName = $this->CI->db->dbprefix($userTableName);
		$fullRoleUserTableName = 
			$this->CI->db->dbprefix(self::USER_ROLE_TABLE);
		$fullRoleTableName = 
			$this->CI->db->dbprefix(self::ROLE_TABLE);
			
		$this->CI->db->select('id,name,description');
		$query = $this->CI->db->get(self::ROLE_TABLE);
		$roles = $query->result_array();
		$query->free_result();
		
		$this->CI->db->select('id,user_id');
		$query = $this->CI->db->get($fullUserTableName);
		$users = $query->result_array();
		$query->free_result();		

		$matrix = array();
		foreach ($users as $user) {
			$item = array();
			foreach ($roles as $role) {
				$item[$role['name']] = 0;
			}
			$matrix[$user['user_id']] = $item;
		}
		
		$this->CI->db->select($fullUserTableName.'.user_id AS user_id, '
			.$fullRoleTableName.'.name AS role_name, '
			.'permission');
		$this->CI->db->from(self::USER_ROLE_TABLE);
		$this->CI->db->join($userTableName,
			self::USER_ROLE_TABLE.'.user_id='.$userTableName.'.id');
		$this->CI->db->join(self::ROLE_TABLE,
			self::USER_ROLE_TABLE.'.role_id='.self::ROLE_TABLE.'.id');
		$this->CI->db->order_by('user_id,role_name');
		
		$query = $this->CI->db->get();
		$roles_users = array(); 
		foreach ($query->result() as $role_user) {
			if ( $role_user->permission == 1 )
			{
				$matrix[$role_user->user_id][$role_user->role_name] = 1;
			}
		}	
		$query->free_result();

		return $matrix;
	}
	/**
	 * 
	 * Set/clear user's role 
	 * @param string $userId
	 * @param string $roleName
	 * @param 0/1 $permission 0 = clear, 1 = set
	 */
	public function updateUserRole($userId, $roleName, $permission = 1)
	{
		$userTableName = $this->CI->authentication->getUserTableName();
		$fullUserTableName = $this->CI->db->dbprefix($userTableName);
		$fullRoleUserTableName = 
			$this->CI->db->dbprefix(self::USER_ROLE_TABLE);
		$fullRoleTableName = 
			$this->CI->db->dbprefix(self::ROLE_TABLE);
			
		/* Default, this record is not exist in db */
		$exist = array(
			'user_id' => 0,
			'role_id' => 0, 
			'permission' => 0
		);
		/* looking for record for $userId-$role */
		$this->CI->db->select($fullUserTableName.'.id AS user_id, '
			.$fullRoleTableName.'.id AS role_id, '
			.'permission');
		$this->CI->db->from(self::USER_ROLE_TABLE);
    	$this->CI->db->join(self::ROLE_TABLE,
    		self::ROLE_TABLE.'.id='.self::USER_ROLE_TABLE.'.role_id');
    	$this->CI->db->join($userTableName,
    		$userTableName.'.id='.self::USER_ROLE_TABLE.'.user_id');
    	$this->CI->db->where(
    		$userTableName.'.user_id',$userId);
    	$this->CI->db->where(
    		self::ROLE_TABLE.'.name',$roleName);
    	$this->CI->db->limit(1);
    	
    	$query = $this->CI->db->get();
    	if ($query->num_rows() == 1)
    	{
    		$row = $query->row();
    		$exist['user_id'] = $row->user_id;
    		$exist['role_id'] = $row->role_id;
    		if ( $row->permission > 0 )
    		{
    			$exist['permission'] = 1;
    		}
    	}
    	$query->free_result();
    	
		/* if permission != exist[permission] => need to update */
    	if ( $permission != $exist['permission'] ) 
    	{
    		if ( $exist['user_id'] != 0 && 
    			$exist['role_id'] != 0 )
    		{
    			$this->CI->db->where('role_id',$exist['role_id']);
    			$this->CI->db->where('user_id',$exist['user_id']);
    			$this->CI->db->update(
    				'acl_role_user',
    				array(
    					'permission'=>$permission
    				));
    		}else 
    		{
    			/* get users.id */
    			$this->CI->db->where('user_id',$userId);
    			$this->CI->db->limit(1);
    			
    			$query = $this->CI->db->get($userTableName);
    			
    			if ( $query->num_rows() != 1 )
    			{
    				return FALSE;
    			}
    			$id['user'] = $query->row()->id;
    			
    			$query->free_result();
    			/* get roles.id */
    			$this->CI->db->where('name',$roleName);
    			$this->CI->db->limit(1);
    			
    			$query = $this->CI->db->get(self::ROLE_TABLE);
    			
    			if ( $query->num_rows() != 1 )
    			{
    				return FALSE;
    			}
    			$id['role'] = $query->row()->id;
    			
    			$query->free_result();    			
    			/* do update */
    			$this->CI->db->insert(
    				'acl_role_user',
    				array(
    					'role_id' => $id['role'],
    					'user_id' => $id['user'],
    					'permission' => $permission
    				));
    		}
    	}
    	return TRUE;
	}
	
	/**
	 * Check specific role of specific user
	 * Return TRUE/FALSE 
	 * @param string $userId
	 * @param string $roleName
	 */
    public function checkRole($userId, $roleName)
    {
    	$userTableName = $this->CI->authentication->getUserTableName(); 
    	
    	$this->CI->db->select('permission');
		$this->CI->db->from(self::USER_ROLE_TABLE);
    	$this->CI->db->join(self::ROLE_TABLE,
    		self::ROLE_TABLE.'.id='.self::USER_ROLE_TABLE.'.role_id');
    	$this->CI->db->join($userTableName,
    		$userTableName.'.id='.self::USER_ROLE_TABLE.'.user_id');
    	$this->CI->db->where(
    		$userTableName.'.user_id',$userId);
    	$this->CI->db->where(
    		self::ROLE_TABLE.'.name',$roleName);
    	$this->CI->db->limit(1);
    	
    	$query = $this->CI->db->get();
    	if( $query->num_rows() != 1 )
    	{
    		return FALSE;
    	}
    	$row = $query->row();
    	if ( $row->permission > 0 )
    	{
    		return TRUE;
    	}
    	return FALSE;
    }
    /**
     * 
     * Get users (id, userId) has role(s). 
     * @param string/array(string) $roles
     * @param bool $getAny 
     * 		TRUE/default: get user has any role in list
     * 		FALSE: get user has all role in list
     */
    public function getUserByRoles($roles, $getAny = TRUE)
    {
    	$userTableName = $this->CI->authentication->getUserTableName(); 
    	$fullUserTableName = $this->CI->db->dbprefix($userTableName);
    	
    	/* Handle role list */
    	if (!is_array($roles))
    	{
    		$roles = array($roles);
    	}
    	for ($i = 0; $i < count($roles); $i++) {
    		$roles[$i] = (string)$roles[$i];
    	}
    	/* Build query */
    	$this->CI->db->select($fullUserTableName.'.id AS id, '
    		 .$fullUserTableName.'.user_id AS user_id');
    	
    	$this->CI->db->from(self::USER_ROLE_TABLE);
    	$this->CI->db->join(self::ROLE_TABLE,
    		self::ROLE_TABLE.'.id='.self::USER_ROLE_TABLE.'.role_id');
    	$this->CI->db->join($userTableName,
    		$userTableName.'.id='.self::USER_ROLE_TABLE.'.user_id');
    	$this->CI->db->where('permission',1);
    	$this->CI->db->where_in(self::ROLE_TABLE.'.name',$roles);
		if (!$getAny)
		{
			$this->CI->db->group_by(array('id','user_id'));
			$this->CI->db->having('COUNT(*) =',count($roles));
		}
    	
    	$this->CI->db->distinct();
    	$query = $this->CI->db->get();
    	
    	return $query->result();
    }
    
}