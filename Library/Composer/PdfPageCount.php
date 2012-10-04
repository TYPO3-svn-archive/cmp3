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
 * Composer which doesn't do any manipulation but count pages only.
 *
 * This might be usefull in combination with PdfIntentionallyBlankPages
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Composer
 * @package    CMP3
 */
class PdfPageCount extends ComposerAbstract {


	/**
	 * Processes a result object array
	 *
	 * @param \Cmp3\Job\Result[] $objResultArray
	 * @return \Cmp3\Job\Result[]
	 */
	public function Process ($objResultArray)
	{
		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Started compose processor');

		$intPageCount = 0;

		foreach($objResultArray as $objResult) {

			$objContent = $objResult->Content;

			if ($objContent->Type !== \Cmp3\Content\ContentType::PDF) {
				throw new Exception (__CLASS__ . ' provided content is not a pdf');
			}


			$objInfo = new \Cmp3\Pdf\Info;

			$objFile = PATH_site . "uploads/tx_nawpdftool/" . $strFile;
			$objInfoData = $objInfo->Get($objContent->File);

			$intPageCount += intval($objInfoData->Pages);

			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Pdf file "' . $objContent->File->Name . '" has ' . $objInfoData->Pages . ' pages');
		}

		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Pages count ' . $intPageCount);

		#TODO this is totally untested

		#FIXME we need to set job data here so it can be used in TS with {JobData:pagesCount}
		# ... otherwise it is totally useless :-)

		return $objResultArray;
	}

}









