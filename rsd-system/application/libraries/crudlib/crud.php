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
	var $_pageIndex;
	var $_totalResults;
	/**
	 * Local method 
	 */
	private function print_html($data)
	{
		$indexColumn = FALSE;
		$numRow = 0;
		/* Init table */
		$result = "\n<table>\n\t<tr>\n";
		/* Checking for index column displaying */
		if ( isset($this->_options['indexColumn']) && $this->_options['indexColumn'])
		{
			$indexColumn = TRUE;
			$numRow++;	// For index column
			$result .= "\t\t<th>#</th>\n"; // Header of ind 
		}

		/* Display all column header which set to display */
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
			$numRow++;// Calculate num rows 
		}
		$result .= "\t</tr>\n";
		
		/* Display index from first item's index */
		$i = $this->_pageIndex*$this->_pageSize;
		/* Ddisplay each row */
		foreach ($data as $row) {
			$result .= "\t<tr>\n";
			/* Index row */
			if ($indexColumn)
			{
				$result .= "\t\t<td>".(++$i)."</td>\n";
			}
			/* data rows */
			foreach ($this->_definitions as $colName => $colDefine) {
				if ( $colDefine['display'] )
				{
					$result .= "\t\t<td>".$row[$colName]."</td>\n";
				}
			}
			$result .= "\t</tr>\n";
		}
		
		/* Calculate and Generate navigation link */
		$navLink = $this->_options["navLink"];
		$result .= "\t<tr><td colspan=\"$numRow\">";
		/* Previous link */
		if ($this->_pageIndex > 0) {
			$result .= "<a href=\"".str_replace("{page-index}", "", $navLink)."\" title=\"First page\" class=\"btn btn-mini\">"
					."<i class=\"icon-fast-backward\"></i></a>";
			$result .= "<a href=\"".str_replace("{page-index}", ($this->_pageIndex-1), $navLink)."\" title=\"Previous page\" class=\"btn btn-mini\">"
					."<i class=\"icon-step-backward\"></i></a>";
		}else 
		{
			$result .= "<a href=\"#\" title=\"First page\" class=\"btn btn-mini disabled\">"
					."<i class=\"icon-fast-backward\"></i></a>";
			$result .= "<a href=\"#\" title=\"Previous page\" class=\"btn btn-mini disabled\">"
					."<i class=\"icon-step-backward\"></i></a>";
		}
		/* Display page, total ... */
		$totalPage = (int)($this->_totalResults/$this->_pageSize);
		$result .= "<span>Page ".$this->_pageIndex."/".$totalPage." totals ".$this->_totalResults." result(s)</span>";
		/* Next link */
		if ( $this->_pageIndex >= $totalPage )
		{
			$result .= "<a href=\"#\" title=\"Next page\" class=\"btn btn-mini disabled\">"
					."<i class=\"icon-step-forward\"></i></a>"
				."<a href=\"#\" title=\"Last page\" class=\"btn btn-mini disabled\">"
					."<i class=\"icon-fast-forward\"></i></a>";
		}else
		{
			$result .= "<a href=\"".str_replace("{page-index}", $this->_pageIndex+1, $navLink)."\" title=\"Next page\" class=\"btn btn-mini\">"
					."<i class=\"icon-step-forward\"></i></a>"
				."<a href=\"".str_replace("{page-index}", $totalPage, $navLink)."\" title=\"Last page\" class=\"btn btn-mini\">"
					."<i class=\"icon-fast-forward\"></i></a>";
		}
		$result .= "</td></tr>\n"
			."</table>\n";
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
	
	public function PageIndex($pageIndex = NULL)
	{
		if ($pageIndex != NULL)
		{
			$this->_pageIndex = (int)$pageIndex;
		}
		return $this->_pageIndex;
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
		/* Cache setup for 2 query 
		 * 1. get count of all results. 
		 * 2. get page of results.
		 */
		$this->CI->db->start_cache();
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
		$this->CI->db->stop_cache();
		
		/* Get count of all result */
		$this->_totalResults = $this->CI->db->count_all_results();
		
		/* Get specific page */
		$this->CI->db->limit($this->_pageSize,$this->_pageIndex*$this->_pageSize);
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