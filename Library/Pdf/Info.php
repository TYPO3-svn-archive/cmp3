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



/**
 * Object that provides meta data about an pdf file
 *
 * Data is inspired by pdfinfo
 *
 * @property string  $Creator eg.: Writer
 * @property string  $Title eg.:   debug136
 * @property string  $Author eg.:  bleed
 * @property string  $Producer eg.: Mac OS X 10.6 Quartz PDFContext
 * @property string  $CreationDate eg.: Mon Sep 14 12:00:34 2009
 * @property string  $ModificationDate eg.: Mon Sep 14 12:00:34 2009
 * @property boolean $Tagged
 * @property integer $Pages eg.:   100
 * @property boolean $Encrypted
 * @property string  $PageSize eg.: 698.976 x 905.906 pts
 * @property string  $PageFormat eg.: A4
 * @property string  $FileSize eg.: 26647338 bytes
 * @property boolean $Optimized
 * @property string  $Version eg.: 1.3
 */
class InfoData extends \Cmp3\Data\DataStructNoExceptionAbstract {

	/**
	 * Initializes the data array with keys that are allowed
	 *
	 * @return void
	 */
	protected function InitDataArray()
	{
		 $this->_dataArray = array(
		 		'Creator' => null,
		 		'Title' => null,
		 		'Author' => null,
		 		'Producer' => null,
		 		'CreationDate' => null,
		 		'ModificationDate' => null,
		 		'Tagged' => null,
		 		'Pages' => null,
		 		'Encrypted' => null,
		 		'PageSize' => null,
		 		'PageFormat' => null,
		 		'FileSize' => null,
		 		'Optimized' => null,
		 		'Version' => null,
		 );
	}
}


/**
 * PDF meta data info using pdfinfo
 *
 * sudo apt-get install poppler-utils
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage PDF
 * @package    CMP3
 *
 */
class Info {



	/**
	 *
	 * @var \Cmp3\Config\Config
	 */
	protected $objConfig;

#TODO Logger
	/**
	 * Constructor
	 *
	 * @param \Cmp3\Config\ConfigInterface $objConfig
	 * @return void
	 * @throws Exception
	 */
	public function __construct($objConfig = null)
	{
		if (is_object($objConfig) AND !($objConfig instanceof \Cmp3\Config\ConfigInterface)) {
			throw new Exception ('$objConfig is not of type \Cmp3\Config\ConfigInterface');
		}
		$this->objConfig = $objConfig ? $objConfig : new \Cmp3\Config\ArrayData();
	}


	/**
	 * Retrieves PDF meta data
	 *
	 * @param \Cmp3\Files\File|string $objFile
	 * @throws \Exception
	 * @return InfoData
	 */
	public function Get ($objFile)
	{
		if ($objFile instanceof \Cmp3\Files\File) {
			$strFilePath = $objFile->GetPathAbsolute();
		} else {
			$strFilePath = $objFile;
		}

		$strBinary = $this->GetPdfInfoBinary();
		$scriptCall = 	$strBinary . ' ' . escapeshellarg($strFilePath);

		if ($this->objLogger) $this->objLogger->Debug(__CLASS__ . ' pdftk called with command: ' . $scriptCall);

		$objExec = new \Cmp3\System\Exec();
		$objExec->Run($scriptCall);
		$strOutput = $objExec->GetOutput();



		/*
		 * pdfinfo is used as backend to retrieve the data which provides data like that:
		 * 	Creator:        Writer
		 *	Title:          debug136
		 *	Author:         bleed
		 *	Producer:       Mac OS X 10.6 Quartz PDFContext
		 *	CreationDate:   Mon Sep 14 12:00:34 2009
		 *	ModDate:        Mon Sep 14 12:00:34 2009
		 *	Tagged:         no
		 *	Pages:          100
		 *	Encrypted:      no
		 *	Page size:      698.976 x 905.906 pts
		 *	File size:      26647338 bytes
		 *	Optimized:      no
		 *	PDF version:    1.3
		 */

		$objInfoData = new InfoData;
		$strOutputArray = trim_explode("\n", $strOutput);
		foreach ($strOutputArray as $strLine) {
			list ($strKey, $strValue) = trim_explode(':', $strLine) ;
			if ($strKey) {
				$strKey = \Cmp3\String\String::CamelCaseFromUnderscore(str_replace(' ', '_' , $strKey));

				switch ($strKey) {

					case 'PageSize':
						preg_match('#\((.*?)\)#', $strValue, $matches);
						$objInfoData->PageFormat = $matches[1];
					break;

					case 'Pages':
						$objInfoData->$strKey = intval($strValue);
					break;

					case 'PDFVersion':
						$objInfoData->Version = $strValue;
					break;

					case 'ModDate':
						$objInfoData->ModificationDate = $strValue;
					break;

					default:
						if ($strValue == 'no') {
							$strValue = false;
						}elseif ($strValue == 'yes') {
							$strValue = true;
						}
						$objInfoData->$strKey = $strValue;
					break;
				}

			}
		}

		return $objInfoData;
	}


	/**
	 * returns the path to the pdfinfo binary
	 *
	 * @throws Exception
	 * @return string
	 */
	protected function GetPdfInfoBinary()
	{
		$strBinary = $this->objConfig->GetFilename('engine.pdfinfo.binaryPath');
		if ($strBinary AND !file_exists($strBinary)) {
			throw new Exception('pdfinfo binary is not available. Path: "' . $strBinary . '"');
		} else {
			$strBinary = 'pdfinfo';
		}
		return $strBinary;
	}
}


