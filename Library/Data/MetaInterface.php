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


#TODO cleanup interface and MetaGeneric, MetaAbstract

#TODO meta of table or record? this is not clearly defined


/**
 * Provides meta data information for fields of a table
 *
 * That is meant to be used in xml field attributes like this
 * <field name="header" type="text" format="line" meta="header">
 *
 * @property-read string $TableName name of the db table
 * @property-read string $Language locale
 * TODO record or table name?
 * @property-read string $Title A human readable name of the table (or the table name)
 * @property-read string $Type The record type. Example: The value of tt_content.CType.
 * @property-read array $DataFields fields names which are the keys of $DataArray
 *
 * @see DataRow_Interface
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Data
 * @package    CMP3
 */
interface MetaInterface {


	/**
	 * Returns the a fields data definition to be used in cmp3 xml
	 *
	 * @param string $strFieldName
	 * @return Field
	 */
	public function GetFieldDefinition($strFieldName);

}


