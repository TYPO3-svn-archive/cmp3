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
 * @subpackage Config
 * @package    CMP3
 * @copyright  Copyright (c) 2008 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Config;




/**
 * Gives access to configutration values.
 * In this case it's access to values of an array which is set in the constructor.
 *
 *  - beta after refactoring
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Config
 * @package    CMP3
 */
class ArrayData extends \Cmp3\Config\ConfigResource {


	/**
	 * stores the configuration values
	 * @var array
	 */
	protected $_config = array();


	/**
	 * Init config values
	 *
	 * @param array $configArray
	 * @return void
	 */
	public function __construct(array $configArray = array())
	{
		$this->_config = (array)$configArray;
	}



	/***************************************
	 *
	 *   GET Configuration
	 *
	 ***************************************/



	/**
	 * Returns configuration value
	 *
	 *
	 * @param string $strConfigPath Pointer to an "object" in the config array
	 * @param mixed $strDefault Default value will be returned if value is empty
	 * @return	mixed	Just the value
	 */
	public function GetValue ($dataKey, $strDefault = null)
	{
		$value = null;

		if($dataKey AND array_key_exists($dataKey, $this->_config)) {
			$value = $this->_config[$dataKey];
		}

		return is_null($value) ? $strDefault : $value;
	}


	/**
	 * Returns all configuration data as (multidimensional) array
	 *
	 * @return array
	 */
	public function GetAll ()
	{
		return $this->_config;
	}




	/***************************************
	 *
	 *   SET Configuration
	 *
	 ***************************************/



	/**
	 * Set a config value
	 *
	 * @param string 	$strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param	mixed 	$value Value to be set.
	 * @return void
	 */
	public function SetValue($strConfigPath, $value)
	{
		if($strConfigPath) {
			return ($this->_config[$strConfigPath] = $value);
		}

		return null;
	}

}







