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
 * @subpackage ContentProcessing
 * @package    CMP3
 * @copyright  Copyright (c) 2008 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\ContentProcessing;



/**
 * Content processors which overlays two pdf files
 *
 * A pdf can be merged as background or as foreground to the content pdf.
 *
 *
 * Example:
 *
 * 20 = \Cmp3\Composer\PdfOverlay
 * 20.background = EXT:myproject/stylesheets/nice-background.pdf
 * 20.multi = 1
 *
 * 30 = \Cmp3\Composer\PdfOverlay
 * 30.overlay = EXT:myproject/stylesheets/nice-foreground.pdf
 *
 * multi=0
 * Only the first page from the overlay pdf is used and will be applied to every page of the content pdf.
 *
 * multi=1
 * Applies each page of the overlay pdf to the corresponding page of the content pdf.
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class PdfOverlay extends ProcessorAbstract {


	/**
	 * Processes a content object
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface $objContent)
	{
		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Started content processor');

		if ($objContent->Type === \Cmp3\Content\ContentType::PDF) {

			$strOverlayFilePath = $this->objConfig->GetFilename('overlay');
			$strBackgroundFilePath = $this->objConfig->GetFilename('background');
			if (!$strOverlayFilePath AND !$strBackgroundFilePath) {
				throw new Exception(__METHOD__ . " overlay or background not defined");
			}

			if ($strOverlayFilePath) {
				$objOverlay = new \Cmp3\Files\File($strOverlayFilePath);
				$strMode = \Cmp3\Pdf\Overlay::FIRST_IS_BOTTOM;
				if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Processes content and overlay pdf with ' . $strOverlayFilePath);
			} else {
				$objOverlay = new \Cmp3\Files\File($strBackgroundFilePath);
				$strMode = \Cmp3\Pdf\Overlay::FIRST_IS_TOP;
				if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Processes content and put background into pdf: ' . $strBackgroundFilePath);
			}

			if ($this->objConfig->isEnabled('multi')) {
				$strMode = $strMode | \Cmp3\Pdf\Overlay::MULTI;
			}

			$objFile = $objContent->GetDataFile();

			$objFileArray = array();
			$objFileArray[] = $objFile;
			$objFileArray[] = $objOverlay;

			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Processes content and overlay pdf files');

			$objOverlay = new \Cmp3\Pdf\Overlay($objFileArray);

			$objFile = $objOverlay->Render($strMode);

			$this->blnHasModified = true;

			$objContent->SetData($objFile, \Cmp3\Content\ContentType::PDF);

		} else {
			throw new Exception (__CLASS__ . ' provided data is not a pdf');
		}
	}

}









