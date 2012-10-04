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
 * @copyright  Copyright (c) 2008 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Files;



/**
 * The cURL class is a thin wrapper around the procedural interface
 * to cURL provided by PHP.
 *
 * STATUS alpha -
 *
 * Based on code from
 * Dick Munroe munroe@csworks.com
 * Richard W. Schlatter (richard@rth10260.info)
 * artem at zabsoft dot co dot in
 *
 * There are other ways doing this without curl. See here:
 * http://www.lornajane.net/posts/2010/Three-Ways-to-Make-a-POST-Request-from-PHP
 * http://netevil.org/blog/2006/nov/http-post-from-php-without-curl
 *
 * @todo make this work were curl is not available
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Files
 * @package    CMP3
 */
class Fetch {

	public static $Useragent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)';
	public static $Referer ="http://www.google.com";
	public static $Timeout = 10;
	public static $MaxRedirect = 4;


	/**
	 * The fetched content
	 * @var string
	 */
	protected $_content;



	/**
	 * The parsed contents of the HTTP header if one happened in the
	 * message.  All repeated elements appear as arrays.
	 *
	 * The headers are stored as an associative array, the key of which
	 * is the name of the header, e.g., Set-Cookie, and the values of which
	 * are the bodies of the header in the order in which they occurred.
	 *
	 * Some headers can be repeated in a single header, e.g., Set-Cookie and
	 * pragma, so each type of header has an array containing one or more
	 * headers of the same type.
	 *
	 * The names of the headers can, potentially, vary in spelling from
	 * server to server and client to client.  No attempt to regulate this
	 * is made, i.e., the curl class does not force all headers to lower
	 * or upper class, but it DOES collect all headers of the same type
	 * under the spelling of the type of header used by the FIRST header
	 * of that type.
	 *
	 * For example, two headers:
	 *
	 *   1. Set-Cookie: ...
	 *   2. set-cookie: ...
	 *
	 * Would appear as $this->_header['Set-Cookie'][0] and ...[1]
	 *
	 * @var array
	 */
	protected $_header;

	/**
	 * Current setting of the curl options.
	 *
	 * @var array
	 */
	protected $_options;

	/**
	 * Status information for the last executed http request.  Includes the errno and error
	 * in addition to the information returned by curl_getinfo.
	 *
	 * The keys defined are those returned by curl_getinfo with two additional
	 * ones specified, 'error' which is the value of curl_error and 'errno' which
	 * is the value of curl_errno.
	 *
	 * @link http://www.php.net/curl_getinfo
	 * @link http://www.php.net/curl_errno
	 * @link http://www.php.net/curl_error
	 * @var array
	 */
	protected $_status;

	/**
	 * Collection of headers when curl follows redirections as per CURLOPTION_FOLLOWLOCATION.
	 * The collection includes the headers of the final page too.
	 *
	 * @var array
	 */
	protected $_followed;


	/**
	 * curl class constructor
	 *
	 * Initializes the curl class for it's default behavior:
	 *  o no HTTP headers.
	 *  o return the transfer as a string.
	 *  o URL to access.
	 * By default, the curl class will simply read the URL provided
	 * in the constructor.
	 *
	 * @link http://www.php.net/curl_init
	 * @param string $strURL [optional] the URL to be accessed by this instance of the class.
	 * @param boolean $blnGetHeader Defines if the HTTP Headers will be fetched
	 * @param boolean $blnGetBody Defines if the content itself will be fetched
	 */
	public function __construct($strURL=null, $blnGetHeader = false, $blnGetBody = true)
	{
		if (!function_exists('curl_init'))
		{
			trigger_error('PHP was not built with --with-curl, rebuild PHP to use the curl class.', E_USER_ERROR);
		}

		$this->_handle = curl_init();

		$this->_content = null;

		$this->_header = null;
		$this->_options = null;
		$this->_status = null;
		$this->_followed = null;


		if ($strURL){
			$this->SetOption(CURLOPT_URL, $strURL);
		}
		$this->SetOption(CURLOPT_HEADER, false);
		$this->SetOption(CURLOPT_RETURNTRANSFER, true);
		$this->SetOption(CURLOPT_FOLLOWLOCATION, true);

		// what is this for?
		$this->SetOption(CURLOPT_HTTPHEADER, array('Expect:'));


		$this->SetOption(CURLOPT_USERAGENT, self::$Useragent);
		$this->SetOption(CURLOPT_REFERER, self::$Referer);
		$this->SetOption(CURLOPT_TIMEOUT, self::$Timeout);
		$this->SetOption(CURLOPT_MAXREDIRS, self::$MaxRedirect);


		if($blnGetHeader) {
			$this->SetOption(CURLOPT_HEADER,true);
		}

		if(!$blnGetBody) {
			$this->SetOption(CURLOPT_NOBODY,true);
		}

	}


	/**
	 * Free the resources associated with the curl session.
	 *
	 * @link http://www.php.net/curl_close
	 */
	function __destruct()
	{
		curl_close($this->_handle);
		$this->_handle = null;
	}


	/**
	 * Enables/Disables authentication
	 *
	 * @param string|false $name Username
	 * @param string $pass
	 * @return void
	 */
	public function SetAuthentication($name, $pass)
	{
		if ($name) {
			$this->SetOption(CURLOPT_USERPWD, $name.':'.$pass);
		} else {
			$this->SetOption(CURLOPT_USERPWD, null);
		}
	}



	/**
	 * The name of the file containing the cookie data. The cookie file can be in Netscape format, or just plain HTTP-style headers dumped into a file.
	 *
	 * @param string $path
	 * @return void
	 */
	public function SetCookiFileLocation($path)
	{
		$this->SetOption(CURLOPT_COOKIEJAR, $path);
		$this->SetOption(CURLOPT_COOKIEFILE, $path);
	}


	/**
	 * Set data to be sent as POST data
	 *
	 * The full data to post in a HTTP "POST" operation.
	 * To post a file, prepend a filename with @ and use the full path.
	 *
	 * IMPORTANT setting an array seems to be buggy in some PHP/libcurl versions
	 *
	 * This can either be passed as a urlencoded string like 'para1=val1&para2=val2&...' or as an array with the field name as key and field data as value. If value  is an array, the Content-Type header will be set to multipart/form-data.
	 * @param string|array $postFields
	 * @return void
	 */
	public function SetPostData ($postFields)
	{
		if ($postFields) {
			$this->SetOption(CURLOPT_POST, true);
			$this->SetOption(CURLOPT_POSTFIELDS, $postFields);
		} else {
			$this->SetOption(CURLOPT_POST, false);
			$this->SetOption(CURLOPT_POSTFIELDS, null);
		}
	}


	/**
	 * Set's the useragent string
	 * @param string $userAgent
	 * @return void
	 */
	public function SetUserAgent($userAgent)
	{
		$this->SetOption(CURLOPT_USERAGENT, $userAgent);
	}


	/**
	 * Set's the referer string
	 * @param string $referer
	 * @return coid
	 */
	public function SetReferer($referer)
	{
		$this->SetOption(CURLOPT_REFERER, $referer);
	}


	/**
	 * Returns the current setting of the request option.  If no
	 * option has been set, it return null.
	 *
	 * @param integer the requested CURLOPT.
	 * @returns mixed
	 */
	function GetOption($theOption)
	{
		if (isset($this->_options[$theOption])) {
			return $this->_options[$theOption];
		}

		return null;
	}


	/**
	 * Set a curl option.
	 *
	 * @link http://www.php.net/curl_setopt
	 * @param mixed $theOption One of the valid CURLOPT defines.
	 * @param mixed $theValue the value of the curl option.
	 */
	function SetOption($theOption, $theValue)
	{
		curl_setopt($this->_handle, $theOption, $theValue);
		$this->_options[$theOption] = $theValue;
	}



	/****************************
	 *
	 * Content Stuff
	 *
	 ****************************/




	/**
	 * Perform the url fetch
	 *
	 * @param $strUrl
	 * @return false|string Content
	 */
	public function Get($strUrl = null)
	{
		if($strUrl){
			$this->SetOption(CURLOPT_URL, $strUrl);
		}


		$this->_content = curl_exec($this->_handle);

#TODO use object for this
		// collect error data
		$this->_status = curl_getinfo($this->_handle);
		$this->_status['errno'] = curl_errno($this->_handle);
		$this->_status['error'] = curl_error($this->_handle);


		//
		// Collect headers espesically if CURLOPT_FOLLOWLOCATION set.
		// Parse out the http header (from last one if any).
		//

		$this->_header = null;

		//
		// If there has been a curl error, just return a null string.
		//

		if ($this->_status['errno']) {
			return $this->_content = false;
		}

		if ($this->getOption(CURLOPT_HEADER)) {

			$this->_followed = array();
			$rv = $this->_content;

			while (count($this->_followed) <= $this->_status['redirect_count'])
			{
				$theArray = preg_split("/(\r\n){2,2}/", $rv, 2);

				$this->_followed[] = $theArray[0];

				$rv = $theArray[1];
			}

			$this->parseHeader($theArray[0]);

			return $this->_content = $theArray[1];

		} else {
			return $this->_content;
		}

		return $this->_content;
	}


	/**
	 * Fetch content and save as file
	 *
	 * @param string $strUrl|\Cmp3\Files\FileInterface Url to be fetched. If $strFilePath is not set this will be used as file path.
	 * @param string|\Cmp3\Files\FileInterface $strFilePath File path to the file where the content will be stored to. If not set $strUrl will be used as file path.
	 * @return boolean
	 */
	public function GetAsFile ($strUrl, $strFilePath=null)
	{
		if($strUrl AND $strFilePath){
			$this->SetOption(CURLOPT_URL, $strUrl);
		} else {
			$strFilePath = $strUrl;
		}

		if ($strFilePath instanceof \Cmp3\Files\FileInterface) {
			$strFilePath = $strFilePath->GetPathAbsolute();
		} else {
			$strFilePath = \Cmp3\System\Files::ResolvePath($strFilePath);
		}

		$fh = fopen($strFilePath, 'w');
		curl_setopt($this->_handle, CURLOPT_FILE, $fh);
		$r = $this->GET();
		fclose($fh);

		return $r ? true : false;
	}


	/**
	 * Returns fetched content
	 * @return string
	 */
	public function __tostring()
	{
		return $this->_content;
	}


	/**
	 * Returns fetched content
	 * @return string
	 */
	public function GetContent()
	{
		if ($this->_content === null) {
			$this->Get();
		}
		return $this->_content;
	}


	/**
	 * Return the status information of the last curl request.
	 *
	 * @param string $theField [optional] the particular portion
	 *                         of the status information desired.
	 *                         If omitted the array of status
	 *                         information is returned.  If a non-existant
	 *                         status field is requested, false is returned.
	 * @returns mixed
	 */
	public function GetStatus($theField=null)
	{
		if (empty($theField)) {
			return $this->_status;

		} else {
			if (isset($this->_status[$theField])) {
				return $this->_status[$theField];
			} else {
				return false;
			}
		}
	}


	/**
	 * Did the last curl exec operation have an error?
	 *
	 * @return mixed The error message associated with the error if an error
	 *               occurred, false otherwise.
	 */
	public function hasError()
	{
		if (isset($this->_status['error'])) {
			return (empty($this->_status['error']) ? false : $this->_status['error']);

		} else {
			return false;
		}
	}


	/**
	 * Returns the error message associated with the error if an error occurred, false otherwise.
	 *
	 * @return string|FALSE The error message, or false
	 */
	public function GetErrorMessage()
	{
		if (isset($this->_status['error'])) {
			return (empty($this->_status['error']) ? false : $this->_status['error']);

		} else {
			return false;
		}
	}


	/**
	 * Returns the parsed http header.
	 *
	 * @param string $theHeader [optional] the name of the header to be returned.
	 *                          The name of the header is case insensitive.  If
	 *                          the header name is omitted the parsed header is
	 *                          returned.  If the requested header doesn't exist
	 *                          false is returned.
	 * @returns mixed
	 */
	public function GetHeader($theHeader=null)
	{
		//
		// There can't be any headers to check if there weren't any headers
		// returned (happens in the event of errors).
		//

		if (empty($this->_header))
		{
			return false;
		}

		if (empty($theHeader))
		{
			return $this->_header;
		}
		else
		{
			$theHeader = strtoupper($theHeader);
			if (isset($this->_caseless[$theHeader]))
			{
				return $this->_header[$this->_caseless[$theHeader]];
			}
			else
			{
				return false;
			}
		}
	}


	/**
	 * Returns the followed headers lines, including the header of the retrieved page.
	 * Assumed preconditions: CURLOPT_HEADER and expected CURLOPT_FOLLOWLOCATION set.
	 * The content is returned as an array of headers of arrays of header lines.
	 *
	 * @param none.
	 * @returns mixed an empty array implies no headers.
	 * @access public
	 */
	public function GetAllHeaders()
	{
		$theHeaders = array();
		if ($this->m_followed)
		{
			foreach ( $this->m_followed as $aHeader )
			{
				$theHeaders[] = explode( "\r\n", $aHeader );
			};
			return $theHeaders;
		}

		return $theHeaders;
	}


	/**
	 * Parse an HTTP header.
	 *
	 * As a side effect it stores the parsed header in the
	 * m_header instance variable.  The header is stored as
	 * an associative array and the case of the headers
	 * as provided by the server is preserved and all
	 * repeated headers (pragma, set-cookie, etc) are grouped
	 * with the first spelling for that header
	 * that is seen.
	 *
	 * All headers are stored as if they COULD be repeated, so
	 * the headers are really stored as an array of arrays.
	 *
	 * @param string $theHeader The HTTP data header.
	 */
	protected function ParseHeader($theHeader)
	{
		$this->_caseless = array();

		$theArray = preg_split("/(\r\n)+/", $theHeader);

		//
		// Ditch the HTTP status line.
		//

		if (preg_match('/^HTTP/', $theArray[0])) {
			$theArray = array_slice($theArray, 1);
		}

		foreach ($theArray as $theHeaderString) {

			$theHeaderStringArray = preg_split("/\s*:\s*/", $theHeaderString, 2);

			$theCaselessTag = strtoupper($theHeaderStringArray[0]);

			if (!isset($this->_caseless[$theCaselessTag])) {
				$this->_caseless[$theCaselessTag] = $theHeaderStringArray[0];
			}

			$this->_header[$this->_caseless[$theCaselessTag]][] = $theHeaderStringArray[1];
		}
	}


}