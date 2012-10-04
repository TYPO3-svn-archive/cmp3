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
 * DOMDocument that stores a full CMP3 XML document
 *
 * Example:
 *
 * <?xml version="1.0" encoding="utf-8"?>
 * <cmp3document>
 *
 * 	<meta>
 * 		<!-- usually here's more stuff -->
 * 	</meta>
 *
 * 	<!-- 'tree' has nodes, 'flat' doesn't and has just records -->
 *
 * 	<content type="tree">
 * 		<!-- subtype = layout?! -->
 * 		<node type="page" subtype="default">
 *
 * 			<record type="pages">
 * 				<!-- fields of page are here -->
 * 				<field name="title" type="text" format="line">
 * 					<meta />
 * 					<content>Bodenbelag</content>
 * 				</field>
 * 				<field name="subtitle" type="text" format="line">
 * 					<content></content>
 * 				</field>
 * 				<field name="description" type="text" format="multiline">
 * 					<content></content>
 * 				</field>
 * 				<field name="abstract" type="text" format="multiline">
 * 					<content></content>
 * 				</field>
 * 				<field name="author" type="text" format="line">
 * 					<content></content>
 * 				</field>
 * 				<field name="lastUpdated" type="text" format="date">
 * 					<content>01-01-1970</content>
 * 				</field>
 * 			</record>
 *
 * 			<node type="group" subtype="plugin">
 * 				<!-- this is the plugin record -->
 * 				<record type="tt_content" subtype="plugin_9">
 * 					<!-- fields of plugin record are here -->
 * 					<field name="header" type="text" format="line">
 * 						<content>
 * 							<!-- h1-6 should be ignored here -->
 * 							<h1>Produkte</h1>
 * 						</content>
 * 					</field>
 * 				</record>
 *
 * 				<!-- here comes the news records -->
 * 				<node type="group" subtype="records">
 * 					<record type="tt_news" subtype="news">
 *
 * 						<!-- fields of record are here -->
 * 						<field name="header" type="text" format="line">
 * 							<content>
 * 								<h2>Haegar</h2>
 * 							</content>
 * 						</field>
 * 						<field name="bodytext" type="text" format="rich">
 * 							<content>
 * 								<p>
 * 									Und
 * 									<strong>bold</strong>
 * 									soooo schrecklich
 * 								</p>
 * 							</content>
 * 						</field>
 * 					</record>
 *
 * 					<record type="tt_news" subtype="news2">
 * 						<field name="header" type="text" format="line">
 * 							<content>
 * 								<h3>Haegar</h3>
 * 							</content>
 * 						</field>
 *
 * 						<image type="images" position="above">
 * 							<!-- imageobject is a fixed tag name -->
 * 							<imageobject align="center" float="none">
 * 								<alias>_var_www_cmp3_uploads_pics_haegar004.gif</alias>
 * 								<caption></caption>
 * 							</imageobject>
 * 						</image>
 *
 * 						<field name="bodytext" type="text" format="rich">
 * 							<content>
 * 								<p>Und soooo schrecklich</p>
 * 							</content>
 * 						</field>
 * 					</record>
 *
 * 				</node>
 * 			</node>
 *
 *
 * 			<node type="group" subtype="records">
 *
 * 				<!-- no fields, this is just a group -->
 *
 * 				<record type="tt_content" subtype="text">
 * 					<!-- fields of record are here -->
 * 					<field name="header" type="text" format="line">
 * 						<meta>
 * 							<ctype>header</ctype>
 * 						</meta>
 * 						<content>
 * 							<h3>Haegar</h3>
 * 						</content>
 * 					</field>
 *
 * 					<field name="bodytext" type="text" format="rich">
 * 						<meta>
 * 							<ctype>body</ctype>
 * 						</meta>
 * 						<content>
 * 							<p>
 * 								Und
 * 								<strong>bold</strong>
 * 								soooo schrecklich
 * 							</p>
 * 						</content>
 * 					</field>
 * 				</record>
 *
 * 				<record type="tt_content" subtype="textpic">
 * 					<field name="header" type="text" format="line">
 * 						<content>
 * 							<h3>Haegar</h3>
 * 						</content>
 * 					</field>
 *
 * 					<image type="images" position="above">
 * 						<imageobject align="center" float="none">
 * 							<alias>_var_www_cmp3_uploads_pics_haegar004.gif</alias>
 * 							<caption></caption>
 * 						</imageobject>
 * 						<imageobject align="center" float="none">
 * 							<alias>_var_www_cmp3_uploads_pics_haegar5635.gif</alias>
 * 							<caption></caption>
 * 						</imageobject>
 * 					</image>
 *
 * 					<field name="bodytext" type="text" format="rich">
 * 						<content>
 * 							<p>Und soooo schrecklich</p>
 * 						</content>
 * 					</field>
 * 				</record>
 *
 * 			</node>
 *
 *
 *
 *
 * 			<node type="page" subtype="default">
 * 				<record type="pages">
 * 					<!-- fields of page are here -->
 * 					<field name="title" format="line">
 * 						<content>Noch eine Seite</content>
 * 					</field>
 * 					<field name="subtitle" format="line">
 * 						<content></content>
 * 					</field>
 * 					<field name="description" format="multiline">
 * 						<content></content>
 * 					</field>
 * 					<field name="abstract" format="multiline">
 * 						<content></content>
 * 					</field>
 * 					<field name="author" format="line">
 * 						<content></content>
 * 					</field>
 * 					<field name="lastUpdated" format="date">
 * 						<content>01-01-1970</content>
 * 					</field>
 * 				</record>
 *
 * 				<node type="group" subtype="records">
 *
 * 					<!-- no fields, this is just a group -->
 *
 * 					<record type="tt_content" subtype="text">
 * 						<!-- fields of record are here -->
 * 						<field name="header" type="text" format="line">
 * 							<content>
 * 								<h1>gfdsgfsd</h1>
 * 							</content>
 * 						</field>
 *
 * 						<field name="bodytext" type="text" format="rich">
 * 							<content>
 * 								<p>
 * 									Und
 * 									<strong>bold</strong>
 * 									soooo schrecklich
 * 								</p>
 * 							</content>
 * 						</field>
 * 					</record>
 *
 * 				</node>
 * 			</node>
 *
 *
 *
 * 		</node>
 *
 * 	</content>
 * </cmp3document>
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage XML
 * @package    CMP3
 */
