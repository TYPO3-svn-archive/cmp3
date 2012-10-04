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
 * @subpackage Data
 * @package    CMP3
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\Data;


/**
 * Data provider class for a single record with iterator
 *
 *
 * @property string $TableName name of the table
 * @property-read string $Language locale
 * @property array $DataArray Current data of the record with key=>$value pairs. Might not yet be saved to DB.
 * @property array $DataFields fields names which are the keys of $DataArray
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Data
 * @package    CMP3
 */
interface RowInterface extends \ArrayAccess,\Iterator {

	/**
	 * Constructor
	 *
	 * @param string $strTableName
	 * @param array $strDataArray full record data
	 * @param string $strLanguage
	 * @return void
	 */
	public function __construct($strTableName, $strDataArray, $strLanguage);

}

