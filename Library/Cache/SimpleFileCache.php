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
 * @subpackage Cache
 * @package    CMP3
 * @copyright  Copyright (c) 2011 Rene Fritz <r.fritz@colorcube.de>
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\Cache;




/**
 * Simple file cache
 *
 *
 * Example usage:
 *
 * $strFilename = '/path/my-excel-file';
 *
 * $objCache = new \Cmp3\Cache\SimpleFileCache($strType, 'excel');
 *
 * $objCache->SetValidOnCachedPages();
 * $objCache->SetValidWhenNewerThan(filemtime($strFilename));
 *
 * // If cached data exists and is valid, use it
 * $strData = $objCache->GetData();
 * if ($strData) {
 * 	$objPHPExcel = unserialize($strData);
 * 	return $objPHPExcel;
 * }
 *
 * // create object we want to use
 * $objPHPExcel = PHPExcel_IOFactory::load($strFilename);
 *
 * // store object in cache
 * $objCache->SaveData(serialize($objPHPExcel));
 *
 *
 *
 * @author     Rene Fritz <r.fritz@colorcube.de>
 * @subpackage Cache
 * @package    CMP3
 */
class SimpleFileCache {

	/**
	 * Default path were files should be stored
	 * @var string
	 */
	public static $StoragePath;

	/**
	 * Path were files should be stored
	 * @var string
	 */
	protected $strStoragePath;

	protected $strNamespace;
	protected $strKey;
	protected $strExtension;

	/**
	 * array of tag names and values to build a cache from which indicates if a cache entry is still valid
	 * @var array
	 */
	protected $strTagsArray = array();

	/**
	 * array of special tags which indicates if a cache entry is still valid
	 * @var array
	 */
	protected $strValidateArray = array();


	/**
	 * Debug logger
	 * This is initialized from outside
	 *
	 * @var \Cmp3\Log\Logger
	 */
	public static $Debug;


	/**
	 *
	 * @param string $strNamespace Used to store cache files in a separate subfolder
	 * @param string $strKey Used for the cache file  name
	 * @param string $strExtension Used for the cache file suffix
	 * @param string $strStoragePath Path where the cache files should be stored
	 */
	public function __construct($strNamespace, $strKey, $strExtension = 'txt', $strStoragePath = null)
	{
		$this->strNamespace = trim(strtolower($strNamespace));
		$this->strKey = md5(trim(strtolower($strKey)));
		$this->strExtension = trim(strtolower($strExtension));

		if ($strStoragePath) {
			$this->strStoragePath = \Cmp3\System\Files::MakePathClean($strStoragePath);
		} else {
			$this->strStoragePath = \Cmp3\System\Files::MakePathClean(self::$StoragePath);
		}

		if (self::$Debug) self::$Debug->Log(__CLASS__ . '| Cache path used: ' . $this->strStoragePath);
	}


	/**
	 * Set how long the cache is valid
	 *
	 * @param integer $intValue seconds
	 */
	public function SetValidLifetime($intValue)
	{
		$this->strValidateArray['ValidLifetime'] = $intValue;

		if (self::$Debug) self::$Debug->Log(__CLASS__ . '| Set ValidLifetime: ' . $intValue);
	}


	/**
	 * Set the cache valid when entry is older than given timestamp
	 *
	 * @param integer $intValue timestamp
	 */
	public function SetValidWhenOlderThan($intValue)
	{
		$this->strValidateArray['ValidWhenOlderThan'] = $intValue;

		if (self::$Debug) self::$Debug->Log(__CLASS__ . '| Set ValidWhenOlderThan: ' . date("H:i:s", $intValue));
	}


	/**
	 * Set the cache valid when entry is newer than given timestamp
	 *
	 * @param integer $intValue timestamp
	 */
	public function SetValidWhenNewerThan($intValue)
	{
		$this->strValidateArray['ValidWhenNewerThan'] = $intValue;

		if (self::$Debug) self::$Debug->Log(__CLASS__ . '| Set ValidWhenNewerThan: ' . date("H:i:s", $intValue));
	}


	/**
	 * Set the cache to be valid on cached pages only
	 * rerendered page will flush the cache
	 *
	 * @param integer $intValue seconds
	 */
	public function SetValidOnCachedPages($blnFlag = true)
	{
		$this->strValidateArray['ValidOnCachedPages'] = $blnFlag;

		if (self::$Debug) self::$Debug->Log(__CLASS__ . '| Set ValidOnCachedPages: ' . $blnFlag);
	}


	/**
	 * Set tag
	 *
	 * @param string $strTagName
	 * @param string $strValue
	 */
	public function SetTag($strTagName, $strValue)
	{
		$this->strTagsArray[$strTagName] = $strValue;

		if (self::$Debug) self::$Debug->LogData(__CLASS__ . '| Set tag: ' . $strTagName, $strValue);
	}


	/**
	 * Set tags from array
	 *
	 * @param array $strValueArray
	 */
	public function SetTags($strValueArray)
	{
		foreach ($strValueArray as $strTagName => $strValue) {
			return $this->strTagsArray[$strTagName] = $strValue;
		}
	}


	/**
	 * Get tag
	 *
	 * @param string $strTagName
	 * @return string
	 */
	public function GetTag($strTagName)
	{
		return $this->strTagsArray[$strTagName];
	}


	/**
	 * Remove tag
	 *
	 * @param string $strTagName
	 */
	public function RemoveTag($strTagName)
	{
		unset($this->strTagsArray[$strTagName]);
	}


	/**
	 * Remove all tags
	 *
	 */
	public function ClearTags()
	{
		$this->strTagsArray = array();
	}


