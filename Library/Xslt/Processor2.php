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
 * xslt2 processor
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Xslt
 * @package    CMP3
 */
class Processor2 extends ProcessorAbstract {


	/**
	 * Returns the processed content
	 *
	 * @param \Cmp3\Xslt\DOMDocument|string $xml Content to be processed
	 * @param \Cmp3\Xslt\DOMDocument|string $xsl_file DOMDocument or filename
	 * @throws \Cmp3\Exception
	 * @return \Cmp3\Xslt\FALSE|string
	 */
	public function Process ($xml, $xsl_file)
	{
		$dom_xml = $this->MakeXmlDom($xml);
		$dom_xsl = $this->MakeXslDom($xsl_file);

		if($dom_xml AND $dom_xsl) {

			/*
			 * Create XSL-Processor and do XSL-Transformation
			 */
			$xp = new \XML_XSLT2Processor('SAXON9', '/home/rene/Programme/SaxonHE9-4-0-3J/saxon9he.jar', 'JAVA-CLI');
			#$xp = new \XML_XSLT2Processor('SAXON9', '/home/rene/Programme/saxonhe9-3-0-11j/saxon9he.jar', 'JAVA-CLI');


#FIXME make saxon binary configurable
#FIXME replace XML_XSLT2Processor because error handling doesn't work well

			$xp->importStylesheet($dom_xsl);
			$result_xml = $xp->transformToXml($dom_xml);

			if ($result_xml === false) {
				#_d($xp->getErrors());
				throw new \Cmp3\Exception(__METHOD__ . $xp->getErrors());
			}

			return $result_xml;
		}

		return false;
	}

}









