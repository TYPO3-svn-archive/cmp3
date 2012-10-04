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
 * @copyright  Copyright (c) 2008 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



/**
 * Provide some public functions
 * - used by the framework
 * - or might be useful anyway
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Base
 * @package    CMP3
 */


if (!function_exists('array_get_value')) {

	/**
	 * Returns a value from an array by a given key.
	 * If the key doesn't exist $returnOnMissingKey will be returned
	 * This function avoids missing index notices of PHP.
	 *
	 * @param string|integer $needle
	 * @param array $haystack
	 * @param mixed $returnOnMissingKey The value to return if the key doesn't exists in the array
	 * @return mixed|$returnOnMissingKey
	 * @author Rene Fritz (r.fritz@bitmotion.de)
	 */
	function array_get_value($needle, array $haystack, $returnOnMissingKey=null)
	{
		if (!is_array($haystack)) debug($haystack);  // @todo: debug remove

		if (array_key_exists($needle, $haystack))
			return $haystack[$needle];
		return $returnOnMissingKey;
	}
}


if (!function_exists('array_merge_recursive_overrule')) {

	/**
	 * Merges two arrays recursively and "binary safe" (integer keys are overridden as well), overruling similar values in the first array ($arr0) with the values of the second array ($arr1)
	 * In case of identical keys, ie. keeping the values of the second.
	 *
	 * @param	array		First array
	 * @param	array		Second array, overruling the first array
	 * @param	boolean		If set, keys that are NOT found in $arr0 (first array) will not be set. Thus only existing value can/will be overruled from second array.
	 * @param	boolean		If set, values from $arr1 will overrule if they are empty or zero. Default: true
	 * @return	array		Resulting array where $arr1 values has overruled $arr0 values
	 */
	function array_merge_recursive_overrule(array $arr0,array $arr1, $notAddKeys=0, $includeEmtpyValues=true)
	{
		foreach ($arr1 as $key => $val) {
			if(is_array($arr0[$key])) {
				if (is_array($arr1[$key]))	{
					$arr0[$key] = array_merge_recursive_overrule($arr0[$key],$arr1[$key],$notAddKeys,$includeEmtpyValues);
				}
			} else {
				if ($notAddKeys) {
					if (isset($arr0[$key])) {
						if ($includeEmtpyValues || $val) {
							$arr0[$key] = $val;
						}
					}
				} else {
					if ($includeEmtpyValues || $val) {
						$arr0[$key] = $val;
					}
				}
			}
		}
		reset($arr0);
		return $arr0;
	}
}



if (!function_exists('trim_explode')) {

	/**
	 * Explodes a string and trims all values for whitespace in the ends.
	 * All blank ('') values are removed.
	 *
	 * @param	string		Delimiter string to explode with
	 * @param	string		The string to explode
	 * @return	array		Exploded values
	 */
	function trim_explode($delim, $string)
	{
		$array = explode($delim, $string);

		$new_array = array();
		foreach($array as $value) {
			$value = trim($value);
			if ($value != '') {
				$new_array[] = $value;
			}
		}
		return $new_array;
	}
}


if (!function_exists('reverse_explode')) {

	/**
	 * Reverse explode which explodes the string counting from behind.
	 * Thus reverse_explode(':','my:words:here',2) will return array('my:words','here')
	 *
	 * @param	string		Delimiter string to explode with
	 * @param	string		The string to explode
	 * @param	integer		Number of array entries
	 * @return	array		Exploded values
	 */
	function reverse_explode($delim, $string, $count=0)	{
		$temp = explode($delim,strrev($string),$count);
		foreach ($temp as &$val) {
			$val = strrev($val);
		}
		$temp = array_reverse($temp);
		reset($temp);
		return $temp;
	}
}


if (!function_exists('int_explode')) {

	/**
	 * Explodes a $string delimited by $delim and passes each item in the array through intval().
	 * Corresponds to explode(), but with conversion to integers for all values.
	 * Empty string will be ignored.
	 *
	 * @param	string		Delimiter string to explode with
	 * @param	string		The string to explode
	 * @return	array		Exploded values, all converted to integers
	 */
	function int_explode($delim, $string)
	{
		if ($string === '' OR $string === NULL) {
			return array();
		}
		$temp = explode($delim,$string);
		foreach ($temp as $key => &$val) {
			if ($string === '' OR $string === NULL) {
				unset($temp[$key]);
			} else {
				$val = intval($val);
			}
		}
		array_unique($temp);
		reset($temp);
		return $temp;
	}
}


