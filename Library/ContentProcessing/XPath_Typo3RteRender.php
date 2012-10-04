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
 * Content processors to convert a dom node with typo3 rte format to xhtml
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class XPath_Typo3RteRender extends XPathAbstract {


	/**
	 * Processes a DOM node
	 *
	 * @param DOMNode $objNode
	 */
	protected function ProcessNode($objNode)
	{
		$strFieldValue = $objNode->nodeValue;

		if (!$strFieldValue) {
			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' No content in this node');
			return;
		}

		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Processing rte to html');


		// BE or FE that's the question here
		// is FE using t3lib_parsehtml_proc too?
		$strFieldValue = $this->ProcessHtmlRendering($strFieldValue);
		// for some reasons entities are created
		$strFieldValue = \Cmp3\Xml\Tools::DecodeHtmlEntities($strFieldValue);

		// $strFieldValue = $this->ProcessRteTransformation($strFieldValue);

		\Cmp3\Xml\Tools::ReplaceTextFieldWithRichText($objNode, $strFieldValue);

		$this->blnHasModified = true;
	}




	/**
	 * Transforms an incoming html string into xhtml based on the application logic of the TYPO3 parseFunc function
	 * Common configuration used from res/richtext/parsefunc_setup.txt
	 *
	 * @param 	string	HTML input
	 * @return 	string 	XHTML output
	 */
	protected function ProcessHtmlRendering ($strContent)
	{
		static $objParseHTML;

		if (!$GLOBALS['CMP_HTML_CONFIG']) {

			require_once (PATH_t3lib.'class.t3lib_tsparser.php');

			$file = PATH_cmp3.'res/richtext/parsefunc_setup.txt';
			$tsConfString = \t3lib_div::getUrl($file);


			// 1. Read transform config and convert to TSconfig array
			$parseObj = \t3lib_div::makeInstance('t3lib_TSparser');
			$parseObj->parse($tsConfString);
			$GLOBALS['CMP_HTML_CONFIG'] = $parseObj->setup;
		}

		if (!is_object($objParseHTML)) {
			require_once (PATH_tslib.'class.tslib_content.php');
			// Initialize transformation object
			$objParseHTML = \t3lib_div::makeInstance('tslib_cObj');
			$objParseHTML->start(array(), 'tt_content');
		}

		$strContent = $objParseHTML->stdWrap($strContent, $GLOBALS['CMP_HTML_CONFIG']['proc.']);

		// use tidy to be safe
		$strContent = \Cmp3\Xml\Tools::CleanHtml($strContent);

		return $strContent;
	}


	/**
	 * UNUSED
	 *
	 * Transforms an incoming html string into xhtml based on the application logic of the TYPO3 parse html class
	 * Common configuration used from res/richtext/rteproc_setup.txt
	 *
	 * @param 	string	HTML input
	 * @return 	string 	XHTML output
	 */
	protected function ProcessRteTransformation ($strContent)
	{
		static $objParseHTML;

		if (!$GLOBALS['CMP_RTE_CONFIG']) {

			require_once (PATH_t3lib.'class.t3lib_parsehtml_proc.php');
			require_once (PATH_t3lib.'class.t3lib_tsparser.php');

			$file = PATH_cmp3.'res/richtext/rteproc_setup.txt';
			$tsConfString = \t3lib_div::getUrl($file);


			// 1. Read transform config and convert to TSconfig array
			$parseObj = \t3lib_div::makeInstance('t3lib_TSparser');
			$parseObj->parse($tsConfString);
			$GLOBALS['CMP_RTE_CONFIG'] = $parseObj->setup;

		}

		if (!is_object($objParseHTML)) {
			// Initialize transformation object
			$objParseHTML = \t3lib_div::makeInstance('t3lib_parsehtml_proc');
			$objParseHTML->init('dummy:dummy', 0);
			$objParseHTML->setRelPath('uploads/rte/');
		}

		//
		// Perform transformation:
		// Keyword: "rte" means direction from db to rte, which is to HTML
		$strContent = $objParseHTML->RTE_transform($strContent, array(), 'rte', $GLOBALS['CMP_RTE_CONFIG']);
		//		$strContent = $objParseHTML->RTE_transform($strContent, array(), 'db', $GLOBALS['CMP_RTE_CONFIG']);
		//		$strContent = $objParseHTML->RTE_transform($strContent, array(), 'rte', $GLOBALS['CMP_RTE_CONFIG']);


		// use tidy to be safe
		$strContent = \Cmp3\Xml\Tools::CleanHtml($strContent);

		return $strContent;
	}

}
