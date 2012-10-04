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
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */

namespace Cmp3;


/**
 * {@inheritdoc}
 *
 * @author	Rene Fritz <r.fritz@bitmotion.de>
 * @package	CMP3
 * @subpackage	Base
 *
 * @property \Cmp3\Log\Logger $objLogger
 */
class BaseLogger extends \Cmp3\Base {


	/**
	 * Logger object
	 *
	 * @var \Cmp3\Log\Logger
	 */

	protected $objLogger;



	/**
	 * Override method to perform a property "Get"
	 * This will get the property $strName
	 *
	 * @param string $strName Name of the property to get
	 * @return mixed
	 */
	public function __get($strName)
	{
		switch ($strName) {

			case 'Logger':
				return $this->objLogger;

			default:
				return parent::__get($strName);
		}
	}

	/**
	 * Override method to perform a property "Set"
	 * This will set the property $strName with $mixValue
	 *
	 * @param string $strName Name of the property to get
	 * @param string $mixValue Value of the property to set
	 * @return mixed
	 */
	public function __set($strName, $mixValue)
	{
		switch ($strName) {

			case 'Logger':
				return $this->objLogger = $mixValue;

			default:
				return parent::__set($strName, $mixValue);
		}
	}

}

