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
 * @subpackage Base
 * @package    CMP3
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */

namespace Cmp3;


/**
 * Base class with generic constructor / array setter
 *
 * @author	Rene Fritz <r.fritz@bitmotion.de>
 * @package	CMP3
 * @subpackage	Base
 */
class Base {


	/**
	 * Generic constructor
	 *
	 * @param array $options
	 */
	public function __construct(array $Properties = null)
	{
		//if it is an array of properties the call SetProperties and apply those properties
		if (is_array($Properties)) {
			$this->SetProperties($Properties);
		}

		$this->Construct();
	}


	/**
	 * Constructor might be used in extended class
	 */
	public function Construct()
	{

	}


	/**
	 *
	 * @param array $options
	 */
	public function SetProperties(array $options)
	{
		// logger might be needed early
		if (isset($options['Logger'])) {
			$this->objLogger = $options['Logger'];
			unset($options['Logger']);
		}

		// config might be needed next
		if (isset($options['Config'])) {
			$this->objConfig = $options['Config'];
			unset($options['Config']);
		}

		//loop through the properties and assign them to $property as setters?
		foreach ($options as $key => $value) {
			$property = ucfirst($key);
			$this->$property = $value;

		}
		return $this;
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
		throw new \Cmp3\UndefinedGetPropertyException($strName);
	}


	/**
	 * Override method to perform a property "Set"
	 * This will set the property $strName with $mixValue
	 *
	 * @param string $strName Name of the property to get
	 * @param string $mixValue Value of the property to set
	 * @throws \Cmp3\UndefinedSetPropertyException
	 * @return mixed
	 */
	public function __set($strName, $mixValue)
	{
		throw new \Cmp3\UndefinedSetPropertyException($strName);
	}

}

