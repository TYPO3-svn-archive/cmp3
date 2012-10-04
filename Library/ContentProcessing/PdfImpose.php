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
 * @copyright  Copyright (c) 2012 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\ContentProcessing;



/**
 * Content processors which imposes pages of a pdf file
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class PdfImpose extends ProcessorAbstract {


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

			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Processes content and impose pdf file');

			$objOverlay = new \Cmp3\Pdf\Impose($objContent->File, $this->objConfig, $this->objLogger);

			$objFile = $objOverlay->Render();

			$this->blnHasModified = true;

			$objContent->SetData($objFile, \Cmp3\Content\ContentType::PDF);

		} else {
			throw new Exception (__CLASS__ . ' provided data is not a pdf');
		}
	}

}









