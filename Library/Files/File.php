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
 * include constants and media types, ...
 * @todo remove from here and place somewhere else
 */
#TODO require_once(PATH_cmp3.'library/files/\Next\file_types.php');
# require_once(PATH_cmp3.'library/files/\Cmp3\Files\Exceptions.php');


/**
 * File object
 *
 * STATUS beta - most of it is unit tested
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Files
 * @package    CMP3
 *
 * @property-read string $Hash md5 file hash
 * @property-read integer $Status TXDAM_status_file_ok, TXDAM_status_file_missing
 * @property-read string $Name The file name
 * @property-read string $DownloadName The name which should be used for downloads
 * @property-read string $Title The title which is NOT the file name
 * @property-read string $AbsolutePath
 * @property-read string $Path This is the default path to be used in most applications which is normally the relative path
 * @property-read string $RelativePath
 * @property-read string $RelativeDirname
 * @property-read string $Dirname
 * @property-read string $AbsoluteDirname
 * @property-read integer $Mtime
 * @property-read integer $Atime
 * @property-read integer $Tstamp
 * @property-read integer $Ctime
 * @property-read integer $Crdate
 * @property-read integer $Inode
 * @property-read integer $Size
 * @property-read string $Owner
 * @property-read string $Perms
 * @property-read boolean $isWritable
 * @property-read boolean $isReadable
 * @property-read integer $Hidden For records and DAM compatibility
 * @property-read integer $Deleted For records and DAM compatibility
 * @property-read string $MimeType a mime content type like: 'image/jpeg'
 * @property-read string $MimeBasetype a mime base content type like: 'image'
 * @property-read string $MimeSubtype a mime sub content type like: 'jpeg'
 * @property-read integer $MediaType see \Cmp3\System\Files::ConvertMediaType()
 * @property-read string $MediaTypeString see \Cmp3\System\Files::ConvertMediaType()
 * @property-read string $Type the file type like mp3, txt, pdf.
 * @property-read string $Suffix is in most cases the same as $Type
 * @property array $MetaData meta data object for the file
 * @property array $MetaDataArray meta data for the file with key=>$value pairs
 * @property object $MetaDataObject meta data object for the file
 */
class File implements FileInterface {


	//@todo can this be done non-static??
	public static $intLastErrorCode = false;
	public static $strLastErrorMessage = '';


	/**
	 * Absolute path to the file
	 * @var string
	 */
	protected $strAbsFilePath;

	/**
	 * cached splitted file path
	 * @var string
	 */
	protected $strPathPartsArray = array();

	/**
	 * cached content of pseudo fields
	 * @var array
	 */
	protected $strDataCacheArray = array();

	/**
	 * additional meta data (eg. from the database)
	 *
	 * @var array
	 */
	protected $objMetaData = null;

	/**
	 * Download handler which might secure downloads
	 * @var \Cmp3\Files\FileDownloadUrl
	 */
	protected $objDownloadHandler = null;

	/**
	 * flag that if set on object destruction the file will be deleted
	 * @var boolean
	 */
	protected $blnDeleteOnDestruct = false;


	/**
	 * Array of properties which can be added to the file object
	 * @var mixed
	 */
	protected $properties = array();



	/**
	 * @param string $filename file path (absolute or relative)
	 * @param array|object $metaData some extra data which can be used for the file
	 * @return void
	 */
	public function __construct($filename, $metaData=null)
	{
		$this->_SetFilename($filename);

		if ($metaData)
			$this->objMetaData = (is_array($metaData) ? new \ArrayObject($metaData, \ArrayObject::ARRAY_AS_PROPS) : $metaData);
	}


	/**
	 * destructor
	 *
	 * deletes a file if blnDeleteOnDestruct is set
	 * @return unknown_type
	 */
	public function __destruct() {
		if ($this->blnDeleteOnDestruct) {
			$this->Delete();
		}
	}


	/**
	 * Sets a flag that the file will be deleted when the object is destroyed
	 *
	 * @param boolean $blnDeleteOnDestruct Default TRUE
	 * @return void
	 * @see __destruct()
	 * @see \Cmp3\System\SystemFile::GetTemp()
	 */
	public function SetDeleteOnDestruct($blnDeleteOnDestruct = true)
	{
		$this->blnDeleteOnDestruct = $blnDeleteOnDestruct;
	}


