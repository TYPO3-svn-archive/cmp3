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
 * This is a data container for one data field including the data itself but also some meta data.
 *
 * @property-read string $Name The field name
 * @property-read string $Type The field type
 * @property-read string $Format The field format type
 * @property-read string $Meta This defines the meaning of fields in relation to the record. Something like 'header' or 'body'
 * @property-read mixed  $Content The field content
 *
 * @see DataRowMeta_Abstract::GetFieldDefinition()
 * @see XmlRecord:: AddField()
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Data
 * @package    CMP3
 */
class Field {


	/**
	 * @var string
	 */
	protected $strName;

	/**
	 * @var string
	 */
	protected $strType;

	/**
	 * @var string
	 */
	protected $strFormat;

	/**
	 * @var string
	 */
	protected $strMeta;

	/**
	 * @var string
	 */
	protected $strContent;


	/**
	 *
	 * Over ride the parent createElement method
	 *
	 * @param string $strName Name The field name
	 * @param string $strType Type The field type
	 * @param string $strFormat Format The field format type
	 * @param string $strMeta  Meta This defines the meaning of fields in relation to the record. Something like 'header' or 'body'
	 * @param mixed $strContent Content The field content
	 */
	public function __construct( $strName, $strType, $strFormat, $strMeta, $strContent )
	{
		$this->strName = $strName;
		$this->strType = $strType;
		$this->strFormat = $strFormat;
		$this->strMeta = $strMeta;
		$this->strContent = $strContent;
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
			case 'Name':
				return $this->strName;

			case 'Type':
				return $this->strType;

			case 'Format':
				return $this->strFormat;

			case 'Meta':
				return $this->strMeta;

			case 'Content':
				return $this->strContent;

			default:
				throw new \Cmp3\UndefinedGetPropertyException ($strName);
			break;
		}
	}

}



