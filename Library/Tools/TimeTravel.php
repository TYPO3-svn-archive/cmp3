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
* @subpackage Transformation
* @package    CMP3
* @copyright  Copyright (c) 2008 Rene Fritz <r.fritz@colorcube.de>
* @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
* @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
*/


namespace Cmp3\Tools;


class TimeTravel {

	protected $execTime;


	/**
	 * Start the runtime mesurement
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->StartMicrotime();
	}


	/**
	 * Return the current microtime
	 *
	 * @return float
	 */
	public function GetCurrentMicrotime()
	{
		return microtime(true);
		$temp = microtime();

		$temp = explode(" ",$temp);
		return $temp[0]+$temp[1];
	}


	/**
	 * Start the runtime mesurement
	 *
	 * @return float
	 */
	public function StartMicrotime()
	{
		$this->execTime = $this->GetCurrentMicrotime();
	}


	/**
	 * Start the runtime mesurement
	 *
	 * @return float
	 */
	public function GetStartMicrotime()
	{
		return $this->execTime;
	}


	/**
	 * Return the total runtime
	 *
	 * @return float
	 */
	public function GetRuntime()
	{
		return number_format(($this->GetCurrentMicrotime() - $this->execTime), 8);
	}
}