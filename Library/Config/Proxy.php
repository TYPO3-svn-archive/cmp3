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
 * In this case it's a proxy to other config object but is restrict access to specific path.
 *
 * The path that should be prepended to all config keys can be set.
 * If set to 'user.' all values will be fetched from this path.
 * GetValue('isAdmin') will return the value from 'user.isAdmin'.
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Config
 * @package    CMP3
 */
class Proxy extends \Cmp3\Config\ConfigResource {


	/**
	 *
	 * @var \Cmp3\Config\ConfigResource
	 */
	protected $objConfig;

	/**
	 * The path that should be prepended to all config keys.
	 * If set to 'user.' all values will be fetched from this path.
	 * GetValue('isAdmin') will return the value from user.isAdmin
	 *
	 * @var string
	 */
	protected $strBasePath;

	/**
	 * stores the configuration values
	 * @var array
	 */
	protected $_config = array();


	/**
	 * constructor
	 *
	 * @param \Cmp3\Config\ConfigResource $objConfig
	 * @param string $basePath The path that should be prepended to all config keys.
	 * @return void
	 */
	public function __construct($objConfig, $basePath='')
	{
		$this->objConfig = $objConfig;
		$this->strBasePath = \Cmp3\String\String::StripDot($basePath) . '.';
	}


	/**
	 * Calling parent config object for all unknown stuff
	 *
	 * @param $name
	 * @param $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments)
	{
		return call_user_func_array(array($this->objConfig, $name), $arguments);
	}


	/***************************************
	 *
	 *   GET Configuration
	 *
	 ***************************************/




	/**
	 * Returns configuration value
	 *
	 * TypoScript path like some.setup.value is possible
	 *
	 * @param string $strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param mixed $strDefault Default value will be returned if value is empty
	 * @return	mixed	Just the value
	 */
	public function GetValue ($strConfigPath, $strDefault = null)
	{
		if($strConfigPath AND array_key_exists($strConfigPath, $this->_config)) {
			$value = (string)$this->_config[$strConfigPath];
			return is_null($value) ? $strDefault : $value;
		}

		return $this->objConfig->GetValue($this->strBasePath.$strConfigPath, $strDefault);
	}


	/**
	 * Returns configuration value which won't be processed in any way
	 *
	 * TypoScript path like some.setup.value is possible
	 *
	 * @param string $strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param mixed $strDefault Default value will be returned if value is empty
	 * @return	mixed	Just the value
	 */
	public function GetRawValue ($strConfigPath, $strDefault = null)
	{
		return $this->objConfig->GetRawValue($this->strBasePath.$strConfigPath, $strDefault);
	}


	/**
	 * Returns configuration properties (array)
	 *
	 * TypoScript path like some.setup.property is possible
	 *
	 * @param string $strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @return mixed
	 */
	public function GetProperties ($strConfigPath)
	{
		return $this->objConfig->GetProperties($this->strBasePath.$strConfigPath);
	}


	/**
	 * Get configuration properties merged from all resources
	 *
	 * Be aware that you may mess up your TS config with this function.
	 * Because the '>' operator in TypoScript doesn't work during run time one couldn't redefine procFields.title inside a mode
	 * if we would use this method.
	 * @todo parameter to limit merging to second level (eg.)
	 *
	 * @param string $strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param string $order	defines the order from where the config shall be fetched from
	 * @return	array configuration properties
	 */
	public function GetPropertiesMerged($strConfigPath, $order=null)
	{
		return $this->objConfig->GetPropertiesMerged($this->strBasePath.$strConfigPath, $order);
	}


	/**
	 * Returns all configuration data as (multidimensional) array
	 *
	 * @return array
	 */
	public function GetAll ()
	{
		return $this->objConfig->GetProperties($this->strBasePath);
	}





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
	public function __set ($strConfigPath, $value)
	{
		return $this->SetValue ($strConfigPath, $value);
	}


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
			return ($this->_config[$strConfigPath] = (string)$value);
		}

		return null;
	}


	/**
	 * @see DataDecorator::InsertData()
	 *
	 * @param	string		Input value
	 * @return	string		Processed input value
	 */
	public function InsertData($str)
	{
		return $this->objConfig->InsertData($str);
	}


	/**
	 * Returns a Config proxy starting from the given path
	 *
	 * @param string $strConfigPath
	 * @return \Cmp3\Config\ConfigInterface
	 */
	public function GetProxy($strConfigPath)
	{
		return new Proxy($this, $strConfigPath);
	}
}










