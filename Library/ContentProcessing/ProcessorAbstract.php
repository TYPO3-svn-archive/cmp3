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
 * @subpackage ContentProcessing
 * @package    CMP3
 * @copyright  Copyright (c) 2008 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\ContentProcessing;



/**
 * Base class of content processors which are used to modifiy string content.
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 *
 * @property-read boolean $hasModified
 * @property mixed $Data data object which might be used for processing
 */
abstract class ProcessorAbstract extends \Cmp3\BaseConfig implements ProcessorInterface {

	/**
	 * Indicates if the processor modified the content in some way
	 * @var boolean
	 */
	protected $blnHasModified = false;

	/**
	 * additional data object
	 * @var mixed
	 */
	protected $objData;


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

			case 'hasModified':
					return $this->blnHasModified;

			case 'Data':
					return $this->objData;

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

			case 'Data':
				return $this->objData = $mixValue;

			default:
				return parent::__set($strName, $mixValue);
		}
	}


	/**
	 * Processes a content object
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface $objContent)
	{
		$this->blnHasModified = true;
		return null;
	}


	/**
	 * Returns hash which can be used as identifier for caching purposes
	 *
	 * @return string hash
	 */
	public function GetConfigHash ()
	{
		$hash = md5(serialize($this->objConfig));
		if ($this->objDataRow) {
			$hash = md5($hash.$this->objDataRow->TableName . '_' . $this->objDataRow->uid);
		}
		return $hash;
	}
}









