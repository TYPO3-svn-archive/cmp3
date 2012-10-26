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
 * @subpackage Job
 * @package    CMP3
 * @copyright  Copyright (c) 2012 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\Job;


/**
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Job
 * @package    CMP3
 *
 * ATTENTION this is here for the IDE not mean as interface - see below
 * @property-read \Cmp3\Files\File $File
 * @property-read \Cmp3\Content\ContentInterface $Content
 *
 * @property-read boolean $hasErrors
 * @property-read string $ErrorMessage
 * @property-read string $Log
 */
interface ResultInterface {}


/**
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Job
 * @package    CMP3
 *
 * @property-read boolean $hasErrors
 * @property-read string $ErrorMessage
 * @property-read string $Log
 */
class Result implements ResultInterface {

	/**
	 * Flag that indicates if errors occured while processing
	 * @var boolean
	 */
	public $hasErrors = false;

	/**
	 * Flag that indicates if errors occured while processing
	 * @var string
	 */
	public $ErrorMessage = false;

	/**
	 * Log text
	 * @var string
	 */
	public $Log = '';

	/**
	 *
	 * @var \Cmp3\Content\ContentInterface
	 */
	public $Content;


	public function __construct($objContent)
	{
		$this->Content = $objContent;
	}
}

