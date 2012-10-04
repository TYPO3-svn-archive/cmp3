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
 * base class for access to Typoscript and TSconfig
 *
 * STATUS: beta
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Config
 * @package    CMP3
 */
abstract class TypoScriptBase extends \Cmp3\Config\ConfigResource  {

	/*
	 * We don't define $_config here because we want to access $_config through a getter in some extended classes
	 */

	/**
	 * cache of configuration values
	 * @var array
	 */
	protected $cache = array();




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
	 * @param string	$strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param mixed 	$strDefault Default value will be returned if value is empty
	 * @return	mixed	Just the value
	 */
	public function GetValue($strConfigPath, $strDefault = null)
	{
		if (array_key_exists($strConfigPath, $this->cache)) {
			$value = $this->cache[$strConfigPath];
			return is_null($value) ? $strDefault : $value;
		}

		$value = $this->_GetValue($strConfigPath, $this->_config);
		$this->cache[$strConfigPath] = $value;

		return is_null($value) ? $strDefault : $value;
	}


	/**
	 * Returns configuration value which won't be processed in any way
	 *
	 * TypoScript path like some.setup.value is possible
	 *
	 * @param string	$strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param mixed 	$strDefault Default value will be returned if value is empty
	 * @return	mixed	Just the value
	 */
	public function GetRawValue($strConfigPath, $strDefault = null)
	{
		$value = null;

		if($strConfigPath) {
			$TSConf = $this->_getConfigObject($strConfigPath, $this->_config);

			$value = $TSConf['value'];
		}

		return is_null($value) ? $strDefault : $value;
	}


	/**
	 * Returns configuration properties (array)
	 *
	 * TypoScript path like some.setup.property is possible
	 *
	 * @param string 	$strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @return mixed 	array with the properties of the $strConfigPath
	 */
	public function GetProperties ($strConfigPath)
	{
		$properties = null;

		if($strConfigPath) {
			$TSConf = $this->_getConfigObject($strConfigPath, $this->_config);
			$properties = $TSConf['properties'];
		}
		//TODO: should this return an empty array instead of null?
		return is_array($properties) ? $properties : null;
	}


	/**
	 * Check a config value if its enabled
	 * Anything except '' and 0 is true
	 * If the option is not set the default value will be returned
	 *
	 * @param string 	$strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param mixed 	$strDefault Default value will be returned if the option is not
	 * @return boolean
	 */
	// FIXME: disabled function for now, I don't get it, what it should do
	public function XXXisEnabled($strConfigPath, $strDefault = null)
	{
		$parts = reverse_explode('.', $strConfigPath, 2);
		$config = $this->GetProperties($parts[0]);

		if (!is_array($config) OR !array_key_exists($parts[1], $config)) {
			return $strDefault;
		}
		return $this->_isEnabled($config[$parts[1]]);
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
	 * Set a config value which is only temporary
	 *
	 * @param string 	$strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param	mixed 	$value Value to be set. Can be an array but must be in TSConfig format
	 * @return void
	 */
	public function SetValue($strConfigPath, $value)
	{
		$perfomMerge = false;
		if(!is_array($this->_config)) {
			$this->_config = array();
		}

		if ($strConfigPath) {
			list ($baseKey, $options) = explode('.', $strConfigPath, 2);
			$options = explode('.', $options);
			$lastOption = count ($options);
			if (!is_array($definedTSconfig[$baseKey.'.'])) {
				$definedTSconfig[$baseKey.'.'] = array();
			}
			$optionArrPath = & $definedTSconfig[$baseKey.'.'];
			$optCount = 0;
			foreach ($options as $optionValue) {
				$optCount++;
				if ($optCount < $lastOption) {
					$optionArrPath = & $optionArrPath[$optionValue.'.'];
				} else {
					$optionArrPath = & $optionArrPath[$optionValue.(is_array($value)?'.':'')];
				}

			}
			$optionArrPath = $value;
			$perfomMerge = true;
		}
		if ($perfomMerge) {
			$this->_config = array_merge_recursive_overrule($this->_config, $definedTSconfig);
		}
		$this->cache[$strConfigPath] = $value;
	}









	/***************************************
	 *
	 *   Internal
	 *
	 ***************************************/


	/**
	 * Get configuration values from TypoScript
	 *
	 * @param	string		TypoScript key to get a value from ($this->conf[$tsKey]) with stdWrap
	 * @param	array		TS setup array that may include the wanted TS key
	 * @return	string		configuration value
	 */
	protected function _GetValue($tsKey, $config)
	{
		$value = null;

		if($tsKey) {
			$TSConf = $this->_getConfigObject($tsKey, $config);

			$value = $TSConf['value'];
		}
		return $value;
	}





	/**
	 * Returns the value/properties of a TS-object as given by $objectString, eg. 'options.dontMountAdminMounts'
	 * Nice (general!) function for returning a part of a TypoScript array!
	 *
	 * @param	string		$objectString Pointer to an "object" in the config array tree, fx. 'options.dontMountAdminMounts'
	 * @param	array		$config TSconfig array
	 * @return	array		An array with two keys, "value" and "properties" where "value" is a string with the value of the object string and "properties" is an array with the properties of the object string.
	 */
	protected function _getConfigObject($objectString, $config)
	{
		$TSConf=array(
			'value' => null,
			'properties' => null,
		);

		if (is_array($config)) {
			$parts = explode('.',$objectString,2);
			$key = $parts[0];
			if (trim($key))	{
				if (count($parts)>1 && trim($parts[1]))	{
					// Go on, get the next level
					if (isset($config[$key.'.']) AND is_array($config[$key.'.']))
						$TSConf = $this->_getConfigObject($parts[1],$config[$key.'.']);
				} else {
					$TSConf['value'] = array_get_value($key, $config);
					$TSConf['properties'] = array_get_value($key.'.', $config);
				}
			}
			$TSConf['properties'] = is_array($TSConf['properties']) ? $TSConf['properties'] : null;
		}
		return $TSConf;
	}

}






