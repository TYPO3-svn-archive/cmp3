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
 * DOMDocument that stores a record as XML in CMP3 format
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage XML
 * @package    CMP3
 */
class Record extends \DOMDocument
{
	/**
	 * The root node of this xml document
	 * @var \DOMElement
	 */
	protected $record;

	/**
	 *
	 * Constructor, Calls parent and sets root node
	 *
	 * @param string $strLang
	 * @param string $strType
	 * @param string $strSubType
	 * @internal param string $version
	 * @internal param string $encoding
	 */
	public function __construct( $strLang, $strType, $strSubType='' )
	{
		parent::__construct('1.0', 'UTF-8');

		// format the created XML
		$this->formatOutput = true;

		// <record type="tt_content" subtype="text">

		#$objRoot = parent::createElement('record');
		$objRoot = parent::createElementNS('http://www.Bitmotion/cmp3/cmp3document', 'record');
		$objRoot->setAttribute('type', $strType);
		$objRoot->setAttribute('subtype', $strSubType);
		$objRoot->setAttribute('language', $strLang);
		$this->record = $this->appendChild($objRoot);
	}


	/**
	 * Returns the root node of the xml document
	 *
	 * @return \DOMElement
	 */
	public function GetRootNode()
	{
		return $this->record;
	}


	/**
	 * Adds an field as XML node
	 *
	 * @param    \Cmp3\Data\Field    $objDataField
	 * @return \DOMElement|\DOMNode
	 */
	public function AddField( $objDataField )
	{
		$objField = parent::createElement('field');
		$objField->setAttribute('name', $objDataField->Name);
		$objField->setAttribute('type', $objDataField->Type);
		$objField->setAttribute('format', $objDataField->Format);
		$this->record->appendChild($objField);

		$objFieldMeta = parent::createElement('meta');
		$objField->appendChild($objFieldMeta);

		$strContent = $objDataField->Content;



		if ($objDataField->Format == 'xml') {

			$objContentField = parent::createElement('value');
			// @todo regex
			$strContent = str_replace('<?xml version="1.0" encoding="utf-8" standalone="yes" ?>', '', $strContent);
			if ($strContent) {
				$objXmlElement = parent::createDocumentFragment();
				$objXmlElement->appendXML($strContent);
				$objContentField->appendChild($objXmlElement);
			}

		} else if ($objDataField->Format == 'rich') {

			$objField->setAttribute('format', 'rich');
			$objContentField = \Cmp3\Xml\Tools::CreateRichTextValueNode($this, $strContent);

		} else {
	 		if ($objDataField->Type == 'blob') {

	 			// we ignore blob data for now
				# $strContent = base64_encode($strContent);

				$strContent = '';
			}

			$objContentField = parent::createElement('value');
			if ($strContent) {
				// this will add the text escaped
				$objTextNode = parent::createTextNode($strContent);
				$objContentField->appendChild($objTextNode);
			}
		}

		$objField->appendChild($objContentField);

		return $objField;
	}

}



