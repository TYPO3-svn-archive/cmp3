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
 * This gives access to the TYPO3 TSFE system.
 *
 *
 * @author Rene Fritz <r.fritz@bitmotion.de>
 */
class System_t3fe extends \Cmp3\System\System_t3 {



	/**
	 * This is the current system object
	 *
	 * @var tslib_fe
	 */
	protected $objSystem = null;


	/**
	 *
	 * @param $objConfig \Cmp3\Config\ConfigInterface
	 */
	public function __construct($objConfig)
	{
		$this->objConfig = $objConfig;

		$this->SetSystem();

		$this->SetEncryptionKey($GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
	}


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
			case 'Type':
				return \Cmp3\System\SystemType::TYPO3_FE;
				break;

			case 'EncodingType':
				if ($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'])	{	// First priority: forceCharset! If set, this will be authoritative!
					$EncodingType = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'];
				} else {
					$EncodingType = $this->objSystem->renderCharset;
				}
				return $EncodingType;

			case 'TSFE':
				return $this->objSystem;
				break;

			case 'PageID':
				return $this->objSystem->id;
				break;

			case 'EncryptionKey':
				return $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'];
				break;


			default:
				return parent::__get($strName);
				break;
		}
	}


	/**
	 * Since the constructor is called in a very early stage,
	 * TSFE object is not there, so we must set it later
	 *
	 * @param tslib_fe $objSystem
	 * @return void
	 */
	public function SetSystem($objSystem = null) {
		$this->objSystem = $objSystem ? $objSystem : $GLOBALS['TSFE'];
	}



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
		$strLocale = $this->objSystem->config['config']['locale_all'];

		if ($strLocale) {

			// workaround for funky locales on windows systems
			//@see http://msdn.microsoft.com/en-us/library/39cwe7zf%28vs.71%29.aspx
			$windowsArray = array(
				'english' => 'en',
				'eng' => 'en',
				'german' => 'de',
				'ger' => 'de',
			);
			$strLocale = str_replace(array_keys($windowsArray), array_values($windowsArray), $strLocale);

			return $strLocale;
		}

		$strLocale = $this->objSystem->sys_language_isocode ? strtolower($this->objSystem->sys_language_isocode) : '';

		if ($strLocale)
			return $strLocale;

		// TYPO3 specific: Array with the iso names used for each system language in TYPO3:
		// Missing keys means: same as Typo3
		$isoArray = array(
			'ba' => 'bs',
			'br' => 'pt_BR',
			'ch' => 'zh_CN',
			'cz' => 'cs',
			'dk' => 'da',
			'si' => 'sl',
			'se' => 'sv',
			'gl' => 'kl',
			'gr' => 'el',
			'hk' => 'zh_HK',
			'kr' => 'ko',
			'ua' => 'uk',
			'jp' => 'ja',
			'vn' => 'vi',
		);

		$strLanguageCode = $this->objSystem->lang ? $this->objSystem->lang : $this->objSystem->config['config']['language'];
		$strLocale = (isset($isoArray[$strLanguageCode]) ? $isoArray[$strLanguageCode] : $strLanguageCode);

		if ($strLocale)
			return $strLocale;

		return 'en_US';
	}


}