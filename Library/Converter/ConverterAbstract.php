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
 * @subpackage PDF
 * @package    CMP3
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\Converter;


/**
 * PDF converter abstract class
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Converter
 * @package    CMP3
 */
class ConverterAbstract implements ConverterInterface {



	/**
	 * Indicates if this engine has successfully added a requested background to the rendered PDF so no further processing is needed
	 * @var boolean
	 */
	protected $blnHasBackgroundApplied = false;

	/**
	 * Indicates if this engine has successfully added a requested overlay to the rendered PDF so no further processing is needed
	 * @var boolean
	 */
	protected $blnHasOverlayApplied = false;

	/**
	 *
	 * @var \Cmp3\Config\ConfigInterface
	 */
	protected $objConfig = array();

	/**
	 * Logger object
	 *
	 * @var \Cmp3\Log\Logger
	 */
	protected $objLogger;


	/**
	 * Constructor
	 *
	 * @param \Cmp3\Config\ConfigInterface $objConfig
	 * @param null|\Cmp3\Log\Logger $objLog Logger object
	 * @return \Cmp3\Converter\ConverterAbstract
	 */
	public function __construct (\Cmp3\Config\ConfigInterface $objConfig, $objLog = null)
	{
		$this->objConfig = $objConfig;
		$this->objLogger = $objLog;
	}


	/**
	 * Override method to perform a property "Get"
	 * This will get the property $strName
	 *
	 * @param string $strName Name of the property to get
	 * @throws \Cmp3\UndefinedGetPropertyException
	 * @return mixed
	 */
	public function __get($strName)
	{
		switch ($strName) {
			case 'hasBackgroundApplied':
				return $this->blnHasBackgroundApplied;

			case 'hasOverlayApplied':
				return $this->blnHasOverlayApplied;

			default:
				throw new \Cmp3\UndefinedGetPropertyException ($strName);
		}
	}


	/**
	 * Processes the transformation
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @throws Exception
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface $objContent)
	{
		throw new Exception (__METHOD__.' is not implemented');
	}
}





