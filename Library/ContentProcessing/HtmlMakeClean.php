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
 * Content processors which cleans HTML markup using t3lib_parsehtml::XHTML_clean
 *
 * STATUS: beta
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class HtmlMakeClean extends ProcessorAbstract {


	/**
	 * cleans HTML markup using t3lib_parsehtml::XHTML_clean
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface $objContent)
	{
		if ($content = $objContent->GetData()) {

			require_once(PATH_t3lib.'class.t3lib_parsehtml.php');
			$XHTML_clean = \t3lib_div::makeInstance('t3lib_parsehtml');
			$content = $XHTML_clean->XHTML_clean($content);

			$this->blnHasModified = true;

			$objContent->SetData($content);
		}
	}

}





