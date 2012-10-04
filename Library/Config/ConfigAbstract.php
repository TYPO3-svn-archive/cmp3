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
 * In this case it's access to typoscript setup.
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Config
 * @package    CMP3
 */
abstract class ConfigAbstract implements \Cmp3\Config\ConfigInterface {

	/**
	 * Debug logger
	 * This is initialized from outside
	 *
	 * @var \Cmp3\Log\Logger
	 */
	public static $Debug;


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
	public function __get ($strName)
	{
		return $this->GetValue($strName);
	}


	/**
	 * Alias to __get
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function Get($key)
	{
		return $this->GetValue($key);
	}


	/**
	 * Check to see if a property is set
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function __isset ($strName)
	{
		return $this->GetValue($strName)===null ? false : true;
	}


	/**
	 * Alias to __isset()
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function has ($key)
	{
		return $this->__isset($key);
	}


	/**
	 * Checks if a property exists
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function hasProperty ($key)
	{
		return (($this->GetProperties($key) === null AND $this->GetValue($key) === null) ? false : true);
	}


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
		return null;
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
		return $this->GetValue ($strConfigPath, $strDefault);
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
		return null;
	}


	/**
	 * Returns configuration properties (array)
	 *
	 * TypoScript path like some.setup.property is possible
	 *
	 * @deprecated
	 * @param string $strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @return mixed
	 */
	public function GetPropertiesMerged ($strConfigPath)
	{
		return null;
	}


	/**
	 * Returns configuration value which is checked for an integer value.
	 * If the configured value is not an integer or isn't inside the min/max range the default value will be returned.
	 *
	 * TypoScript path like some.setup.value is possible
	 *
	 * @param string $strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param mixed $intDefault Default value will be returned if value is empty
	 * @param mixed $intMin If value is below this the default value will be returned
	 * @param mixed $intMax If value is above this the default value will be returned
	 * @return	integer|NULL	the value
	 */
	public function GetInteger ($strConfigPath, $intDefault = null, $intMin = null, $intMax = null)
	{
		$intValue = $this->GetValue ($strConfigPath);

		if ((string)$intValue === (string)(int)$intValue) {
			if (!is_null($intMin) AND $intValue < $intMin) {
				$intValue = $intDefault;
			} else if (!is_null($intMax) AND $intValue > $intMax) {
				$intValue = $intDefault;
			} else {
				$intValue = (int)$intValue;
			}
		} else {
			$intValue = $intDefault;
		}
		return $intValue;
	}


	/**
	 * Returns configuration value which is expected to be a filepath.
	 * The path will be resolved to an absolute path.
	 *
	 * TypoScript path like some.setup.value is possible
	 *
	 * @param string $strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param mixed $strDefault Default value will be returned if value is empty
	 * @return string Filepath
	 */
	public function GetFilename ($strConfigPath, $strDefault = null)
	{
		if ($filepath = $this->GetValue($strConfigPath, $strDefault)) {
			$filepath = \Cmp3\System\Env::ResolvePath($filepath);
		}

		return (string)$filepath;
	}


	/**
	 * Returns configuration value which is expected to be a timespan in seconds.
	 * It is possible to define the timespan as integer but also with a string which can be parsed with strtotime().
	 *
	 * Examples:
	 * 1 year 4m
	 *  10h15m
	 *  10s
	 * 2 weeks 4m
	 *
	 * TypoScript path like some.setup.value is possible
	 *
	 * @param string $strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param mixed $strDefault Default value will be returned if value is empty
	 * @return integer
	 */
	public function GetTimeSpan ($strConfigPath, $strDefault = null)
	{
		$timestamp = $this->GetValue($strConfigPath, $strDefault);

		$timestamp = self::ParseTimeSpan($timestamp);

		return $timestamp;
	}


