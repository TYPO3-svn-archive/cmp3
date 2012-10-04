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



namespace Cmp3\Pdf;



/**
 * Handles the PDF merging using pdftk
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage PDF
 * @package    CMP3
 */
class Merge {


	/**
	 *
	 * @var \Cmp3\Files\File[]
	 */
	protected $objPdfFileArray;

	/**
	 *
	 * @var \Cmp3\Config\Config
	 */
	protected $objConfig;

	/**
	 *
	 * @var \Cmp3\Files\File
	 */
	protected $objOutputFile;


	/**
	 * Constructor
	 *
	 * @param Source[] $objPdfFileArray
	 * @param \Cmp3\Config\ConfigInterface $objConfig
	 * @throws Exception
	 * @return \Cmp3\Pdf\Merge
	 */
	public function __construct(array $objPdfFileArray, $objConfig = null)
	{
		if (!count($objPdfFileArray)) {
			throw new Exception ('$objPdfFileArray needs to have at least on element');
		}
		$this->objPdfFileArray = $objPdfFileArray;

		if (is_object($objConfig) AND !($objConfig instanceof \Cmp3\Config\ConfigInterface)) {
			throw new Exception ('$objConfig is not of type \Cmp3\Config\ConfigInterface');
		}
		$this->objConfig = $objConfig ? $objConfig : new \Cmp3\Config\ArrayData();
	}


	/**
	 * Renders the defined pages
	 *
	 * @throws \Exception
	 * @return \Cmp3\Files\File PDF file
	 */
	public function Render()
	{
		// check if we need to merge pdf files

		if (count($this->objPdfFileArray) == 1) {
			$this->objOutputFile = reset($this->objPdfFileArray);

		} else {

			// merge PDF files using pdftk
			$strScriptOptions = '';
			foreach ($this->objPdfFileArray as $objPdfFile) {
				$strScriptOptions .= ' ' . escapeshellarg($objPdfFile->GetPathAbsolute());
			}

			$this->objOutputFile = \Cmp3\System\Files::GetTemp(str_replace('\\', '_', __CLASS__), '.pdf', \Cmp3\Cmp3::$TempPath);
			if ($this->objConfig->isEnabled('debug')) {
				$this->objOutputFile->SetDeleteOnDestruct(false);
			}

			$strBinary = $this->GetPdftkBinary();
			$scriptCall = 	$strBinary .
							$strScriptOptions. ' cat ' .
							' output ' . escapeshellarg($this->objOutputFile->GetPathAbsolute());

			if ($this->objLogger) $this->objLogger->Debug(__CLASS__ . ' pdftk called with command: ' . $scriptCall);

			$objExec = new \Cmp3\System\Exec();
			$objExec->Run($scriptCall);

			$strErrorOutput = $objExec->GetErrorOutput();
			if ($this->objLogger AND $strErrorOutput) $this->objLogger->Debug(__CLASS__ . " pdftk: \n" . $strErrorOutput);

			if (strpos($strErrorOutput, 'command not found')) {
				throw new \Cmp3\System\ExecException('pdftk seems not to be installed! You might want to configure the pdftk path with engine.pdftk.binaryPath.');
			}

			$this->objOutputFile->Changed();
		}

		return $this->objOutputFile;
	}


	/**
	 * Returns object of rendered pdf
	 * @return \Cmp3\Files\File
	 */
	public function GetFile()
	{
		return $this->objOutputFile;
	}


	/**
	 * returns the path to the pdftk binary
	 * @throws Exception
	 * @return string
	 */
	protected function GetPdftkBinary()
	{
		$strBinary = $this->objConfig->GetFilename('engine.pdftk.binaryPath');
		if ($strBinary AND !file_exists($strBinary)) {
			throw new Exception('pdftk binary is not available. Path: "' . $strBinary . '"');
		} else {
			$strBinary = 'pdftk';
		}
		return $strBinary;
	}
}





