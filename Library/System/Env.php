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
 * Gives access to the applications environment like paths etc.
 *
 * This is about paths and path resources.
 *
 * @todo can be used to provide setup data like path to icons and other stuff?
 *
 * STATUS: rfc - useful?
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Core
 * @package    CMP3
 */
class Env {

#TODO make this not static?
	/**
	 * Stores paths for resources like Icons, ContentFiles, ...
	 * @var array
	 */
	protected static $_resources = array();



	/**
	 * Returns path to a resource folder
	 *
	 * @return string $strResourceName resource name like Icons, ContentFiles, ...
	 * @return string $strFileName file name which should be searched for in the resource folders
	 * @return string|FALSE
	 */
	public function GetResourcePath ($strResourceName, $strFileName=NULL)
	{
		if (empty($strResourceName)) return false;

		$strResourcePath = false;

		$strResourceKey = strtolower($strResourceName);

		// lookup resources registry for a registered filename
		if ($strFileName AND isset(self::$_resources[$strResourceKey.'/'.$strFileName])) {
			$strResourcePathTest = end(self::$_resources[$strResourceKey.'/'.$strFileName]);
			if (@file_exists($strResourcePathTest)) {
				$strResourcePath = $strResourcePathTest;
			}
		}

		// lookup path registry for a registered filename
		try {
			if ($strResourcePath === false AND $strFileName AND ($strResourcePath = \tx_cmp3::ResolvePath($strResourceKey.'/'.$strFileName))) {
				if (!@file_exists($strResourcePath)) {
					$strResourcePath = false;
				}

			}
		} catch (Exception $e) {
		}

		// lookup for registered paths, the last one registered taking precedence (all registered paths are valid and existing!)
		if ($strResourcePath === false AND isset(self::$_resources[$strResourceKey]) AND $strFileName === NULL) {
			$strResourcePath = end(self::$_resources[$strResourceKey]);
		}

		// lookup the file in all registered paths starting from the last path registered and stop when found
		if ($strResourcePath === false AND isset(self::$_resources[$strResourceKey])) {
			$arrResources = array_reverse(self::$_resources[$strResourceKey]);
			foreach ($arrResources as $strPath) {
				if (@file_exists($strPath.$strFileName)) {
					$strResourcePath = $strPath.$strFileName;
					break;
				}
			}
		}

		if (!$strResourcePath) return false;

		return $strResourcePath;
	}


	/**
	 * Returns path to a resource folder or a specific file
	 *
	 * @return string $strResourceName resource name like Icons, ContentFiles, ...
	 * @return string $strFileName file name which should be serached for in the resource folders
	 * @return string
	 */
	public function GetRelativeResourcePath ($strResourceName, $strFileName=NULL)
	{
		return self::MakePathRelative($this->GetResourcePath($strResourceName, $strFileName));
	}

	#FIXME why are these static ?? Can't remember

	/**
	 * Clears paths for a resource type
	 *
	 * @return string resource name like Icons, ContentFiles, ...
	 * @return void
	 */
	public static function ClearResourcePaths ($strResourceName)
	{
		self::$_resources[strtolower($strResourceName)] = array();
	}


	/**
	 * Adds a path to a resource folder
	 *
	 * @return string resource name like Icons, ContentFiles, ...
	 * @return string absolute folderpath
	 * @return string
	 * @throws \Cmp3\System\Exception
	 */
	public static function AddResourcePath ($strResourceName, $strPath)
	{
		$strPathResolved = self::ResolvePath($strPath);
		if ($strPathResolved === false) {
			throw new \Cmp3\System\Exception('Could not add resource path which is not existant: '.$strPath);
		}
		return self::$_resources[strtolower($strResourceName)][] = $strPathResolved;
	}


	/**
	 * Adds resource paths from a given path by adding subfolders
	 *
	 * @return string absolute folderpath
	 * @return void
	 */
	public static function AddResourcePathsFromSubfolder ($strPath)
	{
		// add all subdirs of assets/ to resources
		if (is_dir($strPath)) {
			$iterator = new DirectoryIterator($strPath);
			foreach ($iterator as $fileinfo) {
			    if ($fileinfo->isDir() AND ! $fileinfo->isDot()) {
					self::AddResourcePath($fileinfo->getFilename(), $strPath . '/' . $fileinfo->getFilename() .'/');
			    }
			}
		}
	}


	/**
	 * Returns an absolue path from a path which can be relative or with an location prefix like EXT:
	 *
	 * @param string $strFilename
	 * @return string|FALSE resolved filepath
	 * @see \Cmp3\System\Files
	 */
	public static function ResolvePath ($strFilename, $checkExistence=true)
	{
		if (!$abolutePathFilename = \tx_cmp3::$System->ResolvePath($strFilename, $checkExistence)) {
			return false;
		}
		return ($checkExistence===false OR @file_exists($abolutePathFilename)) ? $abolutePathFilename : false; /*@*/
	}


	/**
	 * Convert absolute path, make it releative to PATH_site
	 *
	 * @param $strFilename
	 * @param $basePath is PATH_site by default
	 * @return string
	 * @see \Cmp3\System\Files
	 */
	public static function MakePathRelative($strFilename, $basePath = null, $backPath = 0)
	{
		if (empty($strFilename)) return $strFilename;
		if ($basePath==='' OR $backPath==99) return $strFilename;

		if (is_null($basePath)) {
			$basePath = \tx_cmp3::ResolvePath('PATH_site');
			$strFilename = clean_realpath($strFilename);
		}

		$strFilenameRel =  preg_replace('#^'.preg_quote($basePath).'#', '', $strFilename);
		if ($strFilenameRel == $strFilename) {
			$strFilenameRel = self::MakePathRelative($strFilename, dirname($basePath), $backPath++);
		}
		$strFilenameRel = preg_replace('#[/]+#', '/', str_repeat('../', $backPath) . $strFilenameRel);

		// this is for backend modules
		if (\tx_cmp3::isTypo3Backend()) {
				$strFilenameRel = $GLOBALS['BACK_PATH'].'../'.$strFilenameRel;
		}

		return $strFilenameRel;
	}



}




