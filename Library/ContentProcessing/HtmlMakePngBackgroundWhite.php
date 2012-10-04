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
 * Content processors which changes png images (with transparency) to png with whicht background.
 *
 * This is useful for PDF generation because acrobat reader has a bug which causes uglyx font rendering when transparent images are included.
 *
 * Use before HtmlMakeImgSrcAbsolute
 *
 * STATUS: beta
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class HtmlMakePngBackgroundWhite extends ProcessorAbstract {


	/**
	 * Processes a content object
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface $objContent)
	{
		if ($content = $objContent->GetData()) {

			$content = preg_replace_callback('/(<img [^>]*src=\")(?!#)(.*?)(\")/',
			                                       array('self','_fix_links_callback'), $content );
			$this->blnHasModified = true;

			$objContent->SetData($content);
		}
	}



	/**
	 * callback function to convert relative URLs to absolute
	 *
	 * @private
	 */
	function _fix_links_callback($matches)
	{
		if (substr($matches[2], -4, 4) !== '.png') {
			return $matches[1].$matches[2].$matches[3];
		}

		$strFilepath =$matches[2];

		if (!file_exists( PATH_site.$strFilepath)) {
			return $matches[1].$matches[2].$matches[3];
		}

		$strFilepathNew = str_replace('.png', '_fffbg.png', $strFilepath);

		if (file_exists( PATH_site.$strFilepathNew)) {
			return $matches[1].$strFilepathNew.$matches[3];
		}

		// Get the original image.
		$src = imagecreatefrompng( PATH_site.$strFilepath);

		// Get the width and height.
		$width = imagesx($src);
		$height = imagesy($src);

		// Create a white background, the same size as the original.
		$bg = imagecreatetruecolor($width, $height);
		$white = imagecolorallocate($bg, 255, 255, 255);
		imagefill($bg, 0, 0, $white);

		// Merge the two images.
		imagecopyresampled(
		$bg, $src,
		0, 0, 0, 0,
		$width, $height,
		$width, $height);

		// Save the finished image.
		imagepng($bg,  PATH_site.$strFilepathNew, 0);


		return $matches[1].$strFilepathNew.$matches[3];
	}

}









