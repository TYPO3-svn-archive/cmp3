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
 * @subpackage Config
 * @package    CMP3
 * @copyright  Copyright (c) 2008 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Config;




/**
 * Gives access to the applications configutration values.
 * In this case it's access to $GLOBALS['TSFE']->tmpl->setup which is the global TS array
 *
 * 
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Config
 * @package    CMP3
 */
class TSFESetup extends \Cmp3\Config\TypoScriptBase {


	private $blnInitialized = false;

	/**
	 * This is were we store the configuration values.
	 * $_config can't be used because we want to trigger the getter
	 * @var array
	 */
	protected $_configSpecial = array();


	/**
	 * Override method to perform a property "Get"
	 * This will get the property $strName
	 *
	 * @param string $strName Name of the property to get
	 * @return mixed
	 */
	public function __get ($strName)
	{
		if ($strName === '_config') {

			if (!$blnInitialized AND is_array($GLOBALS['TSFE']->tmpl->setup)) {
				$this->_configSpecial = & $GLOBALS['TSFE']->tmpl->setup;
			}

			return $this->_configSpecial;
		}
		return parent::__get($strName);
	}





	/***************************************
	 *
	 *   SET Configuration
	 *
	 ***************************************/



	/**
	 * Set a config value which is only temporary
	 *
	 * @param string 	$strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default'
	 * @param	mixed 	$value Value to be set. Can be an array but must be in TSConfig format
	 * @return void
	 */
	public function SetValue($strConfigPath, $value)
	{
		throw new Exception ('SetValue() not allowed. ' . __CLASS__);
	}

}

