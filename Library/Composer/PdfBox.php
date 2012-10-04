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
 * Composer which set bounding boxes of pdf files
 *
 * @see http://de.wikipedia.org/wiki/Portable_Document_Format#Angaben_der_Seitengeometrie
 * @see http://www.prepressure.com/pdf/basics/page_boxes
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Composer
 * @package    CMP3
 */
class PdfBox extends ComposerAbstract {


	/**
	 * Processes a result object array
	 *
	 * @param \Cmp3\Job\Result[] $objResultArray
	 * @return \Cmp3\Job\Result[]
	 */
	public function Process ($objResultArray)
	{
		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Started pdf box processor');

		foreach($objResultArray as $objResult) {

			$objContent = $objResult->Content;

			if ($objContent->Type !== \Cmp3\Content\ContentType::PDF) {
				throw new Exception (__CLASS__ . ' provided content is not a pdf');
			}

			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Processes result and impose pdf file');

			$objBox = new \Cmp3\Pdf\Box($objContent->File, $this->objConfig, $this->objLogger);

			$objFileResult = $objBox->Render();
			$objFileResult->SetDeleteOnDestruct(false);

			$objContent->SetData($objFileResult);
		}

		return $objResultArray;
	}

}









