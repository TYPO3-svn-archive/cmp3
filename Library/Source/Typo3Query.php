<?php
/**
 * Cross Media Publishing - CMP3
 * www.cross-media.net
 *
 * LICENSE
 *
 * This source file is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This script is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
 * Public License for more details.
 *
 * @subpackage Data
 * @package    CMP3
 * @copyright  Copyright (c) 2008-2009 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\Source;

#FIXME move to fetcher?

/**
 * Data query for a single table.
 * Will create single or array of data row objects.
 *
 * This variant uses Zend_DB to build a query.
 *
 * STATUS beta
 *
 * This might not be the most elegant way to do this. It it here for historical reasons and might be replaced in the future by something else.
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Data
 * @package    CMP3
 */
class Typo3Query extends \Cmp3\BaseConfig {


	/**
	 * Name of the table to work with
	 *
	 * @var string
	 */
	protected $strTableName = '';


	/**
	 * Name of the class used as row object
	 *
	 * @var string
	 */
	protected $dataRowClass = '\\Cmp3\\Data\\Row';


	/**
	 * Name of the class used as decorator for row object
	 *
	 * @var string
	 */
	protected $dataRowDecoratorClass = '\\Cmp3\\Data\\MetaTypo3';


	/**
	 * Language ID that should be used for queries
	 *
	 * @var mixed
	 */
	protected $forceLanguage = null;


	/**
	 * If set the records will be fetched for the wanted language without overlay of the fields
	 *
	 * @var boolean
	 */
	protected $forceNoLanguageOverlay = false;


	/**
	 * Query generator object
	 *
	 * @var object
	 */
	protected $objQuery;


	/**
	 * DataRow object of the parent record (pages for example)
	 *
	 * @var object
	 */
	protected $objParentRecord;


	/**
	 * Constructor
	 */
	public function Construct()
	{

#TODO
		#if ((self::$Debug) OR \txApplications::GetCurrent()->Profiling) $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;

		$this->strTableName = $this->objConfig->GetValue('table');


		if ($this->objLogger) $this->objLogger->Debug( 'Query table name: ' . $this->strTableName);

		\Cmp3\Typo3\TcaTools::LoadTca($this->strTableName);


/*
			pages_from_home.table = pages
			pages_from_home.id = 18
// type can be "default" (deleted, versioning, ...), "disabled", "starttime", "endtime", "fe_group" (keys from "enablefields" in TCA).
			pages_from_home.enableFields = default
#TODO set fe_group values
			pages_from_home.where (
				doktype < 200
			)
			#pages_from_home.subqueries = sub_pages,content

			sub_pages.table = pages
			sub_pages.id.exclude = 23,24
			sub_pages.enableFields = default
			sub_pages.order = sorting
			sub_pages.constraints {
					pid = PARENT.pid
					doktype = < 200
			}
			sub_pages.where (
				doktype < 200
			)
			sub_pages.subqueries = sub_pages,content
 */


#FIXME		$this->forceLanguage = $languageID;

		$this->objQuery = new \Cmp3\Db\QueryGenerator();

		$this->SetQueryFields($this->strTableName.'.*');

    	if ($strIdList = $this->objConfig->GetValue('id')) {
    		$this->AddCondition($this->strTableName . '.uid IN (' . $GLOBALS['TYPO3_DB']->cleanIntList($strIdList) . ')');
    	}

    	if ($strIdList = $this->objConfig->GetValue('id.exclude')) {
    		$this->AddCondition($this->strTableName . '.uid NOT IN (' . $GLOBALS['TYPO3_DB']->cleanIntList($strIdList) . ')');
    	}

    	if ($strIdList = $this->objConfig->GetValue('pid')) {
    		$this->AddCondition($this->strTableName . '.pid IN (' . $GLOBALS['TYPO3_DB']->cleanIntList($strIdList) . ')');
    	}

    	if ($strIdList = $this->objConfig->GetValue('pid.exclude')) {
    		$this->AddCondition($this->strTableName . '.pid NOT IN (' . $GLOBALS['TYPO3_DB']->cleanIntList($strIdList) . ')');
    	}

    	if ($strConstraintsArray = $this->objConfig->GetProperties('constraints')) {
    		foreach ($strConstraintsArray as $strField => $strConstraint) {

    			// doing it this way might trigger InsertData() - but doesn't work
    			$strConstraint = $this->objConfig->GetValue('constraints.' . $strField);

    			if (!preg_match('#[<>=()]#', $strConstraint)) {
    				$strConstraint = '= ' . $strConstraint;
    			}

    			if ($this->objParentRecord) {
    				$strConstraint = preg_replace_callback(
						'#PARENT\.([a-z_0-9]+)#',
	    				array($this,'SubstituteParentRecord'),
						$strConstraint
					);
	    		}

    			$this->AddCondition($this->strTableName . '.' . $strField . $strConstraint);
    		}
    	}

    	// set all enable columns condition
    	if ($this->objConfig->enableFields) {
	    	$this->SetEnableFieldsCondition($this->objConfig->GetValue('enableFields'));
    	}


    	if ($strClausesArray = $this->objConfig->GetProperties('clauses')) {
    		$this->SetClauses($strClausesArray);
    	}



    	if ($strOrderBy = $this->objConfig->order) {
    		$this->objQuery->order($strOrderBy);
    	} else {

	    	// set sorting configured in TCA
	    	$this->SetDefaultOrder();
    	}

		// configure query for self joined language data
#		$this->SetLanguageOverlay();
    }



