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
 * In this case it's access to tsconfig for FE and BE usage.
 *
 * STATUS: alpha - code was used already and worked but this a refactored untested version
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Config
 * @package    CMP3
 */
class TSConfig extends \Cmp3\Config\TypoScriptBase  {


	/**
	 * stores the configuration values
	 * @var array
	 */
	protected $_config = null;


	/**
	 * Init config values - which means they are fetched from TSConfig
	 * Page and user TSconfig are merged
	 *
	 * @param integer $pid Page id to fetch the tsconfig from
	 * @return void
	 */
	public function __construct($pid=null)
	{
		$pageTSconfig = array();
		$userTSconfig = array();

		// If the pid is given, we need to read the TSConfig with the backend class.
		// The BE_USER object is not mandatory for this static method.
		if ($pid) {
			require_once(PATH_t3lib . 'class.t3lib_befunc.php');
			$pageTSconfig = t3lib_BEfunc::getPagesTSconfig($pid);
		}

		// Merge userTSconfig into the result
		if (is_object($GLOBALS['TSFE'])) {

			$userTSconfig = $GLOBALS['TSFE']->fe_user->getUserTSconf();
			if (!$pageTSconfig) {
				$pageTSconfig = $GLOBALS['TSFE']->getPagesTSconfig();
			}

		} elseif (is_object($GLOBALS['BE_USER'])) {
			$userTSconfig = $GLOBALS['BE_USER']->userTS;
		}

		$this->_config = array_merge_recursive_overrule($pageTSconfig, $userTSconfig);
	}
}



