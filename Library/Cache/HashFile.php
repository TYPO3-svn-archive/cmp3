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
 * Simple hashed file handling
 *
 * Example
 *
 * $objCache = new \Cmp3\Cache\HashFile($strOutputPath, 'png');
 *
 * $objCache->SetTag('w', $intWidth);
 * $objCache->SetTag('h', $intHeight);
 * $objCache->SetTag('data', serialize($MyData));
 *
 * if (!$objCache->isValid()) {
 *
 * 	// Create the pChart object
 * 	$myPicture = new pImage($intWidth, $intHeight, $MyData);
 *
 * 	// Render the picture (choose the best way)
 * 	$myPicture->Render($objCache->GetFilePath());
 * }
 *
 * echo '<img src="' . htmlspecialchars($this->MakePathRelative($objCache->GetFilePath())) . '" width="' . $intWidth . '" height="' . $intHeight . '" />';
 *
 *
 * @author     Rene Fritz <r.fritz@colorcube.de>
 * @subpackage Cache
 * @package    CMP3
 */
class HashFile extends \Cmp3\Cache\SimpleFileCache {




	/**
	 * Debug logger
	 * This is initialized from outside
	 *
	 * @var \Cmp3\Log\Logger
	 */
	public static $Debug;


	/**
	 *
	 * @param string $strStoragePath Path where the cache files should be stored
	 * @param string $strExtension Used for the cache file suffix
	 */
	public function __construct($strStoragePath, $strExtension)
	{
		$this->strExtension = trim(strtolower($strExtension));

		$this->strStoragePath = \Cmp3\System\Files::MakePathClean($strStoragePath);

		if (self::$Debug) self::$Debug->Log(__CLASS__ . '| Cache path used: ' . $this->strStoragePath);
	}


	/**
	 * Validates hash
	 *
	 * @return boolean if we have no tags or hash is valid return true, otherwise false
	 */
	protected function CompareHashFromFile()
	{
		// dummy
		return true;
	}


	/**
	 * Save additional hash file
	 *
	 */
	protected function SaveHashFile()
	{
		// dummy
	}


	/**
	 * Calculate the hash value for the set tags
	 *
	 * @return string
	 */
	protected function GetTagsHash()
	{
		if (!$this->strTagsArray) {
			throw new Exception ('No tags set. At least one tag is needed!');
		}
		return md5(serialize($this->strTagsArray));
	}


	/**
	 * Get path to the cache file
	 *
	 * @return string
	 */
	public function GetFilePath()
	{
		return sprintf('%s%s.%s', $this->GetStorageDirectory(), $this->GetTagsHash(), $this->strExtension);
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

