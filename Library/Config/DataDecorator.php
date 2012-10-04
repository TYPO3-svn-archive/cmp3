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
 * @copyright  Copyright (c) 2012 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Config;



/**
 * Gives access to the applications configutration values.
 * In this case it's a proxy to another config object.
 *
 * The purpose of this proxy is to insert data into the configuration at runtime.
 * Data could come from different sources, but the main source might be to get data from the job object.
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @package	CMP3
 * @subpackage Config
 */
class DataDecorator extends \Cmp3\Config\ConfigResource {


	/**
	 *
	 * @var \Cmp3\Config\ConfigResource
	 */
	protected $objConfig;


	/**
	 * @var Job
	 */
	protected $objJob;


	/**
	 * constructor
	 *
	 * @param \Cmp3\Config\ConfigResource $objConfig
	 * @param \Cmp3\Job\Job $objJob
	 * @return void
	 */
	public function __construct($objConfig, $objJob)
	{
		$this->objConfig = $objConfig;
		$this->objJob = $objJob;
	}


	/**
	 * Calling RequestGlobal for all unknown stuff
	 *
	 * @param $name
	 * @param $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments)
	{
		return call_user_func_array(array($this->objConfig, $name), $arguments);
	}

	#TODO implement InsertData() for magic getter?

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
		$strValue = $this->objConfig->GetValue($strConfigPath, $strDefault);

		if (is_string($strValue)) {
			$strValue = $this->InsertData($strValue);
		}

		return $strValue;
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
		return $this->objConfig->GetRawValue($strConfigPath, $strDefault);
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
		return $this->objConfig->GetProperties($strConfigPath);
	}


	/**
	 * Returns all configuration data as (multidimensional) array
	 *
	 * @return array
	 */
	public function GetAll ()
	{
		return $this->objConfig->GetAll();
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
		return $this->objConfig->SetValue($strConfigPath, $value);
	}






	/***************************************
	 *
	 *   internal
	 *
	 ***************************************/


	/**
	 * borrowed from tslib_content
	 *
	 * if strings matching {...} is found in the input string they will be substituted with the return value from getData (datatype) which is passed the content of the curly braces.
	 * Example: If input string is "This is the page title: {page:title}" then the part, '{page:title}', will be substituted with the current pages title field value.
	 *
	 * @param	string		Input value
	 * @return	string		Processed input value
	 */
	protected function InsertData($str)
	{
		$inside = 0;
		$newVal = '';
		$pointer = 0;
		$totalLen = strlen($str);
		do {
			if (!$inside) {
				$len = strcspn(substr($str, $pointer), '{');
				$newVal .= substr($str, $pointer, $len);
				$inside = 1;
			} else {
				$len = strcspn(substr($str, $pointer), '}') + 1;
				$newVal .= $this->getData(substr($str, $pointer + 1, $len - 2));
				$inside = 0;
			}
			$pointer += $len;
		} while ($pointer < $totalLen);
		return $newVal;
	}


	/**
	 * borrowed from tslib_content
	 *
	 * Implements the TypoScript data type "getText". This takes a string with parameters and based on those a value from somewhere in the system is returned.
	 *
	 * @param	string		The parameter string, eg. "field : title" or "field : navtitle // field : title" (in the latter case and example of how the value is FIRST splitted by "//" is shown)
	 * @return	string		The value fetched
	 */
	protected function getData($string)
	{
		$retVal = '';
		$sections = explode('//', $string);

		while (!$retVal and list ($secKey, $secVal) = each($sections)) {
			$parts = explode(':', $secVal, 2);
			$key = trim($parts[1]);
			if ((string) $key != '') {
				$type = strtolower(trim($parts[0]));
				switch ($type) {
					case 'jobdata' :
						$retVal = $this->objJob->GetData($key);
						break;

					case 'tsfe' :
						$retVal = $this->getGlobal('TSFE|' . $key);
						break;

					case 'getenv' :
						$retVal = getenv($key);
						break;

					case 'getindpenv' :
						$retVal = \tx_cmp3::$System->GetEnv($key);
						break;

					case 'global' :
						$retVal = $this->getGlobal($key);
						break;

					case 'date' :
						$retVal = date($key, $this->objJob->Time);
						break;
				}
			}
		}

		return $retVal;
	}


	/**
	 * borrowed from tslib_content
	 *
	 * Return global variable where the input string $var defines array keys separated by "|"
	 * Example: $var = "HTTP_SERVER_VARS | something" will return the value $GLOBALS['HTTP_SERVER_VARS']['something'] value
	 *
	 * @param	string		Global var key, eg. "HTTP_GET_VAR" or "HTTP_GET_VARS|id" to get the GET parameter "id" back.
	 * @param	array		Alternative array than $GLOBAL to get variables from.
	 * @return	mixed		Whatever value. If none, then blank string.
	 * @see getData()
	 */
	protected function getGlobal($keyString, $source = NULL) {
		$keys = explode('|', $keyString);
		$numberOfLevels = count($keys);
		$rootKey = trim($keys[0]);
		$value = isset($source) ? $source[$rootKey] : $GLOBALS[$rootKey];

		for ($i = 1; $i < $numberOfLevels && isset($value); $i++) {
			$currentKey = trim($keys[$i]);
			if (is_object($value)) {
				$value = $value->$currentKey;
			} elseif (is_array($value)) {
				$value = $value[$currentKey];
			} else {
				$value = '';
				break;
			}
		}

		if (!is_scalar($value)) {
			$value = '';
		}
		return $value;
	}

}










