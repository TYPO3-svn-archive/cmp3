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
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\ContentProcessing;


/**
 * Content processors base class for xml xpath processing
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
abstract class XPathAbstract extends ProcessorAbstract {


	/**
	 * Processes a content object
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @return void
	 * @throws Exception
	 */
	public function Process (\Cmp3\Content\ContentInterface $objContent)
	{
		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Started content processor');

		if ($objXml = $objContent->GetData()) {

			$objXml = \Cmp3\XML\Tools::MakeXmlDom($objXml);

			$strXPath = $this->objConfig->GetValue('xpath');
			if (!$strXPath) {
				throw new Exception(__METHOD__ . " xpath not defined");
			}
			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Processes content using xpath: ' . $strXPath);

			$xp = new \DOMXPath($objXml);
			$objNodeArray = $xp->Query($strXPath);
			if ($objNodeArray->length) {
				if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Found matching nodes');
				foreach ($objNodeArray as $objNode) {
					$this->ProcessNode($objNode);
				}
			}else {
				if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' No matching nodes found');
			}

			if ($this->blnHasModified) {
				$objContent->SetData($objXml);
			}
		}
	}


	/**
	 * Processes a DOM node
	 *
	 * @param DOMNode $objNode
	 */
	protected function ProcessNode($objNode)
	{
		die(__METHOD__ . ' is not implemented!');

// 		$objNode->nodeName
// 		$objNode->nodeValue


		$this->blnHasModified = true;
	}




}