    /**
     * used as callback function for preg_replace_callback
     * Enter description here ...
     * @param array $matches
     */
	protected function SubstituteParentRecord($matches)
	{
		$strFieldName = $matches[1];
		return $this->objParentRecord->$strFieldName;
	}


	/*************************
	 *
	 * Get/Set methods
	 *
	 *************************/

	/**
	 * Override method to perform a property "Set"
	 * This will set the property $strName with $mixValue
	 *
	 * @param string $strName Name of the property to get
	 * @param string $mixValue Value of the property to set
	 * @return mixed
	 */
	public function __set($strName, $mixValue)
	{
		switch ($strName) {

			case 'ParentRecord':
				return $this->objParentRecord = $mixValue;

			default:
				return parent::__set($strName, $mixValue);
		}
	}


    /**
	 * Quote string for query
	 *
	 * @param string $str
	 * @return string
	 */
	public function Quote($str)
	{
		return $GLOBALS['TYPO3_DB']->fullQuoteStr($str, $this->strTableName);
	}


    /**
     * Set query fields
     *
     * @param array|string $strFields List of fields to be queried
     * @return void
     */
	public function SetQueryFields($strFields)
	{
#FIXME this doesn't work as expected - an inner join will be created
		$strFields = is_array($strFields) ? $strFields : trim_explode(',', $strFields);
		$strFields = \Cmp3\Typo3\QueryTools::PrefixFieldListAsArray($this->strTableName, $strFields);
		$this->objQuery->reset('columns');
		$this->objQuery->from($this->strTableName, $strFields);
	}


    /**
     * Add query fields
     *
     * @param string $strField Field to be queried
     * @return void
     */
	public function AddQueryField($strFields, $tableName=null)
	{
		$tableName = $tableName ? $tableName : $this->strTableName;
		$strFields = is_array($strFields) ? $strFields : trim_explode(',', $strFields);
#FIXME adding fields here has no effect
		$strFields = \Cmp3\Typo3\QueryTools::PrefixFieldListAsArray($tableName, $strFields);
		$this->objQuery->from($tableName, $strFields);
	}