	/**
	 * Initialize the file name
	 * @param string $filename file path (absolute or relative)
	 * @return void
	 */
	protected function _SetFilename($filename)
	{
		$filename = \Cmp3\System\Files::ResolvePath($filename, false);
	    $this->strAbsFilePath = \Cmp3\System\Files::MakeFilePathAbsolute($filename);
	    // doesn't handle utf8 in all php versions: $this->strPathPartsArray = pathinfo($fileInfo);
	    $this->strPathPartsArray = \Cmp3\System\Files::_split_fileref($this->strAbsFilePath);

	    $this->strDataCacheArray = array();
	}


	/**
	 * Tell the object that the file has changed
	 *
	 * @return void
	 */
	public function Changed()
	{
	    $this->strDataCacheArray = array();
		if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
			clearstatcache(true, $this->strAbsFilePath);
		} else {
			clearstatcache();
		}
	}






	/*************************
	 *
	 * Get/Set methods
	 *
	 *************************/


	/**
	 * Override method to perform a property "Set"
	 * This will set the property $strName to be $mixValue
	 *
	 * @param string $strName Name of the property to set
	 * @param string $mixValue New value of the property
	 * @return mixed
	 */
	public function __set($strName, $mixValue)
	{
		switch ($strName) {

			case 'MetaData':
			case 'MetaDataArray':
			case 'MetaDataObject':
	      			$this->objMetaData = (is_array($mixValue) ? new \ArrayObject($mixValue, \ArrayObject::ARRAY_AS_PROPS) : $mixValue);
	      			return $this->objMetaData;
				break;
			default:
				$strName = strtolower($strName);
				return  ($this->objMetaData->$strName = $mixValue);

				throw new \Cmp3\UndefinedSetPropertyException($strName);

		}
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
		// change 'file_name' to 'name' - this is because of DAM
		$strName = str_replace('file_', '', strtolower($strName));

		// use computed value when available
		if (array_key_exists($strName, $this->strDataCacheArray)) {
			return $this->strDataCacheArray[$strName];
		}



		switch ($strName) {
			case 'tablename':
					return null;

			case 'dataarray':
					$dataArray = array(
						'Hash' => $this->hash,
						'Status' => $this->status,
						'Name' => $this->name,
						'DownloadName' => $this->downloadname,
						'Title' => $this->title,
						'AbsolutePath' => $this->absolutepath,
						'Path' => $this->path,
						'RelativePath' => $this->relativepath,
						'AbsoluteDirname' => $this->absolutedirname,
						'Dirname' => $this->dirname,
						'RelativeDirname' => $this->relativedirname,
						'Mtime' => $this->mtime,
						'Tstamp' => $this->tstamp,
						'Ctime' => $this->ctime,
						'Crdate' => $this->crdate,
						'Inode' => $this->inode,
						'Size' => $this->size,
						'Owner' => $this->owner,
						'Perms' => $this->perms,
						'isWritable' => $this->iswritable,
						'Writable' => $this->writable,
						'isReadable' => $this->isreadable,
						'Readable' => $this->readable,
						'Hidden' => $this->hidden,
						'Deleted' => $this->deleted,
						'MimeType' => $this->mimetype,
						'MimeBasetype' => $this->mimebasetype,
						'MimeSubtype' => $this->mimesubtype,
						'Type' => $this->type,
						'Extension' => $this->extension,
						'Suffix' => $this->suffix,
					);

					return array_merge($dataArray, $this->metadataarray);

			case 'metadata':
					return $this->objMetaData;

			case 'metadataarray':

				if ($this->objMetaData instanceof \ArrayObject) {
					return (array) $this->objMetaData;
				}

				$dataArray = array();
				try {
					$dataArray = $this->objMetaData->DataArray;
				} catch ( Exception $e ) {
					// nothing to do
					$dataArray = array();
				}
				return (array) $dataArray;

			case 'dataraw':
				return $this;

			case 'datafields':
				return array_keys ($this->dataarray);

			case 'hash':
				return $this->strDataCacheArray[$strName] = \Cmp3\System\Files::CalcHash($this->strAbsFilePath);

			case 'status':
				# TODO keep DAM codes - use anyway?
				return (@is_file($this->strAbsFilePath) ? TXDAM_status_file_ok : TXDAM_status_file_missing); /*@*/

			case 'name':
			case 'downloadname':
				try {
					if ($this->objMetaData->downloadname) {
						return $this->objMetaData->downloadname;
					}
				} catch (Exception $e) {
				}
				return $this->strPathPartsArray['file'];

			case 'title':
				return $this->strDataCacheArray[$strName] = \Cmp3\System\Files::MakeTitleFromFilename ($this->strPathPartsArray['file']);

			case 'absolutepath':
				return $this->strAbsFilePath;

			case 'path':
			case 'relativepath':
				return \Cmp3\System\Files::MakeFilePathWebRelative($this->strAbsFilePath);

			case 'absolutedirname':
				return $this->strPathPartsArray['path'];

			case 'dirname':
			case 'relativedirname':
				return \Cmp3\System\Files::MakeFilePathWebRelative($this->strPathPartsArray['path']);

			case 'mtime':
			case 'tstamp':
				return $this->strDataCacheArray[$strName] = @filemtime($this->strAbsFilePath); /*@*/

			case 'ctime':
			case 'crdate':
				return $this->strDataCacheArray[$strName] = @filectime($this->strAbsFilePath);

			case 'atime':
				return $this->strDataCacheArray[$strName] = @fileatime($this->strAbsFilePath); /*@*/

			case 'inode':
				return $this->strDataCacheArray[$strName] = @fileinode($this->strAbsFilePath);

			case 'size':
				return $this->strDataCacheArray[$strName] = @filesize($this->strAbsFilePath);

			case 'owner':
				return $this->strDataCacheArray[$strName] = @fileowner($this->strAbsFilePath);

			case 'perms':
				return $this->strDataCacheArray[$strName] = @fileperms($this->strAbsFilePath);

			case 'iswritable':
			case 'writable':
				return $this->strDataCacheArray[$strName] = @is_writable($this->strAbsFilePath);

			case 'isreadable':
			case 'readable':
				return $this->strDataCacheArray[$strName] = @is_readable($this->strAbsFilePath);

			case 'hidden':
			case 'deleted':
				return $this->strDataCacheArray[$strName] = @is_file($this->strAbsFilePath);

			case 'mimetype':
			case 'mimebasetype':
			case 'mimesubtype':
			case 'mediatype':
			case 'mediatypestring':
			case 'type':
			case 'extension':
			case 'suffix':

	      		$mediaTypeArray = \Cmp3\System\Files::DetectFileMimeType($this->strAbsFilePath);

				$this->strDataCacheArray['mimetype'] =     $mediaTypeArray['mime_type'];
				$this->strDataCacheArray['mimebasetype'] = $mediaTypeArray['mime_basetype'];
				$this->strDataCacheArray['mimesubtype'] =  $mediaTypeArray['mime_subtype'];
				$this->strDataCacheArray['mediatype'] =    $mediaTypeArray['media_type'];
				$this->strDataCacheArray['mediatypestring'] = $mediaTypeArray['media_type_string'];
				$this->strDataCacheArray['type'] =         $mediaTypeArray['file_type'];
				$this->strDataCacheArray['extension'] =    $mediaTypeArray['file_type'];
				$this->strDataCacheArray['suffix'] =       $mediaTypeArray['file_type'];

				return $this->strDataCacheArray[$strName];


			default:

				if ($this->objMetaData) {
					try {
						return $this->objMetaData->$strName;
					} catch ( Exception $e ) {
						// nothing to do
					}
				}

				throw new \Cmp3\UndefinedGetPropertyException($strName);
			break;
		}
	}


	/**
	 * Set's the object which provides meta data
	 *
	 * @return void
	 */
	public function SetMetaDataObject($objMetaData)
	{
		$this->objMetaData = $objMetaData;
	}



	/**
	 * Test if the given property exists in the meta data
	 *
     * @param $strName string Name of the meta data property
	 * @return boolean
	 */
	public function FieldExists($strName)
	{
		try {
			$dummy = $this->$strName;
			return true;
		} catch ( Exception $e ) {
			// nothing to do
		}
		return false;
	}





	/*************************
	 *
	 * General methods
	 *
	 *************************/


	/**
	 * Check if file exists
	 *
	 * @return boolean
	 */
	public function Exists()
	{
		return @is_file($this->strAbsFilePath);
	}


	/**
	 * Returns the ID which is the file path here
	 *
	 * @return	integer
	 */
	function GetID ()
	{
		return $this->strAbsFilePath;
	}


	/**
	 * Returns a hash to identify the file. Searching for a file using this hash can be done with DAM only.
	 *
	 * @return	string		hash
	 */
	function GetHash ()
	{
		return $this->Hash;
	}


	/**
	 * Returns the file type like mp3, txt, pdf.
	 *
	 * @return	string		The file type like mp3, txt, pdf.
	 */
	function GetType ()
	{
		return $this->Type;
	}


	/**
	 * checks if the file type is one of the given types
	 *
	 * @return	string|array		The file types like mp3, txt, pdf as array or comma list
	 */
	function isType ($strList)
	{
		return in_list($this->Type, $strList);
	}


	/**
	 * Returns a mime content type like: 'image/jpeg'
	 *
	 * @return	string eg. 'image/jpeg'
	 */
	function GetMimeType ()
	{
		return $this->MimeType;
	}


	/**
	 * Returns the download name for the file.
	 * This don't have to be the real file name. For usage with "Content-Disposition" HTTP header.
	 * header("Content-Disposition: attachment; filename=$downloadFilename");
	 *
	 * @return	string		File name for download.
	 */
	function GetDownloadName ()
	{
		return $this->DownloadName;
	}


	/**
	 * Sets the download name for the file.
	 * This don't have to be the real file name. For usage with "Content-Disposition" HTTP header.
	 * header("Content-Disposition: attachment; filename=$downloadFilename");
	 *
	 * @param	string		File name for download.
	 * @return void
	 */
	function SetDownloadName ($strDownloadName)
	{
		$this->DownloadName = $strDownloadName;
	}


	/**
	 * Returns a file path relative to PATH_site or getIndpEnv('TYPO3_SITE_URL').
	 *
	 * @return	string		Relative path to file
	 */
	function GetPathWebRelative ()
	{
		return \Cmp3\System\Files::MakeFilePathWebRelative($this->strAbsFilePath);
	}


	/**
	 * Returns an absolute file path
	 *
	 * @return	string		Absolute path to file
	 */
	function GetPathAbsolute ()
	{
		return $this->strAbsFilePath;
	}





	/*******************************
	 *
	 * Url/Download related
	 *
	 *******************************/


	/**
	 * Returns a URL that can be used eg. for direct linking.
	 *
	 * @return	string		URL to file
	 */
	function GetURL ()
	{
		$file_url = false;

		if ($this->objDownloadHandler)
			$file_url = $this->objDownloadHandler->MakeDownloadUrl($this, false);
		if ($file_url === false)
			$file_url = \Cmp3\Uri\Url::MakeAbsoluteUrl($this->GetPathWebRelative());

		return $file_url;
	}


	/**
	 * Returns a URL that can be used for direct download.
	 * The download might be done using a wrapper to send the right HTTP headers
	 *
	 * @return	string		URL to file or a download wrapper
	 */
	function GetDownloadURL ()
	{
		$file_url = false;

		if ($this->objDownloadHandler)
			$file_url = $this->objDownloadHandler->MakeDownloadUrl($this, true);
		if ($file_url === false)
			$file_url = \Cmp3\Uri\Url::MakeAbsoluteUrl($this->GetPathWebRelative());

		return $file_url;
	}



	/**
	 * Returns the object which handles downloads - which might be secured
	 *
	 * @return	\Cmp3\Files\FileDownloadUrl
	 */
	public function Download ()
	{
		if (!$this->objDownloadHandler) {
			$this->SetDownloadHandler(true);
		}

		return $this->objDownloadHandler;
	}



	/**
	 * Sets the object which handles downloads - which might be secured
	 *
	 * @return	\Cmp3\Files\FileDownloadUrl
	 */
	public function SetDownloadHandler($objDownloadHandler)
	{
		if ($objDownloadHandler === true)
			$this->objDownloadHandler = \Cmp3\System\Files::GetDownloadHandler();
		elseif($objDownloadHandler === false)
			$this->objDownloadHandler = $objDownloadHandler;
		else
			$this->objDownloadHandler = $objDownloadHandler;

		#TODO test \Cmp3\Files\FileDownloadUrl -> Exception

		return $this->objDownloadHandler;
	}



	/*************************
	 *
	 * Properties
	 *
	 *************************/


	/**
	 * Adds a property to the user which can be any kind of data
	 *
	 * @param $propertyData any data to be added to the user object
	 * @param $propertyID The property id which can be any identifier string
	 * @return mixed
	 */
	public function AddProperty($propertyData, $propertyID)
	{
		$this->properties[$propertyID] = $propertyData;
		return $this->properties[$propertyID];
	}


	/**
	 * Removes the property with id $propertyID
	 *
	 * @param $propertyID The property id which can be any identifier string
	 * @return void
	 */
	public function RemoveProperty($propertyID)
	{
		unset($this->properties[$propertyID]);
	}



	/**
	 * Defines the namespace to get property classes from the registry
	 * @var unknown_type
	 */
	protected $_strPropertyNamespace = '\Cmp3\Files\File';


	/**
	 * Gives access to the property data with id $propertyID
	 *
	 * @param $propertyID The property id which can be any identifier string
	 * @return mixed
	 * @throws Excpetion
	 */
	public function Property($propertyID)
	{
		if (!array_key_exists($propertyID, $this->properties))  {

			if ($strClassProperty = \Cmp3\Registry::Get($this->_strPropertyNamespace . ':Property:'.$propertyID)) {
				$this->properties[$propertyID] = new $strClassProperty($this);

			} else {
				throw new \Cmp3\UndefinedGetPropertyException($propertyID );
			}
		}
		return $this->properties[$propertyID];
	}



	/*******************************
	 *
	 * File operations
	 *
	 *******************************/



	/**
	 * Attempts to set the access and modification times of the file to the value given in time . Note that the access time is always modified, regardless of the number of parameters.
	 * If the file does not exist, it will be created.
	 *
	 * @param integer $time The touch time. If time  is not supplied, the current system time is used.
	 * @param integer $atime If present, the access time of the given filename is set to the value of atime . Otherwise, it is set to time .
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function Touch($time = null, $atime = null)
	{
		$this->_ClearError();

		$intErrorLevel = error_reporting(0);
		set_error_handler(array('\Cmp3\Files\File', 'HandleError'));

		#@todo use DAM if available?

		$returnValue = touch($this->strAbsFilePath, $time, $atime);
		if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
			clearstatcache(true, $this->strAbsFilePath);
		} else {
			clearstatcache();
		}

		if (class_exists('\t3lib_div', false)) {
			\t3lib_div::fixPermissions($this->strAbsFilePath);
		}

		restore_error_handler();
		error_reporting($intErrorLevel);

		$this->strDataCacheArray = array();

		return $returnValue;
	}


	/**
	 * Attempts to set the access and modification times of the file to the value given in time . Note that the access time is always modified, regardless of the number of parameters.
	 * If the file does not exist, it will be created.
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function Delete()
	{
		$this->_ClearError();

		$intErrorLevel = error_reporting(0);
		set_error_handler(array('\Cmp3\Files\File', 'HandleError'));

		$returnValue = unlink($this->strAbsFilePath);

		restore_error_handler();
		error_reporting($intErrorLevel);

		$this->strDataCacheArray = array();

		return $returnValue;
	}


	/**
	 * Attempts to rename a file
	 *
	 * @param string $strNewName A new file name can be given or a new path which result in moving the file
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function Rename($strNewName)
	{
		// if the new name has no path we use the old one
		$strDestinationPath = '';
		if ($strNewName == basename($strNewName))
			$strDestinationPath = $this->AbsoluteDirname;

		$this->_ClearError();

		$intErrorLevel = error_reporting(0);
		set_error_handler(array('\Cmp3\Files\File', 'HandleError'));

		$returnValue = rename($this->strAbsFilePath, $strDestinationPath.$strNewName);


		// Workaround - file get's copied on mount but old file will not be removed for some unknown reason
		if (!$returnValue AND file_exists($strDestinationPath.$strNewName) AND file_exists($this->strAbsFilePath)) {
			unlink($this->strAbsFilePath);
			$returnValue = true;
		}

		restore_error_handler();
		error_reporting($intErrorLevel);

		if ($returnValue)
			$this->_SetFilename($strDestinationPath.$strNewName);

		$this->strDataCacheArray = array();

		return $returnValue;
	}


	/**
	 * Attempts to move a file in another folder
	 *
	 * @param string $strDestinationPath This is the destination path to move the file to
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function Move($strDestinationPath, $blnOverwrite=false)
	{
		$this->_ClearError();

		if (!@is_dir($strDestinationPath)) {
			#@todo use an error code with sense
			self::$intLastErrorCode = 100;
			self::$strLastErrorMessage = 'Destination path is not a directory!';
			return false;
		}

		if ($blnOverwrite AND file_exists($strDestinationPath.'/'.$this->Name)) {
			unlink($strDestinationPath.'/'.$this->Name);
		}

		$returnValue = $this->Rename($strDestinationPath.'/'.$this->Name);

		return $returnValue;
	}


	/**
	 * Attempts to copy a file
	 * Warning: If the destination file already exists, it will be overwritten.
	 *
	 * @param string|\Cmp3\Files\File $strDestinationPath A new file name can be given or a new path
	 * @return \Cmp3\Files\File|FALSE Returns \Cmp3\Files\File object of the new file on success or FALSE on failure.
	 */
	public function Copy($strDestinationPath)
	{
		$objSource = null;

		if ($strDestinationPath instanceof \Cmp3\Files\File) {
			$objSource = $strDestinationPath;
			$strDestinationPath = $objSource->AbsolutePath;

		} else {
			$strPathPartsArray = \Cmp3\System\Files::_split_fileref($strDestinationPath);

			// if the destination is a directory we use the old name
			if (@is_dir($strDestinationPath))
				$strDestinationPath = $strDestinationPath.'/'.$this->Name;
			// if filename is set but not path we use the current path
			elseif (!$strPathPartsArray['path'] AND $strPathPartsArray['file'])
				$strDestinationPath = $this->AbsoluteDirname . $strPathPartsArray['file'];
		}

		$this->_ClearError();

		$intErrorLevel = error_reporting(0);
		set_error_handler(array('\Cmp3\Files\File', 'HandleError'));

		$returnValue = copy($this->strAbsFilePath, $strDestinationPath);

		if (class_exists('\t3lib_div', false)) {
			\t3lib_div::fixPermissions($strDestinationPath);
		}

		restore_error_handler();
		error_reporting($intErrorLevel);

		if ($returnValue) {
			if ($objSource) {
				$objNewFile = $objSource;
			} else {
				$objNewFile = clone $this;
				$objNewFile->_SetFilename($strDestinationPath.$strNewName);
			}
			$objNewFile->Changed();

			return $objNewFile;
		}

		return $returnValue;
	}





	/*******************************
	 *
	 * File Content related
	 *
	 *******************************/


	/**
	 * Reads entire file and processes the content if a content processor object is registred with SetReadContentProcessor().
	 *
	 * @param int $offset The offset where the reading starts.
	 * @param int $maxlen Maximum length of data read.
	 * @return FALSE|mixed
	 */
	public function ReadContent( $offset = null, $maxlen = null )
	{
		$this->_ClearError();

		$intErrorLevel = error_reporting(0);
		set_error_handler(array('\Cmp3\Files\File', 'HandleError'));

		if (!$offset) {
			$offset = 0;
		}
		if (!$maxlen) {
			$strContent = file_get_contents($this->strAbsFilePath, $flags= 0, $context=null, $offset);
		} else {
			$strContent = file_get_contents($this->strAbsFilePath, $flags= 0, $context=null, $offset, $maxlen);
		}

		restore_error_handler();
		error_reporting($intErrorLevel);

		if ($strContent === false) {
			throw new \Cmp3\Files\Exception($this->GetLastErrorMessage() . " ({$this->strAbsFilePath})", $this->GetLastErrorCode());
		}

		if ($strContent !== false AND $this->objReadContentProcessor) {
			#FIXME want's contentobject
			$strContent = $this->objReadContentProcessor->Process($strContent);
		}

		return $strContent;
	}


	/**
	 * @var \Cmp3\ContentProcessing\ProcessorInterface
	 */
	protected $objReadContentProcessor;

	/**
	 * @var \Cmp3\ContentProcessing\ProcessorInterface
	 */
	protected $objWriteContentProcessor;


	/**
	 * Writes entire file.
	 * If a content processor object is registred with SetReadContentProcessor() the data will be processed in beforehand.
	 *
	 * Data can be appended using the FILE_APPEND flag
	 *
	 * @param mixed $strData The data to write. Can be either a string, an array or a stream resource.
	 * @param int $flags The value of flags  can be any combination of FILE_APPEND, LOCK_EX (with some restrictions), joined with the binary OR (|) operator. See http://de3.php.net/manual/en/function.file-put-contents.php
	 * @return FALSE|integer bytes written
	 */
	public function WriteContent($strData, $intFlags= 0)
	{
		$this->_ClearError();

		if ($this->objWriteContentProcessor) {
			#FIXME want's contentobject
			$strData = $this->objWriteContentProcessor->Process($strData);
		}

		$intErrorLevel = error_reporting(0);
		set_error_handler(array('\Cmp3\Files\File', 'HandleError'));

		$intBytesWritten = file_put_contents($this->strAbsFilePath, $strData, $intFlags, $context=null);

		if (class_exists('\t3lib_div', false)) {
			\t3lib_div::fixPermissions($this->strAbsFilePath);
		}

		restore_error_handler();
		error_reporting($intErrorLevel);

		if ($intBytesWritten === false) {
			throw new \Cmp3\Files\Exception($this->GetLastErrorMessage() . " ({$this->strAbsFilePath})", $this->GetLastErrorCode());
		}

		return $intBytesWritten;
	}


	/**
	 * Set a content processor for reading files
	 *
	 * @param \Cmp3\ContentProcessing\ProcessorInterface $objContentProcessor
	 * @return void
	 */
	public function SetReadContentProcessor(\Cmp3\ContentProcessing\ProcessorInterface $objContentProcessor=null)
	{
		$this->objReadContentProcessor = $objContentProcessor;
	}


	/**
	 * Set a content processor for writing files
	 *
	 * @param \Cmp3\ContentProcessing\ProcessorInterface $objContentProcessor
	 * @return void
	 */
	public function SetWriteContentProcessor(\Cmp3\ContentProcessing\ProcessorInterface $objContentProcessor=null)
	{
		$this->objWriteContentProcessor = $objContentProcessor;
	}








	############# TODO this is beta ###############



