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
 * Content processors which generates meta data for images like height and width
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class XPath_ImageMeta extends XPathAbstract {


	/**
	 * Processes a DOM node
	 *
	 * @param DOMNode $objNode
	 */
	protected function ProcessNode($objNode)
	{
		$strUrl = $objNode->getElementsByTagName('value')->item(0)->nodeValue;

		if (!$strUrl) {
			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' No image found to get meta data for');
			return;
		}

		list($width, $height, $type) = getimagesize($strUrl);

		$objMeta = $objNode->getElementsByTagName('meta')->item(0);

		$objImageWidth = $objNode->ownerDocument->createElement('image_width', $width);
		$objImageHeight = $objNode->ownerDocument->createElement('image_height', $height);
		$objFormFactor = $objNode->ownerDocument->createElement('form_factor', $width/$height);
		$objMeta->appendChild($objImageWidth);
		$objMeta->appendChild($objImageHeight);
		$objMeta->appendChild($objFormFactor);

		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Added  meta data for image: ' . $strUrl);
		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' image_width: '  . $width);
		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' image_height: ' . $height);
		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' form_factor: '  . $width/$height);

		$this->blnHasModified = true;
	}




}
