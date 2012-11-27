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
 * Content processors which changes links to absolute
 *
 * This works in TYPO3 FE context only
 *
 * STATUS: beta
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class HtmlMakeTypo3LinksAbsolute extends ProcessorAbstract {

	/**
	 * changes links to absolute
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface $objContent)
	{
		if ($content = $objContent->GetData()) {

			$content = preg_replace_callback('/( href=\")(?!#)(.*?)(\")/',
			                                       array('self','_fix_links_callback'), $content );

			$content = preg_replace_callback('/( src=\")(?!#)(.*?)(\")/',
			                                       array('self','_fix_links_callback'), $content );

			$content = preg_replace_callback('/(<form [^>]*action=\")(?!#)(.*?)(\")/',
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
	function _fix_links_callback($matches) {
		return $matches[1].\t3lib_div::locationHeaderUrl($matches[2]).$matches[3];
	}

}









