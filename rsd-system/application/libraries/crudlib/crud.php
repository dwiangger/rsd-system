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
		$this->_requiredAttribute = 'required="required"';
		
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
	/**
	 * @var array of option
	 * 	indexColumn		TRUE/FALSE		show/hide index column in list viewing 
	 * 	navLink			string			navigation link in list viewing, 
	 * 									using "{page-index}" present page index.
	 * 	detailLink		string			link to detail form viewing
	 * 	confirmDeleleLink		string	link to confirm delete form
	 * 	deleteLink		string			link to action delete  
	 * 	editLink		string			link to edit form 
	 * 	updateLink		string			Link to action update 
	 */
	var $_options; 
	var $_definitions;
	/**
	 * Variable for viewing list. 
	 */
	var $_query;
	var $_pageSize;
	var $_pageIndex; /* count from 1 */
	var $_totalResults;
	/**
	 * Variable for detail
	 */
	var $_itemId;
	/**
	 * Variable for new/edit form
	 */
	var $_requiredAttribute;
	/**
	 * Local method 
	 */
	/**
	 * Based on _definitions, build html table displays list object in $data 
	 */
	private function print_list_html($data)
	{
		$indexColumn = FALSE;
		$numRow = 0;
		/* Init table */
		$result = "\n<div class=\"crud-list-view\"><table>\n\t<tr>\n";
		/* Checking for index column displaying */
		if ( isset($this->_options['indexColumn']) && $this->_options['indexColumn'])
		{
			$indexColumn = TRUE;
			$numRow++;	// For index column
			$result .= "\t\t<th>#</th>\n"; // Header of ind 
		}
		/* Is editable or deletable - Action column*/
		$actionColumn = FALSE;
		if ( isset($this->_options['editLink']) 
			|| isset($this->_options['confirmDeleteLink'])
			|| isset($this->_options['detailLink']) )
		{
			$actionColumn = TRUE;
		}
		/* Display all column header which set to display */
		foreach ($this->_definitions as $colName => $colDefine) {
			if ( $colDefine['display'] )
			{
				$header = $colName;
				if(isset($colDefine['header']))
				{
					$header = $colDefine['header']; 
				}
				$result .= "\t\t<th>".$header."</th>\n";
				$numRow++;// Calculate num rows 
			}
		}
		/* display action column header */
		if ($actionColumn)
		{
			$result .= "\t\t<th>Action</th>\n";
			$numRow++;
		}
		$result .= "\t</tr>\n";
		
		/* Display index from first item's index */
		$i = ($this->_pageIndex-1)*$this->_pageSize;
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
			/* Action column */
			if ($actionColumn) {
				$result .= "\t\t<td><div class=\"btn-group\">\n"
					."\t\t\t<a class=\"btn btn-mini\" href=\"#\"><i class=\"icon-cog\"></i></a>\n"
					."\t\t\t<a class=\"btn btn-mini dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\"><span class=\"caret\"></span></a>\n"
					."\t\t\t<ul class=\"dropdown-menu\">\n";
				if (isset($this->_options['detailLink']))
				{
					$result .= "\t\t\t\t<li><a href=\""
						.$this->_options['confirmDeleteLink']
						."\"><i class=\"icon-info-sign\"></i> Detail</a></li>\n";
					if (isset($this->_options['editLink'])
						|| isset($this->_options['confirmDeleteLink']) )
					{
						$result .= "\t\t\t\t<li class=\"divider\"></li>";
					}	
				}
				if (isset($this->_options['editLink']))
				{
					$result .= "\t\t\t\t<li><a href=\""
						.$this->_options['editLink']
						."\"><i class=\"icon-pencil\"></i> Edit</a></li>\n";
				}
				if (isset($this->_options['confirmDeleteLink']))
				{
					$result .= "\t\t\t\t<li><a href=\""
						.$this->_options['confirmDeleteLink']
						."\"><i class=\"icon-trash\"></i> Delete</a></li>\n";
				}
				$result .= "\t\t\t</ul>\n\t\t</div></td>";
			}
			
			$result .= "\t</tr>\n";
		}
		
		/* Calculate and Generate navigation link */
		$navLink = $this->_options["navLink"];
		$result .= "\t<tr><td colspan=\"$numRow\">";
		/* Previous link */
		if ($this->_pageIndex > 1) {
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
		$totalPage = (int)($this->_totalResults/$this->_pageSize)+1;
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
			."</table></div><!-- crud-list-view -->\n";
		return $result;
	}
	
	private function print_detail_html($data)
	{
		$result = "<div class=\"crud-detail-view\"><form class=\"form-horizontal\">\n<fieldset>\n";
		foreach ($this->_definitions as $colName => $colDefine) {
			if( $colDefine['display'] )
			{
				$result .= "\t<div class=\"control-group\">\n"
					."\t\t<label class=\"control-label\">".$colDefine['header']."</label>\n"
					."\t\t<div class=\"controls\"><input type=\"text\" class=\"input-xlarge\" value=\"".$data[$colName]."\" readonly=\"readonly\"/></div>\n"
					."\t</div>\n";
			}
		}
		
		if (isset($this->_options['confirmDeleteLink'])
			|| isset($this->_options['editLink']) )
			{
				$result .= "<div class=\"form-actions\">\n";
				if ( isset($this->_options['editLink']) )
				{
					$result .= "\t<a class=\"btn btn-info\" href=\""
						.$this->_options['editLink']
						."\"><i class=\"icon-pencil icon-white\"></i> Edit</a>\n";
				}
				if ( isset($this->_options['confirmDeleteLink']) )
				{
					$result .= "\t<a class=\"btn btn-danger\" href=\""
						.$this->_options['confirmDeleteLink']
						."\"><i class=\"icon-trash icon-white\"></i> Delete</a></div>\n";
				}
				$result .= "</fieldset>\n</form></div><!-- crud-detail-view -->\n";
			}
		
		return $result;
	}
	
	private function print_detail_form($data, $buttons)
	{
		/*
		 * NOTE: only allow 1-chain-reference-column. 
		 */
		/* Initial result string */
		$result = "";
		/* Initial render-by-data-type option */
		$renderByDataType = FALSE;
		if (isset($this->_options['render-by-data-type']) 
			&& $this->_options['render-by-data-type'] == TRUE ) {
			$renderByDataType = TRUE;
		}
		/* Get table struct */
		$query = $this->CI->db->query("DESCRIBE ".$this->CI->db->dbprefix($this->_tableName));
		$tableInfo = array();
		foreach ($query->result() as $row) {
			$tableInfo[$row->Field] = array(
				'type' => strtolower($row->Type),
				'null' => strtolower($row->Null),
				'key' => strtolower($row->Key),
				'default' => $row->Default,
				'extra' => strtolower($row->Extra)
			);
		}
		
		$query->free_result();
		/* start display form */
		$result .= "<div class=\"crud-create-form\"><form class=\"form-horizontal\">\n"
    		."\t<fieldset>\n"
    		."\t\t<legend>Create new <strong>{item}</strong>:</legend>\n";
    	/* Loop all field to generate input element */
		foreach ($tableInfo as $colName => $colDefine) {
			if ( strpos($colDefine['extra'],'auto_increment') !== FALSE )
			{
				/* auto-increment column, no need to input */
				continue;
			}
			/* 
			 * Is this filed required/ has no default value 
			 * Column has default==NULL but not allow NULL value
			 */
			$isRequired = FALSE;
			if ($colDefine['null'] == 'no' 
				&& $colDefine['default'] == NULL)
				{
					$isRequired = TRUE;
				}
			/* 
			 * Is this field reference to another table
			 * Define by _definitions option 
			 */
			$refValue = FALSE;
			if ( isset($this->_definitions[$colName]['ref']) 
				&& count($this->_definitions[$colName]['ref']) > 0)
			{
				/* This is an reference column, get seletion */
				$ref = $this->_definitions[$colName]['ref'];
				$indexCol = $ref['chain'][$ref['firstChain']]['indexCol'];// IndexCol of first/only reference table
				$displayCol = $ref['displayCol'];// displayCol of first/only reference table
				
				$this->CI->db->distinct();
				$this->CI->db->select("$indexCol,$displayCol"); 
				$this->CI->db->from($ref['firstChain']);//ref->chain->first
				/* */
				$query = $this->CI->db->get();
				
				/* push options to array $refValue as id->display_text */
				foreach ($query->result() as $row) {
					$refValue[$row->$indexCol] = $row->$displayCol;
				}
				/* free result to prevent flood to next query */
				$query->free_result();
			}
			/* generate start of control group */
			$result .= "<!-- ".$this->_tableName.".$colName -->\n"
				."<div class=\"control-group\">\n"
			    ."\t<label class=\"control-label\" for=\"$colName\">"
			    .(isset($this->_definitions[$colName]['header'])?$this->_definitions[$colName]['header']:$colName)
			    /* $colName is label if header is not set */
			    ."</label>\n"
			    ."\t<div class=\"controls\">\n";
			/* Generate control */
			if ( $refValue !== FALSE )
			{
				/* Reference column: display a select box */
				$result .= "\t\t<select name=\"$colName\" id=\"$colName\" "
					.($isRequired?$this->_requiredAttribute:'').">\n";
				foreach ($refValue as $key => $value) {
					$result .= "\t\t\t<option value=\"$key\">$value</option>\n";
				}
				$result .= "\t\t</select>\n";
			}else 
			{
				/* Based on definitions[inputType], 
				 * some type need more input data in definitions[inputData]:
				 * 	textarea
				 * 	checkbox
				 * 		Description-value 
				 * 	---
				 * 	select
				 * 		Description-value list
				 * 	multiple-select
				 * 		Description-value list
				 * 	radio
				 * 		Description-value list
				 * 	---
				 * 	(file-input)
				 * 	--- 
				 * 	textbox (default)
				 * ----
				 * Compose with data type to set validation
				 */
				$inputType = $renderByDataType?'render-by-data-type':'not-render';
				if ( isset($this->_definitions[$colName]['inputType']) )
				{
					$inputType = strtolower($this->_definitions[$colName]['inputType']); 
				}
				switch ($inputType)
				{
					case "textarea":
						$result .= "\t<textarea class=\"input-xlarge\" "
							."id=\"$colName\" "
							."name=\"$colName\" "
							."rows=\"3\" "
							."style=\"resize:none;\" "
							.($isRequired?$this->_requiredAttribute:'').">"
							."</textarea>\n";
						break;
					case "checkbox":
						$result .= "\t<label class=\"checkbox inline\">"
							."<input type=\"checkbox\" "
							."class=\"input-xlarge\" "
							."id=\"$colName\" "
							."name=\"$colName\" "
							."value=\"".$this->_definitions[$colName]['inputData']['value']."\""
							."> ".$this->_definitions[$colName]['inputData']['description']."</label>\n";
						break;
					case "select":
						$result .= "\t\t<select name=\"$colName\" id=\"$colName\" "
							.($isRequired?$this->_requiredAttribute:'').">\n";
						foreach ($this->_definitions[$colName]['inputData'] as $value => $description ) {
							$result .= "\t\t\t<option value=\"$value\">$description</option>\n";
						}
						$result .= "\t\t</select>\n";
						break;
					case "multiple-select":
						$result .= "\t\t<select multiple=\"multiple\" name=\"$colName\" id=\"$colName\" "
							.($isRequired?$this->_requiredAttribute:'').">\n";
						foreach ($this->_definitions[$colName]['inputData'] as $value => $description ) {
							$result .= "\t\t\t<option value=\"$value\">$description</option>\n";
						}
						$result .= "\t\t</select>\n";
						break;
					case "radio":
						foreach ($this->_definitions[$colName]['inputData'] as $value => $description ) {
							$result .= "\t\t<label class=\"radio\">"
								."<input name=\"$colName\" id=\"$colName\" "
									."value=\"$value\" type=\"radio\" "
									.($isRequired?$this->_requiredAttribute:'')."> $description\n";
						}
						break;
					/* Default: based on data type: 
					 * 	date/datetime/time	: textbox+js
					 * 	text	: textarea
					 * 	enum	: select 
					 * 	int		: textbox+js 
					 * 	decimal/float/double/real	: textbox+js
					 * 	char/varchar/default	: textbox 
					 */
					/*
					 * $colDefine['type'] pattern as type(limit), 
					 * Compare this way to ignore "(limit)"
					 */
					case 'render-by-data-type':/* all comparing must "===" due to issue 0==FALSE*/
						if ( strpos($colDefine['type'], 'datetime') === 0
							|| strpos($colDefine['type'], 'date') === 0 
							|| strpos($colDefine['type'], 'time') === 0 )// date/datetime/time
						{
							$result .= "\t<input type=\"text\" "
								."class=\"input-xlarge\" "
								."id=\"$colName\" "
								."name=\"$colName\" "
								.($isRequired?$this->_requiredAttribute:'').">\n";
						}else if ( strpos($colDefine['type'], 'text') === 0 )// text
						{
							$result .= "\t<textarea class=\"input-xlarge\" "
								."id=\"$colName\" "
								."name=\"$colName\" "
								."rows=\"3\" "
								."style=\"resize:none;\" "
								.($isRequired?$this->_requiredAttribute:'')." >"
								."</textarea>\n";
						}else if ( strpos($colDefine['type'], 'enum') === 0 )// enum
						{
							$optList = substr($colDefine['type'], 5, strlen($colDefine['type'])-6);
							// execpt "enum(" and ")"
							$optList = explode(",", $optList);
							
							$result .= "\t\t<select name=\"$colName\" id=\"$colName\" "
								.($isRequired?$this->_requiredAttribute:'').">\n";
							foreach ($optList as $opt ) {
								$opt = substr($opt, 1, strlen($opt)-2);
								$result .= "\t\t\t<option value=\"$opt\">$opt</option>\n";
							}
							$result .= "\t\t</select>\n";
						}else if ( strpos($colDefine['type'], 'int') === 0 )// int
						{
							$result .= "\t<input type=\"text\" "
								."class=\"input-xlarge\" "
								."id=\"$colName\" "
								."name=\"$colName\" "
								.($isRequired?$this->_requiredAttribute:'').">\n";
						}else if ( strpos($colDefine['type'], 'decimal') === 0
							|| strpos($colDefine['type'], 'float') === 0
							|| strpos($colDefine['type'], 'double') === 0  
							|| strpos($colDefine['type'], 'real') === 0 )// decimal/float/double/real
						{
							
						}else //char/varchar/default
						{
							$result .= "\t<input type=\"text\" "
								."class=\"input-xlarge\" "
								."id=\"$colName\" "
								."name=\"$colName\" "
								.($isRequired?$this->_requiredAttribute:'').">\n";
						}
						break;
					/* all these type will be handle as default textbox */
					case 'not-render':
					case "textbox":
					default: 
						$result .= "\t<input type=\"text\" "
							."class=\"input-xlarge\" "
							."id=\"$colName\" "
							."name=\"$colName\" "
							.($isRequired?$this->_requiredAttribute:'').">\n";
						break;
				}
			}
			/* Close control group */
			$result .= "\t</div><!-- close:controls -->\n"
		    	."</div><!-- close:control-group -->\n";
		}
		/* button area */
    	$result .= "\t</fieldset>\n"
    		."\t<div class=\"form-actions\">\n"
    		."\t\t<a class=\"btn btn-primary\" href=\"#\"><i class=\"icon-file icon-white\"></i> Create</a>\n"
			."\t\t<a class=\"btn\" href=\"#\">Cancel</a>\n"
    		."\t</div>\n"
			."</form></div><!-- crud-create-form -->\n";
		/* return */
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
	
	public function ItemId($itemId = NULL)
	{
		if ($itemId != NULL)
		{
			$this->_itemId = (int)$itemId;
		}
		return $this->_itemId;
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
			/* get primary key from _definitions to use in where clause */
			if (isset($colDefine['primary']) 
				&& $colDefine['primary'] )
				{
					$primaryCol = $colName;
				}
			/* Add a join clause if this column is reference to another column */
			if (isset($colDefine['ref']) 
				&& count($colDefine['ref']) > 0 ) {
				/* join and add to select list */
					$displayCol = $colDefine['ref']['displayCol'];
					
					$chains = $colDefine['ref']['chain'];
					
					$prevTable = $this->_tableName;
					$prevCol = $colName;
					$currChain = $colDefine['ref']['firstChain']; 
					/* join through all chain */
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
		$this->CI->db->limit(
			$this->_pageSize,	// Limit 
			($this->_pageIndex-1)*$this->_pageSize);	// offset
		$query = $this->CI->db->select(implode(",", $selectList));
		$query = $this->CI->db
			->get();
		/* generate result list as an array */
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
		/* pass to an ui-builder before output */
		return self::print_list_html($result);
	}
	/**
	 * Render detail view to a string 
	 */
	public function render_detail()
	{
		/* primary column, just accept one */
		$primaryCol = "";
		$i = 0;// counter to avoid ambiguous column name 
		$selectList = array($this->_tableName.".*");// list of column need select from many tables 
		$this->CI->db->from($this->_tableName);
		
		/* Loop through _definitions to build up query: join */
		foreach ($this->_definitions as $colName => $colDefine) {
			/* get primary key from _definitions to use in where clause */
			if (isset($colDefine['primary']) 
				&& $colDefine['primary'] )
				{
					$primaryCol = $colName;
				}
			/* Add a join clause if this column is reference to another column */
			if (isset($colDefine['ref']) 
				&& count($colDefine['ref']) > 0 ) {
				/* join and add to select list */
					$displayCol = $colDefine['ref']['displayCol'];
					
					$chains = $colDefine['ref']['chain'];
					
					$prevTable = $this->_tableName;
					$prevCol = $colName;
					$currChain = $colDefine['ref']['firstChain']; 
					/* join through all chain */
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
		/* Add general info & query */
		$this->CI->db->where($this->_tableName.".".$primaryCol,$this->_itemId);
		$this->CI->db->limit(1);
		$this->CI->db->select(implode(",", $selectList));
		$query = $this->CI->db
			->get();
		if ($query->num_rows() != 1) {
			/* Item is not found */
			return NULL;
		}
		$row = $query->row();
		/* build up result object as an array */
		$result = array();
		foreach ($this->_definitions as $colName => $colDefine) {
			if ($colDefine['display']) {
				$result[$colName] = $row->$colName;
			}
		}
		/* pass to a ui_builder before output */
		return self::print_detail_html($result);
	}
 
	public function render_confirmDelete()
	{
		/* just return a form to confirm deletation */
		$result = "<div class=\"crud-confirm-delete span6 offset3\">\n";
		$result .= "<div class=\"alert alert-error\">\n"
			."\t<div class=\"row\"><div class=\"span6\">Are you sure you want to delete <strong>{item}</strong> ?</div></div>\n"
			."<br />"
			."\t<div class=\"row\"><div class=\"span6\">"
				."<a class=\"btn btn-danger\" href=\"#\"><i class=\"icon-trash icon-white\"></i> Delete</a>\n"
				."<a class=\"btn\" href=\"#\">Cancel</a>"
			."</div></div>\n";
		$result .= "</div><!-- crud-confirm-delete -->\n";
		
		return $result;
	}
	
	public function render_createForm()
	{
		$data = array();
		$buttons = array();
		
		return self::print_detail_form($data,$buttons);
	}
}