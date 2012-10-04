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



namespace Cmp3\XML;



/**
 * Misc tools for XML processing
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage XML
 * @package    CMP3
 */
abstract class Tools {

	public static function CreateRichTextValueNode ($ownerDocument, $strContent)
	{
		// <field name="bodytext" type="text" format="rich">
		//	<meta>
		//    <type>body</type>
		//  </meta>
		//	<value>
		//      <rich:p>Und <rich:b>bold</rich:b> soooo schrecklich</rich:p>
		//	</value>
		// </field>

		if ($strContent) {
			$strContent = str_replace('<', '<rich:', $strContent);
			$strContent = str_replace('<rich:/', '</rich:', $strContent);
			$strContent= '<value xmlns:rich="http://www.bitmotion.de/cmp3/cmp3xhtml">' . $strContent . '</value>';

			$objXmlElement = $ownerDocument->createDocumentFragment();
			$objXmlElement->appendXML($strContent);

			$objNewNode = $objXmlElement;

		} else {
			$objNewNode = $ownerDocument->createElement('value');
		}

		return $objNewNode;
	}


	public static function ReplaceTextFieldWithRichText ($objNode, $strContent)
	{
		$objContentField = self::CreateRichTextValueNode($objNode->ownerDocument, $strContent);

		$objNode->setAttribute('format', 'rich');
		$objNode->replaceChild($objContentField, $objNode->getElementsByTagName('value')->item(0));
	}


	/**
	 * Processes a HTML string and make it valid XHTML
	 *
	 * @see http://tidy.sourceforge.net/docs/quickref.html
	 *
	 * @param $strContent
	 * @throws Exception
	 * @return string
	 */
	public static function CleanHtml($strContent)
	{
		$strContent = '<html><body>' . $strContent . '</body><html>';

		$config = array(
				'output-xhtml' => true,
				'clean' => true,
				'word-2000' => true,
				'enclose-block-text' => true,
				'wrap' => false,
		);

		if (function_exists('tidy_parse_string')) {
			$tidy = tidy_parse_string($strContent, $config, 'utf8');
			$tidy->cleanRepair();
			$strContent = (string)$tidy;

		} else {


			$config['input-encoding'] = 'utf8';

			$strScriptOptions = '';

			foreach ($config as $option => $value) {
				$strValue = (string)$value;
				$strScriptOptions .= ' --'.$option . ' ' . $strValue;
			}

			$scriptCall = 'tidy ' . $strScriptOptions;

			$objExec = new \Cmp3\System\Exec();
			$objExec->SetOnExitCodeException(false);
			$objExec->SetOnErrorOutputException(false);
			$objExec->SetInput($strContent);
			$objExec->Run($scriptCall);

			// exit code 2 indicates errors so cleaning failed
			// exit code 127 means tidy was not found
			if ($objExec->GetExitCode() > 1) {
				throw new Exception(__METHOD__ . ' Cleaning RTE html with tidy failed with exit code ' . $objExec->GetExitCode() . '. Output: ' . $objExec->GetErrorOutput());
			}

			$strContent = $objExec->GetOutput();
		}


		$strContent = \Cmp3\String\HTML::GetBodyContent($strContent);

		return $strContent;
	}


	/**
	 * This decodes html entities but preserves entities that are needed for XML
	 *
	 * @param string $strContent
	 * @return string
	 */
	public static function DecodeHtmlEntities($strContent)
	{


		/*
		 XML, by default, defines the following 5 entities:

		&amp;

		Ampersand (&)
		&lt;

		Less Than Sign (<)
		&gt;

		Greater Than Sign (>)
		&quot;

		Double Quote (")
		&apos;

		Apostrophe (')
		*/
		$strSearchArray = array(
				'&amp;',
				'&lt;',
				'&gt;',
				'&quot;',
				'&apos;'
		);

		$strReplaceArray = array(
				'__Ampersand__',
				'__Less_Than_Sign__',
				'__Greater_Than_Sign__',
				'__Double_Quote__',
				'__Apostrophe__'
		);


		$strContent = str_replace($strSearchArray, $strReplaceArray, $strContent);

		$strContent = html_entity_decode($strContent, ENT_NOQUOTES, 'UTF-8');

		$strContent = str_replace($strReplaceArray, $strSearchArray, $strContent);

		return $strContent;
	}


	/**
	 * Returns a DOMDocument object from a string or a DOMDocument
	 *
	 * @param string|\DOMDocument 	$xml Content to be processed
	 * @return \DOMDocument
	 * @throws \Cmp3\Exception
	 */
	public static function MakeXmlDom($xml)
	{
		if (is_object($xml)) {
			if ($xml instanceof \DOMDocument) {
				$dom_xml = $xml;

			} else {
				throw new \Cmp3\Exception(__METHOD__ . ' Passed object is not a DOMDocument but ' . get_class($xml));
			}

		} elseif($xml) {

			/*
			 * Create DOM-Object from XML-Data
			*/
			$dom_xml = new Document();
			$dom_xml->resolveExternals = true;
			$dom_xml->loadXML($xml);

		} else {
			throw new \Cmp3\Exception(__METHOD__ . ' Passed xml is empty.');
		}

		return $dom_xml;
	}
}
