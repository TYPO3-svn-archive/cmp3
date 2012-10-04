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
 * @property-read Row $DataRow The data object
 * @property-read array $DataArray Current data of the record with key=>$value pairs. Plain data. Unprocessed.
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Data
 * @package    CMP3
 */
abstract class MetaAbstract implements MetaInterface {


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


	/**
	 * object that provides the data of the record
	 *
	 * @var DataRow_Interface
	 */
	protected $objDataRow;


	/**
	 * Constructor
	 *
	 * @param DataRow_Interface|string $objDataRowOrTableName object that provides the data of the record or the name of the table this object provides meta data for
	 * @throws Exception
	 */
	public function __construct ($objDataRowOrTableName)
	{
		if (!$objDataRowOrTableName) throw new Exception(__CLASS__.': no data row or table name was given!');

		if ($objDataRowOrTableName instanceof RowInterface) {
			$this->objDataRow = $objDataRowOrTableName;
			$this->strTableName = $this->objDataRow->TableName;
			$this->Language = $this->objDataRow->Language;
		} else {
			$this->strTableName = $objDataRowOrTableName;
		}
	}


	/**
	 * Set the object that provides the data of the current record.
	 * This needs to be called before any other method, espacially before getting Type.
	 *
	 * @param DataRow_Interface object that provides the data of the record
	 * @return void
	 */
	public function SetDataRow($objDataRow)
	{
		$this->objDataRow = $objDataRow;
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

			case 'DataRow':
					return $this->objDataRow;
				break;

			default:
				return $this->objDataRow->__get($strName);
			break;
		}
	}


	/**
	 * Returns the a fields data definition to be used in cmp3 xml
	 *
	 * @param string $strFieldName
	 * @return Field
	 */
	public function GetFieldDefinition($strFieldName)
	{
		// we need to get the format first because the type might be changed

		$strContent = $this->GetValue($strFieldName);

		return new Field($strFieldName,  $this->GetType($strFieldName),  $this->GetFormat($strFieldName), $this->GetMeta($strFieldName), $strContent);
	}


	/**
	 * Returns the type of the field
	 *
	 * Types are the more raw presentations of data which reflects not necessarily the specific data meaning.
	 *
	 * text     - string type of any length or format
	 * datetime - format is %Y-%m-%dT%H:%M:%S%z, the W3C format. (xs:datetime)
	 * date     - %Y-%m-%d (xs:date)
	 * time     - %H:%M:%S%z (xs:time)
	 * int      - integer, could be negative
	 * float    - floating number of any precision
	 * blob     - binary data
	 *
	 * @see for date formats: http://www.w3.org/TR/xpath-functions/#date-time-values
	 *
	 * @param string $strFieldName
	 * @return string Field type
	 */
	protected function GetType($strFieldName)
	{
		return 'text';
	}


	/**
	 * Returns the format of the field
	 *
	 * Format defines the specific data meaning.
	 * Example: For the type integer the format could be datetime
	 *
	 * line      - string of any length but only one line. Example: header
	 * multiline - string of any length and multiple lines with no further formating instructions like &lt;b&gt;
	 * rich      - string of any length and multiple lines with formating instructions TODO to be defined
	 * datetime  - field defines date and time
	 * date      - just date
	 * time      - a time
	 * int       - integer, could be negative
	 * float     - floating number of any precision
	 * images    - TODO
	 *
	 *
	 * @param string $strFieldName
	 * @return string Field format
	 */
	protected function GetFormat($strFieldName)
	{
		return '';
	}


	/**
	 * Returns the meta information of the field
	 *
	 * This defines the meaning of fields in relation to the record
	 *
	 * header  - defines that the field is the header of the record
	 * body    - the field holds the body text of the record
	 * TODO more?
	 *
	 * @param string $strFieldName
	 * @return string Field meta desription
	 */
	protected function GetMeta($strFieldName)
	{
		return '';
	}


	/**
	 * Returns the field value proper formatted for use in CMP3XML
	 *
	 * HINT This may change the Type of the field, so GetType() might give a different result afterward.
	 * The Type is then the right type for the processed field value.
	 *
	 * @param string $strFieldName
	 * @return mixed
	 */
	protected function GetValue($strFieldName)
	{
		return $this->objDataRow->$strFieldName;
	}

}

