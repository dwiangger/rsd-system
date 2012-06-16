<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @author thien.an
 *
 */
class CRUD {
	/**
	 * load all needed library
	 */
	public function __construct()
	{
		// Set the super object to a local variable for use later
		$this->CI =& get_instance();

		/* load libraries, helpers manually in case not autoload */
		$this->CI->load->database();

		log_message('debug', "crudlib/CRUD Class Initialized");
	}
	/**
	 * Local variable 
	 */
	/**
	 * @var instance of CI
	 */
	var $CI;
	/**
	 * General variable
	 */
	var $_tableName;
	var $_options; 
	var $_definitions;
	/**
	 * Variable for viewing list. 
	 */
	var $_query;
	var $_pageSize;
	var $_firstItemIndex;
	/**
	 * Local method 
	 */
	private function print_html($data)
	{
		$indexColumn = FALSE;
		if ( isset($this->_options['indexColumn']) && $this->_options['indexColumn'])
		{
			$indexColumn = TRUE;	
		}
		$result = "\n<table>\n\t<tr>\n";
		
		$i = $this->_firstItemIndex;
		if ($indexColumn)
		{
			$result .= "\t\t<th>#</th>\n";
		}
		
		foreach ($this->_definitions as $colName => $colDefine) {
			if ( ! $colDefine['display'] )
			{
				continue;
			}
			$header = $colName;
			if(isset($colDefine['header']))
			{
				$header = $colDefine['header']; 
			}
			$result .= "\t\t<th>".$header."</th>\n";
		}
		$result .= "\t</tr>\n";
		foreach ($data as $row) {
			$result .= "\t<tr>\n";
			if ($indexColumn)
			{
				$result .= "\t\t<td>".(++$i)."</td>\n";
			}
			foreach ($this->_definitions as $colName => $colDefine) {
				if ( $colDefine['display'] )
				{
					$result .= "\t\t<td>".$row[$colName]."</td>\n";
				}
			}
			$result .= "\t</tr>\n";
		}
		$result .= "</table>\n";
		return $result;
	}
	/**
	 * Public method
	 */
	/**
	 * Get/set table name 
	 */
	public function TableName($tableName = NULL)
	{
		if ($tableName != NULL)
		{
			$this->_tableName = (string)$tableName;
		}
		return $this->_tableName;
	}
	
	public function PageSize($pageSize = NULL)
	{
		if ($pageSize != NULL)
		{
			$this->_pageSize = (int)$pageSize; 
		}
		return $this->_pageSize;
	}
	
	public function FirstItemIndex($firstItemIndex = NULL)
	{
		if ($firstItemIndex != NULL)
		{
			$this->_firstItemIndex = (int)$firstItemIndex;
		}
		return $this->_firstItemIndex;
	}

	public function ColumnDefine($columnDefine = NULL)
	{
		if ($columnDefine != NULL) {
			$this->_definitions = $columnDefine;
		}
		return $this->_definitions;
	}
	
	public function Option($name,$value = NULL)
	{
		if ($value != NULL)
		{
			$this->_options[$name] = (string)$value;
		}
		return $this->_options[$name];
	}
	
	/**
	 * Render list view to a string as a specific $type   
	 */
	public function render_list($type = "html")
	{
		$this->CI->db->limit($this->_pageSize,$this->_firstItemIndex);
		$this->CI->db->from($this->_tableName);
		
		$i = 0;
		$selectList = array($this->_tableName.".*");
		foreach ($this->_definitions as $colName => $colDefine) {
			if (isset($colDefine['ref']) 
				&& count($colDefine['ref']) > 0 ) {
				/* join and add to select list */
					$displayCol = $colDefine['ref']['displayCol'];
					
					$chains = $colDefine['ref']['chain'];
					
					$prevTable = $this->_tableName;
					$prevCol = $colName;
					$currChain = $colDefine['ref']['firstChain']; 
					
					while(TRUE)
					{
						$chain = $chains[$currChain];
						$indexCol = $chain['indexCol'];
						
						/* Join */
						$this->CI->db->join("$currChain AS $currChain$i",
							"$prevTable.$prevCol=$currChain$i.$indexCol");
						
						/* Checking last chain */
						if ( $currChain == $colDefine['ref']['lastChain'] )
						{
							/* Add to selecting list */
							array_push($selectList,"$currChain$i.$displayCol AS $colName");
							
							break;
						}
						
						/* prepare for next loop */
						$prevTable = $currChain.$i;
						$prevCol = $chain['refCol'];
						$currChain = $chain['nextChain'];
					}
					
					$i++;
			}
		}
		
		$query = $this->CI->db->select(implode(",", $selectList));
		$query = $this->CI->db
			->get();
		$result = array();
		foreach ($query->result() as $row) {
			$item = array();
			foreach ($this->_definitions as $colName => $colDefine) {
				if ( $colDefine["display"] )
				{
					$item[$colName] = $row->$colName;
				}
			}
			array_push($result,$item);
		}
		
		return self::print_html($result);
	}
}