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
 * Defines content types which might be supported by the system
 *
 * The meaning of it is that we are able to collect content from different sources as different types and merge them in the end.
 *
 *
 * @author	Rene Fritz <r.fritz@bitmotion.de>
 * @package	CMP3
 * @subpackage	Content
 */
abstract class ContentType {

	/**
	 * This is normally not what we wanted and might cause an exception
	 * @var null
	 */
	const UNKNOWN  = null;

	const HTML    = 'html';
	const DOCBOOK = 'docbook';
	const XML     = 'xml';
	const TEXT    = 'text';
	const PDF     = 'pdf';

	const CMP3XML = 'cmp3xml';
}




