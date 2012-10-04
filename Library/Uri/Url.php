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
 * @subpackage Uri
 * @package    CMP3
 * @copyright  Copyright (c) 2008 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\Uri;



/**
 * Class for URL stuff
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Uri
 * @package    CMP3
 */
class Url {


	/**
	 * Returns a full url from a relative url or file path
	 *
	 * @return string
	 */
	public static function MakeAbsoluteUrl($strUrl)
	{
		// @todo respect $GLOBALS['TSFE']->absRefPrefix;

		$urlParts = parse_url($strUrl);
		if (!($urlParts['scheme'])) {
			$strUrl = self::ConcatUrl(\tx_cmp3::$System->GetEnv('SITE_URL'), $strUrl);
		}

		return $strUrl;
	}


	/**
	 * Merge base path with relative path
	 * Will remove duplicate /
	 *
	 * @param string $strString
	 * @param string $strString2
	 * @return string
	 */
	public static function ConcatUrl($strString, $strString2)
	{
		return \Cmp3\String\String::StripSlash($strString) . '/' . \Cmp3\String\String::StripLeadingSlash($strString2);
	}


	/**
	 * Append parameter in the form key1=value&other=value2
	 * to an existing url like http://www.vnoel.com?cmd=list#here
	 *
	 * An anchor will be preserved.
	 *
	 * @param string $strUrl Existing url like http://www.vnoel.com?cmd=list#here
	 * @param string $strParameter
	 * @return string
	 */
	public static function AppendParameter($strUrl, $strParameter)
	{
		// remove leading &
		$strParameter = \Cmp3\String\String::StripPrefix($strParameter, '&');

		// Pick the correct separator to use
		$separator = "?";
		if (strpos($strUrl, "?") !== false)
			$separator = "&";

		// Find the location for the new parameter
		$insertPosition = strlen($strUrl);
		if (strpos($strUrl, "#") !== false)
			$insertPosition = strpos($strUrl, "#");

		// Build the new url
		return substr_replace($strUrl, "$separator$strParameter", $insertPosition, 0);
	}



	/***************************
	 *
	 * Tools and helpers
	 *
	 **************************/

	/**
	 * Implodes a multidimensional-array into GET-parameters (eg. &param[key][key2]=value2&param[key][key3]=value3)
	 *
	 * This can be used statically. (But can't be declared static because of $this->_strPrefix :-/ )
	 *
	 * @param	array		The (multidim) array to implode
	 * @param	string|false|null	Name prefix for entries. Set to false if you wish none.
	 * @param	boolean		If set, parameters which were blank strings would be removed.
	 * @param	boolean		If set, the param name itself (for example "param[key][key2]") would be rawurlencoded as well.
	 * @return	string		Imploded result, fx. &param[key][key2]=value2&param[key][key3]=value3
	 * @todo replace with internal PHP function http_build_query?
	 */
	public function ImplodeParameterArray($theArray, $name=null, $skipBlank=true, $rawurlencodeParamName=false)
	{
		$str = '';
		if (is_array($theArray)) {
			foreach ($theArray as $Akey => $AVal) {
				$thisKeyName = $name ? $name . '[' . $Akey . ']' : $Akey;
				if (is_array($AVal)) {
					$str .= self::ImplodeParameterArray($AVal, $thisKeyName, $skipBlank, $rawurlencodeParamName);
				} else {
					if (!$skipBlank || strcmp($AVal, '')) {
						$str.='&' . ($rawurlencodeParamName ? rawurlencode($thisKeyName) : $thisKeyName) .
								'=' . rawurlencode($AVal);
					}
				}
			}
		}
		return $str;
	}


	/**
	 * Explodes GET-parameters (eg. &param[key][key2]=value2&param[key][key3]=value3) into a one or multidimensional array
	 *
	 * Supported url/parameter formats:
	 * &param...
	 * ?param...
	 * something?param...
	 * /something
	 * http://something
	 *
	 * @param	string		Url or url parameter part
	 * @param   boolean		If set (default) the parameter will be exploded into a multidimensional array otherwise the parameter array is flat (one dimension)
	 * @return	array		Exploded parameter as array. One level deep.
	 */
	public static function ExplodeParameter($strParameter, $blnMultidimensional = true)
	{
		if (!$strParameter) {
			return array();
		}

		if (strpos($strParameter, '?') !== false OR strpos($strParameter, '/') !== false) {
			$strParameter = parse_url($strParameter, PHP_URL_QUERY);
		}

		$arr = array();

		if ($blnMultidimensional) {
			$arr = array();
			parse_str($strParameter, $arr);
		} else {
			$strParameter = html_entity_decode($strParameter);
			$var = explode('&', $strParameter);

			foreach ($var as $val) {
				$x = explode('=', $val);
				$arr[$x[0]] = $x[1];
			}
		}

		return $arr;
	}


	/**
	 * Prefixes a domain for example with http://
	 * If ':/' is found in the string nothing is prefixed
	 *
	 * @param string $strUrl
	 * @return string
	 */
	public static function PrefixHttp($strUrl)
	{
		if (strpos($strUrl, ':/') === false) {
			return 'http://' . $strUrl;
		}
		return $strUrl;
	}


	/**
	 * Returns a hostname from an url. This is useful for link names which should be shortened
	 *
	 * @param string $strUrl
	 * @return string
	 */
	public static function Shorten($strUrl)
	{
		$strUrl = self::PrefixHttp($strUrl);
		return parse_url($strUrl, PHP_URL_HOST);
	}


}