	/**
	 * Check if the file exists and is valid
	 *
	 * @return boolean
	 */
	public function isValid()
	{

		$strFilePath = $this->GetFilePath();

		// First, ensure that the cache file exits
		if (file_exists($strFilePath)) {
			if (self::$Debug) self::$Debug->Log(__CLASS__ . '| Cache file found');

			// check if we're in context of a cached page or not
			if ($this->strValidateArray['ValidOnCachedPages'] AND $GLOBALS['TSFE'] AND $GLOBALS['TSFE']->isGeneratePage()) {
				$this->Flush();
				return false;
			}

			# TODO $this->strValidateArray['ValidLifetime']

			// flushes the cache when cache entry is older than given timestamp
			if ($this->strValidateArray['ValidWhenOlderThan'] AND filemtime($strFilePath) > $this->strValidateArray['ValidWhenOlderThan'])  {
				if (self::$Debug) self::$Debug->Log(__CLASS__ . '| ValidWhenOlderThan is NOT valid');
				$this->Flush();
				return false;
			}

			// flushes the cache when cache entry is new than given timestamp
			if ($this->strValidateArray['ValidWhenNewerThan'] AND filemtime($strFilePath) < $this->strValidateArray['ValidWhenNewerThan'])  {
				if (self::$Debug) self::$Debug->Log(__CLASS__ . '| ValidWhenNewerThan is NOT valid: ' . date("H:i:s", filemtime($strFilePath)) . ' < ' . date("H:i:s", $this->strValidateArray['ValidWhenNewerThan']));
				$this->Flush();
				return false;
			}

			// check hash if it's valid
			if (!$this->CompareHashFromFile()) {
				if (self::$Debug) self::$Debug->Log(__CLASS__ . '| CompareHashFromFile is NOT valid');
				$this->Flush();
				return false;
			}

		} else {
			if (self::$Debug) self::$Debug->Log(__CLASS__ . '| Cache file not found: ' . $strFilePath);
			return false;
		}

		if (self::$Debug AND $cache) self::$Debug->Log(__CLASS__ . '| Has cache');

		return true;
	}


	/**
	 * get cache data
	 *
	 * @return string
	 */
	public function GetData()
	{
		$cache = false;

		if ($this->isValid()) {

			$strFilePath = $this->GetFilePath();

			if (!$fp = @fopen($strFilePath, 'rb')) {
				if (self::$Debug) self::$Debug->Log(__CLASS__ . '| Could not open cache file');
				return FALSE;
			}

			flock($fp, LOCK_SH);

			if (($intFilesize = filesize($strFilePath)) > 0) {
				$cache = fread($fp, $intFilesize);
			}

			flock($fp, LOCK_UN);
			fclose($fp);
		}

		if (self::$Debug AND $cache) self::$Debug->Log(__CLASS__ . '| Has cache');
		if (self::$Debug AND $cache===false) self::$Debug->Log(__CLASS__ . '| NO cache!');

		return $cache;
	}


	/**
	 * Removes cached data
	 */
	public function Flush()
	{
		if (file_exists($this->GetFilePath())) {
			unlink($this->GetFilePath());
		}
	}


	/**
	 * Set cache data
	 *
	 * @param string $strData
	 * @return boolean Could be false when other process is being stored cache data
	 */
	public function SaveData($strData)
	{
		if (!is_dir($this->GetStorageDirectory())) {
			mkdir($this->GetStorageDirectory(), 0777, true);
		}

		if (!$fp = fopen($this->GetFilePath(), 'wb')) {
			return FALSE;
		}

		if (flock($fp, LOCK_EX)) {
			fwrite($fp, $strData);

			if (self::$Debug) self::$Debug->Log(__CLASS__ . '| Cache saved to ' . $this->GetFilePath());

			$this->SaveHashFile();

			flock($fp, LOCK_UN);
		} else {
			return FALSE;
		}
		fclose($fp);
	}


	/**
	 * Validates hash

	 * @return boolean if we have no tags or hash is valid return true, otherwise false
	 */
	protected function CompareHashFromFile()
	{
		if (count($this->strTagsArray)) {

			// If Hash File doesn't exist or if the values don't match, delete and return
			$strHashFile = $this->GetFilePath() . '.hash';
			if (!file_exists($strHashFile) OR ($this->GetTagsHash() != file_get_contents($strHashFile))) {
				unlink($this->GetFilePath());
				if (self::$Debug) self::$Debug->Log(__CLASS__ . '| Cache hash NOT valid');
				return false;
			}
		}
		// if we have no tags or hash is valid return true
		if (self::$Debug) self::$Debug->Log(__CLASS__ . '| Cache hash is valid');
		return true;
	}


	/**
	 * Save additional hash file
	 *
	 */
	protected function SaveHashFile()
	{
		if (count($this->strTagsArray)) {
			file_put_contents($this->GetFilePath() . '.hash', $this->GetTagsHash());
			if (self::$Debug) self::$Debug->Log(__CLASS__ . '| Cache hash saved to ' . $this->GetFilePath() . '.hash');
		}
	}


	/**
	 * Calculate the hash value for the set tags
	 *
	 * @return string
	 */
	protected function GetTagsHash()
	{
		return md5(serialize($this->strTagsArray));
	}


	/**
	 * Get path to the cache file
	 *
	 * @return string
	 */
	public function GetFilePath()
	{
		return sprintf('%s%s.%s', $this->GetStorageDirectory(), $this->strKey, $this->strExtension);
	}


	/**
	 * Get path to the cache directory
	 *
	 * @return string
	 */
	protected function GetStorageDirectory()
	{
		if ($this->strNamespace) {
			return sprintf('%s%s/', $this->strStoragePath, $this->strNamespace);
		} else {
			return $this->strStoragePath;
		}
	}
}


\Cmp3\Cache\SimpleFileCache::$StoragePath = \Cmp3\Cmp3::$CachePath;