if (!function_exists('clean_int_array')) {

	/**
	 * Will force all entries in the input list to integers
	 * Useful when you want to make sure an array of supposed integers really contain only integers; You want to know that when you don't trust content that could go into an SQL statement.
	 *
	 * @param	string|array		Array or list of comma-separated values which should be integers
	 * @return	array		The input list but with every value passed through intval()
	 */
	function clean_int_array($list)
	{
		if (is_array($list)) {
			foreach ($list as &$val) {
				$val = intval($val);
			}
			array_unique($list);
			reset($list);
			return $list;
		}
		return int_explode(',',$list);
	}
}


if (!function_exists('clean_int_list')) {

	/**
	 * Will force all entries in the input list to integers
	 * Useful when you want to make sure a commalist of supposed integers really contain only integers; You want to know that when you don't trust content that could go into an SQL statement.
	 *
	 * @param	string|array		Array or list of comma-separated values which should be integers
	 * @return	string		The input list but with every value passed through intval()
	 */
	function clean_int_list($list)
	{
		return implode(',',clean_int_array($list));
	}
}



if (!function_exists('in_list')) {

	/**
	 * Check for item in list
	 * Check if an item exists in a comma-separated list of items.
	 *
	 * @param	string	$item	item to check for
	 * @param	string|array	$list	comma-separated list of items (string)
	 * @return	boolean		true if $item is in $list
	 */
	function in_list($item, $list)	{
		return in_array($item, (is_array($list) ? $list : trim_explode(',', $list)));
	}
}



if (!function_exists('str_begins')) {

	/**
	 * Returns true if the first part of $str matches the string $partStr
	 *
	 * @param	string		Full string to check
	 * @param	string		Reference string which must be found as the "first part" of the full string
	 * @return	boolean		True if $partStr was found to be equal to the first part of $str
	 */
	function str_begins($str,$partStr)
	{
		// Returns true, if the first part of a $str equals $partStr and $partStr is not ''
		$psLen = strlen($partStr);
		if ($psLen)	{
			return substr($str,0,$psLen)==(string)$partStr;
		} else return false;
	}

}


if (!function_exists('path_part')) {

	/**
	 * Returns the directory part of a path with a trailing slash
	 * If there is no dir-part, then an empty string is returned.
	 *
	 * Behaviour:
	 *
	 * '/dir1/dir2/script.php' => '/dir1/dir2'
	 * '/dir1/' => '/dir1'
	 * 'dir1/script.php' => 'dir1'
	 * 'd/script.php' => 'd'
	 * '/script.php' => ''
	 * '' => ''
	 *
	 * @param	string	$path	Directory name / path
	 * @return	string
	 */
	function path_part($path)
	{
		preg_match('#^(.*/)?([^/]*)$#', $path, $matches);
		return $matches[1];
	}

}


if (!function_exists('clean_realpath')) {

	/**
	 * If the path is a directory, it is always returned with a trailing slash.
	 * Convert all backslashes into forward slashes in the end.
	 *
	 * @param	string	$path	Directory name / path
	 * @return	string
	 */
	function clean_realpath($path, $blnAppendSlash = true)
	{
		$strRealPath = realpath($path);
		// do we need to check if realpath returned false?

		if (is_dir($strRealPath) !== false) {
			// Remove trailing slash (if there) and append DIRECTORY_SEPARATOR if requested
			$strRealPath = preg_replace('#[\\\/]+$#', '', $strRealPath) . ($blnAppendSlash ? DIRECTORY_SEPARATOR : '');
		}

		return str_replace('\\', '/', $strRealPath);
	}

}



if (!function_exists('_highlight_control_chars')) {

	function _highlight_control_chars($strText)
	{
		if (!is_string($strText)) return null;
		$strTextConverted = '';
		for ($a=0; $a<strlen($strText); $a++) {
			$ordChar = ord($strText[$a]);
			if($ordChar < 32)  {

				if ($ordChar == 10) {
					$strTextConverted .= '\\n';
				} elseif ($ordChar == 13) {
					$strTextConverted .= '\\r';
				} elseif ($ordChar == 9) {
					$strTextConverted .= '\\t';
				} else {
					$strTextConverted .= '\\'.$ordChar;
				}
			} else {
				$strTextConverted .= $strText[$a];
			}
		}
		return $strTextConverted;
	}

}



