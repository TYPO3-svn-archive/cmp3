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
 * @subpackage Transformation
 * @package    CMP3
 * @copyright  Copyright (c) 2012 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Transformation;



/**
 * Base class of transformers which are used to transform one content type into another, like cmp3xml to pdf.
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Transformation
 * @package    CMP3
 *
 */
abstract class TransformerAbstract extends \Cmp3\BaseConfig implements TransformerInterface {



	/**
	 * Processes the transformation
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface &$objContent)
	{
		throw new Exception (__METHOD__.' is not implemented');
	}



	/***************************************
	 *
	 *   Configuration
	 *
	 ***************************************/

#@todo needed ?
	/**
	 * Returns hash which can be used as identifier for caching purposes
	 *
	 * @return string hash
	 * @see \Next\CachedContent
	 */
	public function GetConfigHash ()
	{
		$hash = md5(serialize($this->objConfig));
		return $hash;
	}
}









