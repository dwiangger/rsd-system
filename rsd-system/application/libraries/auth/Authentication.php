<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * Provide functions to 
 * @author gcs_an
 *
 */
class Authentication {

	/**
	 * @var instance of CI
	 */
	var $CI;
	/**
	 * table name
	 */
	const USER_TABLE = 'users';
	/**
	 * unique key in session
	 */
	const SESSIONKEY = 'loginData';
	const MAX_NUM_OF_TRIED = 3;
	/**
	 * @var log in data: 
	 * 	userId 
	 * 	logTime
	 * 	NumOfTried 
	 * 	loggedIn
	 */
	var $_loginData;
	/**
	 * 
	 * @param unknown_type $params
	 */
	public function __construct()
	{
		// Set the super object to a local variable for use later
		$this->CI =& get_instance();

		/* load libraries, helpers manually in case not autoload */
		$this->CI->load->library('session');
		$this->CI->load->helper('url');
		$this->CI->load->database();

		// get login data if exist
		$this->getSession();

		log_message('debug', "Authentication Class Initialized");
	}
	/**
	 * Check session to determine does current session user loged in.
	 * Return TRUE/FALSE if not $redirect  
	 */
    public function checkLogin(
    	$redirect = FALSE, /* if TRUE, redirect after check */ 
    	$successTargetURI = NULL, /* redirect to this uri if logged in*/
    	$failTargetURI = NULL) /* redirect to this uri if not logged in*/
    {
    	if ( !$redirect )
    	{
    		return $this->_loginData['loggedIn'];
    	}
    	if ($this->_loginData['loggedIn'])
    	{
    		if ( $successTargetURI != NULL)
    		{
    			redirect($successTargetURI);
    		}
    		return TRUE;
    	}else
    	{
    		if ( $failTargetURI != NULL )
    		{
    			redirect($failTargetURI);
    		}
    		return FALSE;
    	}
    }
    /**
     * Get number of failed
     */
    public function getNumOfFailed()
    {
    	return $this->_loginData['NumOfTried'];
    }
    /**
     * Get user table name
     */
    public function getUserTableName()
    {
    	return self::USER_TABLE;
    } 
    /**
     * Get time of last tried
     */
    public function getLastTried()
    {
    	return $this->_loginData['logTime'];
    }
    /**
     * get max num of tried
     */
    public function getMaxNumOfTried()
    {
    	return self::MAX_NUM_OF_TRIED;
    }
    /**
     * get current logged useId. 
     * return FALSE if not logged yet
     */
    public function getUserid()
    {
    	if ( $this->_loginData['loggedIn'] != TRUE )
    	{
    		return FALSE;
    	}
    	return $this->_loginData['userId'];
    }
    /**
     * 
     * Check userId and password, 
     * 	If OK, write tracked data to session and return TRUE
     * 	Else, return FALSE  
     * @param string $userId
     * @param md5 string $md5pwd
     */
    public function doLogin($userId, $md5pwd)
    {
    	/* Check $userId vs $md5pwd */
    	$userChecked = FALSE;
    	/* SELECT COUNT(*) 
    	 * FROM `user-table` 
    	 * WHERE `user_id`='$userId'
    	 * 	AND `password` = MD5('$md5pwd')
    	 * LIMIT 1 */
    	$query = $this->CI->db->query("SELECT COUNT(*) AS `total`
    		FROM `".$this->CI->db->dbprefix(self::USER_TABLE)."` 
    		WHERE `user_id`=? 
    			AND `password`=MD5(?) 
    		LIMIT 1",
    		array($userId,$md5pwd));
    	if ( $query->num_rows() > 0 )
    	{
    		$row = $query->result();
    		if ( $row[0]->total == 1 )
    		{
    			$userChecked = TRUE;
    		}
    	}
    	/* Write to property & session */
    	if ( $userChecked ) 
    	{
    		$this->_loginData['userId'] = $userId;
    		$this->_loginData['logTime'] = strtotime("now");
    		$this->_loginData['NumOfTried'] = 0;
    		$this->_loginData['loggedIn'] = TRUE;
    	}else 
    	{
    		$this->_loginData['userId'] = '';
    		$this->_loginData['logTime'] = strtotime("now");
    		$this->_loginData['NumOfTried'] = 
    			$this->_loginData['NumOfTried'] + 1;
    		$this->_loginData['loggedIn'] = FALSE;
    	}
		$this->setSession();
    	/* Track any info if needed */
    		
    	/* Return Does log success ? */
    	return $userChecked;
    }
    /**
     * 
     * Log out and clear session
     */
    public function doLogout()
    {
    	if ($this->_loginData['loggedIn'])
    	{
    		$this->_loginData['loggedIn'] = FALSE;
	    	$this->_loginData['userId'] = '';
	    	$this->_loginData['logTime'] = 0;
	    	$this->_loginData['NumOfTried']=0;
	    	
	    	$this->setSession();
    	}
    }
    /**
     * 
     * Push data from properties to session
     */
    private function setSession()
    {
    	$this->CI->session->set_userdata(
    		self::SESSIONKEY,
    		$this->_loginData);
    }
    /**
     * 
     * Pop data from session to properties 
     */
    private function getSession()
    {
    	if (!($this->CI->session
    		->userdata(self::SESSIONKEY) !== FALSE))
		{
			$this->CI->session->set_userdata(
    			self::SESSIONKEY,
    			array(
					'userId' => '',
					'logTime' => 0,
					'NumOfTried' => 0,
					'loggedIn' => FALSE
				)
			);
		}
		$this->_loginData = 
			$this->CI->session
			->userdata(self::SESSIONKEY);
    }
}