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
 * Content processors which do a {URL} marker replacement.
 * The url is for a pdf footer or similar. The url is truncated to 80 characters.
 *
 * STATUS: beta
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class UrlMarker extends ProcessorAbstract {



	/**
	 * does a {URL} marker replacement
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface $objContent)
	{
		if ($content = $objContent->GetData()) {

			$TYPO3_REQUEST_URL = \tx_cmp3::$System->GetEnv('REQUEST_URL');
			$TYPO3_REQUEST_URL = preg_replace('#^http://#', '', $TYPO3_REQUEST_URL);
			$TYPO3_REQUEST_URL = preg_replace('#&type=.*#', '', $TYPO3_REQUEST_URL);
			$TYPO3_REQUEST_URL = preg_replace('#\?$#', '', $TYPO3_REQUEST_URL);
			$TYPO3_REQUEST_URL = \Cmp3\String\String::Truncate($TYPO3_REQUEST_URL, 80);

			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Processes content by replacing {URL} with: ' . $TYPO3_REQUEST_URL);

			$content = str_replace('{URL}', ($TYPO3_REQUEST_URL), $content);

			$this->blnHasModified = true;

			$objContent->SetData($content);
		}
	}

}









