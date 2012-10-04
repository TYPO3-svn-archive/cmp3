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
 * Gives access to the applications configuration values.
 * This class doesn't handle configuration itself but several other config resources can be registered and provide the real configuration.
 * The first config resource that has a definition for a property return the value.
 *
 * 
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Core
 * @package    CMP3
 */
class Config extends \Cmp3\Config\ConfigResource  {





	/***************************************
	 *
	 *   Resources
	 *
	 ***************************************/

	protected $resources = array();


	/**
	 * Adds a config resource
	 * An existing resource with the same  name will be replaced.
	 *
	 * @param $strName Resource identifier
	 * @param \Cmp3\Config\ConfigResource $configObject config object
	 * @return void
	 */
	public function AddResource ($strName, $configObject)
	{
		if (!($configObject instanceof \Cmp3\Config\ConfigResource)) throw new Exception ('Argument 2 passed to \Cmp3\Config\Config::addResource() must be an instance of \Cmp3\Config\ConfigResource');
		$this->resources[$strName] = $configObject;
	}


	/**
	 * Returns configuration resource object
	 *
	 * @param $strName Resource identifier
	 * @return \Cmp3\Config\ConfigResource
	 */
	public function GetResource ($strName)
	{
		if (!array_key_exists($strName, $this->resources))
			throw new Exception ('Config resource "'.$strName.'" is not available!');

		return $this->resources[$strName];
	}


	/**
	 * Remove a configuration resource
	 *
	 * @param $strName Resource identifier
	 * @return void
	 */
	public function RemoveResource ($strName)
	{
		unset($this->resources[$strName]);
	}


	/**
	 * Set the highest priority for a resource
	 * @param $strName Resource identifier
	 * @return void
	 */
	public function SetResourcePriorityTop ($strName)
	{
		array_reverse($this->resources);
		$resource = $this->resources[$strName];
		unset($this->resources[$strName]);
		$this->resources[$strName] = $resource;
		array_reverse($this->resources);
	}


	/**
	 * Set the lowest priority for a resource
	 * @param $strName Resource identifier
	 * @return void
	 */
	public function SetResourcePriorityBottom ($strName)
	{
		$resource = $this->resources[$strName];
		unset($this->resources[$strName]);
		$this->resources[$strName] = $resource;
	}


	/**
	 * Sets the priority order of the resources
	 * @param $order
	 * @return void
	 */
	public function SetResourceOrder($order)
	{
		$order = is_array($oder) ? $order : explode(',', $order);
		if (count($order)) {
			$resources = $this->resources;
			$this->resources = array();
			foreach ($order as $id) {
				if (array_key_exists($id, $resources)) {
					$this->resources[$id] = $resources[$id];
					unset($resources[$id]);
				}
			}
			foreach ($resources as $id) {
				$this->resources[$id] = $resources[$id];
				unset($resources[$id]);
			}
		}
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
	 * @param string $strName
	 * @param mixed $strDefault
	 * @return mixed
	 */
	public function GetValue ($strName, $strDefault = null)
	{
		$value = null;
		foreach ($this->resources as $id => $objConfig) {
			$v = $objConfig->GetValue($strName);
			if (!is_null($v)) {
				$value = $v;

				if (self::$Debug) self::$Debug->Log('Get config \''.$strName.'='.$value.'\' from resource '.$id);

				break;
			}
		}
		return is_null($value) ? $strDefault : $value;
	}


	/**
	 * Returns configuration value which won't be processed in any way
	 *
	 * TypoScript path like some.setup.value is possible
	 *
	 * @param string $strName
	 * @param mixed $strDefault
	 * @return mixed
	 */
	public function GetRawValue ($strName, $strDefault = null)
	{
		$value = null;
		foreach ($this->resources as $id => $objConfig) {
			$v = $objConfig->getRawValue($strName);
			if (!is_null($v)) {
				$value = $v;

				if (self::$Debug) self::$Debug->Log('Get raw config \''.$strName.'='.$value.'\' from resource '.$id);

				break;
			}
		}
		return is_null($value) ? $strDefault : $value;
	}


	/**
	 * Get configuration values from a specific set of config ressources
	 *
	 * @param string 	$strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param mixed 	$strDefault Default value will be returned if value is empty
	 * @param string	$order	defines the order from where the config shall be fetched from
	 * @return	string		configuration value
	 */
	public function GetValueFrom($strConfigPath, $strDefault = null, $order='ts:override,ff,default,ts:default')
	{

		$value = null;

		$order = is_array($oder) ? $order : explode(',', $order);
		if (count($order)) {
			foreach ($order as $id) {
				if (array_key_exists($id, $this->resources)) {
					$v = $this->resources[$id]->getValue($strName);
					if (!is_null($v)) {
						$value = $v;
						break;
					}
				}
			}
		}

		return is_null($value) ? $strDefault : $value;
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
		$properties = null;

		foreach ($this->resources as $id => $objConfig) {
			$v = $objConfig->getProperties($strConfigPath);
			if (!is_null($v)) {
				$properties = $v;
				break;
			}
		}

		return is_array($properties) ? $properties : array();
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
		$properties = array();

		if ($order) {
			$order = is_array($oder) ? $order : explode(',', $order);
		} else {
			$order = array_keys($this->resources);
		}
		if (count($order)) {
			foreach ($order as $id) {
				if (array_key_exists($id, $this->resources)) {
					$p = $this->resources[$id]->getProperties($strConfigPath);
					if (is_array($p)) {
						$properties = array_merge_recursive_overrule($p, $properties);
					}

				}
			}
		}

		return $properties;
	}





	/***************************************
	 *
	 *   SET Configuration
	 *
	 ***************************************/

	 // something todo here?
}







