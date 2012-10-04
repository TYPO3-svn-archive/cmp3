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
 * @subpackage Content
 * @package    CMP3
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\Content;



/**
 * {@inheritdoc}
 *
 * @author	Rene Fritz <r.fritz@bitmotion.de>
 * @package	CMP3
 * @subpackage	Content
 *
 * @property mixed $Data
 * @property mixed $Type Content type
 * @property ContentMeta $Meta This holds any additional data which might be needed by any later processing.
 * @property-read \Cmp3\Files\File $File
 */
class Content extends \Cmp3\BaseLogger implements ContentInterface {


	/**
	 * @var string
	 */
	protected $mixData = null;

	/**
	 * This holds any additional data which might be needed by any later processing.
	 * An example is base url.
	 * The keys to be used are not fixed but defined as needed.
	 *
	 * @var ContentMeta
	 */
	protected $objMeta = null;

	/**
	 * @var \Cmp3\Files\File
	 */
	protected $objDataFile = null;

	/**
	 *
	 * @var ContentType
	 */
	protected $strContentType = \Cmp3\Content\ContentType::UNKNOWN;


	/**
	 * Constructor
	 *
	 */
	public function Construct ()
	{
		$this->objMeta = new ContentMeta;
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
	      		return $this->strContentType;

			case 'Meta':
	      		return $this->objMeta;

			case 'Data':
	      		return $this->GetData();

			case 'File':
	      		return $this->GetDataFile();

			default:
				return parent::__get($strName);
		}
	}


	/**
	 * Override method to perform a property "Set"
	 * This will set the property $strName with $mixValue
	 *
	 * @param string $strName Name of the property to get
	 * @param string $mixValue Value of the property to set
	 * @return mixed
	 */
	public function __set($strName, $mixValue)
	{
		switch ($strName) {

			case 'Type':
	      		return $this->strContentType = $mixValue;

			case 'Data':
	      		return $this->mixData = $mixValue;

			case 'Meta':
	      		return $this->objMeta = $mixValue;

			default:
				return parent::__set($strName, $mixValue);
		}
	}


	/**
	 * Sets the content as string or a file object
	 *
	 * @param string|\Cmp3\Files\File $mixData
	 * @param bool|\Cmp3\Content\ContentType $strContentType
	 */
	public function SetData($mixData, $strContentType=false)
	{
		if ($mixData instanceof \Cmp3\Files\File) {
			$this->objDataFile = $mixData;
			$this->mixData = null;
		} else {
			$this->objDataFile = null;
			$this->mixData = $mixData;
		}
		if ($strContentType!==false) {
			$this->strContentType = $strContentType;
		}
	}


	/**
	 * Returns the content as string
	 *
	 * @throws \Cmp3\Exception
	 * @return mixed the content
	 */
	public function GetData()
	{
		if (!is_null($this->mixData)) {
			return $this->mixData;
		} elseif (!is_null($this->objDataFile)) {
			return $this->objDataFile->ReadContent();
		} else {
			throw new \Cmp3\Exception(__METHOD__ . ' Content not set!');
		}
	}


	/**
	 * Returns the content as file object
	 *
	 * @throws \Cmp3\Exception
	 * @return \Cmp3\Files\File the content file
	 */
	public function GetDataFile()
	{
		if (is_null($this->objDataFile) AND is_null($this->mixData)) {
			throw new \Cmp3\Exception(__METHOD__ . ' Content not set!');
		}

		if (is_null($this->objDataFile)) {
			$this->objDataFile = \Cmp3\System\Files::GetTemp(str_replace('\\', '_', __CLASS__), '.'.$this->Type, \Cmp3\Cmp3::$TempPath);
			$this->objDataFile->WriteContent((string)$this->mixData);
			$this->mixData = null;
		}

		return $this->objDataFile;
	}

}




