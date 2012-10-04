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
 * @copyright  Copyright (c) 2008 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\System;

/**
 * include constants and media types, ...
 */
#TODO require_once(PATH_txnext.'library/files/\Next\file_types.php');



/**
 * DAM API functions
 *
 * This is the official API to access DAM functions.
 * If possible use these functions or tx_dam_media.
 *
 * Basically these public static function are a replacement of t3lib_basicfilefunc and provide typical file operations like delete, copy and rename. In contrast to t3lib_basicfilefunc these functions update the meta data index when needed.
 *
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package    CMP3
 * @subpackage Files
 */
abstract class Files {




	/**
	 * Like basename() this returns the filename without path.
	 * This works NOT with a path to get a folder name.
	 * (needed because pathinfo is broken on some php5.x versions using utf-8 chars)
	 *
	 * @param	string		$filename The file name with path
	 * @return	string		A file name
	 */
	public static function Basename($filename)
	{
		$path_parts = self::_split_fileref($filename);
		return $path_parts['file'];
	}


	/**
	 * Like dirname() this returns the path but the path has a trailing slash.
	 * This works NOT with a path without trailing slash.
	 * (needed because pathinfo is broken on some php5.x versions using utf-8 chars)
	 *
	 * @param	string		$filename The file name with path
	 * @return	string		A file path with trailing slash
	 */
	public static function Dirname($filename)
	{
		$path_parts = self::_split_fileref($filename);
		return $path_parts['path'];
	}


	/**
	 * Checks if the file is already indexed.
	 * Returns the UID of the meta data record if the file is indexed already or false if the file is not indexed yet.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::compileInfo().
	 * @return	mixed		UID of the meta data record or false.
	 * @see tx_dam::compileInfo()
	 * @todo unit test for indexed files
	 */
	public static function isIndexed($fileInfo)
	{
		if(class_exists('tx_dam', false)) {
			return file_isIndexed($fileInfo);
		}

		return false;
	}



	/**
	 * Returns an absolue path from a path which can be relative or with an location prefix like EXT:
	 * If it is unclear where and how an path was defined it is a good idea to apply this function first on a path.
	 *
	 * @param string $filename
	 * @param boolean $checkExistence if set (default) it will be checked if the file exists
	 * @return string resolved filepath
	 * @throws \Cmp3\Files\NotFoundException
	 */
	public static function ResolvePath($filename, $checkExistence=true)
	{
		if (count(explode(':', $filename))>2)	{
			list($filename,$funcRef) = reverse_explode(':',$filename,2);
		}
		if (substr($filename,0,4)=='EXT:')	{
			// we do this only on EXT resources because getFileAbsFileName() might set the filename blank in some circumstances what is not what we want
			$abolutePathFilename = \t3lib_div::getFileAbsFileName($filename);
		} else {
             $abolutePathFilename = self::MakeFilePathAbsolute($filename);
        }

		if ($checkExistence===false OR @file_exists($abolutePathFilename)) /*@*/
			return $abolutePathFilename;

		throw new \Cmp3\Files\NotFoundException('file not found: '.$abolutePathFilename);
	}


	/**
	 * Convert a file path to the format stored in the meta data which is a relative path if possible.
	 *
	 * @param	string		$filename The file name with path
	 * @return	string		Normalized path to file
	 */
	public static function NormalizePath($filename)
	{
		$path_parts = self::_split_fileref($filename);
		$file_name = $path_parts['file'];
		$file_path = self::MakePathRelative($path_parts['path']);

		return $file_path.$file_name;
	}


	/**
	 * Convert/returns a file path to a absolute path if possible.
	 * This is for files managed by the DAM only. Other files may fail.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::compileInfo().
	 * @return	string		Absolute path to file
	 */
	public static function MakeFilePathAbsolute($fileInfo)
	{
		if (is_array($fileInfo)) {
			$file_name = $fileInfo['file_name'];
			$file_path = $fileInfo['file_path_absolute'] ? $fileInfo['file_path_absolute'] : self::MakePathAbsolute ($fileInfo['file_path']);
		} elseif ($fileInfo) {
			$path_parts = self::_split_fileref($fileInfo);
			$file_name = $path_parts['file'];
			$file_path = self::MakePathAbsolute($path_parts['path']);
		}

		return $file_path.$file_name;
	}


