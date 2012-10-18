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
 * PDF converter using mpdf library to convert HTML to PDF
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Converter
 * @package    CMP3
 */
class Mpdf extends ConverterAbstract {


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


		if ($objContent->Type !== \Cmp3\Content\ContentType::HTML) {
			throw new Exception('The file type ' . $objContent->Type . ' does not match HTML in class ' . __CLASS__);
		}

		$objOutputFile = \Cmp3\System\Files::GetTemp(str_replace('\\', '_', __CLASS__), '.pdf', \Cmp3\Cmp3::$TempPath );


		if (!defined('_MPDF_TEMP_PATH')) define("_MPDF_TEMP_PATH", \Cmp3\Cmp3::$TempPath);

		if (!defined('_MPDF_TTFONTPATH')) {
			if ($strFontPath = \Cmp3\Registry::Get('cmp3:fonts')) {
				define('_MPDF_TTFONTPATH',$strFontPath);
			} else {
				throw new Exception('No fonts available! Install cmp3_fonts for example.');
			}
		}

		if (!defined('_MPDF_TTFONTDATAPATH')) {
			if (!file_exists(_MPDF_TEMP_PATH . 'ttfontdata/')) {
				mkdir(_MPDF_TEMP_PATH . 'ttfontdata/');
			}
			define('_MPDF_TTFONTDATAPATH',_MPDF_TEMP_PATH.'ttfontdata/');
		}

// 		echo "_MPDF_TEMP_PATH "._MPDF_TEMP_PATH."\n";
// 		echo "_MPDF_TTFONTDATAPATH "._MPDF_TTFONTDATAPATH."\n";
// 		echo "_MPDF_TTFONTPATH "._MPDF_TTFONTPATH."\n";


		// collect data for mPDF options

		$format = $this->objConfig->GetValue('page.size');
		if ($width = $this->objConfig->GetValue('page.width') AND $height = $this->objConfig->GetValue('page.height') ) {
			$format = array($width, $height);
		}

		$orientation = ($this->objConfig->GetValue('page.orientation') == 'landscape' ? 'L': 'P');

		$mgl = $this->objConfig->GetValue('page.marginLeft');
		$mgr = $this->objConfig->GetValue('page.marginRight');
		$mgt = $this->objConfig->GetValue('page.marginTop');
		$mgb = $this->objConfig->GetValue('page.marginBottom');

		if ($strBackgroundPDF = $this->objConfig->GetValue('background')) {
			$strBackgroundPDF = \Cmp3\System\Files::MakeFilePathAbsolute(\Cmp3\System\Files::ResolvePath($strBackgroundPDF));
		}

		// header footer
		$mgh = 0;
		$mgf = 10;


		$objMpdf = new \mPDF('utf-8', $format, $default_font_size=0, $default_font='', $mgl, $mgr, $mgt, $mgb, $mgh, $mgf, $orientation);

		// The url fetcher make src absolute but CSS url() might still be relative. This fixes the problem.
		$objMpdf->SetBasePath($objContent->Meta->BaseUrl);

		if ($this->objConfig->isEnabled('debug')) {
			$objMpdf->debug = true;
		}

		if ($strBackgroundPDF) {
			$objMpdf->SetImportUse();
			$objMpdf->SetDocTemplate($strBackgroundPDF, 1);
			$this->blnHasBackgroundApplied = true;
		}

		// Set the document title
		if ($title = $this->objConfig->GetValue('title')) {
			$objMpdf->SetTitle('title');
		}

		// Set the dpi resolution for images
		if ($dpi = intval($this->objConfig->GetValue('dpi'))) {
# does that work?
#			$objMpdf->img_dpi = $dpi;
		}

		// Set header
		if ($header = $this->objConfig->GetValue('engine.mpdf.header')) {


			$objProperties = array();
			$objProperties['Logger'] = $this->objLogger;
			$objHeader = new \Cmp3\Content\Content($objProperties);
			$objHeader->SetData($header, \Cmp3\Content\ContentType::TEXT);

			$objUrlMarker = new \Cmp3\ContentProcessing\UrlMarker();
			$objUrlMarker->Process($objHeader);

			$header = $this->GetHeaderFooterArray($objHeader->GetData());

			$objMpdf->SetHeader ($header);
		}

		// Set footer
		if ($footer = $this->objConfig->GetValue('engine.mpdf.footer')) {

			$objProperties = array();
			$objProperties['Logger'] = $this->objLogger;
			$objFooter = new \Cmp3\Content\Content($objProperties);
			$objFooter->SetData($footer, \Cmp3\Content\ContentType::TEXT);

			$objUrlMarker = new \Cmp3\ContentProcessing\UrlMarker();
			$objUrlMarker->Process($objFooter);

			$footer = $this->GetHeaderFooterArray($objFooter->GetData());

			$objMpdf->SetFooter ($footer);
		}

		// set HTML content to process
		$objMpdf->WriteHTML($objContent->GetData());

		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . " Convert content with mpdf");

		// write pdf file
		$objMpdf->Output($objOutputFile->GetPathAbsolute(), 'F');

		$objContent->SetData($objOutputFile, \Cmp3\Content\ContentType::PDF);
	}



	/**
	 * processing of header/footer string
	 *
	 * @param string $footer left|center|right
	 * @return array
	 */
	protected function GetHeaderFooterArray($footer)
	{
		list ($l,$c,$r) = explode('|', $footer);

		$footer = array (
		    'L' => array (
		      'content' => $l,
		      'font-size' => 10,
		      'font-style' => 'N',
		      'font-family' => 'sans-serif',
		      'color'=>'#000000'
		    ),
		    'C' => array (
		      'content' => $c,
		      'font-size' => 10,
		      'font-style' => 'N',
		      'font-family' => 'sans-serif',
		      'color'=>'#000000'
		    ),
		    'R' => array (
		      'content' => $r,
		      'font-size' => 10,
		      'font-style' => 'N',
		      'font-family' => 'sans-serif',
		      'color'=>'#000000'
		    ),
		    'line' => 0,
		);
		$footer = array ('odd' => $footer, 'even' => $footer);

		return $footer;
	}
}





