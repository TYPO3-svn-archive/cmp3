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
 * @subpackage Files
 * @package    CMP3
 * @copyright  Copyright (c) 2008 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Files;



/**
 * File interface which is the base for DAM and non-DAM file objects.
 *
 * STATUS alpha -
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Files
 * @package    CMP3
 *
 * @property string $TableName name of the table
 * @property array $DataArray Current data of the record with key=>$value pairs. Might not yet be saved to DB.
 * @property string $TableName
 * @property string $Hash md5 file hash
 * @property integer $Status TXDAM_status_file_ok, TXDAM_status_file_missing
 * @property string $Name The file name
 * @property string $DownloadName The name which should be used for downloads
 * @property string $Title The title which is NOT the file name
 * @property string $AbsolutePath
 * @property string $Path This is the default path to be used in most applications which is normally the relative path
 * @property string $RelativePath
 * @property integer $Mtime
 * @property integer $Tstamp
 * @property integer $Ctime
 * @property integer $Crdate
 * @property integer $Inode
 * @property integer $Size
 * @property string $Owner
 * @property string $Perms
 * @property boolean $isWritable
 * @property boolean $isReadable
 * @property integer $Hidden For records and DAM compatibility
 * @property integer $Deleted For records and DAM compatibility
 * @property string $MimeType a mime content type like: 'image/jpeg'
 * @property string $MimeBasetype a mime base content type like: 'image'
 * @property string $MimeSubtype a mime sub content type like: 'jpeg'
 * @property integer $MediaType see \Cmp3\System\Files::ConvertMediaType()
 * @property string $Type the file type like mp3, txt, pdf.
 * @property string $Suffix is in most cases the same as $Type
 */
interface FileInterface {



	/**
	 * Check if file exists
	 *
	 * @return boolean
	 */
	public function Exists ();


	/**
	 * Returns the ID which is the file path here
	 *
	 * @return	integer
	 */
	function GetID ();


	/**
	 * Returns a hash to identify the file. Searching for a file using this hash can be done with DAM only.
	 *
	 * @return	string		hash
	 */
	function GetHash ();


	/**
	 * Returns the file type like mp3, txt, pdf.
	 *
	 * @return	string		The file type like mp3, txt, pdf.
	 */
	function GetType ();


	/**
	 * Returns a mime content type like: 'image/jpeg'
	 *
	 * @return	string eg. 'image/jpeg'
	 */
	function GetMimeType ();


	/**
	 * Returns the download name for the file.
	 * This don't have to be the real file name. For usage with "Content-Disposition" HTTP header.
	 * header("Content-Disposition: attachment; filename=$downloadFilename");
	 *
	 * @return	string		File name for download.
	 */
	function GetDownloadName ();


	/**
	 * Returns a file path relative to PATH_site or getIndpEnv('TYPO3_SITE_URL').
	 *
	 * @return	string		Relative path to file
	 */
	function GetPathWebRelative ();


	/**
	 * Returns an absolute file path
	 *
	 * @return	string		Absolute path to file
	 */
	function GetPathAbsolute ();


	/**
	 * Returns a URL that can be used eg. for direct linking.
	 *
	 * @return	string		URL to file
	 */
	function GetURL ();


	/**
	 * Returns a URL that can be used for direct download.
	 * The download might be done using a wrapper to send the right HTTP headers
	 *
	 * @return	string		URL to file or a download wrapper
	 */
	function GetDownloadURL ();


	/**
	 * Returns the object which handles downloads - which might be secured
	 *
	 * @return	\Cmp3\Files\FileDownloadUrl
	 */
	function Download ();


	/**
	 * Returns an absolute file path
	 *
	 * @return	string	The file size in a formatted way like 45 kb
	 */
	function GetSizeFormatted ();
}









