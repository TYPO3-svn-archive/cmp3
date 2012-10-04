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
 * @subpackage Files
 * @package    CMP3
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\Fetcher;


/**
 * {@inheritdoc}
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Fetcher
 * @package    CMP3
 */
abstract class FetcherAbstract extends \Cmp3\BaseConfig implements FetcherInterface {


	protected $strContentType = \Cmp3\Content\ContentType::UNKNOWN;

	protected $strUrl = null;
	protected $strBaseUrl = null;


	/**
	 * {@inheritdoc}
	 */
	public function GetContent()
	{
		die("Oops. Inoverridden 'GetContent' method called in ".get_class($this));
	}


	/**
	 * {@inheritdoc}
	 */
	public function GetUrl()
	{
		return $this->strUrl;
	}


	/**
	 * {@inheritdoc}
	 */
	public function GetBaseUrl()
	{
		return $this->strBaseUrl ? $this->strBaseUrl : $this->strUrl;
	}


	/**
	 * {@inheritdoc}
	 */
	public function GetContentType()
	{
		return $this->strContentType;
	}


	/**
	 * Returns the content type for a given file extension.
	 *
	 * If extension is not known, the default \Cmp3\Content\ContentType::UNKNOWN
	 * type will be returned
	 *
	 * @param  string $strSuffix File suffix like jpg, pdf, txt
	 * @return string
	 */
	public static function GetContentTypeForSuffix($strSuffix)
	{
		$mimeType = self::$suffix2type[strtolower($strSuffix)];

		// Fallback to the default
		if (! $mimeType) {
			$mimeType = \Cmp3\Content\ContentType::UNKNOWN;
		}

		return $mimeType;
	}


	protected static $suffix2type = array(
				'html' => \Cmp3\Content\ContentType::HTML,
				'htm' => \Cmp3\Content\ContentType::HTML,

				'xml' => \Cmp3\Content\ContentType::XML,

				'txt' => \Cmp3\Content\ContentType::TEXT,

				'pdf' => \Cmp3\Content\ContentType::PDF,
			);
}