	/**
	 * Parses a string and transforms it into a time span (seconds)
	 *
	 * y|year|years
	 * month|months
	 * w|week|weeks
	 * d|day|days
	 * h|hour|hours
	 * m|min|minute|minutes
	 * s|sec|second|seconds
	 *
	 * @param string $time time string
	 * @return integer
	 */
	protected static function ParseTimeSpan($time)
	{
		$val = intval($time);

		if ("$val"===(string)$time) {
			return $val;
		}

		$timeArray = preg_split('/([^a-z]+)/', str_replace(' ', '', strtolower((string) $time)), -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

		$intSeconds = 0;

		reset($timeArray);
		while (list(, $value) = each($timeArray)) {
			if ($value = intval($value)) {
				$range = current($timeArray);
				next($timeArray);

				switch ($range) {
					case 'y':
					case 'year':
					case 'years':
						$intSeconds += $value * 365 * 24 * 3600;
					break;

					case 'month':
					case 'months':
						$intSeconds += $value * 2628000;
					break;

					case 'w':
					case 'week':
					case 'weeks':
						$intSeconds += $value * 7 * 24 * 3600;
					break;

					case 'd':
					case 'day':
					case 'days':
						$intSeconds += $value * 24 * 3600;
					break;

					case 'h':
					case 'hour':
					case 'hours':
						$intSeconds += $value * 3600;
					break;

					case 'm':
					case 'min':
					case 'minute':
					case 'minutes':
						$intSeconds += $value * 60;
					break;

					case 's':
					case 'sec':
					case 'second':
					case 'seconds':
						$intSeconds += $value;
					break;

					default:
						;
					break;
				}
			}
		}

		return $intSeconds;
	}


	/**
	 * Returns configuration value which is expected to be a timestamp.
	 * It is possible to define the timestamp as integer but also with a date string which can be parsed with strtotime().
	 *
	 * Examples:
	 * now
	 * 10 September 2000
	 * +1 day
	 * +1 week
	 * +1 week 2 days 4 hours 2 seconds
	 * next Thursday
	 * last Monday
	 *
	 * @see strtotime()
	 *
	 * TypoScript path like some.setup.value is possible
	 *
	 * @param string $strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param mixed $strDefault Default value will be returned if value is empty
	 * @return integer
	 */
	public function GetTimeStamp ($strConfigPath, $strDefault = null)
	{
		if ($timestamp = $this->GetValue($strConfigPath, $strDefault)) {

			if ((string)intval($timestamp) === (string)$timestamp) {

				$timestamp = intval($timestamp);
			} else {
				$timestamp = strtotime($timestamp);
			}
		}

		return $timestamp;
	}


	/**
	 * Returns a color object from a string
	 *
	 * CSS color notation is allowed
	 *
	 * lime               - predefined color name
	 * rgb(0,255,0)       - RGB range 0-255
	 *
	 * #f00               - #rgb
	 * #ff0000            - #rrggbb
	 * rgb(255,0,0)
	 * rgb(100%, 0%, 0%)
	 * rgba(0,0,255,0.5)       - semi-transparent solid blue
	 * rgba(255, 50%, 0%, 0.1) - very transparent solid orange
	 *
	 * hsl(  0, 100%, 50%)       - red
	 * hsl(120, 100%, 50%)       - green
	 * hsla(120, 100%, 50%, 1)   - the same, with explicit opacity of 1
	 * hsla(240, 100%, 50%, 0.5) - semi-transparent solid blue
	 * hsla(30, 100%, 50%, 0.1)  - very transparent solid orange
	 *
	 * TypoScript path like some.setup.value is possible
	 *
	 * @param string $strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param mixed $strDefault Default is 'none' which generates a color object of color 'none'
	 * @return \Cmp3\Graphics\Color
	 * @see \Cmp3\Graphics\Color
	 */
	public function GetColor ($strConfigPath, $strDefault = 'none')
	{
		$strColor = $this->GetValue($strConfigPath, $strDefault);

		if (!$strColor) {
			// we might return null or false here
			return $strColor;
		}

		$objcolor = \Cmp3\Graphics\Color::Create($strColor);

		return $objcolor;
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
	public function isEnabled($strConfigPath, $strDefault = null)
	{
		if ($value = $this->GetValue($strConfigPath, $strDefault)) {
			$value = ((string)($value)) == '0' ? false : true;
		}

		return (boolean)$value;
	}


	/**
	 * Returns all configuration data as (multidimensional) array
	 *
	 * @return array
	 */
	public function GetAll ()
	{
		return null;
	}

	/**
	 * Returns a Config proxy starting from the given path
	 *
	 * @param string $strConfigPath
	 * @return \Cmp3\Config\ConfigInterface
	 */
	public function GetProxy($strConfigPath)
	{
		return new \Cmp3\Config\Proxy($this, $strConfigPath);
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
	 * @param	mixed 	$value Value to be set. Can be an array but must be in right format
	 * @return void
	 */
	public function SetValue ($strConfigPath, $value)
	{
		return null;
	}



}


/**
 *
 * @todo finish description
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Config
 * @package    CMP3
 */
abstract class ConfigResource extends \Cmp3\Config\ConfigAbstract {

}