    /**
     * Adds a FROM table and optional columns to the query.
     *
     * The first parameter $name can be a simple string, in which case the
     * correlation name is generated automatically.  If you want to specify
     * the correlation name, the first parameter must be an associative
     * array in which the key is the physical table name, and the value is
     * the correlation name.  For example, array('table' => 'alias').
     * The correlation name is prepended to all columns fetched for this
     * table.
     *
     * The second parameter can be a single string or Zend_Db_Expr object,
     * or else an array of strings or Zend_Db_Expr objects.
     *
     * The first parameter can be null or an empty string, in which case
     * no correlation name is generated or prepended to the columns named
     * in the second parameter.
     *
     * @param  array|string|Zend_Db_Expr $strTable The table name or an associative array relating table name to
     *                                         correlation name.
     * @param  array|string|Zend_Db_Expr $cols The columns to select from this table.
     * @return void
     */
	public function AddFrom($strTable, $strField)
	{
		$this->objQuery->from($strTable, $strField);
	}


    /**
     * Adds a MM table condition to the query.
     *
     *
     * @param  array|string|Zend_Db_Expr $strTable The table name or an associative array relating table name to
     *                                         correlation name.
     * @param string   $cond  The WHERE condition.
     * @return void
     */
	public function AddMMJoin($strTable, $cond)
	{
	    $this->objQuery->from($strTable, array());
	    $this->objQuery->where($cond);
	}


    /**
     * Add a LEFT OUTER JOIN table and colums to the query
     * All rows from the left operand table are included,
     * matching rows from the right operand table included,
     * and the columns from the right operand table are filled
     * with NULLs if no row exists matching the left table.
     *
     * The first parameter $name can be a simple string, in which case the
     * correlation name is generated automatically.  If you want to specify
     * the correlation name, the first parameter must be an associative
     * array in which the key is the physical table name, and the value is
     * the correlation name.  For example, array('table' => 'alias').
     * The correlation name is prepended to all columns fetched for this
     * table.
     *
     * @param  array|string|Zend_Db_Expr $strTable The table name.
     * @param  string $cond Join on this condition.
     * @param  array|string $cols The columns to select from the joined table.
     * @return void
     */
	public function AddJoin($strTable, $cond, $cols)
	{
    	$this->objQuery->joinLeft($strTable, $cond, $cols);
	}


    /**
     * Set SQL default conditions
     *
     * @return void
     */
	public function SetDefaultCondition()
	{
    	// set all enable columns condition
    	$this->SetEnableFieldsCondition();
	}


    /**
     * Set SQL condition manually
     *
     * @param string $strCondition
     * @param string $strName
     * @return void
     */
	public function AddCondition($strCondition, $strName=null)
	{
		$this->objQuery->where($strCondition, null, null, $strName);
	}


    /**
     * Set SQL condition for enable fields of table tx_xxx
     * type can be "default" (deleted, versioning, ...), "disabled", "starttime", "endtime", "fe_group" (keys from "enablefields" in TCA).
     * Setting type null will set all enable fields
     *
     * @param string $strType
     * @return void
     */
	public function AddEnableFieldsCondition($strType=null)
	{
    	$this->SetEnableFieldsCondition($strType);
	}


    /**
     * Clears SQL conditions
     *
     * @return void
     */
	public function ClearConditions()
	{
		$this->objQuery->reset('where');
	}


    /**
     * Set SQL clause
     *
     * @param string $strClause
     * @return void
     */
	public function AddGroupBy($strClause)
	{
		$this->objQuery->group($strClause);
	}



    /**
     * Set SQL clause
     *
     * @return void
     */
	public function SetDefaultOrder()
	{
		$this->objQuery->reset('order');

    	if (key_exists('default_sortby', $GLOBALS['TCA'][$this->strTableName]['ctrl']))
    		$this->objQuery->order($this->strTableName . '.' . $GLOBALS['TYPO3_DB']->stripOrderBy($GLOBALS['TCA'][$this->strTableName]['ctrl']['default_sortby']));

    	elseif (key_exists('sortby', $GLOBALS['TCA'][$this->strTableName]['ctrl']))
    		$this->objQuery->order($this->strTableName . '.' . $GLOBALS['TYPO3_DB']->stripOrderBy($GLOBALS['TCA'][$this->strTableName]['ctrl']['sortby']));
	}


