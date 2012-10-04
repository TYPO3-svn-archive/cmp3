<?php
/**
 * Naw EXTension framework - NEXT
 *
 * LICENSE
 *
 * This source file is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 *
 * This script is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
 * Public License for more details.
 *
 * @subpackage DataStructure
 * @package    NEXT
 * @copyright  Copyright (c) 2009 Rene Fritz <r.fritz@colorcube.de>
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 */

namespace Cmp3\Data;




/**
 * This is a general purpose Data Object with getter and setter.
 * The class needs to be extended to initialize the internal data array.
 * Accessing other keys than the defined will throw an exception.
 */

interface DataStructInterface {


	/**
	 * Initializes the data array with keys that are allowed
	 *
	 * @return void
	 */
	public function ClearDataArray();


	/**
	 * Set data with array
	 *
	 * @param $dataArray Can be used to set the objects data
	 * @return void
	 */
	public function SetDataArray($dataArray = array());


	/**
	 * Returns the internal data array
	 *
	 * @return array
	 */
	public function GetDataArray();


	/**
	 * check if an element is set (which still can be NULL!)
	 *
	 * @param string $strName Name of the property to get
	 * @return boolean
	 */
	public function has($strName);


	/**
	 * Removes an element
	 *
	 * @param string $strName Name of the property to get
	 */
	public function Remove($strName);
}



/**
 * This is a general purpose Data Object with getter and setter.
 * The class needs to be extended to initialize the internal data array.
 * Accessing other keys than the defined will throw an exception.
 */
abstract class DataStructAbstract {


	protected $_dataArray = array();


	/**
	 * constructor
	 *
	 * @param $dataArray Can be used to set the objects data
	 * @return void
	 */
	public function __construct($dataArray = array())
	{
		$this->InitDataArray();
		$this->SetDataArray($dataArray);
	}


	/**
	 * Initializes the data array with keys that are allowed
	 *
	 * @return void
	 */
	protected function InitDataArray()
	{
		/*
		$this->_dataArray = array(
				'example1' => null,
				'example2' => null,
			);
		*/
	}


	/**
	 * Initializes the data array with keys that are allowed
	 *
	 * @return void
	 */
	public function ClearDataArray()
	{
		$this->InitDataArray();
	}


	/**
	 * Set data with array
	 *
	 * @param $dataArray Can be used to set the objects data
	 * @return void
	 */
	public function SetDataArray($dataArray = array())
	{
		if ($dataArray and is_array($dataArray)) {
			foreach($dataArray as $key => $value) {
				$this->$key = $value;
			}
		}
	}


	/**
	 * Returns the internal data array
	 *
	 * @return array
	 */
	public function GetDataArray()
	{
		return $this->_dataArray;
	}



	/*************************
	 *
	 * Get/Set methods
	 *
	 *************************/


	/**
	 * Override method to perform a property "Set"
	 * This will set the property $strName to be $mixValue
	 *
	 * Elements which are not yet set will throw an exception
	 *
	 * @param string $strName Name of the property to set
	 * @param string $mixValue New value of the property
	 * @return mixed
	 * @throws \Cmp3\UndefinedSetPropertyException
	 */
	public function __set($strName, $mixValue)
	{
		if (array_key_exists($strName, $this->_dataArray)) {
			return ($this->_dataArray[$strName] = $mixValue);
		}
		throw new \Cmp3\UndefinedSetPropertyException ($strName);
	}


	/**
	 * Override method to perform a property "Get"
	 * This will get the property $strName
	 *
	 * Elements which are not yet set will throw an exception
	 *
	 * @param string $strName Name of the property to get
	 * @return mixed
	 * @throws \Cmp3\UndefinedGetPropertyException
	 */
	public function __get($strName)
	{
		if (array_key_exists($strName, $this->_dataArray)) {
			return $this->_dataArray[$strName];
		}
		throw new \Cmp3\UndefinedGetPropertyException ($strName);
	}


	/**
	 * check if an element is set (which still can be NULL!)
	 *
	 * @param string $strName Name of the property to get
	 * @return boolean
	 */
	public function has($strName)
	{
		return array_key_exists($strName, $this->_dataArray);
	}


	/**
	 * Removes an element
	 *
	 * @param string $strName Name of the property to get
	 */
	public function Remove($strName)
	{
		unset($this->_dataArray[$strName]);
	}
}




abstract class DataStructNoExceptionAbstract extends \Cmp3\Data\DataStructAbstract {




	/**
	 * Clears the data array
	 *
	 * @return void
	 */
	public function ClearDataArray()
	{
		$this->_dataArray = array();
	}


	/**
	 * Override method to perform a property "Set"
	 * This will set the property $strName to be $mixValue
	 *
	 * @param string $strName Name of the property to set
	 * @param string $mixValue New value of the property
	 * @return mixed
	 */
	public function __set($strName, $mixValue)
	{
		return ($this->_dataArray[$strName] = $mixValue);
	}


	/**
	 * Override method to perform a property "Get"
	 * This will get the property $strName
	 *
	 * Elements which are not yet set will return NULL.
	 *
	 * @param string $strName Name of the property to get
	 * @return mixed
	 */
	public function __get($strName)
	{
		return $this->_dataArray[$strName];
	}
}



// it might not be needed to initialize the data array, so this is not abstract
class DataStructNoException extends \Cmp3\Data\DataStructNoExceptionAbstract {}




