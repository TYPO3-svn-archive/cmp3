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
 * Gives access to the applications configutration values.
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Config
 * @package    CMP3
 */
interface ConfigInterface {




	/***************************************
	 *
	 *   GET Configuration
	 *
	 ***************************************/


	/**
	 * Override method to perform a property "Get"
	 * This will get the property $strName
	 *
	 * @param string $strName Name of the property to get
	 * @return mixed
	 */
	public function __get ($strName);


    /**
     * Alias to __get
     *
     * @param string $key
     * @return mixed
     */
    public function Get($key);


    /**
     * Check to see if a property is set
     *
     * @param string $key
     * @return boolean
     */
    public function __isset ($strName);


    /**
     * Alias to __isset()
     *
     * @param string $key
     * @return boolean
     */
    public function has ($key);


	/**
	 * Returns configuration value
	 *
	 * TypoScript path like some.setup.value is possible
	 *
	 * @param string $strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param mixed $strDefault Default value will be returned if value is empty
	 * @return	mixed	Just the value
	 */
	public function GetValue ($strConfigPath, $strDefault = null);


	/**
	 * Returns configuration value which won't be processed in any way
	 *
	 * TypoScript path like some.setup.value is possible
	 *
	 * @param string $strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param mixed $strDefault Default value will be returned if value is empty
	 * @return	mixed	Just the value
	 */
	public function GetRawValue ($strConfigPath, $strDefault = null);


	/**
	 * Returns configuration properties (array)
	 *
	 * TypoScript path like some.setup.property is possible
	 *
	 * @param string $strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @return mixed
	 */
	public function GetProperties ($strConfigPath);


	/**
	 * Check a config value if its enabled
	 * Anything except '' and 0 is true
	 * If the option is not set the default value will be returned
	 *
	 * @param string 	$strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param mixed 	$strDefault Default value will be returned if the option is not
	 * @return boolean
	 */
	public function isEnabled ($strConfigPath, $strDefault = null);


	/**
	 * Returns all configuration data as (multidimensional) array
	 *
	 * @return array
	 */
	public function GetAll ();





	/***************************************
	 *
	 *   SET Configuration
	 *
	 ***************************************/


	/**
	 * Override method to perform a property "Set"
	 * This will set the property $strConfigPath
	 *
	 * @param string 	$strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param	mixed 	$value Value to be set. Can be an array but must be in TSConfig format
	 * @return void
	 */
	public function __set ($strConfigPath, $value);


	/**
	 * Set a config value
	 *
	 * @param string 	$strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param	mixed 	$value Value to be set. Can be an array but must be in right format
	 * @return void
	 */
	public function SetValue ($strConfigPath, $value);



}