#TODO create decorator which provides stream content?
# context - A valid context resource created with stream_context_create().
# or better inject an \Next\Stream object?
# or function SetStreamContext()


//@see http://cvs.php.net/viewvc.cgi/pecl/spl/internal/splfileobject.inc?revision=1.1.2.2.2.3&view=co

	protected $fp;


	/**
	 * Open file for read/write
	 *
	 * @see http://de.php.net/manual/en/function.fopen.php
	 *
	 * @param string $open_mode The file open mode
	 * @param resource $context A stream context
	 * @return resource
	 * @throw \Cmp3\Files\Exception   If file cannot be opened (e.g. insufficient access rights).
	 */
	public function Open($open_mode = 'r', $context = NULL)
	{
		$this->_ClearError();

		$intErrorLevel = error_reporting(0);
		set_error_handler(array('\Cmp3\Files\File', 'HandleError'));

		if (is_resource($context)) {
			$this->fp = fopen($this->strAbsFilePath, $open_mode, FALSE, $context);
		} else {
			$this->fp = fopen($this->strAbsFilePath, $open_mode);
		}

		if (!$this->fp)
		{
			throw new \Cmp3\Files\Exception("Cannot open file {$this->strAbsFilePath}");
		}

		if (class_exists('\t3lib_div', false)) {
			\t3lib_div::fixPermissions($this->strAbsFilePath);
		}

		restore_error_handler();
		error_reporting($intErrorLevel);

		return $this->fp;
	}
