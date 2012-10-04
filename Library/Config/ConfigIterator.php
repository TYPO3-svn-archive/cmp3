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
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Config;




/**
 * Iterator for config object
 *
 * Example
 *
 * TS Setup:
 *
 * parts {
 *     10.something = abc
 *     20.something = xyz
 *     30.another = 123
 *     40 = gfsd
 * }
 *
 * Usage:
 *
 * $objConfigIterator = new \Cmp3\Config\ConfigIterator ($objJobConfig, 'parts.');
 *
 * foreach($objConfigIterator as $key => $objConfig) {
 *     $someValue = $objConfig->GetValue('something');
 * }
 *
 * The Iterator will never use '40' because there's no '40.'!
 *
 * Therefore $someValue will be: abc, xyz, NULL
 *
 * STATUS: beta
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Config
 * @package    CMP3
 */

class ConfigIterator implements \Iterator {


	private $objConfig;

	private $objConfigProxy;

	private $strConfigKeysArray;


	/**
	 *
	 *
	 *
	 * @param \Cmp3\Config\ConfigResource $objConfig
	 * @param string $strConfigPath Pointer to an "object" in the config array tree, fx. 'setup.selections.default.'
	 * @return mixed
	 */
	public function __construct( \Cmp3\Config\ConfigInterface $objConfig, $strConfigPath )
	{
		$this->objConfig = $objConfig;
		$this->strConfigPath = $strConfigPath;

		$this->strConfigKeysArray = array_keys($this->objConfig->GetProperties($strConfigPath));
		$isDot = create_function('$value', 'return (substr($value,-1)===".");');
		$this->strConfigKeysArray = array_filter($this->strConfigKeysArray, $isDot);

		$this->rewind();
	}


	function rewind()
	{
		reset($this->strConfigKeysArray);
		$this->objConfigProxy = new \Cmp3\Config\Proxy($this->objConfig, $this->strConfigPath.$this->key());
		return $this->objConfigProxy;
	}


	function current()
	{
		return $this->objConfigProxy;
	}


	function key()
	{
		return current($this->strConfigKeysArray);
	}


	function next()
	{
		next($this->strConfigKeysArray);
		$this->objConfigProxy = new \Cmp3\Config\Proxy($this->objConfig, $this->strConfigPath.$this->key());
		return $this->objConfigProxy;
	}


	function valid()
	{
		return key($this->strConfigKeysArray) !== null;
	}
}

