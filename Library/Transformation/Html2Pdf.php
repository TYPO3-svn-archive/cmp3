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
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Transformation;



/**
 * Transforms HTML to PDF using a Converter
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Transformation
 * @package    CMP3
 */
class Html2Pdf extends TransformerAbstract {



	/**
	 * process content
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @throws Exception
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface &$objContent)
	{
		$strConverterClass = $this->objConfig->GetValue('converter');
		if (!class_exists($strConverterClass)) {
			throw new Exception("Could not find converter class: $strConverterClass");
		}


		$objPdfConverter = new $strConverterClass($this->objConfig, $this->objLogger);

		if ($this->objLogger) $this->objLogger->Debug(__CLASS__ . ' Use PDF Converter: ' . get_class($objPdfConverter));


		$objPdfConverter->Process($objContent);

		if ($objContent->Type !== \Cmp3\Content\ContentType::PDF) {
			throw new Exception("The transformation result is of wrong type: " . $objContent->Type);
		}

		$objOutputFile = $objContent->GetDataFile();

		// apply PDF layer with pdftk
		if ($this->objConfig->background AND !$objPdfConverter->hasBackgroundApplied) {

			$strBackgroundFilePath = \Cmp3\System\Files::MakeFilePathAbsolute(\Cmp3\System\Files::ResolvePath($this->objConfig->background));
			$objBackgroundFile = new \Cmp3\Files\File($strBackgroundFilePath);
			$objFileArray = array($objOutputFile, $objBackgroundFile);

			$objOverlay = new \Cmp3\Pdf\Overlay($objFileArray, $this->objConfig);
			$objOutputFile = $objOverlay->Render(Overlay::FIRST_IS_TOP);
		}

		if ($this->objConfig->overlay) {
			// it is not possible to apply background and stamp at once

			$strBackgroundFilePath = \Cmp3\System\Files::MakeFilePathAbsolute(\Cmp3\System\Files::ResolvePath($this->objConfig->overlay));
			$objBackgroundFile = new \Cmp3\Files\File($strBackgroundFilePath);
			$objFileArray = array($objOutputFile, $objBackgroundFile);

			$objOverlay = new \Cmp3\Pdf\Overlay($objFileArray, $this->objConfig);
			$objOutputFile = $objOverlay->Render(Overlay::FIRST_IS_BOTTOM);
		}

		$objContent->SetData($objOutputFile);

		if ($this->objLogger) $this->objLogger->Debug(__CLASS__ . 'Dest file: ' . $objOutputFile->Path);


	}

}





