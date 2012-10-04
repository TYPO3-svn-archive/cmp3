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
* @subpackage TYPO3
* @package    CMP3
* @copyright  Copyright (c) 2008 Rene Fritz <r.fritz@colorcube.de>
* @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
* @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
*/

namespace Cmp3\Typo3;




/**
 * Data query stuff
 *
 *
 * @author     Rene Fritz <r.fritz@colorcube.de>
* @subpackage TYPO3
* @package    CMP3
 */
abstract class QueryTools {


    /**
     * Just not allow to instantiate
     * This is a static class
     */
    private function __construct() {
    }



	/*************************
	 *
	 * Enable fields
	 *
	 *************************/


	/**
	 * Returns condition which will filter out records with start/end times or hidden/fe_groups fields set to values that should de-select them according to the current time, preview settings or user login.
	 * Works for FE and BE
	 * Is using the $TCA arrays "ctrl" part where the key "enablefields" determines for each table which of these features applies to that table.
	 *
	 * @param	string $table Table name found in the $TCA array
	 * @param	string $type  Type define which enable condition to add where type can be "default" (deleted, versioning, ...), "disabled", "starttime", "endtime", "fe_group" (keys from "enablefields" in TCA).
	 * @param 	string $alias Table name alias which will be used as prefix instead of the given table
	 * @param string \Cmp3\System\SystemType
	 * @return string SQL condition for enable fields
	 */
	public static function GetEnableFieldsCondition($table, $type= 'default', $alias = null, $SystemType = null)
	{
		static $sysPage;

		$SystemType = $SystemType ? $SystemType : \tx_cmp3::$System->Type;


		if ($type == 'default') {
			$ignore = array();
		} else {
			$ignore = self::GetEnableColumArray($table);

			// should not be ignored
			unset ($ignore[$type]);
		}


		if ($SystemType == \Cmp3\System\SystemType::TYPO3_FE) {


			// in EID script there's no TSFE
			if (!is_object($sysPage)) {
				if (is_object($GLOBALS['TSFE']) AND is_object($GLOBALS['TSFE']->sys_page)) {
					$sysPage = $GLOBALS['TSFE']->sys_page;
				} else {
					require_once(PATH_t3lib.'class.t3lib_page.php');
					$sysPage = new \t3lib_pageSelect;
					$sysPage->init(false);
				}
			}


			// get conditions without the 'ignored'
			$condition = $sysPage->enableFields($table, false, $ignore);

			// if some specific type/column we have to remove the default (deleted)
			if ($type!='default' AND $type!='enablecolumns') {
				// pass all columns to ignore to get the default condition (deleted)
				$ignore = self::GetEnableColumArray($table);
				$conditionDefault = $sysPage->enableFields($table, false, $ignore);
				$condition = str_replace($conditionDefault, '', $condition);
			}


		} elseif ($SystemType == \Cmp3\System\SystemType::TYPO3_BE) {
#TODO perms?
			if ($table == 'pages' AND ($type == 'default' OR $type == 'perms'))
				$condition .= $GLOBALS['BE_USER']->getPagePermsClause(1);
#TODO use BEenableFields?
#			if ($type == 'default' OR $type == 'xxxxxx')
#				$condition .= t3lib_befunc::BEenableFields($table);
			if ($type == 'default' OR $type == 'disabled')
				$condition .= t3lib_BEfunc::deleteClause($table);


		} elseif($type == 'default' OR $type == 'disabled')
				$condition .= t3lib_BEfunc::deleteClause($table);

		if ($alias) {
			$condition = str_replace($table.'.', $alias.'.', $condition);
		}

    	return self::StripAND($condition);
	}


	/**
	 * Returns all enable columns for a table
	 *
	 * @param string $table
	 * @return array
	 */
	public static function GetEnableColumArray($table)
	{
		global $TCA;

		$enableColumns = array();
		if (key_exists($table, $TCA)) {

			if (key_exists('enablecolumns', $TCA[$table]['ctrl']))
				$enableColumns = array_keys($TCA[$table]['ctrl']['enablecolumns']);
			$enableColumns[] = 'default';
			$enableColumns = array_flip($enableColumns);
			array_walk($enableColumns, create_function('&$item, $key', '$item = $key;'));
		}

		return $enableColumns;
	}



	/*************************
	 *
	 * Field Mapping
	 *
	 *************************/


	/**
	 * Remaps field names
	 *
	 * @param array $strDataArray Record data array: field => value
	 * @param array $strMappingArray Mapping array: current key => target key
	 * @return array
	 */
	public static function RenameFields($strDataArray, $strMappingArray)
	{
		foreach ($strMappingArray as $strCurrentName => $strFieldName) {
			if (key_exists($strCurrentName, $strDataArray)) {
				$strDataArray[$strFieldName] = $strDataArray[$strCurrentName];
				unset ($strDataArray[$strCurrentName]);
			}
		}
		return $strDataArray;
	}




	/*************************
	 *
	 * Tools
	 *
	 *************************/


	/**
	 * Strip a 'AND' from the beginning of a string
	 *
	 * @param string $string
	 * @return string
	 */
	public static function StripAND($string)
	{
		return trim(preg_replace('#^AND #', '', trim($string)));
	}


	/**
	 * prepend the table to the field if necessary
	 *
	 * @param string $table
	 * @param string $field
	 * @return string
	 */
	public static function PrefixField ($table, $field)
	{
		list ($p0, $p1) = explode('.', $field);
		if ($p0 == $field) {
			return $table.'.'.$field;
		}
		return $field;
	}


	/**
	 * prepend the table to the list of fieldsfield if necessary
	 *
	 * @param string $table
	 * @param array|string $fieldList
	 * @return string
	 */
	public static function PrefixFieldList ($table, $fieldList)
	{
		$fieldList = is_array($fieldList) ? $fieldList : \t3lib_div_trimExplode(';', $fieldList, true);

		foreach ($fieldList as $key => $field) {
			$fieldList[$key] = self::PrefixField($table, $field);
		}

		return implode(',', $fieldList);
	}


	/**
	 * prepend the table to the list of fieldsfield if necessary
	 *
	 * @param string $table
	 * @param array|string $fieldList
	 * @return string
	 */
	public static function PrefixFieldListAsArray ($table, $fieldList)
	{
		$fieldList = is_array($fieldList) ? $fieldList : \t3lib_div_trimExplode(';', $fieldList, true);

		foreach ($fieldList as $key => $field) {
			$fieldList[$key] = self::PrefixField($table, $field);
		}

		return $fieldList;
	}

}