	/**
	 * Convert a file path to a relative path to PATH_site or getIndpEnv('TYPO3_SITE_URL').
	 * This is for files managed by the DAM only. Other files may fail.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::compileInfo().
	 * @return	string		Relative path to file
	 */
	public static function MakeFilePathWebRelative($fileInfo)
	{
		if (is_array($fileInfo)) {
			$file_name = $fileInfo['file_name'];
			$file_path = $fileInfo['file_path_absolute'] ? $fileInfo['file_path_absolute'] : self::MakePathAbsolute ($fileInfo['file_path']);
		} else {
			$path_parts = self::_split_fileref($fileInfo);
			$file_name = $path_parts['file'];
			$file_path = self::MakePathAbsolute($path_parts['path']);
		}

			// for now MakePathRelative() do what we want but that may change
		$file_path = self::MakePathRelative ($file_path, \tx_cmp3::ResolvePath('PATH_site'));

		return $file_path.$file_name;
	}


	/**
	 * Returns a string where any invalid character of a filename is substituted by '_'.
	 * By the way this can be used to clean folder names as well.
	 * This public static function don't do any charset conversion for good reasons. Most file systems don't have charset support. TYPO3 may use a different charset than the system locale setting. So the safest ist to set $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'], which is then the charset for filenames automatically.
	 *
	 * @param	string		$filename filename (without path)
	 * @param	string		$crop If true the name will be shortened if needed
	 * @return	string		Output string with any invalid characters is substituted by '_'
	 */
	public static function MakeFileNameClean($filename, $crop=false)
	{
		$filename = trim($filename);

		if (\tx_cmp3::isWindows()) {
			$filename = str_replace('[', '(', $filename);
			$filename = str_replace(']', ')', $filename);
			$filename = str_replace('+', '_', $filename);
		}

			// chars like ?"*:<> are allowed on some filesystems but will be removed to secure shell commands
		$filename = preg_replace('#[/|\\?"*:<>]#', '_', trim($filename));
		if ($filename === '.' OR $filename === '..') {
			$filename .= '_';
		}

			// handle UTF-8 characters
		if ($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] == 'utf-8' && $GLOBALS['TYPO3_CONF_VARS']['SYS']['UTF8filesystem'])	{
				// allow ".", "-", 0-9, a-z, A-Z and everything beyond U+C0 (latin capital letter a with grave)
			$filename = preg_replace('/[\x00-\x2C\/\x3A-\x3F\x5B-\x60\x7B-\xBF]/u','_', $filename);
		}

		$maxFileNameLength = $GLOBALS['TYPO3_CONF_VARS']['SYS']['maxFileNameLength'] ? $GLOBALS['TYPO3_CONF_VARS']['SYS']['maxFileNameLength'] : 60;
		if ($crop AND strlen($filename) > $maxFileNameLength) {
			$path_parts = self::_split_fileref($filename);
			if ($extLen = strlen($path_parts['realFileext'])) {
				$extLen += 1;
				$filename = substr ($path_parts['filebody'], 0, $maxFileNameLength-$extLen).'.'.$path_parts['realFileext'];
			} else {
				$filename = substr ($path_parts['filebody'], 0, $maxFileNameLength);
			}
		}

