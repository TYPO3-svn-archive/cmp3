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

#TODO use \Cmp3\BaseConfig

/**
 * Plain Ghostscript processor where parameter has to be set to do anything
 *
 * @see http://ghostscript.com/doc/current/Use.htm
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage PDF
 * @package    CMP3
 */
class Ghostscript {


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
		// get temp output file
		$this->objOutputFile = \Cmp3\System\Files::GetTemp(str_replace('\\', '_', __CLASS__), '.pdf', \Cmp3\Cmp3::$TempPath);
		if ($this->objConfig->isEnabled('debug')) {
			$this->objOutputFile->SetDeleteOnDestruct(false);
		}
		if ($this->objLogger) $this->objLogger->Debug( var_export($this->objConfig->GetProperties(''),true));

		// set general parameter
		$strScriptOptions =  $this->objConfig->GetValue('parameter');

		// set output file
		$strScriptOptions .= ' -sOutputFile=' . escapeshellarg($this->objOutputFile->GetPathAbsolute());

		// set input file
		$strScriptOptions .= ' ' . escapeshellarg($this->objInputFile->GetPathAbsolute());

		// append parameter
		$strScriptOptions .= ' ' . $this->objConfig->GetValue('appendParameter');

		$strBinary = $this->GetGhostscriptBinary();
		$scriptCall = 	$strBinary . ' ' . $strScriptOptions;

		if ($this->objLogger) $this->objLogger->Debug(__CLASS__ . ' ghostscript called with command: ' . $scriptCall);

		$objExec = new \Cmp3\System\Exec();
		$objExec->SetOnErrorOutputException(false);
		$objExec->Run($scriptCall);

		$strErrorOutput = $objExec->GetErrorOutput();
		if ($this->objLogger AND $strErrorOutput) $this->objLogger->Debug(__CLASS__ . " Ghostscript: \n" . $strErrorOutput);

		if (strpos($strErrorOutput, 'command not found')) {
			throw new \Cmp3\System\ExecException('Ghostscript seems not to be installed! You might want to configure the ghostscript path with engine.ghostscript.binaryPath.');
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
	 * returns the path to the ghostscript binary
	 * @throws Exception
	 * @return string
	 */
	protected function GetGhostscriptBinary()
	{
		$strBinary = $this->objConfig->GetFilename('engine.ghostscript.binaryPath');
		if ($strBinary AND !file_exists($strBinary)) {
			throw new Exception('ghostscript binary is not available. Path: "' . $strBinary . '"');
		} else {
			$strBinary = 'gs';
		}
		return $strBinary;
	}
}





