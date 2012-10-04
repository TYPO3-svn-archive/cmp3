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
 * PDF converter interface
 *
 * A converter takes some input and produces a pdf file.
 * Additionally the converter might be able to overlay pdf files.
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Converter
 * @package    CMP3
 *
 * @property-read boolean $hasBackgroundApplied Indicates if this engine has succesfully added a requested background to the rendered PDF so no further processing is needed
 * @property-read boolean $hasOverlayApplied Indicates if this engine has succesfully added a requested overlay to the rendered PDF so no further processing is needed
 */
interface ConverterInterface {

	/**
	 * Constructor
	 *
	 * @param \Cmp3\Config\ConfigInterface $objConfig
	 * @param null|\Cmp3\Log\Logger $objLog Logger object
	 * @return \Cmp3\Converter\ConverterInterface
	 */
	public function __construct(\Cmp3\Config\ConfigInterface $objConfig, $objLog = null);


	/**
	 * Processes a content object
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface $objContent);
}