		return $filename;
	}


	/**
	 * Creates a file with unique file name
	 *
	 * @param string $strFilePrefix The prefix of the generated temporary filename.
	 * @param string $strFileSuffix The suffix of the generated temporary filename.
	 * @param string $strPath The path where the file will be generated
	 * @return string|FALSE
	 */
	public static function MakeTempName($strFilePrefix, $strFileSuffix=null, $strPath=null)
	{
		if ($strPath===null) {
			$strPath = \tx_cmp3::ResolvePath('PATH_site').'typo3temp/';
		}

		// find a temporary name
		$tries = 1;
		do {
			// get a known, unique temporary file name
			$sysFileName = tempnam($strPath, (string)$strFilePrefix);
			if ($sysFileName === false) {
				return false;
			}

			// tack on the extension
			$newFileName = $sysFileName . $strFileSuffix;
			if ($sysFileName == $newFileName) {
				return $sysFileName;
			}

			// move or point the created temporary file to the new filename
			if (!file_exists($newFileName)) {
				if (rename($sysFileName, $newFileName)) {
					return $newFileName;
				}
			}

			unlink ($sysFileName);
			$tries++;
		} while ($tries <= 5);

		return false;
	}



	/***************************************
	 *
	 *	 Path related function
	 *
	 ***************************************/


	/**
	 * Returns the last part from a path.
	 * If the last part is a filename the filename will be returned.
	 * (needed because pathinfo is broken on some php5.x versions using utf-8 chars)
	 * Examples:
	 * example/folder/ -> folder
	 * example/folder -> folder
	 * example/folder/filename -> filename
	 *
	 * @param	string		$path
	 * @return	string		The name of the folder
	 */
	public static function PathBasename($path)
	{
		preg_match ('#([^/]+)/{0,1}$#', $path, $match);
		return $match[1];
	}


	/**
	 * Convert a path to a relative path if possible.
	 * The result is normally a relative path to PATH_site (but don't have to).
	 * It might be possible that back paths '../' will be supported in the future.
	 *
	 * @param	string		$path Path to convert
	 * @param	string		$mountpath Path which will be used as base path. Otherwise PATH_site is used.
	 * @return	string		Relative path
	 */
	public static function MakePathRelative($path, $mountpath=NULL)
	{

		$path = clean_realpath(self::MakePathAbsolute($path, $mountpath));

		$mountpath = is_null($mountpath) ? \tx_cmp3::ResolvePath('PATH_site') : self::MakePathClean($mountpath);
		$mountpath = clean_realpath($mountpath);

			// remove the site path from the beginning to make the path relative
			// all other's stay absolute
		return preg_replace('#^'.preg_quote($mountpath).'#','',$path);
	}


	/**
	 * Convert a path to an absolute path
	 *
	 * @param	string		$path Path to convert
	 * @param	string		$mountpath Path which will be used as base path. Otherwise PATH_site is used.
	 * @return	string		Absolute path
	 * @todo unit test ['dir_path_absolute'] and ['dir_path']
	 */
	public static function MakePathAbsolute($path, $mountpath=NULL)
	{
		if ($path) {
			if (is_array($path)) {
				if (isset($path['dir_name'])) {
					$path = $path['dir_path_absolute'] ? $path['dir_path_absolute'] : $path['dir_path'];
				} else {
					$path = $path['file_path_absolute'] ? $path['file_path_absolute'] : $path['file_path'];
				}
			}

			$path = self::MakePathClean ($path);

			if(self::isAbsolutePath($path)) {
				return $path;
			}
			$mountpath = is_null($mountpath) ? \tx_cmp3::ResolvePath('PATH_site') : self::MakePathClean ($mountpath);
			$path = $mountpath ? $mountpath.$path : '';
		}
		return $path;
	}


	/**
	 * Cleans a path
	 * - resolve back paths '../'
	 * - append '/' to the path if missing
	 *
	 * @param	string		$path Path to clean
	 * @return	string		Cleaned path
	 */
	public static function MakePathClean($path)
	{
		if ($path) {
			$path = str_replace('/./', '/', $path);
			$path = self::ResolveBackPath($path);
			$path = preg_replace('#[\/\. ]*$#', '', $path).'/';
			$path = str_replace('//', '/', $path);
		}
		return $path;
	}


	/**
	 * Resolves "../" sections in the input path string.
	 * For example "fileadmin/directory/../other_directory/" will be resolved to "fileadmin/other_directory/"
	 *
	 * @param	string		File path in which "/../" is resolved
	 * @return	string
	 */
	public static function ResolveBackPath($pathStr)
	{
		$parts = explode('/',$pathStr);
		$output=array();
		$c = 0;
		foreach($parts as $pV)	{
			if ($pV=='..')	{
				if ($c)	{
					array_pop($output);
					$c--;
				} else $output[]=$pV;
			} else {
				$c++;
				$output[]=$pV;
			}
		}
		return implode('/',$output);
	}


	/**
	 * Checks if the $path is absolute or relative (detecting either '/' or 'x:/' as first part of string) and returns true if so.
	 *
	 * @param	string		Filepath to evaluate
	 * @return	boolean
	 */
	public static function isAbsolutePath($path)
	{
		return \tx_cmp3::isWindows() ? substr($path,1,2)==':/' :  substr($path,0,1)=='/';
	}


	/**
	 * Collects and returns an array with info's about the given path/folder.
	 * Returns false if the path is not a folder.
	 *
	 * Example:
	 * __type => dir
	 * dir_path => /var/www/dam/fileadmin//test/
	 * dir_path_from_mount => test/
	 * dir_path_relative => fileadmin/test/
	 * dir_name => test
	 * dir_title => test
	 * dir_size => 115
	 * dir_tstamp => 1132751825
	 * dir_writable => 1
	 * dir_readable => 1
	 * dir_owner => 1000
	 * dir_perms => 16895
	 * mount_id => 875349e03c95ae6bc79dc22c0b7c2f7c
	 * mount_name => fileadmin/
	 * mount_path => /var/www/dam/fileadmin/
	 * mount_type =>
	 * web_nonweb => web
	 *
	 * @param	string		$path Path to a folder (not file)
	 * @return	array		Info array
	 * @todo localization of TEMP, RECYCLER
	 * @todo unit test - useraccesrights
	 */
	public static function CompilePathInfo($path)
	{
		global $FILEMOUNTS, $TYPO3_CONF_VARS;

			// cache entries - static would work too but couldn't be flushed then
		if (isset($GLOBALS['T3_VAR']['ext']['dam']['pathInfoCache'][$path])) {
			return $GLOBALS['T3_VAR']['ext']['dam']['pathInfoCache'][$path];
		}


		$pathInfo = false;


		$path = self::MakePathAbsolute($path);

		if (\tx_cmp3::isTypo3()) {
			require_once (PATH_t3lib.'class.t3lib_basicfilefunc.php');

			$basicFF = \t3lib_div::makeInstance('t3lib_basicFileFunctions');
			$basicFF->init($FILEMOUNTS, $TYPO3_CONF_VARS['BE']['fileExtensions']);

			$path = $basicFF->is_directory($path);
		}

		$path = $path ? $path.'/' : '';

		if($path) {

			$pathInfo = array();
			$pathInfo['__type'] = 'dir';
			$pathInfo['__exists'] = @is_dir($path); /*@*/
			$pathInfo['__protected'] = @is_file($path.'.htaccess');
			$pathInfo['__protected_type'] = $pathInfo['__protected'] ? 'htaccess' : '';
			$pathInfo['dir_ctime'] = @filectime($path);
			$pathInfo['dir_mtime'] = @filemtime($path);
			$pathInfo['dir_size'] = @filesize($path);
			$pathInfo['dir_type'] = @filetype($path);
			$pathInfo['dir_owner'] = @fileowner($path);
			$pathInfo['dir_perms'] = @fileperms($path);
				// I have no idea why these are negated in t3lib_basicfilefunc
			$pathInfo['dir_writable'] = @is_writable($path);
			$pathInfo['dir_readable'] = @is_readable($path);

				// find mount
			if (\tx_cmp3::isTypo3())
				$pathInfo['mount_id'] = $basicFF->checkPathAgainstMounts($path);
			else
				$pathInfo['mount_id'] = false;
			$pathInfo['mount_path'] =  $FILEMOUNTS[$pathInfo['mount_id']]['path'];
			$pathInfo['mount_name'] =  $FILEMOUNTS[$pathInfo['mount_id']]['name'];
			$pathInfo['mount_type'] =  $FILEMOUNTS[$pathInfo['mount_id']]['type'];
			// $pathInfo['web_nonweb'] = t3lib_BEfunc::getPathType_web_nonweb($path); // prevent using t3lib_BEfunc
			$pathInfo['web_nonweb'] = \t3lib_div::isFirstPartOfStr($path, \tx_cmp3::$System->GetEnv('DOCUMENT_ROOT')) ? 'web' : '';
			$pathInfo['web_sys'] = $pathInfo['web_nonweb'] ? 'web' : 'sys';

			if (\tx_cmp3::isTypo3Backend()) {
				$pathInfo['dir_accessable'] = $pathInfo['mount_id'] ? true : false;
			}

				// extra path info
			$pathInfo['dir_name'] = basename($path);
			$pathInfo['dir_title'] = $pathInfo['dir_name'];
			$pathInfo['dir_path_absolute'] = $path;
			$pathInfo['dir_path_relative'] = self::MakePathRelative($path);
			$pathInfo['dir_path'] = $pathInfo['dir_path_relative'];
			$pathInfo['dir_path_normalized'] = $pathInfo['dir_path_relative'];
			$pathInfo['dir_path_from_mount'] = self::MakePathRelative($path, $pathInfo['mount_path']);

			// ksort($pathInfo);

			if ($pathInfo['dir_name'] === '_temp_')	{
				$pathInfo['dir_title'] = 'TEMP';
			}
			if ($pathInfo['dir_name'] === '_recycler_')	{
				$pathInfo['dir_title'] = 'RECYCLER';
			}

		}
		$GLOBALS['T3_VAR']['ext']['dam']['pathInfoCache'][$path] = $pathInfo;

		return $pathInfo;
	}


	/**
	 * Appended '/' or '\' will be removed from the string
	 *
	 * @param string $strString
	 * @return string
	 */
	public static function StripSlash($strString)
	{
		return preg_replace('#[\\/]+$#', '', $strString);
	}


	/**
	 * Prepended '/' or '\' will be removed from the string
	 *
	 * @param string $strString
	 * @return string
	 */
	public static function StripLeadingSlash($strString)
	{
		return preg_replace('#^[\\/]+#', '', $strString);
	}


	/**
	 * Merge base path with relative path
	 * Will remove duplicate /
	 *
	 * @param string $strString
	 * @param string $strString2
	 * @return string
	 */
	public static function Concat($strString, $strString2)
	{
		return self::MakePathClean($strString) . self::StripLeadingSlash(self::MakePathClean($strString2));
	}




	/***************************************
	 *
	 *	 Access check related function
	 *
	 ***************************************/


	#TODO see tx_dam::access_*





	/***************************************
	 *
	 *	 Media objects functions
	 *
	 ***************************************/



	/**
	 * Returns a file object by a given file path
	 *
	 * @param	mixed		$filepath Is a file path or an uid of a DAM record
	 * @return	\Cmp3\Files\File File object or false
	 * @throws \Cmp3\Files\Exception
	 * @throws \Cmp3\Files\NotFoundException
	 * @see \Cmp3\Files\File
	 * @todo unit test
	 */
	public static function GetFile($filepath) {

		$media = null;

		if (class_exists('tx_dam', false)) {

			if(!strcmp($filepath,intval($filepath))) {

				// this seems to be an uid of an DAM record
				if ($strMetaArray = tx_dam::meta_getDataByUid($filepath, '*'))
					return new \Cmp3\Files\File_dam(tx_dam::file_absolutePath($strMetaArray), $strMetaArray);

				throw new \Cmp3\Files\Exception('DAM was requested but index is not available!');

			} else {

				$filepath = self::MakeFilePathAbsolute($filepath);
				if ($strMetaArray = tx_dam::meta_getDataForFile($filepath, '*'))
					return new \Cmp3\Files\File_dam($filepath, $strMetaArray);
			}


		}

		$filepath = self::MakeFilePathAbsolute($filepath);
		if (@is_file($filepath)) {
			return new \Cmp3\Files\File($filepath);
		}


		throw new \Cmp3\Files\NotFoundException('file not found: '.$filepath);
	}


	/**
	 * Creates a file with unique file name and returns file object
	 *
	 * @param string $strFilePrefix The prefix of the generated temporary filename.
	 * @param string $strFileSuffix The suffix of the generated temporary filename.
	 * @param string $strPath The path where the file will be generated
	 * @param array|object $metaData some extra data which can be used for the file
	 * @return \Cmp3\Files\File|FALSE
	 */
	public static function GetTemp($strFilePrefix, $strFileSuffix=null, $strPath=null, $metaData=null)
	{
		$strPath = $strPath ? \Cmp3\System\Files::MakePathAbsolute($strPath) :  PATH_site.'typo3temp/';

		if (!$strFileSuffix) {
			$filename = tempnam($strPath, $strFilePrefix);
		} else {
			$filename = \Cmp3\System\Files::MakeTempName($strFilePrefix, $strFileSuffix, $strPath);
		}

		if (!$filename) {
			throw new \Cmp3\Files\NotFoundException("Temp file could not be created: {$strPath} {$strFilePrefix}*.{$strFilePrefix}");
		}

		if (class_exists('\t3lib_div', false)) {
			\t3lib_div::fixPermissions($filename);
		}

		$objFile = new \Cmp3\Files\File($filename, $metaData);
		$objFile->SetDeleteOnDestruct();
		return $objFile;
	}



	/**
	 * Returns an url's content as string
	 *
	 * Timeout is not supported when in TYPO3
	 *
	 * @param	mixed		$strUrl
	 * @return	string
	 * @todo unit test
	 */
	public static function GetUrlContent($strUrl)
	{
#TODO see \Next\Fetch
		if (\tx_cmp3::isTypo3()) {

			$report = array();

			$content = \t3lib_div::getURL($strUrl, false, false, $report);

			if ($report['error']) {
				#TODO use \Next\ErrorData ?
			}
			return $content;

		}

		if (function_exists('curl_init')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			$tmp = curl_exec($ch);
			curl_close($ch);
			return $tmp;

		} else {
			return file_get_contents($strUrl);
		}
	}




	/***************************************
	 *
	 *   Donwload handler
	 *
	 ***************************************/


	/**
	 * Returns the default object which handles downloads - which might be secured
	 *
	 * @return	\Cmp3\Files\FileDownloadUrl
	 */
	public static function GetDownloadHandler()
	{
		try {
			$objDownloadHandler = txApplications::GetCurrent()->Property('DownloadHandler');

		} catch (\Cmp3\UndefinedPropertyException $e) {
#TODO manual?
			$propertiesArray = txApplications::GetCurrent()->Config->GetProperties('policies.download');
			$objDownloadHandler = new \Cmp3\Files\FileDownloadUrl($propertiesArray);
			txApplications::GetCurrent()->SetProperty('DownloadHandler', $objDownloadHandler);
		}

		return $objDownloadHandler;
	}



	/***************************************
	 *
	 *   process file or folder changes like rename
	 *
	 ***************************************/


	#TODO see tx_dam::process_*



	/***************************************
	 *
	 *   Misc Tools
	 *
	 ***************************************/



	/**
	 * Search for a file and walk up the path if not found in current dir.
	 *
	 * @param 	string 		$fileName File name to search for
	 * @param 	string 		$path Path to search for file
	 * @param 	boolean 	$walkUp If set it will be searched for the file in folders above the given
	 * @param 	string 		$basePath This absolute path is the limit for searching with $walkUp
	 * @return	FALSE|string 		file path
	 * @todo unit test walkUp
	 */
	public static function FindFileInPath($fileName, $path, $walkUp=true, $basePath='')
	{
		$basePath = $basePath ? $basePath : \tx_cmp3::ResolvePath('PATH_site');

		$path = self::MakeFilePathAbsolute($path);

		if (is_file($path.$fileName) AND is_readable($path.$fileName)) {

			return $path.$fileName;
		}

		if (!$walkUp OR ($path == $basePath)) {
			return false;
		}

		if (self::MakePathRelative($path) == '') {
			return false;
		}

		if (!($path=dirname($path))) {
			return false;
		}

		return self::FindFileInPath($fileName, $path, $walkUp, $basePath);
	}


	/**
	 * Calculates a hash value from a file.
	 * The hash is used to identify file changes or a file itself.
	 * Remember that a file can occur multiple times in the file system, therefore you can detect only that it is the same file. But you have to take the location (path) into account to identify the right file.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::compileInfo().
	 * @return	string		hash value
	 */
	public static function CalcHash($fileInfo)
	{
		$hash = false;

		$filename = self::MakeFilePathAbsolute($fileInfo);
		if (function_exists('md5_file')) {
			$hash = @md5_file($filename); /*@*/
		} else {
			if(filesize ($filename) > 0xfffff ) {	// 1MB
#TODO
				$cmd = t3lib_exec::getCommand('md5sum');
				$output = array();
				$retval = 0;
				exec($cmd.' -b '.escapeshellcmd($filename), $output, $retval);
				$output = explode(' ',$output[0]);
				$match = array();
				if (preg_match('#[0-9a-f]{32}#', $output[0], $match)) {
					$hash = $match[0];
				}
			} else {
				$file_string = file_get_contents($filename);
				$hash = md5($file_string);
			}
		}

		return $hash;
	}


	/**
	 * convert/cleans a file name to be more usable as title
	 *
	 * @param	string		Filename or similar
	 * @return	string		Title string
	 */
	public static function MakeTitleFromFilename($title)
	{
		$orgTitle = $title;
		$extpos = strrpos($title,'.');
		$title = $extpos ? substr($title, 0, $extpos) : $title; // remove extension
		$title = str_replace('_',' ',$title);	// Substituting "_" for " " because many filenames may have this instead of a space char.
		$title = str_replace('%20',' ',$title);
			// studly caps: add spaces
		$title = preg_replace('#([a-z])([A-Z])#', '\\1 \\2', $title);

		return $title;
	}


	/**
	 * Attempt to detect the MIME type of a file using available extensions
	 *
	 * This method will try to detect the MIME type of a file. If the fileinfo
	 * extension is available, it will be used. If not, the mime_magic
	 * extension which is deprected but is still available in many PHP setups
	 * will be tried.
	 *
	 * If neither extension is available, the default application/octet-stream
	 * MIME type will be returned
	 *
	 * @param  string $file File path
	 * @return array       MIME type
	 */
	public static function DetectFileMimeType($file)
	{
		$type = null;

		$mimeType = array();
		$mimeType['mime_type'] = '';
		$mimeType['mime_basetype'] = '';
		$mimeType['mime_subtype'] = '';
		$mimeType['file_type'] = '';


		// get file extension
		$path_parts = self::_split_fileref($file);

		$fileType = strtolower($path_parts['realFileext']);
			// cleanup bakup files extension
		$mimeType['file_type'] = preg_replace('#\~$#', '', $fileType);


		// should work even if file doesn't exists
		if (file_exists($file)) {

			// First try with fileinfo functions
			if (function_exists('finfo_open')) {
				if ($_fileInfoDb === null) {
					$_fileInfoDb = @finfo_open(FILEINFO_MIME); /*@*/
				}

				if ($_fileInfoDb) {
					$type = finfo_file($_fileInfoDb, $file);
				}

			} elseif (function_exists('mime_content_type')) {
				$type = mime_content_type($file);

			} else {
				$type = $GLOBALS['T3_VAR']['ext']['dam']['file2mime'][$fileType];
			}

		} else {
			$type = $GLOBALS['T3_VAR']['ext']['dam']['file2mime'][$fileType];
		}



		// Fallback to the default application/octet-stream
		if (! $type) {
			$type = 'application/octet-stream';
		}

		// text/plain; charset=us-ascii
		list($type) = trim_explode(';', $type);

		$mimeType['mime_type'] = $type;

		$mtarr = explode ('/', $type);
		if (is_array($mtarr) && count($mtarr)==2) {
			$mimeType['mime_basetype'] = $mtarr[0];
			$mimeType['mime_subtype'] = $mtarr[1];
		}

		if ($mimeType['file_type'] == '') {
			$mimeType['file_type'] = array_search($mimeType['mime_type'], $GLOBALS['T3_VAR']['ext']['dam']['file2mime'], true);
		}

		return $mimeType;
	}


	/**
	 * Returns the MIME type for a given file extension.
	 *
	 * If neither extension is available, the default application/octet-stream
	 * MIME type will be returned
	 *
	 * @param  string $strSuffix File suffix like jpg, pdf, txt
	 * @return array       MIME type
	 */
	public static function GetMimeTypeForSuffix($strSuffix)
	{
		$mimeType = $GLOBALS['T3_VAR']['ext']['dam']['file2mime'][$strSuffix];

		// Fallback to the default application/octet-stream
		if (! $mimeType) {
			$mimeType = 'application/octet-stream';
		}

		return $mimeType;
	}




	/**
	 * Splits a reference to a file in 5 parts
	 *
	 * @param	string		Filename/filepath to be analysed
	 * @return	array		Contains keys [path], [file], [filebody], [fileext], [realFileext]
	 * @todo remove or replace
	 */
	public static function _split_fileref($fileref)	{
		$reg = array();
		if (	preg_match('#(.*/)(.*)$#', $fileref, $reg)	)	{
			$info['path'] = $reg[1];
			$info['file'] = $reg[2];
		} else {
			$info['path'] = '';
			$info['file'] = $fileref;
		}
		$reg='';
		if (	preg_match('#(.*)\.([^\.]*$)#', $info['file'], $reg)	)	{
			$info['filebody'] = $reg[1];
			$info['fileext'] = strtolower($reg[2]);
			$info['realFileext'] = $reg[2];
		} else {
			$info['filebody'] = $info['file'];
			$info['fileext'] = '';
		}
		reset($info);
		return $info;
	}
}





