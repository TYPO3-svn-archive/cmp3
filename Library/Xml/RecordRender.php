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
 * @subpackage XML
 * @package    CMP3
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\Xml;



/**
 * {@inheritdoc}
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage XML
 * @package    CMP3
 */
class RecordRender implements RecordRenderInterface {


	/**
	 * @var \Cmp3\Data\MetaInterface
	 */
	protected $objDataRowMeta;


	/**
	 * Constructor
	 *
	 * @param \Cmp3\Data\MetaInterface $objDataRowMeta
	 * @return \Cmp3\Xml\RecordRender
	 */
	public function __construct(\Cmp3\Data\MetaInterface $objDataRowMeta)
	{
		$this->objDataRowMeta = $objDataRowMeta;
	}


	/**
	 * Renders Xml of the given data
	 *
	 * @return Record
	 */
	public function GetXml()
	{
		//@see http://phpro.org/tutorials/Dynamically-Create-Menu-With-PHP-DOM.html
		$objXml = new Record($this->objDataRowMeta->Language, $this->objDataRowMeta->TableName, $this->objDataRowMeta->Type);

		$strFieldNameArray = $this->objDataRowMeta->DataFields;
		foreach ($strFieldNameArray as $strFieldName) {

			$objDataField = $this->objDataRowMeta->GetFieldDefinition($strFieldName);

			$objXml->AddField($objDataField);
		}

		return $objXml;
	}
}