#TODO Close()
#TODO use more error handling for return values
#TODO Naming?

	/**
	 * Returns internally used file pointer
	 * @return resource
	 */
	public function GetFileResource()
	{
		return $this->fp;
	}


	/**
	 * Tests for end-of-file on a file
	 *
	 * @see http://de.php.net/manual/en/function.feof.php
	 *
	 * @return boolean TRUE if the file pointer is at EOF or an error occurs (including socket timeout); otherwise returns FALSE.
	 */
	public function isEnd()
	{
		if (!$this->fp) {
			throw new \Cmp3\Files\NoStreamException();
		}
		return eof($this->fp);
	}


	/**
	 * Portable advisory file locking
	 *
	 * @see http://de.php.net/manual/en/function.flock.php
	 *
	 * @param operation lock operation (LOCK_SH, LOCK_EX, LOCK_UN, LOCK_NB)
	 * @return $wouldblock  whether the operation would block
	 */
	public function Lock($operation, &$wouldblock)
	{
		if (!$this->fp) {
			throw new \Cmp3\Files\NoStreamException();
		}
		return flock($this->fp, $operation, $wouldblock);
	}


	/**
	 * Flush current data
	 *
	 * @see http://de.php.net/manual/en/function.fflush.php
	 *
	 * @return success or failure
	 */
	public function Flush()
	{
		if (!$this->fp) {
			throw new \Cmp3\Files\NoStreamException();
		}
		return fflush($this->fp);
	}


	/**
	 * Returns the current position of the file read/write pointer
	 *
	 * @see http://de.php.net/manual/en/function.ftell.php
	 *
	 * @return current file position
	 */
	public function Tell()
	{
		if (!$this->fp) {
			throw new \Cmp3\Files\NoStreamException();
		}
		return ftell($this->fp);
	}


	/**
	 * Seeks on a file pointer
	 *
	 * @see http://de.php.net/manual/en/function.fseek.php
	 *
	 * @param pos new file position
	 * @param whence seek method (SEEK_SET, SEEK_CUR, SEEK_END)
	 * @return Upon success, returns 0; otherwise, returns -1. Note that seeking past EOF is not considered an error.
	 */
	public function Seek($pos, $whence = SEEK_SET)
	{
		if (!$this->fp) {
			throw new \Cmp3\Files\NoStreamException();
		}
		return fseek($this->fp, $pos, $whence);
	}


	/**
	 * Gets line from file
	 *
	 * @see http://de.php.net/manual/en/function.fgets.php
	 *
	 * @return next line from stream
	 */
	public function GetLine()
	{
		if (!$this->fp) {
			throw new \Cmp3\Files\NoStreamException();
		}
		return fgets($this->fp, 2000);

	}


	/**
	 * Gets character from file
	 *
	 * @see http://de.php.net/manual/en/function.fgetc.php
	 *
	 * @return next char from file
	 */
	public function GetChar()
	{
		if (!$this->fp) {
			throw new \Cmp3\Files\NoStreamException();
		}
		return fgetc($this->fp);
	}


	/**
	 * Read and return remaining part of stream
	 *
	 * @see http://de.php.net/manual/en/function.fpassthru.php
	 *
	 * @return size of remaining part passed through
	 */
	public function PassThru()
	{
		if (!$this->fp) {
			# throw new \Cmp3\Files\NoStreamException();
			return readfile($this->strAbsFilePath);
		}
		return fpassthru($this->fp);
	}


	/**
	 * Binary-safe file write
	 *
	 * @see http://de.php.net/manual/en/function.fwrite.php
	 *
	 * @param $str to write
	 * @param $length maximum line length to write
	 */
	public function Write($str, $length = NULL)
	{
		if (!$this->fp) {
			throw new \Cmp3\Files\NoStreamException();
		}
		return fwrite($this->fp, $str, $length);
	}


	/**
	 * Gets information about a file using an open file pointer
	 *
	 * @see http://de.php.net/manual/en/function.fstat.php
	 *
	 * @return array of file stat information
	 */
	public function Stat()
	{
		if (!$this->fp) {
			throw new \Cmp3\Files\NoStreamException();
		}
		return fstat($this->fp);
	}


	/**
	 * Truncates a file to a given length
	 *
	 * @see http://de.php.net/manual/en/function.ftruncate.php
	 *
	 * @param $size new size to truncate file to
	 */
	public function Truncate($size)
	{
		if (!$this->fp) {
			throw new \Cmp3\Files\NoStreamException();
		}
		return ftruncate($this->fp, $size);
	}

