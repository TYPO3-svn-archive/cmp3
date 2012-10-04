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


if (!defined('USE_DEBUGLIB')) define('USE_DEBUGLIB', TRUE);





/**
 * This gives access to the system.
 *
 *
 * @author Rene Fritz <r.fritz@bitmotion.de>
 */
class System_standalone extends \Cmp3\System\System_Abstract {



	/**
	 *
	 * @param $objConfig \Cmp3\Config\ConfigInterface
	 */
	public function __construct($objConfig)
	{
		$this->objConfig = $objConfig;

    	if ( get_magic_quotes_gpc() ) {
            $_REQUEST = $this->_stripSlashes($_REQUEST);
            $_GET     = $this->_stripSlashes($_GET);
            $_POST    = $this->_stripSlashes($_POST);
            $_COOKIE  = $this->_stripSlashes($_COOKIE);
        }
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
		#FIXME needs to be configurable!!

		$strLocaleArray = \Next\Locale::GetBrowser();
		if ($strLocaleArray)
			return reset($strLocaleArray);

		return 'en_US';
	}


    /**
     * Strip slashes on data
     *
     * stripslashes() needs to be used when magic_quotes is on
     *
     * @param mixed $data
     * @return mixed
     */
    protected function _stripSlashes($data)
    {
        if ( is_array($data) ) {
        	$r = array();
            foreach ( $data as $k => $v ) {
                $r[$k] = is_scalar($v)
                    ? stripslashes($v)
                    : $this->_stripSlashes($v);
            }
        } else {
        	$r = stripslashes($data);
        }

        return $r;
    }

}