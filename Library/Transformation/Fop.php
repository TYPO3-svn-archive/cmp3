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
 * @subpackage Transformation
 * @package    CMP3
 * @copyright  Copyright (c) 2012 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Transformation;


use Cmp3\Content\ContentType;

use Cmp3\Content\Content;


/**
 * TODO documentation
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Transformation
 * @package    CMP3
 *
 */
class Fop extends TransformerAbstract {


	/**
	 * Processes the transformation
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface &$objContent)
	{
		$xslFile = $this->objConfig->GetFilename('stylesheet');
		$fopConfigFile = $this->objConfig->GetFilename('engine.fop.config');
		$fopParameter = $this->objConfig->GetValue('engine.fop.parameter');

		$objOutputFile = \Cmp3\System\Files::GetTemp(str_replace('\\', '_', __CLASS__), '.pdf', \Cmp3\Cmp3::$TempPath);
		$objOutputFile->SetDeleteOnDestruct(false);

		$strScriptOptions = $fopParameter;

		if ($fopConfigFile) {
			$strScriptOptions .= ' -c ' . escapeshellarg($fopConfigFile);
		}

		$strScriptOptions .= ' -xml ' . escapeshellarg($objContent->File->GetPathAbsolute());

		if ($xslFile) {
			$strScriptOptions .= ' -xsl ' . escapeshellarg($xslFile);
		} else {
			if ($this->objLogger) $this->objLogger->Debug(__CLASS__ . ' FOP stylesheet: ' . $this->objConfig->GetValue('stylesheet'));
		}


		if ($foFile = $this->objConfig->GetValue('foout')) {
			$foFile = \Cmp3\System\Env::ResolvePath($foFile, false);
		}
		if ($foFile) {
			if ($this->objLogger) $this->objLogger->Debug(__CLASS__ . ' Write fo to file ' . $foFile);
			$strScriptOptions .= ' -foout ' . escapeshellarg($foFile);
		} else {
			$strScriptOptions .= ' -pdf ' . escapeshellarg($objOutputFile->GetPathAbsolute());
		}


		// add some paths to params so life will be easier
		$strScriptOptions .= ' -param cmp3_path_xml ' . escapeshellarg($objContent->File->AbsoluteDirname);
		$strScriptOptions .= ' -param cmp3_path_output ' . escapeshellarg($objOutputFile->AbsoluteDirname);
		if ($xslFile) $strScriptOptions .= ' -param cmp3_path_xsl ' . escapeshellarg(dirname($xslFile).'/');
		if ($fopConfigFile) $strScriptOptions .= ' -param cmp3_path_config ' . escapeshellarg(dirname($fopConfigFile).'/');


		$strBinary = $this->GetFopkBinary();
		$scriptCall = 	$strBinary . ' ' . $strScriptOptions;


		// it seems to be a good idea to change dir to config file because of relative paths
		$cwd = getcwd();
		if ($fopConfigFile) {
			chdir(dirname($fopConfigFile));
		}


		// this might work but not on debian, see http://www.mail-archive.com/fop-users%40xmlgraphics.apache.org/msg14027.html
		putenv('FOP_HYPHENATION_PATH=' . PATH_cmp3 . 'res/fop/offo-hyphenation-binary/fop-hyph.jar');
		// so we use this way
		putenv('JAVA_CLASSPATH=' . getenv('JAVA_CLASSPATH') . ':' . PATH_cmp3 . 'res/fop/offo-hyphenation-binary/fop-hyph.jar');


		$objExec = new \Cmp3\System\Exec();
		$objExec->SetOnErrorOutputException(false);

		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Transform content with FOP start');
		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' using stylesheet: ' . $xslFile);
		if ($this->objLogger) $this->objLogger->Debug(__CLASS__ . ' FOP call: ' . $scriptCall);

		$objExec->Run($scriptCall);

		$strErrorOutput = $objExec->GetErrorOutput();
		if ($this->objLogger AND $strErrorOutput) $this->objLogger->Debug(__CLASS__ . " FOP: \n" . $strErrorOutput);

		if (strpos($strErrorOutput, 'command not found')) {
			throw new \Cmp3\System\ExecException('fop seems not to be installed! You might want to configure the fop path with engine.fop.binaryPath.');
		}

		$objOutputFile->Changed();
		if (!$objOutputFile->Exists()) {
			throw new \Cmp3\System\ExecException('Something went wrong with fop call. No output file is generated');
		}

		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Transform content with FOP finished');

		$objProperties = array();
		$objProperties['Logger'] = $this->objLogger;
		$objContent = new \Cmp3\Content\Content($objProperties);
		$objContent->SetData($objOutputFile, ContentType::PDF);

		// switch back to original dir
		chdir($cwd);
	}


	/**
	 * returns the path to the fop binary
	 *
	 * @throws Exception
	 * @return string
	 */
	protected function GetFopkBinary()
	{
		$strBinary = $this->objConfig->GetFilename('engine.fop.binaryPath');
		if ($strBinary AND !file_exists($strBinary)) {
			throw new Exception('fop binary is not available. Path: "' . $strBinary . '"');
		} else {
			$strBinary = 'fop';
		}
		return $strBinary;
	}

}