#TODO implement decorator for line based files etc - see SplFileObject?



	############################




	/*******************************
	 *
	 * Output related
	 *
	 *******************************/


	/**
	 * Returns an absolute file path
	 *
	 * @return	string	The file size in a formatted way like 45 kb
	 */
	function GetSizeFormatted()
	{
		return is_null($this->Size) ? '' : \Cmp3\String\Format::FormatFileSize(intval($this->Size));
	}






	/*******************************
	 *
	 * Error handling
	 *
	 *******************************/


	/**
	* Clears any previous error
	*
	* @return void
	*/
	protected function _ClearError()
	{
		self::$intLastErrorCode = false;
		self::$strLastErrorMessage = '';
	}


	/**
	 * Returns last error code which might be created by PHP of the object itself
	 *
	 * @return integer
	 */
	public function GetLastErrorCode()
	{
		return self::$intLastErrorCode;
	}


	/**
	 * Returns last error message which might be created by PHP of the object itself
	 *
	 * @return string
	 */
	public function GetLastErrorMessage()
	{
		return self::$strLastErrorMessage;
	}


	/**
	 * This is a dummy function to make php silent with some file functions
	 * @param $__exc_errno
	 * @param $__exc_errstr
	 * @param $__exc_errfile
	 * @param $__exc_errline
	 * @return void
	 */
	public static function HandleError($__exc_errno, $__exc_errstr, $__exc_errfile, $__exc_errline)
	{
		// If a command is called with "@", then we should return
		#if (error_reporting() == 0)
		#	return;

		list ($a, $b) = explode(':', $__exc_errstr, 2);

		self::$intLastErrorCode = $__exc_errno;
		self::$strLastErrorMessage = ($b ? $b : $a);

		return;
	}

}