    /**
     * Set SQL clause
     *
     * @param string $strClause
     * @return void
     */
	public function SetOrder($strClause)
	{
		$this->objQuery->reset('order');
		$this->objQuery->order($this->strTableName . '.' . $strClause);
	}


    /**
     * Set SQL clause
     *
     * @param string $strClause
     * @return void
     */
	public function AddOrder($strClause)
	{
		$this->objQuery->order($this->strTableName . '.' . $strClause);
	}


    /**
     * Sets a limit count and offset to the query.
     *
     * @param int $count OPTIONAL The number of rows to return.
     * @param int $offset OPTIONAL Start returning after this many rows.
     * @return void
     */
    public function SetLimit($count = null, $offset = null)
    {
		$this->objQuery->limit($count, $offset);
	}


    /**
     * Clears SQL clauses
     *
     * @return void
     */
	public function ClearClauses()
	{
		$this->objQuery->reset('group');
		$this->objQuery->reset('having');
		$this->objQuery->reset('order');
		$this->objQuery->reset('limitcount');
		$this->objQuery->reset('limitoffset');
	}



	/**
	 * add SQL clauses
	 *
	 * @param array
	 * @return void
	 */
	public function SetClauses($mixOptionalClauses)
	{
		if (is_array($mixOptionalClauses)) {

			foreach($mixOptionalClauses as $type => $clause) {
				switch (strtolower($type)) {
					case \Cmp3\Db\QueryGenerator::GROUP:
						$this->objQuery->group($clause);
						break;
					case \Cmp3\Db\QueryGenerator::HAVING:
						$this->objQuery->having($clause);
						break;
					case \Cmp3\Db\QueryGenerator::ORDER:
						$this->objQuery->order($clause);
						break;
					case 'limitcount':
						$this->objQuery->limit($clause);
						break;
					case 'limitoffset':
						$this->objQuery->limit(null, $clause);
						break;
					case 'limit':
						list($offset, $limit) = explode(',', $clause);
						$this->objQuery->limit($limit, $offset);
						break;
					default:
						throw new Exception('SetClauses: unknown clause type: ' . $type .'(' . $clause . ')');

						break;
				}
			}
		}
	}



	/*************************
	 *
	 * Load/Count methods
	 *
	 *************************/


	/**
	 * Load a single object by Uid Index
	 *
	 * @param integer $intUid
	 * @return \Cmp3\Data\Row
	 */
	public function LoadByUid($intUid)
	{
		return $this->QuerySingle($this->strTableName.'.uid='.intval($intUid));
	}


	/**
	 * Load a dataRow object from PK Info
	 *
	 * @param integer $intUid
	 * @return \Cmp3\Data\Row
	 */
	public function Load($intUid)
	{
		return $this->QuerySingle($this->strTableName.'.uid='.intval($intUid));
	}


	/**
	 * Load all dataRow object
	 *
	 * @param array $mixOptionalClauses additional optional clauses for this query
	 * @return \Cmp3\Data\Row[]
	 */
	public function LoadAll($mixOptionalClauses = null)
	{
		return $this->QueryArray(null, $mixOptionalClauses);
	}


	/**
	 * Load an array of dataRow objects
	 * by Pid Index
	 *
	 * @param integer $intPid
	 * @param array $mixOptionalClauses additional optional clauses for this query
	 * @return \Cmp3\Data\Row[]
	 */
	public function LoadArrayByPid($intPid, $mixOptionalClauses = null)
	{
		return $this->QueryArray($this->strTableName.'.pid='.intval($intPid), $mixOptionalClauses);
	}


