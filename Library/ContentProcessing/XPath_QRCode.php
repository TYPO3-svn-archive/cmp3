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
 * Content processors which generates qrcodes
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class XPath_QRCode extends XPathAbstract {


	/**
	 * Processes a DOM node
	 *
	 * @param DOMNode $objNode
	 */
	protected function ProcessNode($objNode)
	{
		$strOutputPath = PATH_site . 'typo3temp/pics/';

		$strUrl = $objNode->nodeValue;

		if (!$strUrl) {
			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' No Url found for QRCode creation');
			return;
		}
		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Create QRCode for url: ' . $strUrl);

		$strContent = \Cmp3\Uri\UriEncode::Url($strUrl);

		$objCache = new \Cmp3\Cache\HashFile($strOutputPath, 'png');
		$objCache->Debug = $this->objLogger;

		$code_params = array(
			'text'            => $strContent,
			'backgroundColor' => $this->objConfig->GetValue('backgroundColor', '#FFFFFF'),
			'foreColor' => $this->objConfig->GetValue('foreColor', '#000000'),
			'padding' => $this->objConfig->GetInteger('padding', 0),  //array(10,5,10,5),
			'moduleSize' => $this->objConfig->GetInteger('moduleSize', 4),
			'ecclevel' => $this->objConfig->GetInteger('ecclevel', 'M'),
		);

		$objCache->SetTag('params', serialize($code_params));

		if (!$objCache->isValid()) {

			$renderer_params = array('imageType' => 'png', 'sendResult' => false);

			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Create QRCode for url: ' . $strContent);

			$res = \Zend_Matrixcode::render('qrcode', $code_params, 'image', $renderer_params);
			imagepng($res, $objCache->GetFilePath());

		}


		$objNode->nodeValue = $objCache->GetFilePath();

		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Create QRCode image: ' . $objNode->nodeValue);

		$this->blnHasModified = true;
	}




}
