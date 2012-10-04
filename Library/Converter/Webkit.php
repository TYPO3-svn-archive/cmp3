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
 * @subpackage PDF
 * @package    CMP3
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Converter;



/**
 * PDF converter using webkit cli to convert HTML to PDF
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Converter
 * @package    CMP3
 */
class Webkit extends ConverterAbstract {


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

		$strBinary = $this->objConfig->GetFilename('engine.wkhtmltopdf.binaryPath');

		if (!$strBinary OR !file_exists($strBinary)) {
			throw new Exception('wkhtmltopdf binary is not available. Path: "' . $strBinary . '"');
		}

		if ($objContent->Type !== \Cmp3\Content\ContentType::HTML) {
			throw new Exception('The file type ' . $objContent->Type . ' does not match HTML in class ' . __CLASS__);
		}

		$objOutputFile = \Cmp3\System\Files::GetTemp(str_replace('\\', '_', __CLASS__), '.pdf', \Cmp3\Cmp3::$TempPath);


		$strScriptOptions = '';

		$format = $this->objConfig->GetValue('page.size');
		if ($width = $this->objConfig->GetValue('page.width') AND $height = $this->objConfig->GetValue('page.height') ) {
			$strScriptOptions .= " --page-width {$width} --page-height {$height}";
		} else {
			$strScriptOptions .= " --page-size {$format}";
		}
		if ($orientation = $this->objConfig->GetValue('page.orientation')) {
			$orientation = ucfirst($orientation);
			$strScriptOptions .= " --orientation {$orientation}";
		}

		$mgl = $this->objConfig->GetValue('page.marginLeft');
		$mgr = $this->objConfig->GetValue('page.marginRight');
		$mgt = $this->objConfig->GetValue('page.marginTop');
		$mgb = $this->objConfig->GetValue('page.marginBottom');
		$strScriptOptions .= " --margin-left {$mgl}mm";
		$strScriptOptions .= " --margin-right {$mgr}mm";
		$strScriptOptions .= " --margin-top {$mgt}mm";
		$strScriptOptions .= " --margin-bottom {$mgb}mm";

		if ($strTitle = $this->objConfig->GetValue('title')) {
			$strScriptOptions .= " --title " . escapeshellarg($strTitle);
		}

		if ($parameter = $this->objConfig->GetValue('engine.wkhtmltopdf.parameter')) {
			$strScriptOptions .= " {$parameter}";
		}


		// setting environment
		$strEnvArray = array();

		if ($strLibsPath = $this->objConfig->GetFilename('engine.wkhtmltopdf.libsPath')) {
			$strEnvArray['LD_LIBRARY_PATH'] = $strLibsPath . ':/lib:/usr/lib/:/usr/local/lib/:'. getenv('LD_LIBRARY_PATH');
		}
		if ($strFontConfigPath = $this->objConfig->GetFilename('engine.wkhtmltopdf.fontConfigPath')) {
			$strEnvArray['FONTCONFIG_PATH'] = $strFontConfigPath;
		}

		if (!$strEnvArray) {
			$strEnvArray = NULL;
		}


		$scriptCall = 	$strBinary .
						$strScriptOptions. ' ' .
						escapeshellarg($objContent->File->GetPathAbsolute()) . ' ' .
						escapeshellarg($objOutputFile->GetPathAbsolute());


		$objExec = new \Cmp3\System\Exec();
		$objExec->SetOnErrorOutputException(false);

		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . " Convert content with wkhtmltopdf");
		if ($this->objLogger) $this->objLogger->Debug(__CLASS__ . " Convert content with $strBinary: $scriptCall");

		$objExec->Run($scriptCall, dirname($strBinary), $strEnvArray);

		if ($objExec->GetExitCode() > 0) {
			throw new Exception($objExec->GetErrorOutput());
		}


//if ($_GET['dbg']) {
//	echo ($scriptCall);
//	echo '<br>';
//	var_dump($strEnvArray);
//	echo '<br>ExitCode: ';
//	echo $objExec->GetExitCode();
//	echo '<br>';
//	echo $objExec->GetOutput();
//	echo '<br>';
//	echo $objExec->GetErrorOutput();
//	die();
//}

		$objContent->SetData($objOutputFile, \Cmp3\Content\ContentType::PDF);
	}
}





