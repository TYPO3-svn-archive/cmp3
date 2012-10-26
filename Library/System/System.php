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



#TODO rename tx_cmp3

/**
 * This static class gives global access to the current system.
 *
 * That means all special system access (TYPO3, FLOW3, Standalone, ...) should use this class when possible.
 * The idea is to have CMP3 code as clean as possible.
 *
 * @author Rene Fritz <r.fritz@bitmotion.de>
 */
abstract class tx_cmp3 {



	/**
	 * This is the current system object
	 *
	 * @var \Cmp3\System\System_Abstract
	 */
	public static $System = null;


	/**
	 * This is the current database object based on QDatabaseBase
	 *
	 * @var QDatabaseBase
	 */
	public static $Database = null;


	/**
	 * Stores information to resolve path
	 *
	 * @var array
	 */
	protected static $_paths = array();


	/**
	 * Initialization
	 *
	 * @param \Cmp3\Config\ConfigInterface|\Cmp3\System\System_Abstract|NULL
	 * @return void
	 */
	public static function Init ($object = null)
	{
		if ($object instanceof \Cmp3\System\System_Abstract) {
			self::$System = $object;
			return;
		}

		$objConfig = null;
		if ($object instanceof \Cmp3\Config\ConfigInterface) {
			// we don't want to kill the system :-)
			// throw new Exception ('system configuration is not instance of \Cmp3\Config\ConfigInterface');
			$objConfig = $object;
		}

		if (TYPO3_MODE == 'FE') {
			if (!$objConfig)
				$objConfig = new \Cmp3\Config\TSFESetup();
			self::$System = new \Cmp3\System\System_t3fe($objConfig);

		} elseif (TYPO3_MODE == 'BE') {
			#@todo use \Cmp3\Config\TSConfig
			if (!$objConfig)
				$objConfig = new \Cmp3\Config\TypoScriptArray(array());
			self::$System = new \Cmp3\System\System_t3be($objConfig);

		} else {
			if (!$objConfig)
				$objConfig = new \Cmp3\Config\TypoScriptArray(array());
			self::$System = new \Cmp3\System\System_standalone($objConfig);
		}
	}


	/**
	 * Returns true if the current environment is TYPO3
	 * @return boolean
	 */
	public static function isTypo3()
	{
		if (defined('TYPO3_MODE') AND TYPO3_MODE)
			return true;
		return false;
	}


	/**
	 * Returns true if the current environment is TYPO3 frontend
	 *
	 * This will return the right value even if TYPO3_MODE == 'BE' which might be the case in unit tests.
	 * self::$System needs to be instanceof \Cmp3\System\System_t3fe
	 *
	 * @return boolean
	 */
	public static function isTypo3Frontend()
	{
		if (is_object(self::$System)) {
			return (self::$System->Type == \Cmp3\System\SystemType::TYPO3_FE);
		}
		return (TYPO3_MODE == 'FE');
	}


	/**
	 * Returns true if the current environment is TYPO3 backend
	 * @return boolean
	 */
	public static function isTypo3Backend()
	{
		if (is_object(self::$System)) {
			return (self::$System->Type == \Cmp3\System\SystemType::TYPO3_BE);
		}
		return (TYPO3_MODE == 'BE');
	}


	/**
	 * Returns true if the machine is running Windows
	 * @return boolean
	 */
	public static function isWindows()
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			return true;
		}
		return false;
	}


	/**
	 * Checks if the $path is absolute or relative (detecting either '/' or 'x:/' as first part of string) and returns true if so.
	 *
	 * @param	string		Filepath to evaluate
	 * @return	boolean
	 */
	public static function isAbsolutePath($path)
	{
		return ( substr($path,0,1)=='/' ? true : ( stristr(PHP_OS,"win")&&!stristr(PHP_OS,"darwin") ? substr($path,1,2)==':/' :  false ) );
	}


	/**
	 * Register a system path
	 *
	 * @param $key can be any shortcut which is an TYPO3 extension key for example
	 * @param $strAbsolutePath
	 * @return void
	 */
	public static function RegisterPath($key, $strAbsolutePath)
	{
		self::$_paths[$key] = $strAbsolutePath;
	}


	/**
	 * Returns an absolute path for a registred key.
	 * Also relative paths with an location prefix like EXT: will be resolved.
	 *
	 * @param string $key
	 * @return string resolved filepath
	 * @see \Cmp3\System\Files
	 * @throws Exception
	 */
	public static function ResolvePath($key)
	{
		// path exists in registry array
		if (array_key_exists($key, self::$_paths))
			return self::$_paths[$key];

		// path is absolute - nothing to do (we rely on paths having forward slashes only)
		if ((\tx_cmp3::isWindows() ? substr($key,1,2)==':/' :  substr($key,0,1)=='/'))
			return $key;

		$filepath = $key;
		$key = '';
		$parts = explode(':', $filepath);

		if (count($parts) > 1 AND $parts[0] == 'EXT') {
			list ($key, $filepath) = explode ('/', $parts[1], 2);

				// if path is not registerd yet, resolve it with TYPO3 API functions
			if (!isset(self::$_paths[$key]) AND self::isTypo3() AND t3lib_extMgm::isLoaded($key)) {
					self::$_paths[$key] = clean_realpath(t3lib_extMgm::extPath($key));
			}

		} else {

			$parts = explode ('/', $filepath, 2);
			if (count($parts) > 1) {
				$key = $parts[0];
				$filepath = $parts[1];

			} else {
				$key = $filepath;
			}
		}

		if (!isset(self::$_paths[$key])) {
				// FIXME: this will break the TYPO3 installation, if something goes wrong, because
				// ResolvePath is also called in ext_localconf.php and/or ext_tables.php
				// But we need the Exception here, because the System Object's ResolvePath relies on it
				throw new \Cmp3\System\Exception ('No path registered for key: '.$key);
		}
		return self::$_paths[$key].$filepath;
	}
}

\tx_cmp3::RegisterPath('PATH_site', (defined('PATH_site') ? clean_realpath(PATH_site) : clean_realpath($_SERVER['DOCUMENT_ROOT'])));

