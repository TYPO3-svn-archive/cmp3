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

namespace Cmp3;




/**
 * Handle class autoloading and include paths
 *
 * This class allows to register folders and files which will be used to search for classes to do autoloading.
 * Additionally a folder can be added to the php include path.
 *
 * STATUS: final
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Base
 * @package    CMP3
 * @see http://php.net/manual/en/function.spl-autoload-register.php
 * @see http://php.net/manual/en/function.set-include-path.php
 */
abstract class Autoloader {


	/**
	 * Class File Array - used by self::autoLoad to more quickly load
	 * core class objects without making a file_exists call.
	 *
	 * @var array ClassFile
	 */
	protected static $classFile = array();


	/**
	 * Class Folder Search Array - used by self::autoLoad to to search for class files
	 *
	 * @var array ClassFolder
	 */
	protected static $classFolder = array();


	/**
	 * Path to check file path against
	 * If a file is outside this path an exception will be thrown on autoloading
	 *
	 * @var string
	 */
	protected static $basePath;



	/**
	 * Stores the base path of the application in a static variable
	 *
	 * @param string $basePath (optional)
	 * @return void
	 */
	public static function SetBasePath($basePath = null)
	{
		self::$basePath = $basePath ? $basePath : \tx_cmp3::ResolvePath('PATH_site');
	}



	/**
	 * Register {@link Autoload()} with spl_autoload()
	 *
	 * @param boolean $enabled (optional)
	 * @return void
	 * @throws Exception if spl_autoload() is not found
	 * @see http://php.net/manual/en/function.spl-autoload-register.php
	 */
	public static function RegisterAutoload($blnEnabled = true)
	{
		if (!function_exists('spl_autoload_register')) {
			throw new Exception('spl_autoload does not exist in this PHP installation');
		}

		if ($blnEnabled === true) {
			spl_autoload_register(array('\Cmp3\Autoloader', 'Autoload'));
		} else {
			spl_autoload_unregister(array('\Cmp3\Autoloader', 'Autoload'));
		}
	}


	/**
	 * This is called by the PHP5 Autoloader.
	 *
	 * This is called just too much because class_exists() call the autloader unless the secend parameter is false, which is not the case in the T3 source code.
	 *
	 * @param string $strClassName Class name
	 * @return boolean whether or not a class was found / included
	 */
	public static function Autoload($strClassName)
	{
		if (substr($strClassName, 0 ,3)==='ux_') return false;
		if (substr($strClassName, 0 ,5)==='user_') return false;
		if (substr($strClassName, 0 ,6)==='t3lib_') return false;
		if (substr($strClassName, 0 ,6)==='tslib_') return false;

		// remove backslash from the beginning to find classes with namespace
		if ($strClassName{0} == '\\') {
			$strClassName = substr($strClassName, 1);
		}

		if (array_key_exists(strtolower($strClassName), self::$classFile)) {
			if (false === self::$classFile[strtolower($strClassName)]) {

				trigger_error('No valid path was registered for class \''.$strClassName.'\'.', E_USER_WARNING);
				return false;
			}

			require(self::$classFile[strtolower($strClassName)]);
			return true;
		}


		if ($strClassName !== 'Zend_Loader' AND substr($strClassName, 0 ,5)==='Zend_') {
			if (!class_exists('Zend_Loader', false)) {
				self::Autoload('Zend_Loader');
			}

			try {
				Zend_Loader::loadClass($strClassName);
				return true;
			} catch (Exception $e) {
				return false;
			}
		}


		$strClassName = preg_replace('#Zend_#', '', $strClassName);

		foreach (self::$classFolder as $folder) {
			$strFilePath = sprintf('%s/%s.php', $folder, $strClassName);
			if (file_exists($strFilePath)) {
				require($strFilePath);
				return true;
			}
		}


		return false;
	}


