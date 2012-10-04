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
 * @subpackage Content
 * @package    CMP3
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\Content;


/**
 * A content object provides content (as a string/object) but also provides additional properties and methods with meta data.
 * For example it is defined which type the content is (xml, pdf, ...).
 *
 * The content can be accessed as a string to process the data by php,
 * or via a file object which might be useful for processing with external tools.
 *
 * @author	Rene Fritz <r.fritz@bitmotion.de>
 * @package	CMP3
 * @subpackage	Content
 *
 * @property mixed $Data
 * @property mixed $Type Content type
 * @property ContentMeta $Meta This holds any additional data which might be needed by any later processing.
 * @property-read \Cmp3\Files\File $File
 */
interface ContentInterface {

	/**
	 * Sets the content as string or a file object
	 *
	 * @param mixed|\Cmp3\Files\File $mixData
	 * @param bool|\Cmp3\Content\ContentType $strContentType
	 * @return
	 */
	public function SetData($mixData, $strContentType=false);


	/**
	 * Returns the content
	 *
	 * @return mixed the content
	 */
	public function GetData();


	/**
	 * Returns the content as file object
	 *
	 * @return \Cmp3\Files\File the content file
	 */
	public function GetDataFile();

}


