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
class MetaGeneric implements MetaInterface {


	/**
	 * Name of the table this object provides meta data for
	 *
	 * @var string
	 */
	protected $strTableName;

	/**
	 * Language iso code
	 *
	 * @var string
	 */
	protected $strLanguage;


	protected $objFieldArray = array();


	/**
	 * Constructor
	 *
	 * @param string $strTableName the name of the table this object provides meta data for
	 * @param string $strLanguage
	 * @throws Exception
	 */
	public function __construct ($strTableName, $strLanguage='EN')
	{
		$this->strTableName = $strTableName;
		$this->strLanguage = $strLanguage;
	}



	/**
	 * Override method to perform a property "Get"
	 * This will get the property $strName
	 *
	 * @param string $strName Name of the property to get
	 * @return mixed
	 * @throws \Cmp3\UndefinedGetPropertyException
	 */
	public function __get($strName)
	{
		switch ($strName) {

			case 'TableName':
				return $this->strTableName;
				break;

			case 'Title':
				return $this->strTableName;
				break;

			case 'Language':
					return $this->strLanguage;
				break;

			case 'Type':
				return '';
				break;

			case 'DataFields':
					return array_keys ($this->objFieldArray);
				break;

			case 'DataRow':
				return null;
				break;

			default:
				throw new \Cmp3\UndefinedGetPropertyException ($strName);
			break;
		}
	}


	/**
	 *
	 */
	public function SetFieldArray($objFieldArray)
	{
		if (!is_array($objFieldArray)) {
			throw new Exception('Parameter needs to be of type Field[]!');
		}

		foreach ($objFieldArray as $objField) {
			$this->AddField($objField);
		}
	}


	/**
	 *
	 */
	public function AddField(Field $objField)
	{
		$this->objFieldArray[$objField->Name] = $objField;
	}


	/**
	 * Returns the a fields data definition to be used in cmp3 xml
	 *
	 * @param string $strFieldName
	 * @return Field
	 */
	public function GetFieldDefinition($strFieldName)
	{
		return $this->objFieldArray[$strFieldName];
	}


}

