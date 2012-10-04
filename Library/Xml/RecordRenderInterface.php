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
 * @subpackage XML
 * @package    CMP3
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\Xml;


/**
 * This generates from an DataRowMeta object a XmlRecord object
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage XML
 * @package    CMP3
 */
interface RecordRenderInterface {


	/**
	 * Constructor
	 *
	 * @param \Cmp3\Data\MetaInterface $objDataRowMeta
	 * @return \Cmp3\Xml\RecordRenderInterface
	 */
	public function __construct(\Cmp3\Data\MetaInterface $objDataRowMeta);


	/**
	 * Creates Xml of the given data
	 *
	 * @return Record
	 */
	public function GetXml();
}



