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
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\ContentProcessing;



/**
 * Content processors which rewrite image sources using hires images from uploads/
 *
 * This works in TYPO3 FE context only.
 * An XCLASS needs to be installed so images will be tracked in $GLOBALS['TSFE']->imagesOnPageMapping().
 * If the XCLASS is not installed NO error will occur here!
 *
 * STATUS: beta
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class HtmlInsertHiresImages extends ProcessorAbstract {


	/**
	 * processes content
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface $objContent)
	{
		if (($content = $objContent->GetData()) AND is_object($GLOBALS['TSFE'])) {

			if (preg_match_all ('#"[^"]*(typo3temp/pics/[^"]*)"#', $content, $matches)) {

			    foreach ($matches[1] as $match) {
				    if ($match AND $filenameUploads = $GLOBALS['TSFE']->imagesOnPageMapping[$match]) {
				    	if ($this->_isImageFile($filenameUploads)) {
				    		$content = str_replace($match, $filenameUploads, $content);
							$this->blnHasModified = true;
				    	}
				    }
			    }
			}

			if ($this->blnHasModified) $objContent->SetData($content);
		}
	}


	/**
	 * returns true if a file is an image type: 'png', 'jpg', 'jpeg', 'gif'
	 *
	 * @param string $filenameUploads
	 * @return boolean
	 */
	function _isImageFile($filenameUploads) {
		$extension = strtolower(array_pop(explode(".", $filenameUploads)));
		$allowed = array('png', 'jpg', 'jpeg', 'gif');
		return in_array($extension, $allowed);
	}

}









