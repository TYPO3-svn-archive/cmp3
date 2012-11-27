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
 * Handles the PDF overlay using pdftk
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage PDF
 * @package    CMP3
 */
class Overlay {

	const FIRST_IS_TOP = 1;

	const FIRST_IS_BOTTOM = 2;

	const MULTI = 4;



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
	 * @return \Cmp3\Pdf\Overlay
	 */
	public function __construct(array $objPdfFileArray, $objConfig = null)
	{
		if (!count($objPdfFileArray)) {
			throw new Exception ('$objPdfFileArray needs to have at least one element');
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
	 * @return \Cmp3\Files\File PDF file
	 */
	public function Render($mode = self::FIRST_IS_BOTTOM)
	{
		if ($mode & self::FIRST_IS_BOTTOM) {
			$strMode = 'stamp';
		} else {
			$strMode = 'background';
		}

		if ($mode & self::MULTI) {
			$strMode = 'multi' . $strMode;
		}

		if (count($this->objPdfFileArray) == 1) {
			return $this->objOutputFile = reset($this->objPdfFileArray);

		} elseif (count($this->objPdfFileArray) == 2) {

			$strScriptOptions = '';
			$strScriptOptions .= ' ' . escapeshellarg($this->objPdfFileArray[0]->GetPathAbsolute());
			$strScriptOptions .= " $strMode " . escapeshellarg($this->objPdfFileArray[1]->GetPathAbsolute());

			$objNewOutputFile = \Cmp3\System\Files::GetTemp(str_replace('\\', '_', __CLASS__), '.pdf', \Cmp3\Cmp3::$TempPath);

			$strBinary = $this->GetPdftkBinary();
			$scriptCall = 	$strBinary .
							$strScriptOptions.
							' output ' . escapeshellarg($objNewOutputFile->GetPathAbsolute());

		} else {

			# first is top
			# cat  test-1.pdf | pdftk - background test-2.pdf output - | pdftk - background test-3.pdf output out.pdf

			# first is bottom
			# cat  test-1.pdf | pdftk - stamp test-2.pdf output - | pdftk - stamp test-3.pdf output out.pdf

			$strBinary = $this->GetPdftkBinary();

			$objNewOutputFile = \Cmp3\System\Files::GetTemp(str_replace('\\', '_', __CLASS__), '.pdf', \Cmp3\Cmp3::$TempPath);

			$objFileFirst = array_shift($this->objPdfFileArray);
			$objFileLast = array_pop($this->objPdfFileArray);

			$scriptCall = '';
			$scriptCall .= 'cat ' . escapeshellarg($objFileFirst->GetPathAbsolute());
			foreach ($this->objPdfFileArray as $objPdfFile) {
				$scriptCall .= " | $strBinary - $strMode " . escapeshellarg($objPdfFile->GetPathAbsolute()) . " output -";
			}
			$scriptCall .= " | $strBinary - $strMode " . escapeshellarg($objFileLast->GetPathAbsolute()) . ' output ' . escapeshellarg($objNewOutputFile->GetPathAbsolute());
		}

		if ($this->objLogger) $this->objLogger->Debug(__CLASS__ . ' pdftk called with command: ' . $scriptCall);

		$objExec = new \Cmp3\System\Exec();
		$objExec->Run($scriptCall);

		$strErrorOutput = $objExec->GetErrorOutput();
		if ($this->objLogger AND $strErrorOutput) $this->objLogger->Debug(__CLASS__ . " pdftk: \n" . $strErrorOutput);

		if (strpos($strErrorOutput, 'command not found')) {
			throw new \Cmp3\System\ExecException('pdftk seems not to be installed! You might want to configure the pdftk path with engine.pdftk.binaryPath.');
		}

		$this->objOutputFile = $objNewOutputFile;
		$this->objOutputFile->Changed();

		if ($this->objConfig->isEnabled('debug')) {
			$this->objOutputFile->SetDeleteOnDestruct(false);
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