	/**
	 * Returns a class file for a class name
	 *
	 * @param string $strClassName Class name
	 */
	public static function GetFile ($strClassName)
	{
		if (array_key_exists(strtolower($strClassName), self::$classFile)) {
			return (self::$classFile[strtolower($strClassName)]);
		}
		return false;
	}


	/**
	 * Register a class file to autoload
	 *
	 * @param string $strClassName Class name
	 * @param string $strPath Path to the class file
	 */
	public static function RegisterFile ($strClassName, $strPath)
	{
		$strPath = \tx_cmp3::ResolvePath($strPath);

		if ((!str_begins($strPath, \tx_cmp3::ResolvePath('PATH_site')) AND (self::$basePath AND (!str_begins($strPath, self::$basePath))))) {
			//FIXME: What to do in this case? This happens if the NEXT extension is linked from somewhere else
			//error_log('Registered Path "' .$strPath. '" is not in Base Path ' . self::$basePath);
		}
		self::$classFile[strtolower($strClassName)] = $strPath;
	}


	/**
	 * Register path(s) to search for class files
	 *
	 * @param string $strPath Path to search for class files
	 * @param boolean $blnRecursive If set subpath will be registered too
	 * @todo check against PATH_site
	 */
	public static function RegisterPath ($strPath, $blnRecursive=false)
	{
		self::$classFolder[] = $strPath;
		if ($blnRecursive) {
			$folderArray = self::_get_dirs($strPath);
			self::$classFolder= array_unique(array_merge(self::$classFolder, $folderArray));
		}
	}





	/***************************
	 *
	 * Modify PHPs include path
	 *
	 ***************************/


	/**
	 * Add one or more path to the PHP include path
	 *
	 * @param string $strPath Path to add to include paths
	 * @return void
	 * @see http://php.net/manual/en/function.set-include-path.php
	 */
	public static function AddIncludePath ($strPath)
	{
		foreach (func_get_args() AS $strPath)		{
			if (!file_exists($strPath) OR (file_exists($strPath) && filetype($strPath) !== 'dir'))			{
				trigger_error("Include path '{$strPath}' not exists", E_USER_WARNING);
				continue;
			}

			$strPaths = explode(PATH_SEPARATOR, get_include_path());

			if (array_search($strPath, $strPaths) === false)
			array_push($strPaths, $strPath);

			set_include_path(implode(PATH_SEPARATOR, $strPaths));
		}
	}


	/**
	 * Remove one or more path from the PHP include path
	 *
	 * @param string $strPath Path to remove from include paths
	 * @return void
	 */
	public static function RemoveIncludePath ($strPath)
	{
		foreach (func_get_args() AS $strPath)		{
			$strPaths = explode(PATH_SEPARATOR, get_include_path());

			if (($k = array_search($strPath, $strPaths)) !== false)
				unset($strPaths[$k]);
			else
				continue;

			if (!count($strPaths))			{
				trigger_error("Include path '{$strPath}' can not be removed because it is the only", E_USER_NOTICE);
				continue;
			}

			set_include_path(implode(PATH_SEPARATOR, $strPaths));
		}
	}



	/***************************
	 *
	 * helper
	 *
	 ***************************/

	/**
	 * Returns an array with the names of folders in a specific path
	 *
	 * @param	string		Path to list directories from
	 * @return	array		Returns an array with the directory entries as values. If no path, the return value is nothing.
	 */
	protected static function _get_dirs($strPath, $filearray=array())
	{
		if ($strPath)	{
			$d = @dir($strPath); /*@*/
			if (is_object($d))	{
				while($entry = $d->read()) {
					if (@is_dir($strPath.'/'.$entry) && substr($entry,0,1)!= '.')	{ /*@*/
						$filearray[] = $strPath.'/'.$entry.'/';
						$filearray = self::_get_dirs($strPath.'/'.$entry.'/', $filearray);
					}
				}
				$d->close();
			}
			return $filearray;
		}
	}
}



