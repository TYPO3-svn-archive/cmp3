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
 * @copyright  Copyright (c) 2012 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Pdf;

#TODO use \Cmp3\BaseConfig

/**
 * It can set the media, crop, bleed, trim, and art box on pages of a PDF file.
 * @see http://de.wikipedia.org/wiki/Portable_Document_Format
 *
 * Config property 'parameter' has to be set with a string like this:
 * [box] [left] [bottom] [width] [height]
 *
 * Box is one of media crop bleed trim art.
 * Give values * 100 as integers and in postscript points (pt)
 *
 * REMARK
 * I hopen that is possible to add bleed to a PDF using this tool.
 * But it doesn't work because the content of the PDF has to be moved by the amount of the bleed and this doesn't happen.
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage PDF
 * @package    CMP3
 */
class Box {



	/**
	 *
	 * @var \Cmp3\Files\File
	 */
	protected $objInputFile;

	/**
	 *
	 * @var \Cmp3\Files\File
	 */
	protected $objOutputFile;

	/**
	 *
	 * @var \Cmp3\Config\Config
	 */
	protected $objConfig;

	/**
	 * Logger object
	 *
	 * @var \Cmp3\Log\Logger
	 */
	protected $objLogger;

	/**
	 * Constructor
	 *
	 * @param \Cmp3\Files\File $objInputFile
	 * @param \Cmp3\Config\ConfigInterface $objConfig
	 * @throws Exception
	 * @return \Cmp3\Pdf\Shrink
	 */
	public function __construct(\Cmp3\Files\File $objInputFile, $objConfig = null, $objLogger = null)
	{
		$this->objInputFile = $objInputFile;
		$this->objLogger = $objLogger;

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

		/*
		Usage: podofobox [inputfile] [outpufile] [box] [left] [bottom] [width] [height]
		Box is one of media crop bleed trim art.
		Give values * 100 as integers and in postscript points (pt)
		*/


		$strParameter = $this->objConfig->GetValue('parameter');

		if (!$strParameter) {
			throw new Exception ("Configuration property 'parameter' has no value set");
		}

		// get temp output file
		$this->objOutputFile = \Cmp3\System\Files::GetTemp(str_replace('\\', '_', __CLASS__), '.pdf', \Cmp3\Cmp3::$TempPath);
		if ($this->objConfig->isEnabled('debug')) {
			$this->objOutputFile->SetDeleteOnDestruct(false);
		}

		// set input file
		$strScriptOptions = escapeshellarg($this->objInputFile->GetPathAbsolute());

		// set output file
		$strScriptOptions .= ' ' . escapeshellarg($this->objOutputFile->GetPathAbsolute());

		// set parameter
		$strScriptOptions .= ' ' . escapeshellarg($strParameter);


		$strBinary = $this->GetBoxBinary();
		$scriptCall = 	$strBinary . ' ' . $strScriptOptions;

		if ($this->objLogger) $this->objLogger->Debug(__CLASS__ . ' podofobox called with command: ' . $scriptCall);

		$objExec = new \Cmp3\System\Exec();
		$objExec->SetOnErrorOutputException(false);
		$objExec->Run($scriptCall);

		$strErrorOutput = $objExec->GetErrorOutput();
		if ($this->objLogger AND $strErrorOutput) $this->objLogger->Debug(__CLASS__ . " podofobox: \n" . $strErrorOutput);

		if (strpos($strErrorOutput, 'command not found')) {
			throw new \Cmp3\System\ExecException('podofobox seems not to be installed! You might want to configure the podofobox path with engine.podofobox.binaryPath.');
		}

		$this->objOutputFile->Changed();

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
	 * returns the path to the podofobox binary
	 * @throws Exception
	 * @return string
	 */
	protected function GetBoxBinary()
	{
		$strBinary = $this->objConfig->GetFilename('engine.podofobox.binaryPath');
		if ($strBinary AND !file_exists($strBinary)) {
			throw new Exception('podofobox binary is not available. Path: "' . $strBinary . '"');
		} else {
			$strBinary = 'podofobox';
		}
		return $strBinary;
	}
}





