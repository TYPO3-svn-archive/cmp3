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
 * Misc functions to access TYPO3 TCA
 *
 * This shouldn't be used by applications directly.
 *
 *
 *
 * @author     Rene Fritz <r.fritz@colorcube.de>
 * @subpackage TYPO3
 * @package    CMP3
 */
abstract class TcaTools {


    /**
     * Just not allow to instantiate
     * This is a static class
     */
    private function __construct() {
    }


	/**
	 * Load full TCA for a table
	 *
	 * @param string $table
	 * @return array
	 */
	public static function LoadTca($table)
	{
		global $TCA;

		if (is_array($GLOBALS['TYPO3_CONF_VARS']['TCA'][$table])) {

				foreach($GLOBALS['TYPO3_CONF_VARS']['TCA'][$table] as $tcaFile) {
					if (\t3lib_div::isAbsPath($tcaFile) && @is_file($tcaFile)) {
						include_once($tcaFile);
					}
				}

		} elseif (\tx_cmp3::isTypo3Frontend() AND is_object($GLOBALS['TSFE'])) {
			$GLOBALS['TSFE']->includeTCA();

			# TODO process tcacachedextras?

		} else {
			self::_LoadTca($table);
		}
	}


#TODO cleanup
	/**
	 * Load full TCA for a table
	 *
	 * @param string $table
	 * @return array
	 */
	function _LoadTca($table)
	{
		global $TCA;

		if (!defined('TYPO3_MODE') OR !TYPO3_MODE) {
			# TODO how to resolve the extension dir for a table?
			#@include ($extDir.'tca_ctrl.php');
			#@include ($extDir.'tca.php');

		}


		// workaround for static_info_tables
		if (defined('STATIC_INFO_TABLES_EXTkey') AND isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][STATIC_INFO_TABLES_EXTkey]['tables'][$table])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][STATIC_INFO_TABLES_EXTkey]['extendingTCA'] as $extKey) {
				$extDir = t3lib_extmgm::extPath($extKey);
				if (@is_file($extDir.'ext_tables.php')) {
					include_once($extDir.'ext_tables.php');
				}
			}
		}


		if (is_array($GLOBALS['TYPO3_CONF_VARS']['TCA'][$table])) {

			#FIXME this will not triggering loading when a tca file was added to the array after columns already exists
			# we migth need to track which file was included or not? :-/

			// we don't want to load tca on every call so we check this
			if (!isset($TCA[$table]['ctrl']) OR !isset($TCA[$table]['columns'])) {
				foreach($GLOBALS['TYPO3_CONF_VARS']['TCA'][$table] as $tcaFile) {
					if (\tx_cmp3::isAbsolutePath($tcaFile) && @is_file($tcaFile)) {
						// include_once doesn't work here because TCA might be wiped after tca.php was already included
						include($tcaFile);
					}
				}
			}

		} elseif(defined('TYPO3_MODE') AND TYPO3_MODE) {

			// for some reason $GLOBALS['TSFE'] could be an object in BE but not tslib_fe
			if (is_object($GLOBALS['TSFE']) AND $GLOBALS['TSFE'] instanceof tslib_fe) {

				// load std tables tca
				#TODO this might erase all TCA the first time
				$GLOBALS['TSFE']->includeTCA();
			}

			\t3lib_div::loadTCA($table);

			# TODO process tcacachedextras?
		}
	}



	/**
	 * Get TCA for a table
	 *
	 * @param string $table
	 * @return array
	 */
	public static function GetTableTca($table)
	{
		global $TCA;
		self::_LoadTca($table);

		return $TCA[$table];
	}


	/**
	 * Get TCA for a field
	 *
	 * @param string $table
	 * @param string $field
	 * @return array
	 */
	public static function GetFieldTca($table, $field)
	{
		$TCA = self::GetTableTca($table);
		return $TCA['columns'][$field];
	}


	/**
	 * Get config array from TCA for a field
	 *
	 * @param string $table
	 * @param string $field
	 * @return array
	 */
	public static function GetFieldTcaConfig($table, $field)
	{
		$TCA = self::GetTableTca($table);
		return $TCA['columns'][$field]['config'];
	}


	/**
	 * Get config items array from TCA for a select field
	 *
	 * @param string $table
	 * @param string $field
	 * @return array
	 */
	public static function GetFieldTcaSelectItems ($table, $field)
	{
		$TCA = self::GetTableTca($table);
		return $TCA['columns'][$field]['config']['items'];
	}



	/******************************
	 *
	 * Label
	 *
	 ******************************/


	/**
	 * Returns the tranlated label for a fields content.
	 * In this case the label of the first found entry in an "items" array from $TCA (tablename = $table/fieldname = $field) where the value is $key
	 *
	 * @param	string		Table name, present in $TCA
	 * @param	string		Field name, present in $TCA
	 * @param	string		items-array value to match
	 * @return	string		Label for item entry
	 */
	public static function GetLabelFromContent($table, $field, $key)
	{
		$TCA = self::GetTableTca($table);
			// Check, if there is an "items" array:
		if (is_array($TCA) && is_array($TCA['columns'][$field]) && is_array($TCA['columns'][$field]['config']['items'])) {
				// Traverse the items-array...
			foreach($TCA['columns'][$field]['config']['items'] as $k => $v) {
					// ... and return the first found label where the value was equal to $key
				if (!strcmp($v[1], $key))	return txApplications::GetCurrent()->Translate($v[0]);
			}
		}

		return $key;
	}


	/**
	 * Get a language label for a table field
	 * appended ':' will be removed
	 *
	 * @param string $table
	 * @param string $field
	 * @return string
	 */
	public static function GetFieldLabel($table, $field)
	{
		$TCA = self::GetFieldTca($table, $field);
		$label = txApplications::GetCurrent()->Translate($TCA['label']);
		$label = \Cmp3\String\String::StripColon($label);
		return $label;
	}


	/**
	 * Get a language label for a table field label
	 * appended ':' will be removed
	 *
	 * @param string $label language resource, eg. 'LLL:EXT:lang/locallang_general.xml:LGL.hidden'
	 * @return string
	 */
	public static function TranslateLabel($label)
	{
		$label = txApplications::GetCurrent()->Translate($label);
		$label = \Cmp3\String\String::StripColon($label);
		return $label;
	}




	/******************************
	 *
	 * TranslationTable
	 *
	 ******************************/



	/**
	 * Returns the table in which translations for input table is found.
	 *
	 * @param	[type]		$table: ...
	 * @return	[type]		...
	 */
	public static function GetTranslationTable($table) {
		return self::isTranslationInOwnTable($table) ? $table : self::foreignTranslationTable($table);
	}


	/**
	 * Returns true, if the input table has localization enabled and done so with records from the same table
	 *
	 * @param	[type]		$table: ...
	 * @return	[type]		...
	 */
	public static function isTranslationInOwnTable($table) {
		global $TCA;

		return ($TCA[$table]['ctrl']['languageField'] && $TCA[$table]['ctrl']['transOrigPointerField'] && !$TCA[$table]['ctrl']['transOrigPointerTable']);
	}


	/**
	 * Returns foreign translation table, if any
	 *
	 * @param	[type]		$table: ...
	 * @return	[type]		...
	 */
	public static function foreignTranslationTable($table) {
		global $TCA;

		$trTable = $TCA[$table]['ctrl']['transForeignTable'];

		if ($trTable && $TCA[$trTable] && $TCA[$trTable]['ctrl']['languageField'] && $TCA[$trTable]['ctrl']['transOrigPointerField'] && $TCA[$trTable]['ctrl']['transOrigPointerTable']===$table)	{
			return $trTable;
		}
	}




	/******************************
	 *
	 * fields
	 *
	 ******************************/


	/**
	 * Returns an array of fields for a table which are configured in TCA or ctrl fields.
	 * This includes uid, pid, and ctrl fields.
	 *
	 * @param 	string		$strTableName
	 * @param	boolean		$mainFieldsOnly If true not all fields from the TCA columns-array will be used but the ones from the ctrl-array.
	 * @param	array		$addFields Field list array which should be appended to the list no matter if defined in TCA.
	 * @return	array		Field list array
	 */
	public static function GetFieldListArray($strTableName, $mainFieldsOnly=FALSE, $addFields=array())
	{
		global $TCA;

		$fieldListArr=array();

		if (!is_array($addFields)) {
			$addFields = trim_explode(',', $addFields);
		}
		foreach ($addFields as $field)	{
			#if ($TCA[$strTableName]['columns'][$field]) {
				$fieldListArr[$field] = $field;
			#}
		}

		if (is_array($TCA[$strTableName]))	{
			self::_LoadTca($strTableName);
			if (!$mainFieldsOnly) {
				foreach($TCA[$strTableName]['columns'] as $fieldName => $dummy)	{
					$fieldListArr[$fieldName] = $fieldName;
				}
			}
			$fieldListArr['uid'] = 'uid';
			$fieldListArr['pid'] = 'pid';

			$ctrlFields = array('label','label_alt','type','typeicon_column','tstamp','crdate','cruser_id','sortby','delete','fe_cruser_id','fe_crgroup_id','languageField','transOrigPointerField');
			foreach ($ctrlFields as $field)	{
				if ($TCA[$strTableName]['ctrl'][$field]) {
					$subFields = trim_explode(',',$TCA[$strTableName]['ctrl'][$field]);
					foreach ($subFields as $subField)	{
						$fieldListArr[$subField] = $subField;
					}
				}
			}

			if (is_array($TCA[$strTableName]['ctrl']['enablecolumns'])) {
				foreach ($TCA[$strTableName]['ctrl']['enablecolumns'] as $field)	{
					if ($field) {
						$fieldListArr[$field] = $field;
					}
				}
			}
		}
		return $fieldListArr;
	}

}