class DocumentCmp3 extends \DOMDocument {


	/**
	 * magic value for
	 * @var string
	 */
	const PARENT = 'parent';


	/**
	 * This is the root node for records and nodes
	 *
	 * @var \DOMNode
	 */
	protected $objNodeContent;


	/**
	 * This is the current node where records will be added to
	 *
	 * @var \DOMNode
	 */
	protected $objCurrentNode;


	/**
	 *
	 * Constructor, Calls parent and sets root node
	 *
	 * @internal param string $version
	 * @internal param string $encoding
	 */
	public function __construct(  )
	{
		parent::__construct('1.0', 'UTF-8');

		// format the created XML
		$this->formatOutput = true;


		// <cmp3document>
		// <cmp3document xmlns="http://www.bitmotion.de/cmp3/cmp3document" xmlns:cmp3="http://www.bitmotion.de/cmp3/cmp3document">
		$objRoot = $this->createElementNS('http://www.bitmotion.de/cmp3/cmp3document', 'cmp3document');


		parent::appendChild($objRoot);
		$objRoot->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:cmp3', 'http://www.bitmotion.de/cmp3/cmp3document');

		// <meta>
		$objMeta = parent::createElement('meta');
		$objRoot->appendChild($objMeta);

		// <content type="tree">
		$this->objNodeContent = parent::createElement('content');
		$this->objNodeContent->setAttribute('type', 'tree');
		$objRoot->appendChild($this->objNodeContent);

		// records will be added to this
		$this->objCurrentNode = $this->objNodeContent;

	}


	/**
	 * Make a sting out of the xml document
	 * This is useful for the content object and processors which need the xml as string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->saveXML();
	}


	/**
	 * Adds a record node to document at the current node location
	 *
	 * @param Record|\DOMNode $objDocument
	 * @return \DOMNode
	 */
	public function AddRecord($objDocument)
	{
		if ($objDocument instanceof Record) {
			$objDocument = $objDocument->GetRootNode();
		}

		// Import the node, and all its children, to the document
		$objNewNode = parent::importNode($objDocument, true);
		// And then append it to the current node
		return $this->objCurrentNode->appendChild($objNewNode);
	}


	/**
	 * Adds a 'node' node to document at the current node location
	 *
	 * @param string
	 * @param string
	 * @return \DOMNode Current node
	 */
	public function AddNode($strNodeType, $strNodeSubtype)
	{
		// <node type="page" subtype="default">
		$objNewNode = parent::createElement('node');
		$objNewNode->setAttribute('type', $strNodeType);
		$objNewNode->setAttribute('subtype', $strNodeSubtype);

		// And then append it to the current node
		$this->objCurrentNode->appendChild($objNewNode);

		return $this->objCurrentNode = $objNewNode;
	}


	/**
	 * Sets the current node location
	 *
	 * @param \DOMNode|self::PARENT $objNode
	 * @return \DOMElement|\DOMNode
	 */
	public function SetCurrentNode($objNode)
	{
		/* @var $objNode \DOMNode */
		if ($objNode === self::PARENT) {
			if ($this->objCurrentNode->isSameNode($this->objNodeContent)) {
				return $this->objCurrentNode;
			}

			$this->objCurrentNode = $this->objCurrentNode->parentNode;
		} else {
			$this->objCurrentNode = $objNode;
		}

		return $this->objCurrentNode;
	}
}



