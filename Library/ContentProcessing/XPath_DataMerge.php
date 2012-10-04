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
 * Content processors which merges two xml files
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class XPath_DataMerge extends ProcessorAbstract {

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

			$strXPathParent = $this->objConfig->GetValue('xpathParent');
			if (!$strXPathParent) {
				throw new Exception(__METHOD__ . " xpath not defined");
			}

			$strXPathData = $this->objConfig->GetValue('xpathData');
			if (!$strXPathData) {
				throw new Exception(__METHOD__ . " xpathData not defined");
			}

			if (!$this->Data) {
				throw new Exception(__METHOD__ . " data not set");
			}
			$objXmlData = \Cmp3\XML\Tools::MakeXmlDom($this->Data);

			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . " Merge content using xpathParent: '$strXPathParent' and xpathData: '$strXPathData'");

			$xp = new \DOMXPath($objXml);

#TODO this doens't help - see Config_Cmp3Gui2.txt
			foreach( $xp->query('namespace::*', $objXml->documentElement) as $node ) {
				#error_log( $node->prefix . ':' . $node->namespaceURI);
				if (!$node->prefix)  {
					$xp->registerNamespace('default', $node->namespaceURI);
				} else {
					$xp->registerNamespace($node->prefix, $node->namespaceURI);
				}
			}




			$objNodeArray = $xp->Query($strXPathParent);
			if ($objNodeArray->length) {
				if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Found matching parent nodes');

				$xp2 = new \DOMXPath($objXmlData);


#TODO this doens't help - see Config_Cmp3Gui2.txt
				foreach( $xp2->query('namespace::*', $objXmlData->documentElement) as $node ) {
					#error_log( $node->prefix . ':' . $node->namespaceURI);
					if (!$node->prefix)  {
						$xp2->registerNamespace('default', $node->namespaceURI);
					} else {
						$xp2->registerNamespace($node->prefix, $node->namespaceURI);
					}
				}



				$objNodeDataArray = $xp2->Query($strXPathData);
				if ($objNodeDataArray->length) {
					if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Found matching data nodes');

					foreach ($objNodeArray as $objNode) {
						if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Parent node: ' . $objNode->tagName);
						foreach ($objNodeDataArray as $objNodeData) {
							if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Append data node: ' . $objNodeData->tagName);

							// Import the node, and all its children, to the document
							$objNodeData = $objNode->ownerDocument->importNode($objNodeData, true);
							// And then append it to the node
							$objNode->appendChild($objNodeData);

							$this->blnHasModified = true;
						}
					}

				}else {
					if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' No matching data nodes found with xpath: ' . $strXPathData);
				}

			}else {
				if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' No matching parent nodes found with xpath: ' . $strXPathParent);
			}

			if ($this->blnHasModified) {
				$objContent->SetData($objXml);
			}
		}
	}


}
