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
 * @subpackage Composer
 * @package    CMP3
 * @copyright  Copyright (c) 2012 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Composer;



/**
 * Composer which overlays pdf files.
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
 * @subpackage Composer
 * @package    CMP3
 */
class PdfOverlay extends ComposerAbstract {


	/**
	 * Processes a result object array
	 *
	 * @param \Cmp3\Job\Result[] $objResultArray
	 * @return \Cmp3\Job\Result[]
	 */
	public function Process ($objResultArray)
	{
		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Started compose processor');

		$objProperties = array();
		$objProperties['Config'] = $this->objConfig;
		$objProperties['Logger'] = $this->objLogger;
		$objOberlayProcessor = new \Cmp3\ContentProcessing\PdfOverlay($objProperties);

		foreach($objResultArray as $objResult) {

			$objContent = $objResult->Content;

			if ($objContent->Type !== \Cmp3\Content\ContentType::PDF) {
				throw new Exception (__CLASS__ . ' provided content is not a pdf');
			}

			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Processes result and overlay pdf file');

			$objOberlayProcessor->Process($objContent);
			$objContent->File->SetDeleteOnDestruct(false);
		}

		return $objResultArray;
	}

}









