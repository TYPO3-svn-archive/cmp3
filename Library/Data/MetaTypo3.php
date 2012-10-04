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
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Data;



/**
 * {@inheritdoc}
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Data
 * @package    CMP3
 */
class MetaTypo3 extends MetaAbstract {

	/**
	 * Some predefined TCA entries which can't be found in TCA
	 *
	 * @var array
	 */
	public static $DefaultTCA = array(
		'columns' => array(
			'tstamp' => array(
				'config' => array(
					'type' => 'input',
					'eval' => 'datetime'
				)
			),
			'crdate' => array(
				'config' => array(
					'type' => 'input',
					'eval' => 'datetime'
				)
			),
		)
	);


	/**
	 * TYPO3 TCA - Table Configuration Array
	 *
	 * @see http://typo3.org/documentation/document-library/core-documentation/doc_core_api/4.0.0/view/4/2/
	 *
	 * @var array
	 */
	protected $TCA;


	/**
	 * Array that describes fields types of a db table
	 * @see $GLOBALS['TYPO3_DB']->admin_get_fields()
	 * @var array
	 */
	protected $FieldsTypes;


	/**
	 * Constructor
	 *
	 * @param \Cmp3\Data\DataRow_Interface|\Cmp3\Data\RowInterface|string $objDataRowOrTableName object that provides the data of the record or the name of the table this object provides meta data for
	 * @throws Exception
	 */
	public function __construct ($objDataRowOrTableName)
	{
		parent::__construct($objDataRowOrTableName);

		$this->TCA = $this->GetTableTca($this->strTableName);

		if (!$this->TCA) throw new Exception(__CLASS__.': no TCA for table '.$this->strTableName.' was found!');

		$this->FieldsTypes = $GLOBALS['TYPO3_DB']->admin_get_fields($this->strTableName);
	}


	/**
	 * Override method to perform a property "Get"
	 * This will get the property $strName
	 *
	 * @param string $strName Name of the property to get
	 * @return mixed
	 */
	public function __get($strName)
	{
		switch ($strName) {

			case 'Title':
				// todo resolve LL
				return $this->TCA['ctrl']['title'];

			case 'Type':
					$strType = '';
					if ($strTypeField = $this->TCA['ctrl']['type']) {
						$strType = $this->objDataRow->$strTypeField;
					}
					return $strType;
				break;

			default:
				return parent::__get($strName);
			break;
		}
	}


	/**
	 * {@inheritdoc}
	 */
	protected function GetType($strFieldName)
	{
		// we want int from int(11)
		list($strFieldType) = trim_explode('(', $this->FieldsTypes[$strFieldName]['Type']);

		$strFieldType = strtoupper($strFieldType);
		$strFieldType = str_replace('TINY', '', $strFieldType);
		$strFieldType = str_replace('SMALL', '', $strFieldType);
		$strFieldType = str_replace('MEDIUM', '', $strFieldType);
		$strFieldType = str_replace('LONG', '', $strFieldType);
		$strFieldType = str_replace('BIG', '', $strFieldType);


		$strFieldType = str_replace('VARCHAR', 'TEXT', $strFieldType);
		$strFieldType = str_replace('CHAR', 'TEXT', $strFieldType);

		switch ($strFieldType) {
			case 'INT':
				$strFieldType = 'int';
			break;

			case 'FLOAT':
			case 'DOUBLE':
			case 'DECIMAL':
				$strFieldType = 'float';
			break;

			case 'DATETIME':
				$strFieldType = 'datetime';
			break;

			case 'DATE':
				$strFieldType = 'date';
			break;

			case 'TIME':
				$strFieldType = 'time';
			break;

			case 'BLOB':
				#TODO blob can be int or string when relations are used in T3
				$strFieldType = 'blob';
			break;

			case 'TEXT':
			default:
				$strFieldType = 'text';
			break;
		}
		return $strFieldType;
	}