	/**
	 * Count all items
	 *
	 * @return integer
	 */
	public function CountAll()
	{
		return $this->QueryCount();
	}


	/**
	 * Count items by Pid Index
	 *
	 * @param integer $intPid
	 * @return integer
	 */
	public function CountByPid($intPid)
	{
		return $this->QueryCount($this->strTableName.'.pid='.intval($intPid));
	}



	/*************************
	 *
	 * Query methods
	 *
	 *************************/



	/**
	 * query for a single \Cmp3\Data\Row object.
	 *
	 * @param mixed $mixOptionalCondition any conditions on the query, itself
	 * @param array $mixOptionalClauses additional optional clause objects for this query
	 * @param array $mixParameterArray Dummy and unused - see qcodo. A array of name-value pairs to perform PrepareStatement with
	 * @return \Cmp3\Data\Row the queried object
	 */
	public function QuerySingle($mixOptionalCondition = null, $mixOptionalClauses = null, $mixParameterArray = null)
	{

		if (!is_array($mixOptionalClauses)) {
			$mixOptionalClauses = array();
		}
		$mixOptionalClauses['limitcount'] = 1;
		$mixOptionalClauses['limitoffset'] = 0;

		$objDataRowArray = $this->QueryArray($mixOptionalCondition, $mixOptionalClauses);


		reset($objDataRowArray);
		return current($objDataRowArray);
	}


	/**
	 * query for an array of \Cmp3\Data\Row objects.
	 *
	 * @param mixed $mixOptionalCondition any conditions on the query, itself
	 * @param array $mixOptionalClauses additional optional clause objects for this query
	 * @param array $mixParameterArray Dummy and unused - see qcodo. A array of name-value pairs to perform PrepareStatement with
	 * @return \Cmp3\Data\Row[] the queried objects as an array
	 */
	public function QueryArray($mixOptionalCondition = null, $mixOptionalClauses = null, $mixParameterArray = null)
	{
		$objDataRowArray = array();

		$select = clone($this->objQuery);

		if (!is_array($mixOptionalCondition)) {
			$mixOptionalCondition = array($mixOptionalCondition);
		}
		foreach ($mixOptionalCondition as $key => $condition) {
			if ($condition)
				$select->where($condition, $type = null, $key);
		}

		if (is_array($mixOptionalClauses)) {

#TODO move in qg?
			foreach($mixOptionalClauses as $type => $clause) {
			     switch (strtolower($type)) {
					case \Cmp3\Db\QueryGenerator::GROUP:
						$select->group($clause);
					break;
					case \Cmp3\Db\QueryGenerator::HAVING:
						$select->having($clause);
					break;
					case \Cmp3\Db\QueryGenerator::ORDER:
						$select->order($clause);
					break;
					case 'limitcount':
						$select->limit($clause);
					break;
					case 'limitoffset':
						$select->limit(null, $clause);
					break;
					case 'limit':
						list($offset, $limit) = explode(',', $clause);
						$select->limit($limit, $offset);
					break;
					default:
						throw new Exception('QueryArray: unknown clause type: ' . $type .'(' . $clause . ')');

					break;
				}
			}
		}

		if ($this->objLogger) $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;

		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($select->getArray());

		if ($this->objLogger) $this->objLogger->Debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);


