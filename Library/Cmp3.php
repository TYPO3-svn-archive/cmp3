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
 * @subpackage Base
 * @package    CMP3
 * @copyright  Copyright (c) 2012 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3;



/**
 * This is just a container for some globally needed values
 *
 * @package    CMP3
 * @subpackage    Base
 */
abstract class Cmp3 {

	/**
	 * Path for temporary files
	 *
	 * @var string
	 */
	public static $TempPath;

	/**
	 * Path for cache files
	 *
	 * @var string
	 */
	public static $CachePath;

	/**
	 * Path for log files
	 *
	 * @var string
	 */
	public static $LogPath;

	/**
	 * The language iso code of the default language - needs to be in lower case
	 *
	 * @var string
	 */
	public static $DefaultLanguage = 'en';


}
