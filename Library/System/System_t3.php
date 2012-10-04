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
 * @subpackage System
 * @package    CMP3
 * @copyright  Copyright (c) 2009 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\System;


/**
 * This gives access to the system.
 *
 *
 * @author Rene Fritz <r.fritz@bitmotion.de>
 */
class System_t3 extends System_Abstract {




	/**
	 *
	 * @param $objConfig \Cmp3\Config\ConfigInterface
	 */
	public function __construct($objConfig)
	{
		parent::__construct($objConfig);

		$this->SetEncryptionKey($GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
	}


	/**
	 * Override method to perform a property "Get"
	 * This will get the property $strName
	 *
	 * @param string $strName Name of the property to get
	 * @return mixed
	 */
	public function __get($strName)
	{
		switch ($strName) {
			case 'Type':
				return SystemType::TYPO3_BE;
				break;

			case 'EncodingType':
				if ($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'])	{	// First priority: forceCharset! If set, this will be authoritative!
					$EncodingType = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'];
				} else {
					$EncodingType = $GLOBALS['LANG']->charSet;	// If "LANG" is around, that will hold the current charset
				}
				return $EncodingType;

			default:
				return parent::__get($strName);
				break;
		}
	}




	/***************************
	 *
	 * Encryption Methods
	 *
	 **************************/


	/**
	 * Returns a string of highly randomized bytes (over the full 8-bit range).
	 *
	 * @param		integer  Number of characters (bytes) to return
	 * @return		string   Random Bytes
	 */
	public function GenerateRandomBytes($count)
	{
		if (!is_callable(array('t3lib_div','generateRandomBytes'))) {
			return parent::GenerateRandomBytes($count);
		}

		return \tx_cmp3::generateRandomBytes($count);
	}



	/***************************
	 *
	 * Database Methods
	 *
	 **************************/


	/**
	 * Returns an QDatabaseBase object
	 *
	 * @return QDatabaseBase
	 */
	public function GetDatabase ()
	{
		static $objDB;

		if ($objDB)
			return $objDB;

		$objDB = new QTypo3MySqlDatabase(1, array());
		if (txApplications::GetCurrent()->Profiling)
			$objDB->EnableProfiling();

		return $objDB;
	}



	/***************************
	 *
	 * Paths Methods
	 *
	 **************************/


	/**
	 * Returns an absolute path from a path which can be relative or with an location prefix like EXT:
	 *
	 * @param string $filename
	 * @return string resolved filepath
	 * @see Files
	 */
	public static function ResolvePath ($filename, $checkExistence = true)
	{
		$abolutePathFilename = '';

		try {

			$abolutePathFilename = \tx_cmp3::ResolvePath($filename);

		} catch (Exception $e) {

			if (preg_match('#^[a-z_]+$#', $filename)) {
				// we assume this must be an extension key
				$abolutePathFilename = t3lib_extmgm::extPath($filename);
				\tx_cmp3::RegisterPath($filename, $abolutePathFilename);

			} else {
				if (count(explode(':', $filename))>2)	{
					list($filename,$funcRef) = reverse_explode(':',$filename,2);
				}
				$abolutePathFilename = \t3lib_div::getFileAbsFileName($filename);
				//TODO: call \tx_cmp3::RegisterPath() also here if a path is found?
			}

		}

		return ($checkExistence === false OR @file_exists($abolutePathFilename)) ? $abolutePathFilename : false; /*@*/
	}


	/**
	 * Abstraction method which returns System Environment Variables regardless of server OS, CGI/MODULE version etc. Basically this is SERVER variables for most of them.
	 * This should be used instead of getEnv() and $_SERVER/ENV_VARS to get reliable values for all situations.
	 *
	 * @param	string		Name of the "environment variable"/"server variable" you wish to use. Valid values are SCRIPT_NAME, SCRIPT_FILENAME, REQUEST_URI, PATH_INFO, REMOTE_ADDR, REMOTE_HOST, HTTP_REFERER, HTTP_HOST, HTTP_USER_AGENT, HTTP_ACCEPT_LANGUAGE, QUERY_STRING, DOCUMENT_ROOT, HOST_ONLY, HOST_ONLY, REQUEST_HOST, REQUEST_URL, REQUEST_SCRIPT, REQUEST_DIR, SITE_URL, _ARRAY
	 * @return	string		Value based on the input key, independent of server/os environment.
	 */
	public static function GetEnv($getEnvName)	{

		$retVal = '';

		switch ((string)$getEnvName)	{
			case 'REV_PROXY':
			case 'DOCUMENT_ROOT':
			case 'HOST_ONLY':
			case 'PORT':
			case 'REQUEST_HOST':
			case 'REQUEST_URL':
			case 'REQUEST_SCRIPT':
			case 'REQUEST_DIR':
			case 'SITE_URL':
			case 'SITE_PATH':
			case 'SITE_SCRIPT':
			case 'SSL':
				$retVal = \tx_cmp3::$System->GetEnv('TYPO3_'.$getEnvName);
			break;
			case '_ARRAY':
				$out = array();
					// Here, list ALL possible keys to this function for debug display.
				$envTestVars = trim_explode(',','
					HTTP_HOST,
					TYPO3_HOST_ONLY,
					TYPO3_PORT,
					PATH_INFO,
					QUERY_STRING,
					REQUEST_URI,
					HTTP_REFERER,
					TYPO3_REQUEST_HOST,
					TYPO3_REQUEST_URL,
					TYPO3_REQUEST_SCRIPT,
					TYPO3_REQUEST_DIR,
					TYPO3_SITE_URL,
					TYPO3_SITE_SCRIPT,
					TYPO3_SSL,
					TYPO3_REV_PROXY,
					SCRIPT_NAME,
					TYPO3_DOCUMENT_ROOT,
					SCRIPT_FILENAME,
					REMOTE_ADDR,
					REMOTE_HOST,
					HTTP_USER_AGENT,
					HTTP_ACCEPT_LANGUAGE');
				foreach ($envTestVars as $v) {
					$out[$v]=t3lib_div::getIndpEnv($v);
				}
				reset($out);
				$retVal = $out;
			break;
			default:
				$retVal = \t3lib_div::getIndpEnv($getEnvName);
		}
		return $retVal;
	}
}

