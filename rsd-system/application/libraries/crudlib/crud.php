<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CRUD library 
 * @author thien.an
 * Provide 5 views 
 * 	- detail 
 * 	- list 
 * 	- update form 
 * 	- create form 
 * 	- delete confirmation 
 * 3 actions 
 * 	- create 
 * 	- update 
 * 	- delete 
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

		/* General default value */ 
		$this->_primaryCol = NULL;
		$this->_options["render-by-data-type"] = TRUE;
		$this->_options["indexColumn"] = TRUE;
		$this->_options["itemName"] = "{item}";/* "{item}" will be replaced by object name */
		$this->_pageSize = 10;
		$this->_pageIndex = 1;
		/* Default value for links */
		$this->_links['view_list'] = NULL; 
		$this->_links['view_detail'] = NULL; 
		$this->_links['view_edit'] = NULL; 
		$this->_links['view_confirmDelete'] = NULL; 
		$this->_links['view_create'] = NULL; 
		$this->_links['create'] = NULL; 
		$this->_links['update'] = NULL; 
		$this->_links['delete'] = NULL;

		/* Inform creating CRUD */
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
	const _REQUIRED_ATTRIBUTE = 'required="required"';
	/**
	 * table name need to handle 
	 */
	var $_tableName;
	/**
	 * @var array of option<br/>
	 * 	"indexColumn"		TRUE/FALSE		show/hide index column in list viewing<br/> 
	 * 	"render-by-data-type"		TRUE/FALSE	if definition for rendering is not set, <br/>
	 * using this option to decide  
	 */
	var $_options;
	/**
	 * Contain link to other function<br />
	 * @var array(<br />
	 * 	"view_list" =><br />
	 * 	"view_detail" =><br />
	 * 	"view_edit" =><br />
	 * 	"view_confirmDelete" =><br />
	 * 	"view_create" =><br />
	 * 	"create" =><br />
	 * 	"update" =><br />
	 * 	"delete" =><br />
	 * ) <br />
	 */
	var $_links; 
	/**
	 * Define for table. 
	 * array(
	 * 		$colname => array(
	 * 			"header" => "", // Header to display in list and form label, default is colName 
	 * 			"display" => array("view_list"|"view_detail"|...), // display this col in which view ? ref to _links
	 * 			"width" => int, // with of cell in list table 
	 * 			"primary" => TRUE/FALSE, // use to get primary key, just use 1 col 
	 * 			"inputType" => "textarea"|"checkbox"|"select"|"multiple-select"|"radio"|"file-input"|"textbox", // guiding for rendering  
	 * 			"inputData" => array(
	 * 				"value1" => "description1",
	 * 				"value2" => "description2",
	 * 				...
	 * 			), // support for inputType select or radio ...
	 * 			"default" => "", // Using in create/update, priority higher than default in db
	 * 			"nameidentity" => "" // identify name to use in header in display view_detail/edit/confirmDelete
	 * 			// still thinking to add more customize. 
	 * 		),
	 * 		...
	 * )
	 */
	var $_definitions;
	/**
	 * Variable for viewing list. 
	 */
	var $_query; /* Using _query instead _tableName in view */
	var $_pageSize; /* page size, default 10 */
	var $_pageIndex; /* current viewing index, count from 1, also default */
	var $_totalResults; /* total result after query */
	/**
	 * Variable for detail
	 */
	var $_primaryCol; /* primary key extracted from _definition */
	var $_itemId; /* value for primary key when query specific item */
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
		$result = "\n<div class=\"crud-list-view\">\n"
			."<legend><strong>".$this->_options['itemName']."</strong> list</legend>\n"
			."<table class=\"table table-striped table-bordered table-condensed\">\n"
			."\t<thead>\n"
			."\t<tr>\n";
		/* Checking for index column displaying */
		if ( isset($this->_options['indexColumn']) && $this->_options['indexColumn'])
		{
			$indexColumn = TRUE;
			$numRow++;	// For index column
			$result .= "\t\t<th style=\"width:25px;text-align:right;\">#</th>\n"; // Header of ind 
		}
		/* Is editable or deletable - Action column*/
		$actionColumn = FALSE;
		if ( $this->_links['view_edit'] != NULL 
			|| $this->_links['view_confirmDelete'] != NULL
			|| $this->_links['view_detail'] != NULL )
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
				$result .= "\t\t<th".(isset($colDefine['width'])?(" style=\"width:".$colDefine['width']."px;\""):'').">"
					.$header."</th>\n";
				$numRow++;// Calculate num rows 
			}
		}
		/* display action column header */
		if ($actionColumn)
		{
			$result .= "\t\t<th style=\"width:60px;\">Action</th>\n";
			$numRow++;
		}
		$result .= "\t</tr>\n"
			."\t</thead>\n";
		
		/* Display index from first item's index */
		$i = ($this->_pageIndex-1)*$this->_pageSize;
		/* in case nothing found */
		if ($this->_totalResults <= 0) {
			$result .= "\t<tr>\n<td colspan=\"$numRow\">Nothing found.</td></tr>\n";
			$totalPage = 0;
		}else 
		{
			/* count total page */
			$totalPage = (int)(($this->_totalResults - 1)/$this->_pageSize)+1;
			/* Display each row */
			foreach ($data as $row) {
				$result .= "\t<tr>\n";
				/* Index row */
				if ($indexColumn)
				{
					$result .= "\t\t<td style=\"width:25px;text-align:right;\">".(++$i)."</td>\n";
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
					$result .= "\t\t<td style=\"width:60px;\"><div class=\"btn-group\">\n"
						."\t\t\t<a class=\"btn btn-mini\" href=\"#\"><i class=\"icon-cog\"></i></a>\n"
						."\t\t\t<a class=\"btn btn-mini dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\"><span class=\"caret\"></span></a>\n"
						."\t\t\t<ul class=\"dropdown-menu\">\n";
					if ($this->_links['view_detail'] != NULL)
					{
						$result .= "\t\t\t\t<li><a href=\""
							.str_replace('{item-index}', $row[$this->_primaryCol], $this->_links['view_detail'])
							."\"><i class=\"icon-info-sign\"></i> Detail</a></li>\n";
						if ($this->_links['view_edit'] != NULL
							|| $this->_links['view_confirmDelete'] != NULL )
						{
							$result .= "\t\t\t\t<li class=\"divider\"></li>";
						}	
					}
					if ($this->_links['view_edit'] != NULL)
					{
						$result .= "\t\t\t\t<li><a href=\""
							.str_replace('{item-index}', $row[$this->_primaryCol], $this->_links['view_edit'])
							."\"><i class=\"icon-pencil\"></i> Edit</a></li>\n";
					}
					if ($this->_links['view_confirmDelete'] != NULL)
					{
						$result .= "\t\t\t\t<li><a href=\""
							.str_replace('{item-index}', $row[$this->_primaryCol], $this->_links['view_confirmDelete'])
							."\"><i class=\"icon-trash\"></i> Delete</a></li>\n";
					}
					$result .= "\t\t\t</ul>\n\t\t</div></td>";
				}
				
				$result .= "\t</tr>\n";
			}
		}/* all row of page are shown */
		
		/* Calculate and Generate navigation link */
		$result .= "\t<tfoot><tr><td colspan=\"$numRow\" style=\"text-align:center;\">\n";
		/* Previous link */
		if ($this->_pageIndex > 1) {
			$result .= "<a href=\"".str_replace("{page-index}", "", $this->_links['view_list'])."\" title=\"First page\" class=\"btn btn-mini\">"
					."<i class=\"icon-fast-backward\"></i></a>\n";
			$result .= "<a href=\"".str_replace("{page-index}", ($this->_pageIndex-1), $this->_links['view_list'])."\" title=\"Previous page\" class=\"btn btn-mini\">"
					."<i class=\"icon-step-backward\"></i></a>\n";
		}else 
		{
			$result .= "<a href=\"#\" title=\"First page\" class=\"btn btn-mini disabled\">"
					."<i class=\"icon-fast-backward\"></i></a>\n";
			$result .= "<a href=\"#\" title=\"Previous page\" class=\"btn btn-mini disabled\">"
					."<i class=\"icon-step-backward\"></i></a>\n";
		}
		/* Display page, total ... */
		$result .= "\n<span>Page ".$this->_pageIndex."/".$totalPage." totals ".$this->_totalResults." result(s)</span>\n";
		/* Next link */
		if ( $this->_pageIndex >= $totalPage )
		{
			$result .= "<a href=\"#\" title=\"Next page\" class=\"btn btn-mini disabled\">"
					."<i class=\"icon-step-forward\"></i></a>\n"
				."<a href=\"#\" title=\"Last page\" class=\"btn btn-mini disabled\">"
					."<i class=\"icon-fast-forward\"></i></a>\n";
		}else
		{
			$result .= "<a href=\"".str_replace("{page-index}", $this->_pageIndex+1, $this->_links['view_list'])."\" title=\"Next page\" class=\"btn btn-mini\">"
					."<i class=\"icon-step-forward\"></i></a>\n"
				."<a href=\"".str_replace("{page-index}", $totalPage, $this->_links['view_list'])."\" title=\"Last page\" class=\"btn btn-mini\">"
					."<i class=\"icon-fast-forward\"></i></a>\n";
		}
		$result .= "\n</td></tr></tfoot>\n"
			."</table></div><!-- crud-list-view -->\n";
		return $result;
	}
	
	private function print_detail_html($data)
	{
		$result = "<div class=\"crud-detail-view\">\n"
			."<legend><strong>".$this->_options["itemName"]."</strong> detail</legend>"
			."<div class=\"row\">\n<div class=\"span10 offset1\">"
			."<form class=\"form-horizontal\">\n<fieldset>\n";
		foreach ($this->_definitions as $colName => $colDefine) {
			if( $colDefine['display'] )
			{
				$result .= "\t<div class=\"row\" style=\"padding-bottom:3px;\">\n"
					."\t\t<div class=\"span2\" style=\"text-align:right;\">"
						."<strong>".$colDefine['header']."</strong>"
					."</div>\n"
					."\t\t<div class=\"span7\">"
					//."<input type=\"text\" class=\"input-xlarge\" value=\"".$data[$colName]."\" readonly=\"readonly\"/>"
					.$data[$colName]
					."</div>\n"
					."\t</div>\n";
			}
		}
		
		if ($this->_links['view_confirmDelete'] != NULL
			|| $this->_links['view_edit'] != NULL )
			{
				$result .= "<div class=\"form-actions\">\n";
				if ( $this->_links['view_edit'] != NULL )
				{
					$result .= "\t<a class=\"btn btn-info\" href=\""
						.str_replace('{item-index}', $this->_itemId, $this->_links['view_edit'])
						."\"><i class=\"icon-pencil icon-white\"></i> Edit</a>\n";
				}
				if ( $this->_links['view_confirmDelete'] != NULL )
				{
					$result .= "\t<a class=\"btn btn-danger\" href=\""
						.str_replace('{item-index}', $this->_itemId, $this->_links['view_confirmDelete'])
						."\"><i class=\"icon-trash icon-white\"></i> Delete</a></div>\n";
				}
				$result .= "</fieldset>\n</form>\n"
					."</div>\n</div>\n"
					."</div><!-- crud-detail-view -->\n";
			}
		
		return $result;
	}
	
	private function print_detail_form(
		$data, /* Data object */ 
		$action, /* form action */
		$title = NULL, 
		$submitText = 'Create') /* text dixplay at submit button */
	{
		if ($title == NULL) {
			$title = "Create new <strong>".$this->_options["itemName"]."</strong>";
		}
		/*
		 * NOTE: only allow 1-chain-reference-column. 
		 */
		/*
		 * With reference column, 
		 * Render detail form not using display text to compare value, but using reference_id value, 
		 * So Object received without display text but reference id. 
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
		$result .= "<div class=\"crud-create-form\">\n"
    		."\t<legend>$title</legend>\n"
    		."<div class=\"row\">\n<div class=\"span10 offset1\">"
			."<form class=\"form-horizontal\" "
			."method=\"post\" action=\"$action\">\n"
    		."\t<fieldset>\n";
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
					.($isRequired?self::_REQUIRED_ATTRIBUTE:'').">\n";
				foreach ($refValue as $key => $value) {
					$result .= "\t\t\t<option value=\"$key\" "
						.((isset($data[$colName])&&$data[$colName]==$key)?' selected="selected" ':'')
						.">$value</option>\n";
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
							.($isRequired?self::_REQUIRED_ATTRIBUTE:'').">"
							.(isset($data[$colName])?$data[$colName]:'')
							."</textarea>\n";
						break;
					case "checkbox":
						$result .= "\t<label class=\"checkbox inline\">"
							."<input type=\"checkbox\" "
							."class=\"input-xlarge\" "
							."id=\"$colName\" "
							."name=\"$colName\" "
							."value=\"".$this->_definitions[$colName]['inputData']['value']."\""
							.((isset($data[$colName])&&$data[$colName]==$this->_definitions[$colName]['inputData']['value'])?' checked="checked" ':'')
							."> ".$this->_definitions[$colName]['inputData']['description']."</label>\n";
						break;
					case "select":
						$result .= "\t\t<select name=\"$colName\" id=\"$colName\" "
							.($isRequired?self::_REQUIRED_ATTRIBUTE:'').">\n";
						foreach ($this->_definitions[$colName]['inputData'] as $value => $description ) {
							$result .= "\t\t\t<option value=\"$value\""
								.((isset($data[$colName])&&$data[$colName]==$value)?' selected="selected" ':'')
								.">$description</option>\n";
						}
						$result .= "\t\t</select>\n";
						break;
					case "multiple-select":
						$result .= "\t\t<select multiple=\"multiple\" name=\"$colName\" id=\"$colName\" "
							.($isRequired?self::_REQUIRED_ATTRIBUTE:'').">\n";
						foreach ($this->_definitions[$colName]['inputData'] as $value => $description ) {
							$result .= "\t\t\t<option value=\"$value\""
								.((isset($data[$colName])&&$data[$colName]==$value)?' selected="selected" ':'')
								.">$description</option>\n";
						}
						$result .= "\t\t</select>\n";
						break;
					case "radio":
						foreach ($this->_definitions[$colName]['inputData'] as $value => $description ) {
							$result .= "\t\t<label class=\"radio\">"
								."<input name=\"$colName\" id=\"$colName\" "
									."value=\"$value\" type=\"radio\" "
									.($isRequired?self::_REQUIRED_ATTRIBUTE:'').""
									.((isset($data[$colName])&&$data[$colName]==$value)?' selected="selected" ':'')
									."> $description\n";
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
								.($isRequired?self::_REQUIRED_ATTRIBUTE:'')." "
								."value=\"".(isset($data[$colName])?$data[$colName]:'')."\""
								.">\n";
						}else if ( strpos($colDefine['type'], 'text') === 0 )// text
						{
							$result .= "\t<textarea class=\"input-xlarge\" "
								."id=\"$colName\" "
								."name=\"$colName\" "
								."rows=\"3\" "
								."style=\"resize:none;\" "
								.($isRequired?self::_REQUIRED_ATTRIBUTE:'')." >"
								.(isset($data[$colName])?$data[$colName]:'')
								."</textarea>\n";
						}else if ( strpos($colDefine['type'], 'enum') === 0 )// enum
						{
							$optList = substr($colDefine['type'], 5, strlen($colDefine['type'])-6);
							// execpt "enum(" and ")"
							$optList = explode(",", $optList);
							
							$result .= "\t\t<select name=\"$colName\" id=\"$colName\" "
								.($isRequired?self::_REQUIRED_ATTRIBUTE:'').">\n";
							foreach ($optList as $opt ) {
								$opt = substr($opt, 1, strlen($opt)-2);
								$result .= "\t\t\t<option value=\"$opt\" "
									.((isset($data[$colName])&&$data[$colName]==$opt)?' selected="selected" ':'')
									.">$opt</option>\n";
							}
							$result .= "\t\t</select>\n";
						}else if ( strpos($colDefine['type'], 'int') === 0 )// int
						{
							$result .= "\t<input type=\"text\" "
								."class=\"input-xlarge\" "
								."id=\"$colName\" "
								."name=\"$colName\" "
								.($isRequired?self::_REQUIRED_ATTRIBUTE:'')." "
								."value=\"".(isset($data[$colName])?$data[$colName]:'')."\""
								." >\n";
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
								.($isRequired?self::_REQUIRED_ATTRIBUTE:'')." "
								."value=\"".(isset($data[$colName])?$data[$colName]:'')."\""
								.">\n";
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
							.($isRequired?self::_REQUIRED_ATTRIBUTE:'')." "
							."value=\"".(isset($data[$colName])?$data[$colName]:'')."\""
							.">\n";
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
    		."\t\t<button class=\"btn btn-primary\" ><i class=\"icon-file icon-white\"></i> ".$submitText."</button>\n"
			."\t\t<a class=\"btn\" href=\""
			.(($this->_links['view_list'] != NULL )?str_replace('{page-index}','', $this->_links['view_list']):'#')
			."\">Cancel</a>\n"
    		."\t</div>\n"
			."</form>\n"
			."</div></div>\n"
			."</div><!-- crud-create-form -->\n";
		/* return */
		return $result;
	}
	
	private function prepare_query()
	{
		$primaryCol = "";// primary column, just accept one, store in property
		$selectList = array($this->_tableName.".*");// list of column need select from many tables, return result of this function
		
		$i = 0;// counter to avoid ambiguous column name 
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
						/*
						 * add dbprefix() to avoid CI auto add dbprefix to alias on join/join-condition clause 
						 */
						$this->CI->db->join("$currChain AS ".$this->CI->db->dbprefix($currChain.$i),
							"$prevTable.$prevCol=".$this->CI->db->dbprefix($currChain.$i).".$indexCol");
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
		/* Push primaryCol to property */
		$this->_primaryCol = $primaryCol;
		/* Return selectList */ 
		return $selectList;
	}
	/**
	 * Welform input array, remove all item which is not column name
	 * @param array($colName => $value) $data
	 */
	private function posted_object_data()
	{
		$data = array();
		$query = $this->CI->db->query("DESCRIBE ".$this->CI->db->dbprefix($this->_tableName));
		$colsName = array();
		foreach ($query->result() as $row) {
			$data[$row->Field] = $this->CI->input->post($row->Field);
		}
		/* return */
		return $data;
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
	/**
	 * 
	 * @param array of definition $columnDefine
	 */
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
	 * function to manage links<br/>
	 * Accepted name: <br/> 
	 * "view_list" - Link to list page <br/>
	 * "view_detail" - Link to detail page <br/>
	 * "view_edit" - Link to edit form page <br/>
	 * "view_confirmDelete" - Link to confirm delete page <br/> 
	 * "view_create" - Link to create form <br/>
	 * "create" - Link to create action <br/>
	 * "update" - Link to update action <br/>
	 * "delete" - Link to delete action <br/>
	 * <br />
	 * Add "{page-index}" where need page index. 
	 * Add "{item-index}" where need item index
	 */
	public function Link($name , $value = NULL)
	{
		/* Checking $name with defined keys */
		switch ($name)
		{
			case "view_list": 
			case "view_detail": 
			case "view_edit": 
			case "view_confirmDelete": 
			case "view_create": 
			case "create": 
			case "update": 
			case "delete": 
				break;
			default: /* Prevent undefined value */
				return NULL;
		}
		/* set value, value == NULL is unset */
		$this->_links[$name] = (string)$value;
		return $this->_links[$name]; 
	} 
	/**
	 * 5 view function
	 */
	/**
	 * return html code of list view   
	 */
	public function render_list($type = "html")
	{
		/* Cache setup for 2 query 
		 * 1. get count of all results. 
		 * 2. get page of results.
		 */
		$this->CI->db->start_cache();
		$selectList = self::prepare_query();
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
				if ( $colDefine["display"] 
					|| $colName == $this->_primaryCol )
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
	 * return html code of detail view
	 */
	public function render_detail()
	{
		$selectList = self::prepare_query();
		/* Add general info & query */
		$this->CI->db->where(
			$this->_tableName.".".$this->_primaryCol,
			$this->_itemId);
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
		$result = "<div class=\"crud-confirm-delete span8 offset2\">\n";
		$result .= "\t<div class=\"alert alert-error\">\n"
			."\t<div class=\"row\"><div class=\"span8\">\n"
				."\t\t<h3>Delete confirmation</h3>"
			."\t</div></div>\n"
			."\t<div class=\"row\"><div class=\"span8\">\n"
				."\t\t<div>Are you sure you want to delete <strong>".$this->_options["itemName"]."</strong> ?</div>\n"
			."\t</div></div>\n"
			."\t<br />"
			."\t<div class=\"row\"><div class=\"span8\" style=\"text-align: center;\">\n"
				."\t\t<a class=\"btn btn-danger\" href=\""
				.(($this->_links['delete'] != NULL)?str_replace('{item-index}',$this->_itemId, $this->_links['delete']):'#')
				."\">\n\t\t<i class=\"icon-trash icon-white\"></i> Delete</a>\n"
				."\t\t<a class=\"btn\" href=\""
				.(($this->_links['view_list'] != NULL)?str_replace('{page-index}','', $this->_links['view_list']):'#')
				."\">Cancel</a>\n"
			."\t</div>\n</div>\n";
		$result .= "</div></div><!-- crud-confirm-delete -->\n";
		
		return $result;
	}
	/**
	 * Return html code of create form
	 */
	public function render_createForm()
	{
		$data = array();
		$action = "";
		if ($this->_links['create'] != NULL)
		{
			$action = $this->_links['create'];
		}
		
		return self::print_detail_form($data,$action);
	}
	/**
	 * Return html codo of edit form. 
	 */
	public function render_editForm()
	{
		/*
		 * With reference column, 
		 * Render edit not using display text to compare, but using reference_id value, 
		 * So needn't to join. Object pass without text but reference id. 
		 */
		/* Copy from render_detail, get object */
		$this->CI->db->from($this->_tableName);
		
		/* Loop through _definitions to find primary key */
		foreach ($this->_definitions as $colName => $colDefine) {
			/* get primary key from _definitions to use in where clause */
			if (isset($colDefine['primary']) 
				&& $colDefine['primary'] )
				{
					$this->_primaryCol = $colName;
					break;
				}
		}
		/* Add general info & query */
		$this->CI->db->where(
			$this->_tableName.".".$this->_primaryCol,
			$this->_itemId);
		$this->CI->db->limit(1);
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
		/* finish copying */
		$action = "";
		if ($this->_links['update'] != NULL)
		{
			$action = str_replace('{item-index}', $this->_itemId, $this->_links['update']);
		}
		
		return self::print_detail_form(
			$result,
			$action,'Edit <strong>'.$this->_options["itemName"].'</strong>',
			'Update');
	}
	/**
	 * 3 action function 
	 * for all 3 actions, $data array( $colName => $value )  
	 */	
	/**
	 * Create/insert new object
	 * $data[$primaryKey] is wasted
	 * $data[$else] is required 
	 */
	public function action_create($data = NULL)
	{
		if( $data == NULL )
		{
			$data = self::posted_object_data();
		}
		$this->CI->db->insert(
			$this->_tableName,
			$data
		);
		return $this->CI->db->insert_id();
	}
	/**
	 * Update an object 
	 * $data[$primaryKey] is required
	 * $data[$else] is optional  
	 */
	public function action_update($data = NULL)
	{
		if ( $data == NULL )
		{
			$data = self::posted_object_data();
		}
		if ( $this->_primaryCol == NULL ) {
			/* get primary key */
			foreach ($this->_definitions as $colName => $colDefine) {
				if($colDefine['primary'] == TRUE)
				{
					$this->_primaryCol = $colName;
					break;
				}
			}
		}
		/* use $this->_itemId instead store in $data, will consire using both 2 */
		$id = $this->_itemId;
		$this->CI->db->where($this->_primaryCol,$id);
		unset($data[$this->_primaryCol]);
		
		$this->CI->db->update(
			$this->_tableName,
			$data
		);
		
		return $id;
	}
	/**
	 * Delete an object
	 * $data[$primaryKey] is required
	 * $data[$else] is wasted
	 */
	public function action_delete($data = NULL)
	{
		if ( $data == NULL )
		{
			$data = self::posted_object_data();
		}
		if ( $this->_primaryCol == NULL ) {
			/* get primary key */
			foreach ($this->_definitions as $colName => $colDefine) {
				if($colDefine['primary'] == TRUE)
				{
					$this->_primaryCol = $colName;
					break;
				}
			}
		}
		$this->CI->db->delete(
			$this->_tableName, 
			array($this->_primaryCol => $this->_itemId) 
			/* the same as using primary keys in editing, will use in both 2 ways */
		);

		return $this->CI->db->affected_rows();
	}
}