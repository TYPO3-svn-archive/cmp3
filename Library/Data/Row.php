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
 * @see Data\MetaInterface
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Data
 * @package    CMP3
 */
class Row implements RowInterface {


	/**
	 * Name of the table to work on
	 *
	 * @var string
	 */
	protected $_strTableName;

	/**
	 * Language iso code
	 *
	 * @var string
	 */
	protected $_strLanguage;

	/**
	 * data from the database
	 *
	 * @var array
	 */
	protected $_dataArray = array();


	/**
	 * Constructor
	 *
	 * @param string $strTableName
	 * @param array $strDataArray full record data
	 * @param string $strLanguage
	 * @return \Cmp3\Data\Row $objDataSource
	 */
	public function __construct($strTableName, $strDataArray, $strLanguage=null)
	{
		$this->_strTableName = $strTableName;
		$this->_dataArray = $strDataArray;
		$this->_strLanguage = $strLanguage ? $strLanguage : \Cmp3\Cmp3::$DefaultLanguage;
	}


	/**
	 * Override method to perform a property "Get"
	 * This will get the property $strName
	 *
	 * @param string $strName Name of the property to get
	 * @throws \Cmp3\UndefinedGetPropertyException
	 * @return mixed
	 */
	public function __get($strName)
	{
		switch ($strName) {
			case 'TableName':
					return $this->_strTableName;
				break;

			case 'Language':
					return $this->_strLanguage;
				break;

			case 'DataArray':
					return $this->_dataArray;
				break;

			case 'DataFields':
					return array_keys ($this->_dataArray);
				break;
			default:

				if (array_key_exists($strName, (array)$this->_dataArray)) {
					return $this->_dataArray[$strName];

				// Qcodo ask for Uid instead of uid - underscores will still fail because of studly caps used by qcodo
				// todo remove?
				} elseif (($strNameLower = strtolower($strName)) AND $strNameLower != $strName) {
					if ($strNameLower AND array_key_exists($strNameLower, (array)$this->_dataArray)) {
						return $this->_dataArray[$strNameLower];
					}
				}

				throw new \Cmp3\UndefinedGetPropertyException ($strName);
			break;
		}
	}



	/*************************
	 *
	 * Iterator methods
	 *
	 *************************/


	protected $_fieldList = array();

	public function key()	{
		return current($this->_fieldList);
	}

	public function current()	{
		if ($offset = $this->key())
			return ($this->_dataArray[$offset]);
		return false;
	}

	public function next()	{
		next($this->_fieldList);
		return $this->current();
	}

	public function rewind()	{
		$this->_fieldList = array_keys($this->_dataArray);

		return reset($this->_fieldList);
	}

	public function valid()    {
		return (bool) current($this->_fieldList);
	}

	public function offsetExists($offset)    {
		if(array_key_exists($this->_dataArray, $offset)) return true;
		else return false;
	}

	public function offsetGet($offset)    {
		if(array_key_exists($offset, $this->_dataArray)) $this->_dataArray[$offset];
		else return false;
	}

	public function offsetSet($offset, $value)    {
		if (is_null($offset)) {
			// this is not a valid operation
		} else {
			$this->_dataArray[$offset] = $value;
		}
	}

	public function offsetUnset($offset)    {
		$this->_dataArray[$offset] = '';
	}

}



