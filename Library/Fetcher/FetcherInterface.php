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
 * Fetches content from a location based on configuration.
 * Different fetcher supports different locations and protocols: file, http, ftp, ldap.
 *
 * Fetchers are meant to be used by Sources. It is possible that some Sources only works with a subset of fetchers.
 *
 * Fetchers should handle different charsets by itself. The result has to be utf-8.
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Fetcher
 * @package    CMP3
 */
interface FetcherInterface {

	/**
	 * Fetches the data, wraps it into content object together with
	 * any auxiliary information (like content type) and returns this object.
	 *
	 * @return \Cmp3\Content object containing the fetched file contents and auxiliary information, if exists.
	 */
	public function GetContent();


	/**
	 * Returns the URL of the document
	 *
	 * @return string
	 */
	public function GetUrl();


	/**
	 * Returns a base URL to be used for resolving relative links inside the document
	 *
	 * @return string
	 */
	public function GetBaseUrl();


}
