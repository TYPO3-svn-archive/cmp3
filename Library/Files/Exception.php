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
 * @copyright  Copyright (c) 2008 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Files;




/**
 * General file exception
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Files
 * @package    CMP3
 */
class Exception extends \Cmp3\Exception {
#TODO why is $previous not here - see below
}


/**
 * File exception for 'No stream available! File not open?'
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Files
 * @package    CMP3
 */
class NoStreamException extends Exception {
#TODO what is $previous for
	public function __construct($message = "", $code = 0, Exception $previous = NULL)
	{
		$message = $message ? $message : 'No stream available! File not open?';
		#parent::__construct($message, $code, $previous );
		parent::__construct($message, $code);
	}
}



/**
 * File exception for 'file could not access' which is of user rights issues or the file doesn't even exist
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Files
 * @package    CMP3
 */
class AccessException extends Exception {
}


/**
 * File exception for 'file not found'
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Files
 * @package    CMP3
 */
class NotFoundException extends AccessException {
}










