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
 * @subpackage Files
 * @package    CMP3
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Fetcher;



/**
 * {@inheritdoc}
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Fetcher
 * @package    CMP3
 */
class Typo3CurrentPage extends FetcherAbstract {


	/**
	 * {@inheritdoc}
	 */
	public function GetContent()
	{
		$this->strBaseUrl = $GLOBALS['TSFE']->baseUrlWrap('');

		$objProperties = array();
		$objProperties['Logger'] = $this->objLogger;
		$objContent = new \Cmp3\Content\Content($objProperties);
		$objContent->SetData($GLOBALS['TSFE']->content, $this->GetContentType());

		return $objContent;
	}


	/**
	 * {@inheritdoc}
	 */
	public function GetContentType()
	{
		// strtolower($this->objConfig->type)


		// we're not necessarily render HTML
		return \Cmp3\Content\ContentType::HTML;
	}


	/**
	 * rewrite image sources using hires images from uploads/
	 * needs xclass
	 *
	 * @param string $content
	 */
	protected function ReplaceImagesWithHiresVersion(& $content) {
		if (is_object($GLOBALS['TSFE'])) {
			preg_match_all ('#"[^"]*(typo3temp/pics/[^"]*)"#', $content, $matches);

		    foreach ($matches[1] as $match) {
			    if ($match AND $filenameUploads = $GLOBALS['TSFE']->imagesOnPageMapping[$match]) {
			    	if ($this->_isImageFile($filenameUploads)) {
			    		$content = str_replace($match, $filenameUploads, $content);
			    	}
			    }
			}
		}
	}


	/**
	 * returns true if a file is an image type: 'png', 'jpg', 'jpeg', 'gif'
	 *
	 * @param string $filenameUploads
	 * @return boolean
	 */
	protected function _isImageFile($filenameUploads) {
		$extension = strtolower(array_pop(explode(".", $filenameUploads)));
		$allowed = array('png', 'jpg', 'jpeg', 'gif');
		return in_array($extension, $allowed);
	}

}