		if ($error = $GLOBALS['TYPO3_DB']->sql_error())	{
			if ($this->objLogger) $this->objLogger->Debug( $error . ' ' . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
			throw new Exception('Database query error');

		} else {

			// fields we want to ignore
			$strExcludeFieldsArray = array_flip(array('php_tree_stop', 'tx_impexp_origuid', 't3ver_oid', 't3ver_id', 't3ver_wsid', 't3ver_label', 't3ver_count', 't3ver_state',  't3ver_stage', 't3ver_tstamp', 't3ver_swapmode', 't3_origuid', 'l18n_diffsource', 't3ver_move_id', 'tx_templavoila_ds', 'tx_templavoila_to', 'tx_templavoila_flex', 'tx_templavoila_pito'));

			while($tempRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{


				// remove unneeded field data
				$tempRow = array_diff_key($tempRow,  $strExcludeFieldsArray);


#TODO check if $objDataSource is needed in dataRowClass

				$strLanguage = $this->GetLanguage($this->strTableName, $tempRow);

				if ($tempRow['uid']) {
					if ($this->dataRowDecoratorClass) {
						$objDataRowArray[$tempRow['uid']] = new $this->dataRowDecoratorClass( new $this->dataRowClass($this->strTableName, $tempRow, $strLanguage));
					} else {
						$objDataRowArray[$tempRow['uid']] = new $this->dataRowClass($this->strTableName, $tempRow, $strLanguage);
					}
				} else {
					if ($this->dataRowDecoratorClass) {
						$objDataRowArray[] = new $this->dataRowDecoratorClass( new $this->dataRowClass($this->strTableName, $tempRow, $strLanguage));
					} else {
						$objDataRowArray[] = new $this->dataRowClass($this->strTableName, $tempRow, $strLanguage);
					}
				}
#TODO	versionOL
//				if (\tx_cmp3::isTypo3Frontend()) {
//					$GLOBALS['TSFE']->sys_page->versionOL($this->strTableName, $objDataRowArray[$tempRow['uid']]);
//				}
			}

			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}


		if (!count($objDataRowArray)) {
			# just return empty result - throw new \Cmp3\DataQuery_Exception_NoResult;
		}


		return $objDataRowArray;


	}


	/**
	 * Query method to query for a count of the current table and conditions.
	 *
	 * @param mixed $mixOptionalCondition any conditions on the query, itself
	 * @param array $mixOptionalClauses additional optional clause objects for this query
	 * @param array $mixParameterArray Dummy and unused - see qcodo. A array of name-value pairs to perform PrepareStatement with
	 * @return integer the count of queried objects as an integer
	 */
	public function QueryCount($mixOptionalCondition = null, $mixOptionalClauses = null, $mixParameterArray = null)
	{

		$count = 0;

		$select = clone($this->objQuery);
		$select->reset('columns');
		$select->from('', 'COUNT('.$this->strTableName.'.uid) as count');

		if (!is_array($mixOptionalCondition)) {
			$mixOptionalCondition = array($mixOptionalCondition);
		}
		foreach ($mixOptionalCondition as $key => $condition) {
			if ($condition)
				$select->where($condition, $type = null, $key);
		}

#TODO 		$this->SetClause($mixOptionalClauses);


		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($select->getArray());

		if ($error = $GLOBALS['TYPO3_DB']->sql_error())	{
			if ($this->objLogger) $this->objLogger->Debug( $error . ' ' . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
			if ($this->objLogger) $this->objLogger->Debug('SQL error: ' . $error . ' in ' . __FILE__ . ' > ' . __CLASS__ . '::' . __FUNCTION__ . '()', \Next\Log::ERR);
#TODO Exception
			throw new Exception('Database query error');

		} else {
			if ($this->objLogger) $this->objLogger->Debug(__METHOD__ . ' ' . $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
			$tempRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$count = intval($tempRow['count']);

			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}

		return $count;
	}




	/*************************
	 *
	 * Tools
	 *
	 *************************/




	/**
	 * Sets a condition which will filter out records with start/end times or hidden/fe_groups fields set to values that should de-select them according to the current time, preview settings or user login.
	 * Is using the $TCA arrays "ctrl" part where the key "enablefields" determines for each table which of these features applies to that table.
	 *
	 * @param	string		Type defines which enable condition to add where type can be null to set all or "default" (deleted, versioning, ...), "disabled", "starttime", "endtime", "fe_group" (keys from "enablefields" in TCA).
	 */
	public function SetEnableFieldsCondition($type=null)
	{
		if ($type)
			$enableColumns = array($type);
		else {
			$enableColumns = \Cmp3\Typo3\QueryTools::GetEnableColumArray($this->strTableName);
			$enableColumns[] = 'perms';
		}

		foreach ($enableColumns as $column) {
#TODO hardcoded FE
    		if ($condition = \Cmp3\Typo3\QueryTools::GetEnableFieldsCondition($this->strTableName, $column, null, \Cmp3\System\SystemType::TYPO3_FE)) {
				if ($this->objLogger) $this->objLogger->Info( 'SetEnableFieldsCondition: ' . $condition);
				$this->objQuery->where($condition); #TODO add key
    		}
		}
	}


	/**
     * configure query for a language or for self joined language data
     *
     * @return void
     */
	public function SetLanguageOverlay($sys_language_content = null, $OLmode = null)
	{
		if ($this->strTableName == 'pages') {
			return $this->SetLanguageOverlayPages($sys_language_content, $OLmode);
		} else {
			return $this->SetLanguageOverlayContent($sys_language_content, $OLmode);
		}
	}


    /**
     * configure query for a language or for self joined language data
     *
     * @return void
     */
	protected function SetLanguageOverlayPages($sys_language_content = null, $OLmode = null)
	{
		global $TCA;

		if (!array_key_exists($this->strTableName, $TCA))
			return;

		$lang0Table = $this->strTableName;

		$sys_language_content = $sys_language_content ? $sys_language_content : $this->forceLanguage;

		if (\tx_cmp3::isTypo3Frontend()) {
			$sys_language_content = $sys_language_content ? $sys_language_content : $GLOBALS['TSFE']->sys_language_content;
			$OLmode = $OLmode ? $OLmode : $GLOBALS['TSFE']->sys_language_contentOL;
		}

		#TODO make use of OLmode

		#TODO resolve iso codes

		$sys_language_content = intval($sys_language_content);

		// Will try to overlay a record only if the sys_language_content value is larger than zero.
		if ($sys_language_content>0 AND ($lang1Table=\Cmp3\Typo3\TcaTools::foreignTranslationTable($this->strTableName)))	{

			\Cmp3\Typo3\TcaTools::LoadTca($lang1Table);

			$languageField = $TCA[$lang1Table]['ctrl']['languageField'];
			$transOrigPointerField = $TCA[$lang1Table]['ctrl']['transOrigPointerField'];

			$fieldArr = trim_explode(',', $GLOBALS['TYPO3_CONF_VARS']['FE']['pageOverlayFields']);
			$columns = array_intersect($fieldArr,array_keys($TCA[$lang1Table]['columns']));
			$columns = \Cmp3\Typo3\QueryTools::PrefixFieldListAsArray($lang1Table, $columns);

			$enableConditions = array();
			if (count($columns)) {
				$on = $lang0Table.'.uid='.$lang1Table.'.'.$transOrigPointerField;
				$this->objQuery->joinLeft($lang1Table, $on, $columns);


				// set enable columns condition for language overlay
				$enableColumns = \Cmp3\Typo3\QueryTools::GetEnableColumArray($lang1Table);
				$enableColumns[] = 'perms';
				foreach ($enableColumns as $enableColumn) {
		    		$enableConditions[] = \Cmp3\Typo3\QueryTools::GetEnableFieldsCondition($lang1Table, $enableColumn);
				}
				$enableConditions = array_filter($enableConditions);
			}

			$enableConditions[] = $lang1Table.'.'.$languageField.'='.intval($sys_language_content);

			$this->objQuery->where(implode(' AND ', $enableConditions));


		}
	}


    /**
     * configure query for a language or for self joined language data
     *
     * @return void
     */
	protected function SetLanguageOverlayContent($sys_language_content = null, $OLmode = null)
	{
		global $TCA;

		if (!array_key_exists($this->strTableName, $TCA))
			return;

		$sys_language_content = $sys_language_content ? $sys_language_content : $this->forceLanguage;

		if (\tx_cmp3::isTypo3Frontend()) {
			$sys_language_content = $sys_language_content ? $sys_language_content : $GLOBALS['TSFE']->sys_language_content;
			$OLmode = $OLmode ? $OLmode : $GLOBALS['TSFE']->sys_language_contentOL;
		}

		#TODO make use of OLmode

		#TODO resolve iso codes

		$sys_language_content = intval($sys_language_content);

		// Will try to overlay a record only if the sys_language_content value is larger than zero.
		if ($sys_language_content>0 AND \Cmp3\Typo3\TcaTools::isTranslationInOwnTable($this->strTableName))	{

			// is table configured for language overlay in own table but no overlay is wanted?
			if ($this->forceNoLanguageOverlay)	{

				$languageField = $TCA[$this->strTableName]['ctrl']['languageField'];
				$this->objQuery->where($this->strTableName.'.'.$languageField.'='.$sys_language_content);


			} else {

					$languageField = $TCA[$this->strTableName]['ctrl']['languageField'];
					$transOrigPointerField = $TCA[$this->strTableName]['ctrl']['transOrigPointerField'];

					// set query fields for language overlay
					$columns = array();
					foreach($TCA[$this->strTableName]['columns'] as $field => $config) {

						// FIXME this should be applied to selected fields only
						if ($config['l10n_mode'] == 'mergeIfNotBlank') {
							$columns[] = 'IF(('.$this->strTableName.'.'.$field.'=\'\'),lang0.'.$field.','.$this->strTableName.'.'.$field.') as '.$field.'';
						} else if ($config['l10n_mode'] == 'exclude') {
							$columns[] = 'lang0.'.$field.' as '.$field.'';
						}
					}

					$enableConditions = array();

					if (count($columns)) {
						$on = $this->strTableName.'.'.$transOrigPointerField.'=lang0.uid';
						$this->objQuery->joinLeft(array('lang0' => $this->strTableName), $on, $columns);
						$enableConditions[] = $this->strTableName.'.pid=lang0.pid';


						// set enable columns condition for language overlay
						$enableColumns = \Cmp3\Typo3\QueryTools::GetEnableColumArray($this->strTableName);
						foreach ($enableColumns as $column) {
				    		$enableConditions[] = \Cmp3\Typo3\QueryTools::GetEnableFieldsCondition($this->strTableName, $column, 'lang0');
						}
						$enableConditions = array_filter($enableConditions);
					}

					$enableConditions[] = $this->strTableName.'.'.$languageField.'='.intval($sys_language_content);

					$this->objQuery->where(implode(' AND ', $enableConditions));

			}
		} else if ($sys_language_content === 0) {
			$languageField = $TCA[$this->strTableName]['ctrl']['languageField'];
			$this->objQuery->where($this->strTableName.'.'.$languageField.'='.$sys_language_content);
		}

	}





	/**
	 * Returns the languager iso code for the record
	 *
	 * @return string
	 */
	function GetLanguage($strTableName, $tempRow)
	{
		global $LANG, $TSFE, $TYPO3_DB;


		$languageField = $TCA[$strTableName]['ctrl']['languageField'];
		$sys_language_uid = intval($tempRow[$languageField]);

		if ($sys_language_uid==0) {
			return \Cmp3\Cmp3::$DefaultLanguage;
		}

		$res = $TYPO3_DB->exec_SELECTquery(
				'sys_language.uid',
				'sys_language LEFT JOIN static_languages ON sys_language.static_lang_isocode=static_languages.uid',
				'sys_language.uid='.$TYPO3_DB->fullQuoteStr($sys_language_uid,'sys_language')
		);
		$row = $TYPO3_DB->sql_fetch_assoc($res);

		$TYPO3_DB->sql_free_result($res);
		return $row[''];;
	}
}










