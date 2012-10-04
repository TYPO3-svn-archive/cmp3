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
 * Composer which inserts pdf files in the result array to fit an imposition
 *
 * @see http://en.wikipedia.org/wiki/Intentionally_blank_page
 * @see http://de.wikipedia.org/wiki/Vakatseite
 *
 *
 * example config:
 *
 * 10 = \Cmp3\Composer\PdfIntentionallyBlankPages
 * // this needs to be provided by the calling application
 * // PdfPageCount might be used before this composer?
 * 10.pagesCount = {JobData:pagesCount}
 * 10.modulo = 4
 * 10.insertPartPosition = -1
 * 10.blankPages.1 = EXT:myproject/assets/pdf/blank-page-1.pdf
 * 10.blankPages.2 = EXT:myproject/assets/pdf/blank-page-2.pdf
 * 10.blankPages.3 = EXT:myproject/assets/pdf/blank-page-3.pdf
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Composer
 * @package    CMP3
 */
class PdfIntentionallyBlankPages extends ComposerAbstract {


	/**
	 * Processes a result object array
	 *
	 * @param \Cmp3\Job\Result[] $objResultArray
	 * @return \Cmp3\Job\Result[]
	 */
	public function Process ($objResultArray)
	{
		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Started compose processor');

		$intModulo = $this->Config->GetInteger('modulo');
		$intPagesCount = $this->Config->GetInteger('pagesCount');
		$intInsertPartPosition = $this->Config->GetInteger('insertPartPosition');

		if (!$intModulo) {
			throw new Exception('modulo not configured');
		}
		if (!$intPagesCount) {
			throw new Exception('pagesCount not configured');
		}
		if (!$intInsertPartPosition) {
			throw new Exception('insertPartPosition not configured');
		}


		if ($intPagesCount % $intModulo) {
			$intBlankPagesCount = $intModulo - ($intPagesCount % $intModulo);

			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . " Add $intBlankPagesCount blank pages. Pages count ($intPagesCount) doesn't fit needed document length defined by modulo $intModulo.");

			$objFile = $this->Config->GetFilename('blankPages.'.$intBlankPagesCount);
			if (!$objFile) {
				throw new Exception('Missing definition of blankPages.'.$intBlankPagesCount);
			}
			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . " Insert file $objFile at part position $intInsertPartPosition");
			$objFile = new \Cmp3\Files\File($objFile);

			$objContent = new \Cmp3\Content\Content;
			$objContent->SetData($objFile, \Cmp3\Content\ContentType::PDF);
			$objNewResult = new \Cmp3\Job\Result($objContent);
			$objNewResultArray = array($objNewResult);

			$objNewResultArray = array_merge(
					array_slice($objResultArray, 0, $intInsertPartPosition),
					array($objNewResult),
					array_slice($objResultArray, $intInsertPartPosition)
					);

		} else {

			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . " Nothing to do. Pages count ($intPagesCount) fit needed document length defined by modulo $intModulo.");

			return $objResultArray;
		}


		return $objNewResultArray;
	}

}









