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
 * @subpackage Source
 * @package    CMP3
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */

#FIXME move to somewhere else

namespace Cmp3\Source;


/**
 * Creates a simple pdf with a content area defined by configuration.
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Source
 * @package    CMP3
 */
class ContentBox extends SourceAbstract {


	/**
	 * Override method to perform a property "Get"
	 * This will get the property $strName
	 *
	 * @param string $strName Name of the property to get
	 * @return mixed
	 */
	public function __get($strName)
	{
		switch ($strName) {

			case 'Type':
				return \Cmp3\Content\ContentType::PDF;
				break;

			default:
				return parent::__get($strName);
		}
	}


	/**
	 * Fetch and preprocess source content for PDF conversion
	 * @return \Cmp3\Content\ContentInterface
	 */
	public function Process()
	{


		// create new PDF document
#FIXME use config

		$pdf = new \tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);



		//set margins
		$pdf->SetMargins(0, 0, 0);
		$pdf->SetHeaderMargin(0);
		$pdf->SetFooterMargin(0);


#FIXME should not be needed
		require_once(K_PATH_MAIN . 'config/lang/eng.php');
		global $l;
		//set some language-dependent strings
		$pdf->setLanguageArray($l);

		// ---------------------------------------------------------


		/* example

		$strContent =
		"<style>
			// defines the content box itself
			// font family and font size must be defined here too!
			body {
		        color: #000;
		        font-family: dejavusans;
		        font-size: 11pt;
				line-height: 120%;
				top:220mm;
				left:120mm;
				width:70mm;
				height:40mm;
			}
		    p {
		        color: #000;
		        font-family: dejavusans;
		        font-size: 11pt;
				line-height: 120%;
				margin:0 0 0.3em 0;
		    }
		    p.a {
				margin:0 0 1em 0;
		    }
		</style>
		<p class=\"a\"><b>Bitmotion GmbH</b></p>
		<p>René Fritz</p>
		<p>Immengarten 16-18<br />
		30177 Hannover</p>
		<p>Telefon: +49 (0)511/62 62 93-12<br />
		Email: r.fritz@bitmotion.de</p>";

		*/




		// create content for writeHTMLCell()
		$strContent =
		"<style>
			{$this->objConfig->styles}
		</style>
		{$this->objConfig->html}";

		// parse css and get some values from it
		$objCss = new \Cmp3\Css\Css($this->objConfig->styles);
		$bodyStyles = $objCss->GetProperties('body');

		$pdf->SetFont($bodyStyles['font-family'], $bodyStyles['font-style'], $bodyStyles['font-size']);

		// add a page
		$pdf->AddPage();



		/*
		 TCPDF::writeHTMLCell 	(
		 		$  	w,
		 		$  	h,
		 		$  	x,
		 		$  	y,
		 		$  	html = '',
		 		$  	border = 0,
		 		$  	ln = 0,
		 		$  	fill = false,
		 		$  	reseth = true,
		 		$  	align = '',
		 		$  	autopadding = true
		 ) 		*/

		// write the first column
		$pdf->writeHTMLCell(intval($bodyStyles['width']), intval($bodyStyles['height']), intval($bodyStyles['left']), intval($bodyStyles['top']), $strContent, 0, 0, false, true, '', false);

		if ($this->objLogger)  $this->objLogger->Info(__CLASS__ . ' Writing content at coordinates (x,y,w,h): ' . intval($body['style']['left']) .','. intval($body['style']['top']) .','. intval($body['width']) .','. intval($body['height']));

		// reset pointer to the last page
		$pdf->lastPage();

		// ---------------------------------------------------------


		$objSourceFile = \Cmp3\System\Files::GetTemp(str_replace('\\', '_', __CLASS__), '.pdf', \Cmp3\Cmp3::$TempPath);

		//Close and output PDF document
		$pdf->Output($objSourceFile->AbsolutePath, 'F');

		$objSourceFile->Changed();

		$objProperties = array();
		$objProperties['Logger'] = $this->objLogger;
		$this->objContent = new \Cmp3\Content\Content($objProperties);
		$this->objContent->SetData($objSourceFile, \Cmp3\Content\ContentType::PDF);

		$this->blnIsProcessed = true;

		if (!$this->Processor) {
			if ($this->objLogger)  $this->objLogger->Info(__CLASS__ . ' No processing defined for source');
			return $this->objContent;
		}

		$this->ProcessContent($this->objContent);

		return $this->objContent;
	}

}





