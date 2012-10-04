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
 * Imposes pages of a pdf file using podofoimpose
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage PDF
 * @package    CMP3
 */
class Impose {



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


	protected $strPlanFile;
	protected $blnIsLuaPlan = true;


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

		$this->SetPlan($this->objConfig->GetFilename('plan'), $this->objConfig->isEnabled('lua'));
	}


	/**
	 * Sets the impose plan
	 *
	 * @param string $strPlanFile
	 * @return void
	 */
	public function SetPlan($strPlanFile, $blnIsLua=null)
	{
		$this->strPlanFile = $strPlanFile;

		if ($this->strPlanFile) {

			if ($blnIsLua===null) {
				$strPlan = file_get_contents($this->strPlanFile);
				if (strpos($strPlan, 'lua')) {
					$this->blnIsLuaPlan = true;
				} else {
					$this->blnIsLuaPlan = false;
				}
			}
		}
	}


	/**
	 * Renders the defined pages
	 *
	 * @throws \Exception
	 * @return \Cmp3\Files\File PDF file
	 */
	public function Render()
	{
		if (!$this->strPlanFile) {
			throw new Exception ("Configuration property 'plan' has no value set");
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

		// set plan file
		$strScriptOptions .= ' ' . escapeshellarg($this->strPlanFile);

		// set special parameter for lua plans
		$strScriptOptions .= ($this->blnIsLuaPlan ? ' lua' : '');


		$strBinary = $this->GetImposeBinary();
		$scriptCall = 	$strBinary . ' ' . $strScriptOptions;

		if ($this->objLogger) $this->objLogger->Debug(__CLASS__ . ' podofoimpose called with command: ' . $scriptCall);

		$objExec = new \Cmp3\System\Exec();
		$objExec->SetOnErrorOutputException(false);
		$objExec->Run($scriptCall);

		$strErrorOutput = $objExec->GetErrorOutput();
		if ($this->objLogger AND $strErrorOutput) $this->objLogger->Debug(__CLASS__ . " podofoimpose: \n" . $strErrorOutput);

		if (strpos($strErrorOutput, 'command not found')) {
			throw new \Cmp3\System\ExecException('podofoimpose seems not to be installed! You might want to configure the podofoimpose path with engine.podofoimpose.binaryPath.');
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
	 * returns the path to the podofoimpose binary
	 * @throws Exception
	 * @return string
	 */
	protected function GetImposeBinary()
	{
		$strBinary = $this->objConfig->GetFilename('engine.podofoimpose.binaryPath');
		if ($strBinary AND !file_exists($strBinary)) {
			throw new Exception('podofoimpose binary is not available. Path: "' . $strBinary . '"');
		} else {
			$strBinary = 'podofoimpose';
		}
		return $strBinary;
	}
}





