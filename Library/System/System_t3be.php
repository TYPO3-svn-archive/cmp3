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
 * @subpackage System
 * @package    CMP3
 * @copyright  Copyright (c) 2009 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\System;




/**
 * This gives access to the TYPO3 system in backend context which might be an module.
 *
 *
 * @author Rene Fritz <r.fritz@bitmotion.de>
 */
class System_t3be extends \Cmp3\System\System_t3 {


	/***************************
	 *
	 * Locale Methods
	 *
	 **************************/


	/**
	 * Returns a locale string which is default for the system
	 *
	 * @return string
	 */
	public function GetLocale ()
	{
		$strLang = $GLOBALS['LANG']->lang;
		switch ($GLOBALS['LANG']->lang) {
			case 'de':
				$strLang = 'de_DE';
			break;

			default:
				$strLang = 'en_US';
			break;
		}

		return $strLang;
	}





	/***************************
	 *
	 * Get/Set
	 *
	 **************************/



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


			case 'PageID':
				$intPageId = 0;
				if (is_object($GLOBALS['SOBE'])) {
					$intPageId = $GLOBALS['SOBE']->id;
				}
				return $intPageId;
				break;



			default:
				return parent::__get($strName);
				break;
		}
	}


}