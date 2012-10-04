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
 * Shrink a PDF
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage PDF
 * @package    CMP3
 */
class Shrink {



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

	/*
	 * -dPDFSETTINGS=/screen (screen-view-only quality, 72 dpi images)
	 * -dPDFSETTINGS=/ebook (low quality, 150 dpi images)
	 * -dPDFSETTINGS=/printer (high quality, 300 dpi images)
	 * -dPDFSETTINGS=/prepress (high quality, color preserving, 300 dpi imgs)
	 * -dPDFSETTINGS=/default (almost identical to /screen)
	 */

	const QualityScreen   = '-dPDFSETTINGS=/screen';
	const QualityEbook    = '-dPDFSETTINGS=/ebook';
	const QualityPrinter  = '-dPDFSETTINGS=/printer';
	const QualityPrepress = '-dPDFSETTINGS=/prepress';
	const QualityDefault  = '-dPDFSETTINGS=/default';

	protected $strParameter = self::QualityEbook;

	const DefaultParameter = '-sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH';


	/*
	 * @see http://www.ubuntugeek.com/ubuntu-tiphowto-reduce-adobe-acrobat-file-size-from-command-line.html
	 *
	 * I had to use -dUseCIEColor to maintain the correct colors in the conversion process (by default, all of the shades turn out weird).
	 *
	 * If you’d like to fine-tune the compression settings beyond what the presets offer (-dPDFSETTINGS)=/preset), you can manipulate the parameters directly; a list of them can be found here: http://pages.cs.wisc.edu/~ghost/doc/cvs/Ps2pdf.htm
	 *
	 * I needed a target file size beyond what even /screen could produce, so I set the resolution to 38 (images really start to degrade at this point):
	 *
	 * gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dColorImageResolution=38 -dColorImageDownsampleType=/Average -dGrayImageDownsampleType=/Average -dGrayImageResolution=38 -dMonoImageResolution=38 -dMonoImageDownsampleType=/Average -dOptimize=true -dDownsampleColorImages=true -dDownsampleGrayImages=true -dDownsampleMonoImages=true -dUseCIEColor -dColorConversionStrategy=/sRGB -dNOPAUSE -dQUIET -dBATCH -sOutputFile=output.pdf input.pdf
	 *
	 * Optimal results may require some experimenting.
	 *
	 */

	/*
	 * It seems pdf with masks on cmyk images breaks ghostscript when -dUseCIEColor is used
	 *
	 * This seems to work
	 * -dPDFSETTINGS=/ebook -dColorConversionStrategy=/sRGB -dColorConversionStrategyForImages=/sRGB -dProcessColorModel=/DeviceRGB
	 *
	 * Colors does not perfectly match but are good enough
	 */


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

		$this->SetQuality($this->objConfig->GetValue('quality'));
	}


	/**
	 * Sets the quality
	 *
	 * @param string $strQuality
	 * @return void
	 */
	public function SetQuality($strQuality)
	{
		if ($strQuality) {
			switch (strtolower($strQuality)) {
				case 'screen':
					$this->strParameter = self::QualityScreen;
				break;
				case 'ebook':
					$this->strParameter = self::QualityEbook;
				break;
				case 'prepress':
					$this->strParameter = self::QualityPrepress;
				break;
				case 'printer':
					$this->strParameter = self::QualityPrinter;
				break;
				case 'default':
					$this->strParameter = self::QualityDefault;
				break;

				default:
					$this->strParameter = $strQuality;
				break;
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
		// get temp output file
		$this->objOutputFile = \Cmp3\System\Files::GetTemp(str_replace('\\', '_', __CLASS__), '.pdf', \Cmp3\Cmp3::$TempPath);
		if ($this->objConfig->isEnabled('debug')) {
			$this->objOutputFile->SetDeleteOnDestruct(false);
		}

		// set general parameter
		$strScriptOptions =  $this->objConfig->GetValue('parameter.default', self::DefaultParameter);

		// set special parameter for defined quality
		$strScriptOptions .= ' ' . $this->strParameter;

		// set special parameter for defined quality
		$strScriptOptions .= ' ' . $this->objConfig->GetValue('parameter.additional');

		// set output file
		$strScriptOptions .= ' -sOutputFile=' . escapeshellarg($this->objOutputFile->GetPathAbsolute());

		// set input file
		$strScriptOptions .= ' ' . escapeshellarg($this->objInputFile->GetPathAbsolute());

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





