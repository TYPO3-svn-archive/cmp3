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
 * General cmp3 exception
 *
 * @package    CMP3
 * @subpackage    Base
 */
class Exception extends \Exception {}


/**
 * Property exception for missing property
 * @package    CMP3
 * @subpackage    Base
 */
class UndefinedPropertyException extends Exception {
	public function __construct($strProperty) {
		parent::__construct(sprintf("Undefined property or variable '%s'", $strProperty), 2);
	}
}


/**
 * Property exception for missing get property
 * @package    CMP3
 * @subpackage    Base
 */
class UndefinedGetPropertyException extends UndefinedPropertyException {
	public function __construct($strProperty) {
		parent::__construct(sprintf("Undefined get property or variable '%s'", $strProperty), 2);
	}
}


/**
 * Property exception for missing set property
 * @package    CMP3
 * @subpackage    Base
 */
class UndefinedSetPropertyException extends UndefinedPropertyException {
	public function __construct($strProperty) {
		parent::__construct(sprintf("Undefined set property or variable '%s'", $strProperty), 2);
	}
}


/**
 * Wrong type exception
 * @package    CMP3
 * @subpackage    Base
 */
class WrongTypeException extends Exception {
	public function __construct($strName, $mixValue, $strWanted) {
		$strType = is_object($mixValue) ? get_class($mixValue) : gettype($mixValue);
		parent::__construct(sprintf("Wrong type of set property '%s'. '%s' given '%s' wanted", $strName, $mixValue, $strWanted), 2);
	}
}


/**
 * Exception to be used when parameter of a function call doesn't match the expected.
 *
 * @package    CMP3
 * @subpackage    Base
 */
class WrongParameterException extends Exception {}


/**
 * Exception to be used when an expected configuration is not set
 *
 * @package    CMP3
 * @subpackage    Base
 */
class UndefinedConfigurationException extends Exception {
	public function __construct($strProperty, $strClass) {
		parent::__construct(sprintf("Configuration '%s' expected in class %s", $strProperty, $strClass), 2);
	}
}


