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



namespace Cmp3\Source;



/**
 * {@inheritdoc}
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Source
 * @package    CMP3
 *
 * @property-write \Cmp3\Data\MetaInterface[] $DataRowMetaArray
 */
class DataRowMetaXml extends SourceAbstract {


	/**
	 *
	 * @var \Cmp3\Config\ConfigInterface
	 */
	protected $objConfig;

	/**
	 *
	 * @var \Cmp3\Data\MetaInterface[]
	 */
	protected $objDataRowMetaArray = array();

	/**
	 * This is the xml document the source should add nodes to
	 * @var \Cmp3\Xml\DocumentCmp3
	 */
	protected $objDocument;


	/**
	 * Override method to perform a property "Set"
	 * This will set the property $strName with $mixValue
	 *
	 * @param string $strName Name of the property to get
	 * @param string $mixValue Value of the property to set
	 * @throws \Cmp3\WrongTypeException
	 * @return mixed
	 */
	public function __set($strName, $mixValue)
	{
		switch ($strName) {

			case 'DataRowMetaArray':
				if (!is_array($mixValue)) {
					throw new \Cmp3\WrongTypeException($strName, $mixValue, 'array');
				}
				return $this->objDataRowMetaArray = $mixValue;

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
		$this->objDocument = new \Cmp3\Xml\DocumentCmp3();

		$strNodeType = \Cmp3\Xml\DocumentNodeType::GROUP;
		$strNodeSubtype = \Cmp3\Xml\DocumentNodeSubtype::RECORDS;

		$this->objDocument->AddNode($strNodeType, $strNodeSubtype);

		/* @var $objDataRowMeta \Cmp3\Data\MetaInterface */
		foreach ($this->objDataRowMetaArray as $objDataRowMeta) {
			$objRecordRenderXml = new \Cmp3\Xml\RecordRender($objDataRowMeta);
			$this->objDocument->AddRecord($objRecordRenderXml->GetXml());
		}


		$objProperties = array();
		$objProperties['Logger'] = $this->objLogger;
		$this->objContent = new \Cmp3\Content\Content($objProperties);
		$this->objContent->SetData($this->objDocument, \Cmp3\Content\ContentType::CMP3XML);
	}

}



