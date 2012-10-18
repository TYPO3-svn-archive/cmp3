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
 * @subpackage Xslt
 * @package    CMP3
 * @copyright  Copyright (c) 2012 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Xslt;


/**
 * Base class of xslt processors
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Xslt
 * @package    CMP3
 */
abstract class ProcessorAbstract implements ProcessorInterface {




	/**
	 * Returns the processed content
	 *
	 * @param string|\DOMDocument 	$content Content to be processed
	 * @param string|\DOMDocument 	$stylesheet Content to be processed
	 * @return string
	 */
	public function Process ($content, $stylesheet)
	{
		return false;
	}




	/***************************************
	 *
	 *   Helper
	 *
	 ***************************************/


	/**
	 * Returns a DOMDocument object from a string or a DOMDocument
	 *
	 * @param string|\DOMDocument 	$xml Content to be processed
	 * @return \DOMDocument
	 * @throws \Cmp3\Xslt\Exception
	 */
	protected function MakeXmlDom($xml)
	{
		if (is_object($xml)) {
			if ($xml instanceof \DOMDocument) {
				$dom_xml = $xml;

			} else {
				throw new Exception(__METHOD__ . ' Passed object is not a DOMDocument but ' . get_class($xml));
			}

		} elseif($xml) {

			/*
			 * Create DOM-Object from XML-Data
			*/
			$dom_xml = new \DOMDocument();
			$dom_xml->resolveExternals = true;
			$dom_xml->loadXML($xml);

		} else {
			throw new Exception(__METHOD__ . ' Passed xml is empty.');
		}

		return $dom_xml;
	}


	/**
	 *
	 * @param string|\DOMDocument 	$xsl_file DOMDocument or filename
	 * @return \DOMDocument
	 * @throws \Cmp3\Xslt\Exception
	 */
	protected function MakeXslDom($xsl_file)
	{
		if (is_object($xsl_file)) {
			if ($xsl_file instanceof \DOMDocument) {
				$dom_xsl = $xsl_file;

			} else {
				throw new Exception(__METHOD__ . ' Passed object is not a DOMDocument but ' . get_class($xsl_file));
			}

		} elseif(file_exists($xsl_file)) {

			/*
			 * Create DOM-Object from XSL-Data
			*/
			$dom_xsl = new \DOMDocument();
			$dom_xsl->resolveExternals = true;
			$dom_xsl->load($xsl_file);

		} else {
			throw new Exception(__METHOD__ . ' Passed stylesheet is not a file: ' . $xsl_file);
		}

		return $dom_xsl;
	}

}









