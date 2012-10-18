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
 * @subpackage Converter
 * @package    CMP3
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Converter;


/**
 * PDF converter using prince cli tool to convert HTML to PDF
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Converter
 * @package    CMP3
 */
class Prince extends ConverterAbstract {


	/**
	 * Processes the transformation
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @throws Exception
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface $objContent)
	{
		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Start converter processing');

		$strBinary = $this->objConfig->GetFilename('engine.prince.binaryPath');
		if (!$strBinary OR !file_exists($strBinary)) {
			throw new Exception('prince binary is not available. Path: "' . $strBinary . '"');
		}


		if ($objContent->Type !== \Cmp3\Content\ContentType::HTML) {
			throw new Exception('The file type ' . $objContent->Type . ' does not match HTML in class ' . __CLASS__);
		}

		$objOutputFile = \Cmp3\System\Files::GetTemp(str_replace('\\', '_', __CLASS__), '.pdf', \Cmp3\Cmp3::$TempPath);


		$objPrince = new Prince($strBinary);
		$objPrince->setHTML(true);
		$objPrince->setEmbedFonts(true);

		if ($strFontPath = \Cmp3\Registry::Get('cmp3:fonts')) {
			#TODO call prince --scanfonts ?
		} else {
			#throw new Exception('No fonts available! Install cmp3_fonts for example.');
		}

		// The url fetcher make src absolute but CSS url() might still be relative. This fixes the problem.
		$objPrince->setBaseURL($this->objSource->Fetcher->GetBaseUrl());

		$strCssArray = array();

		$format = $this->objConfig->GetValue('page.size');
		if ($width = $this->objConfig->GetValue('page.width') AND $height = $this->objConfig->GetValue('page.height') ) {
			$strCssArray[] = "@page { size: ${width} ${height} }";
		} else {
			$orientation = $this->objConfig->GetValue('page.orientation');
			$strCssArray[] = "@page { size: {$format} {$orientation} }";
		}


		$mgl = $this->objConfig->GetInteger('page.marginLeft');
		$mgr = $this->objConfig->GetInteger('page.marginRight');
		$mgt = $this->objConfig->GetInteger('page.marginTop');
		$mgb = $this->objConfig->GetInteger('page.marginBottom');
		$strCssArray[] = "@page { margin: {$mgt}mm {$mgl}mm {$mgl}mm {$mgl}mm }";

		$strCssArray[] = '@page { prince-shrink-to-fit: none }';

		// TODO for headers/footers see http://princexml.com/doc/8.0/page-headers-footers/



		$strHTML = $objContent->GetData();
		$strHTML = str_replace('</style>', implode("\n", $strCssArray) . '</style>', $strHTML);

		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . " Convert content with prince");

		$msgs = array();
		if (!$objPrince->convert_string_to_file($strHTML, $objOutputFile->GetPathAbsolute(), $msgs)) {
			throw new Exception('prince failed to create pdf. ' . implode(' ', $msgs));
		}

		$objContent->SetData($objOutputFile, \Cmp3\Content\ContentType::PDF);
	}


}





