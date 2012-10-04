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
 * @subpackage Source
 * @package    CMP3
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\Source;



/**
 * {@inheritdoc}
 *
 *
 * Container for a content source which includes:
 * - configuration
 * - file fetcher
 * - content processor
 *
 * After all it provides a content object ready for conversion eg. to PDF
 *
 * @property-read \Cmp3\Fetcher\FetcherInterface $Fetcher
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Source
 * @package    CMP3
 */
class Fetcher extends SourceAbstract {


	/**
	 * @var \Cmp3\Fetcher\FetcherInterface
	 */
	protected $objFetcher;


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

			case 'Fetcher':
				if (is_null($this->objFetcher)) {
					$strFetcher = $this->objConfig->GetValue('fetcher');
					$objFetcherConfig = $this->objConfig->GetProxy('fetcher');
					$objProperties = array();
					$objProperties['Logger'] = $this->objLogger;
					$objProperties['Config'] = $objFetcherConfig;
					$this->objFetcher = new $strFetcher($objProperties);
				}
				return $this->objFetcher;

			case 'Type':
				// possibly the content isn't fetched already
				if ($this->objContent) {
	      			return $this->objContent->Type;
				}
				// we may have a fetcher with more information
				if ($this->objFetcher) {
					return $this->objFetcher->GetContentType();
				}
				return strtolower($this->objConfig->GetValue('type'));

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

			case 'Fetcher':
				return $this->objFetcher = $mixValue;

			default:
				return parent::__set($strName, $mixValue);
		}
	}


	/**
	 * This actually retrieves the content and sets objContent
	 *
	 * @return void
	 */
	protected function FetchContent()
	{
		$this->objContent = $this->Fetcher->GetContent();
	}

}