	/**
	 * {@inheritdoc}
	 */
	protected function GetFormat($strFieldName)
	{
		$strFieldFormat = '';

		$strFieldConfig = $this->TCA['columns'][$strFieldName]['config'];

		if (isset($strFieldConfig['eval'])) {
			// don't know id it's valid to have an array here but it happens
			$strFieldEvalArray = is_array($strFieldConfig['eval']) ? $strFieldConfig['eval'] : trim_explode(',', $strFieldConfig['eval']);
			if ($strFieldEvalArray) {

				#TODO timesec, year

				if (array_search('datetime', $strFieldEvalArray) !== false) {
					$strFieldFormat = 'datetime';

				} else if (array_search('date', $strFieldEvalArray) !== false) {
					$strFieldFormat = 'date';

				} else if (array_search('time', $strFieldEvalArray) !== false) {
					$strFieldFormat = 'time';

				} else if (array_search('integer', $strFieldEvalArray) !== false) {
					$strFieldFormat = 'int';

				} else if (array_search('double2', $strFieldEvalArray) !== false) {
					$strFieldFormat = 'float';

				} else if (array_search('num', $strFieldEvalArray) !== false) {
					$strFieldFormat = 'int';
				}
			}
		}


		if (!$strFieldFormat) {
			switch ($strFieldConfig['type']) {
				case 'input':
					$strFieldFormat = 'line';
				break;

				case 'text':
					$strFieldFormat = 'multline';

					if (isset($strFieldConfig['wizards']['RTE']) OR isset($strFieldConfig['_isRTE'])) {
						$strFieldFormat = 'typo3_rte';
					} elseif (isset($strFieldConfig['wizards']['table'])) {
						$strFieldFormat = 'typo3_table';
					}
				break;

#TODO images, select, group,

				default:

					if ($strFieldConfig['type'] == 'flex') {
						return 'xml';
					}

					$strFieldFormat = 'text';
				break;
			}
		}
		return $strFieldFormat;
	}


	/**
	 * {@inheritdoc}
	 */
	protected function GetMeta($strFieldName)
	{
		if ($strFieldName == $this->TCA['ctrl']['label']) {
			return 'header';
		}

		# TODO detect which field bodytext field is
		# make it configurable in TCA

		return '';
	}



	#TODO add preprocessing of fields:
	# - link fields to url
	# link fields depend on TSFE and TypoScript - see tslib_cObj::typolink()

	#TODO make this processors using xpath for example

	# doing it with xslt and PHP functions might be a good idea
	# see http://php.net/manual/en/xsltprocessor.registerphpfunctions.php

	/*
	 * to process content
	   <xsl:value-of select="php:functionString('bla::renderRTE', . )">

	   to modfy node which is needed to change type attribute ??
	   <xsl:copy-of select="php:functionString('bla::renderTable', . )">
	 */




	/**
	 * {@inheritdoc}
	 */
	protected function GetValue($strFieldName)
	{
		$strFieldValue = $this->objDataRow->$strFieldName;
		$strFieldConfig = $this->TCA['columns'][$strFieldName]['config'];
		$strFormat = $this->GetFormat($strFieldName);
		$strType  = $this->GetType($strFieldName);

		if ($strFormat == 'datetime' AND $strType == 'int') {
			$strFieldValue = date('c', $strFieldValue);
			$this->FieldsTypes[$strFieldName]['Type'] = 'datetime';

		} else if ($strFormat == 'date' AND $strType == 'int') {
			list($strFieldValue) = explode('T', date('c', $strFieldValue));
			$this->FieldsTypes[$strFieldName]['Type'] = 'date';

		} else if ($strFormat == 'time' AND $strType == 'int') {
			list($dummy, $strFieldValue) = explode('T', date('c', $strFieldValue));
			$this->FieldsTypes[$strFieldName]['Type'] = 'time';

		}

		// TODO it might be better to check field type before doing this
		if ($strFieldConfig['uploadfolder'] AND $strFieldValue) {
			$strFieldValue = PATH_site . $strFieldConfig['uploadfolder'] .'/' . $strFieldValue;
		}

		$strFieldValue = \Cmp3\Xml\Tools::DecodeHtmlEntities($strFieldValue);

		return $strFieldValue;
	}


	/**
	 * Get TCA for a table
	 *
	 * @param $strTableName
	 * @throws Exception
	 * @return array
	 */
	protected function GetTableTca($strTableName)
	{
		global $TCA;
		\Cmp3\Typo3\TcaTools::LoadTca($strTableName);

		if (!is_array($TCA[$strTableName])) {
			throw new Exception("Could't find TCA for table '$strTableName'!");
		}

		$tca = array_merge_recursive(self::$DefaultTCA, $TCA[$strTableName]);

		return $tca;
	}


}



