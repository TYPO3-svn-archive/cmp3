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
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\Fetcher;


/**
 * {@inheritdoc}
 *
 * @todo doc
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Fetcher
 * @package    CMP3
 */
class Memory extends FetcherAbstract {

	protected $strContent = null;

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

			case 'Content':
				$this->strContent = $mixValue;

			default:
				return parent::__set($strName, $mixValue);
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function GetContent()
	{
		$objProperties = array();
		$objProperties['Logger'] = $this->objLogger;
		$objContent = new \Cmp3\Content\Content($objProperties);
		$objContent->SetData($this->strContent, $this->GetContentType());

		return $objContent;
	}

}
