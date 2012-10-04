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
 * @subpackage System
 * @package    CMP3
 * @copyright  Copyright (c) 2009 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\System;




/**
 * This gives access to the system.
 *
 * @property-read const $Type
 * @property-read \Cmp3\Config\ConfigInterface $Config
 * @property boolean $IndexingDynamicContent Flag to indicate if dynamic content was created which should be indexed
 * @property boolean $IndexingForced Flag which will trigger new indexing of the content
 * @property boolean $IndexingProcessFallback Flag ... TODO
 * @property boolean $EncryptionKey Encryption key for general use
 * @property boolean $EncodingType The default cahcaracter encoding (eg. UTF-8)
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Core
 * @package    CMP3
 */
Abstract class System_Abstract {


	/**
	 *
	 * @var \Cmp3\Config\ConfigInterface
	 */
	protected $objConfig;


	protected $strIndexingDocTitle;
	protected $blnIndexingDynamicContent = false;
	protected $blnIndexingForce = false;
	protected $blnIndexingProcessFallback = false;

	/**
	 * The system's encryption key, must not be empty!
	 *
	 * @var string
	 */
	protected $strEncryptionKey;

	/**
	 *
	 * @param $objConfig \Cmp3\Config\ConfigInterface
	 */
	public function __construct($objConfig)
	{
		$this->objConfig = $objConfig;

		$this->blnIndexingProcessFallback = $this->objConfig->isEnabled('indexing.processContentFallback');
	}


	/**
	 * Override method to perform a property "Get"
	 * This will get the property $strName
	 *
	 * @param string $strName Name of the property to get
	 * @return mixed
	 * @throws \Cmp3\UndefinedGetPropertyException
	 */
	public function __get($strName)
	{
		switch ($strName) {
			case 'Type':
				return \Cmp3\System\SystemType::STANDALONE;
				break;

			case 'Config':
				return $this->objConfig;

			case 'EncodingType':
				return 'UTF-8';

			case 'IndexingDynamicContent':
				return $this->blnIndexingDynamicContent;

			case 'IndexingForced':
				return $this->blnIndexingForce;

			case 'IndexingProcessFallback':
				return $this->blnIndexingProcessFallback;

			case 'EncryptionKey':
				if ($this->strEncryptionKey) {
					return $this->strEncryptionKey;
				} else {
					throw new WrongParameterException('The system\'s encryption key was not set, but has been tried to be used.');
				}
				break;

			default:
				throw new \Cmp3\UndefinedGetPropertyException ($strName);
		}
	}


	/**
	 * Override method to perform a property "Set"
	 * This will set the property $strName to be $mixValue
	 *
	 * @param string $strName Name of the property to set
	 * @param string $mixValue New value of the property
	 * @return mixed
	 * @throws \Cmp3\UndefinedSetPropertyException
	 */
	public function __set($strName, $mixValue)
	{
		switch ($strName) {

			case 'IndexingDynamicContent':
				$this->blnIndexingDynamicContent = (boolean)$mixValue;
				break;

			case 'IndexingForced':
				$this->blnIndexingForce = (boolean)$mixValue;
				break;

			case 'IndexingProcessFallback':
				$this->blnIndexingProcessFallback = (boolean)$mixValue;
				break;

			default:
				throw new \Cmp3\UndefinedSetPropertyException ($strName);
		}
	}


	/**
	 * Set the system's encryption key
	 * which should be a long random string
	 *
	 * @return void
	 */
	public function SetEncryptionKey($strEncryptionKey)
	{
		$this->strEncryptionKey = (string)$strEncryptionKey;
	}



	/***************************
	 *
	 * Locale Methods
	 *
	 **************************/


	/**
	 * Returns a locale string which is default for the system
	 *
	 * @return string
	 */
	public function GetLocale ()
	{
		return 'en_US';
	}




	/***************************
	 *
	 * Database Methods
	 *
	 **************************/


	/**
	 * Returns an QDatabaseBase object
	 *
	 * @return QDatabaseBase
	 */
	public function GetDatabase ()
	{
		static $objDB;

		if ($objDB)
			return $objDB;


		include(PATH_site.'/typo3conf/localconf.php');
		$tempConfig = array(
			'adapter' => 'MySql',
			'server' => $typo_db_host, // dummy
			'port' => null,
			'database' => $typo_db,
			'username' => $typo_db_username,
			'password' => $typo_db_password,
			'profiling' => false);
		unset($typo_db);
		unset($typo_db_host);
		unset($typo_db_username);
		unset($typo_db_password);
		if ($TYPO3_CONF_VARS['BE']['forceCharset'])
			$tempConfig['encoding'] = str_replace('-', '', $TYPO3_CONF_VARS['BE']['forceCharset']);
		unset($TYPO3_CONF_VARS);


		$objDB = new QMySqlDatabase(1, $tempConfig);
		if (txApplications::GetCurrent()->Profiling)
			$objDB->EnableProfiling();

		return $objDB;
	}



	/*******************************
	 *
	 * Indexing
	 *
	 *******************************/



	/**
	 * Set the document title which is used for indexing
	 * @return void
	 */
	public function SetIndexingDocTitle($strIndexingDocTitle)
	{
		$this->strIndexingDocTitle = $strIndexingDocTitle;
	}



	/***************************
	 *
	 * Encryption Methods
	 *
	 **************************/


	/**
	 * Returns a string of highly randomized bytes (over the full 8-bit range).
	 *
	 * @copyright	Drupal CMS
	 * @license		GNU General Public License version 2
	 * @param		integer  Number of characters (bytes) to return
	 * @return		string   Random Bytes
	 */
	public function GenerateRandomBytes($count)
	{

		// from \t3lib_div::generateRandomBytes();

		$output = '';
			// /dev/urandom is available on many *nix systems and is considered
			// the best commonly available pseudo-random source.
		if (!\tx_cmp3::isWindows() && ($fh = @fopen('/dev/urandom', 'rb'))) {
			$output = fread($fh, $count);
			fclose($fh);
		} elseif (\tx_cmp3::isWindows() && function_exists('mcrypt_create_iv') && version_compare(PHP_VERSION, '5.3.0', '>=')) {
			$output = mcrypt_create_iv($count, MCRYPT_DEV_URANDOM);
		} elseif (\tx_cmp3::isWindows() && version_compare(PHP_VERSION, '5.3.0', '>=') && function_exists('openssl_random_pseudo_bytes')) {
			$isStrong = null;
			$output = openssl_random_pseudo_bytes($count, $isStrong);
		}

			// fallback if /dev/urandom is not available
		if (!isset($output{$count - 1})) {
				// We initialize with somewhat random.
			$randomState = $this->strEncryptionKey
							. base_convert(memory_get_usage() % pow(10,6), 10, 2)
							. microtime() . uniqid('') . getmypid();
			while (!isset($output{$count - 1})) {
				$randomState = sha1(microtime() . mt_rand() . $randomState);
				$output .= sha1(mt_rand() . $randomState, true);
			}
			$output = substr($output, strlen($output) - $count, $count);
		}
		return $output;
	}

	/**
	 * Returns a hex representation of a random byte string.
	 *
	 * @param		integer  Number of characters to return
	 * @return		string   Random Bytes
	 */
	public function GenerateRandomString($count)
	{
		return substr(bin2hex($this->GenerateRandomBytes(intval(($count + 1) / 2))),0, $count);
	}


	/***************************
	 *
	 * Paths Methods
	 *
	 **************************/


	/**
	 * Returns an absolue path from a path which can be relative or with an location prefix like EXT:
	 *
	 * @param string $filename
	 * @param string $checkExistence
	 * @return string resolved filepath
	 */
	public static function ResolvePath ($filename, $checkExistence=true)
	{
		$abolutePathFilename = \tx_cmp3::ResolvePath($filename);
		return ($checkExistence===false OR @file_exists($abolutePathFilename)) ? $abolutePathFilename : false; /*@*/
	}


	/**
	 * Returns a path relative to the site
	 *
	 * @param $key
	 * @return string
	 * @todo seems to be unused
	 */
	public static function ResolveSitePath($strResource)
	{
		$pathAbsolute = \tx_cmp3::$System->ResolvePath($strResource);
		return preg_replace('#^' . preg_quote(self::$_paths['PATH_site']) . '#', '', $pathAbsolute);
	}


	/**
	 * Resolve class name
	 *
	 * @param string $strResource
	 * @return string|false class name
	 */
	public static function ResolveClassResource($strResource)
	{
		if (!$strResource) {
			throw new \Cmp3\Dispatcher_Exception ('No class defined. Class can\'t be resolved!');
		}

		$class = end(explode(':',trim($strResource)));

		if ($class) {

			if ($path = \tx_cmp3::$System->ResolvePath($strResource)) {

				require_once($path);
			}

		} else {
			$class = $strResource;
		}

		if (!class_exists($class)) {

			throw new \Cmp3\Dispatcher_Exception ('The class "' . $class. '" was not found. Naming error? (' . $strResource . ')');

			return false;
		}

		return $class;
	}



	/***************************
	 *
	 * HTML Methods
	 *
	 **************************/


	/**
	 * stores HTML header code internally
	 * @var array
	 */
	protected $strHTMLHeaderCode = array();


	/**
	 * Add HTML header code for later output
	 *
	 * @param string HTML header code
	 * @return void
	 */
	public function AddHTMLHeaderCode($strHTMLHeaderCode)
	{
		$this->strHTMLHeaderCode[] = $strHTMLHeaderCode;
	}


	/**
	 * Returns HTML header code for output
	 *
	 * @return string HTML header code
	 */
	public function GetHTMLHeaderCode()
	{
		return implode ("\n", $this->strHTMLHeaderCode);
	}





	/**
	 * stored registered content parts
	 * @var array
	 */
	protected $strContentPartArray;


	/**
	 * Registering a content part using one or more keys
	 *
	 * @param string|array System key for this (_INTincKey). An array with multiple keys can be given
	 * @param string HTML content
	 * @return void
	 * @see \Next\CObjectNEXT
	 */
	public function RegisterContentPart($strContentPartKey, $strContent)
	{
		$strContentPartKeyArray = null;

		if (is_array($strContentPartKey)) {
			$strContentPartKeyArray = $strContentPartKey;
			$strContentPartKey = array_shift($strContentPartKeyArray);
		}

		$this->strContentPartArray[$strContentPartKey] = $strContent;

		if ($strContentPartKeyArray) {
			foreach ($strContentPartKeyArray as $strKey) {
				$this->strContentPartArray[$strKey] = & $this->strContentPartArray[$strContentPartKey];
			}
		}
	}


	/**
	 * Returns registeried content parts as array
	 *
	 * @param string System key for this (_INTincKey). If NULL full array will be returned
	 * @return array|string
	 * @see \Next\PageIndexing
	 */
	public function GetRegisteredContent($strContentPartKey=null)
	{
		if ($strContentPartKey)
			return $this->strContentPartArray[$strContentPartKey];

		return $this->strContentPartArray;
	}





#TODO this needs to be totally t3 independent


	/**
	 * Abstraction method which returns System Environment Variables regardless of server OS, CGI/MODULE version etc. Basically this is SERVER variables for most of them.
	 * This should be used instead of getEnv() and $_SERVER/ENV_VARS to get reliable values for all situations.
	 *
	 * @param	string		Name of the "environment variable"/"server variable" you wish to use. Valid values are SCRIPT_NAME, SCRIPT_FILENAME, REQUEST_URI, PATH_INFO, REMOTE_ADDR, REMOTE_HOST, HTTP_REFERER, HTTP_HOST, HTTP_USER_AGENT, HTTP_ACCEPT_LANGUAGE, QUERY_STRING, DOCUMENT_ROOT, HOST_ONLY, HOST_ONLY, REQUEST_HOST, REQUEST_URL, REQUEST_SCRIPT, REQUEST_DIR, SITE_URL, _ARRAY
	 * @return	string		Value based on the input key, independent of server/os environment.
	 */
	public static function GetEnv($getEnvName)	{
		/*
			Conventions:
			output from parse_url():
			URL:	http://username:password@192.168.1.4:8080/typo3/32/temp/phpcheck/index.php/arg1/arg2/arg3/?arg1,arg2,arg3&p1=parameter1&p2[key]=value#link1
				[scheme] => 'http'
				[user] => 'username'
				[pass] => 'password'
				[host] => '192.168.1.4'
				[port] => '8080'
				[path] => '/typo3/32/temp/phpcheck/index.php/arg1/arg2/arg3/'
				[query] => 'arg1,arg2,arg3&p1=parameter1&p2[key]=value'
				[fragment] => 'link1'

				Further definition: [path_script] = '/typo3/32/temp/phpcheck/index.php'
									[path_dir] = '/typo3/32/temp/phpcheck/'
									[path_info] = '/arg1/arg2/arg3/'
									[path] = [path_script/path_dir][path_info]


			Keys supported:

			URI______:
				REQUEST_URI		=	[path]?[query]		= /typo3/32/temp/phpcheck/index.php/arg1/arg2/arg3/?arg1,arg2,arg3&p1=parameter1&p2[key]=value
				HTTP_HOST		=	[host][:[port]]		= 192.168.1.4:8080
				SCRIPT_NAME		=	[path_script]++		= /typo3/32/temp/phpcheck/index.php		// NOTICE THAT SCRIPT_NAME will return the php-script name ALSO. [path_script] may not do that (eg. '/somedir/' may result in SCRIPT_NAME '/somedir/index.php')!
				PATH_INFO		=	[path_info]			= /arg1/arg2/arg3/
				QUERY_STRING	=	[query]				= arg1,arg2,arg3&p1=parameter1&p2[key]=value
				HTTP_REFERER	=	[scheme]://[host][:[port]][path]	= http://192.168.1.4:8080/typo3/32/temp/phpcheck/index.php/arg1/arg2/arg3/?arg1,arg2,arg3&p1=parameter1&p2[key]=value
										(Notice: NO username/password + NO fragment)

			CLIENT____:
				REMOTE_ADDR		=	(client IP)
				REMOTE_HOST		=	(client host)
				HTTP_USER_AGENT	=	(client user agent)
				HTTP_ACCEPT_LANGUAGE	= (client accept language)

			SERVER____:
				SCRIPT_FILENAME	=	Absolute filename of script		(Differs between windows/unix). On windows 'C:\\blabla\\blabl\\' will be converted to 'C:/blabla/blabl/'

			Special extras:
				HOST_ONLY =		[host] = 192.168.1.4
				PORT =			[port] = 8080 (blank if 80, taken from host value)
				REQUEST_HOST = 		[scheme]://[host][:[port]]
				REQUEST_URL =		[scheme]://[host][:[port]][path]?[query] (scheme will by default be "http" until we can detect something different)
				REQUEST_SCRIPT =  	[scheme]://[host][:[port]][path_script]
				REQUEST_DIR =		[scheme]://[host][:[port]][path_dir]
				SITE_URL = 		[scheme]://[host][:[port]][path_dir] of the TYPO3 website frontend
				SITE_SCRIPT = 		[script / Speaking URL] of the TYPO3 website
				DOCUMENT_ROOT =		Absolute path of root of documents: DOCUMENT_ROOT.SCRIPT_NAME = SCRIPT_FILENAME (typically)
				SSL = 			Returns TRUE if this session uses SSL/TLS (https)
				PROXY = 			Returns TRUE if this session runs over a well known proxy

			Notice: [fragment] is apparently NEVER available to the script!


			Testing suggestions:
			- Output all the values.
			- In the script, make a link to the script it self, maybe add some parameters and click the link a few times so HTTP_REFERER is seen
			- ALSO TRY the script from the ROOT of a site (like 'http://www.mytest.com/' and not 'http://www.mytest.com/test/' !!)

		*/

#		if ($getEnvName=='HTTP_REFERER')	return '';

		$retVal = '';

		switch ((string)$getEnvName)	{
			case 'SCRIPT_NAME':
				$retVal = (php_sapi_name()=='cgi'||php_sapi_name()=='cgi-fcgi')&&($_SERVER['ORIG_PATH_INFO']?$_SERVER['ORIG_PATH_INFO']:$_SERVER['PATH_INFO']) ? ($_SERVER['ORIG_PATH_INFO']?$_SERVER['ORIG_PATH_INFO']:$_SERVER['PATH_INFO']) : ($_SERVER['ORIG_SCRIPT_NAME']?$_SERVER['ORIG_SCRIPT_NAME']:$_SERVER['SCRIPT_NAME']);
					// add a prefix if TYPO3 is behind a proxy: ext-domain.com => int-server.com/prefix
				if (\Next\IP::Compare($_SERVER['REMOTE_ADDR'], $GLOBALS['CONF_VARS']['SYS']['reverseProxyIP'])) {
					if (self::GetEnv('SSL') && $GLOBALS['CONF_VARS']['SYS']['reverseProxyPrefixSSL']) {
						$retVal = $GLOBALS['CONF_VARS']['SYS']['reverseProxyPrefixSSL'].$retVal;
					} elseif ($GLOBALS['CONF_VARS']['SYS']['reverseProxyPrefix']) {
						$retVal = $GLOBALS['CONF_VARS']['SYS']['reverseProxyPrefix'].$retVal;
					}
				}
			break;
			case 'SCRIPT_FILENAME':
				$retVal = str_replace('//','/', str_replace('\\','/', (php_sapi_name()=='cgi'||php_sapi_name()=='isapi' ||php_sapi_name()=='cgi-fcgi')&&($_SERVER['ORIG_PATH_TRANSLATED']?$_SERVER['ORIG_PATH_TRANSLATED']:$_SERVER['PATH_TRANSLATED'])? ($_SERVER['ORIG_PATH_TRANSLATED']?$_SERVER['ORIG_PATH_TRANSLATED']:$_SERVER['PATH_TRANSLATED']):($_SERVER['ORIG_SCRIPT_FILENAME']?$_SERVER['ORIG_SCRIPT_FILENAME']:$_SERVER['SCRIPT_FILENAME'])));
			break;
			case 'REQUEST_URI':
					// Typical application of REQUEST_URI is return urls, forms submitting to itself etc. Example: returnUrl='.rawurlencode(self::GetEnv('REQUEST_URI'))
				if ($GLOBALS['CONF_VARS']['SYS']['requestURIvar'])	{	// This is for URL rewriters that store the original URI in a server variable (eg ISAPI_Rewriter for IIS: HTTP_X_REWRITE_URL)
					list($v,$n) = explode('|',$GLOBALS['CONF_VARS']['SYS']['requestURIvar']);
					$retVal = $GLOBALS[$v][$n];
				} elseif (!$_SERVER['REQUEST_URI'])	{	// This is for ISS/CGI which does not have the REQUEST_URI available.
					$retVal = '/'.ereg_replace('^/','',self::GetEnv('SCRIPT_NAME')).
						($_SERVER['QUERY_STRING']?'?'.$_SERVER['QUERY_STRING']:'');
				} else {
					$retVal = $_SERVER['REQUEST_URI'];
				}
					// add a prefix if TYPO3 is behind a proxy: ext-domain.com => int-server.com/prefix
				if (\Next\IP::Compare($_SERVER['REMOTE_ADDR'], $GLOBALS['CONF_VARS']['SYS']['reverseProxyIP'])) {
					if (self::GetEnv('SSL') && $GLOBALS['CONF_VARS']['SYS']['reverseProxyPrefixSSL']) {
						$retVal = $GLOBALS['CONF_VARS']['SYS']['reverseProxyPrefixSSL'].$retVal;
					} elseif ($GLOBALS['CONF_VARS']['SYS']['reverseProxyPrefix']) {
						$retVal = $GLOBALS['CONF_VARS']['SYS']['reverseProxyPrefix'].$retVal;
					}
				}
			break;
			case 'PATH_INFO':
					// $_SERVER['PATH_INFO']!=$_SERVER['SCRIPT_NAME'] is necessary because some servers (Windows/CGI) are seen to set PATH_INFO equal to script_name
					// Further, there must be at least one '/' in the path - else the PATH_INFO value does not make sense.
					// IF 'PATH_INFO' never works for our purpose in TYPO3 with CGI-servers, then 'php_sapi_name()=='cgi'' might be a better check. Right now strcmp($_SERVER['PATH_INFO'],self::GetEnv('SCRIPT_NAME')) will always return false for CGI-versions, but that is only as long as SCRIPT_NAME is set equal to PATH_INFO because of php_sapi_name()=='cgi' (see above)
//				if (strcmp($_SERVER['PATH_INFO'],self::GetEnv('SCRIPT_NAME')) && count(explode('/',$_SERVER['PATH_INFO']))>1)	{
				if (php_sapi_name()!='cgi' && php_sapi_name()!='cgi-fcgi')	{
					$retVal = $_SERVER['PATH_INFO'];
				}
			break;
			case 'REV_PROXY':
				$retVal = \Next\IP::Compare($_SERVER['REMOTE_ADDR'], $GLOBALS['CONF_VARS']['SYS']['reverseProxyIP']);
			break;
			case 'REMOTE_ADDR':
				$retVal = $_SERVER['REMOTE_ADDR'];
				if (\Next\IP::Compare($_SERVER['REMOTE_ADDR'], $GLOBALS['CONF_VARS']['SYS']['reverseProxyIP'])) {
					$ip = trim_explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
						// choose which IP in list to use
					if (count($ip)) {
						switch ($GLOBALS['CONF_VARS']['SYS']['reverseProxyHeaderMultiValue']) {
							case 'last':
								$ip = array_pop($ip);
							break;
							case 'first':
								$ip = array_shift($ip);
							break;
							case 'none':
							default:
								$ip = '';
							break;
						}
					}
					if (\Next\IP::isValid($ip)) {
						$retVal = $ip;
					}
				}
			break;
			case 'HTTP_HOST':
				$retVal = $_SERVER['HTTP_HOST'];
				if (\Next\IP::Compare($_SERVER['REMOTE_ADDR'], $GLOBALS['CONF_VARS']['SYS']['reverseProxyIP'])) {
					$host = trim_explode(',', $_SERVER['HTTP_X_FORWARDED_HOST']);
						// choose which host in list to use
					if (count($host)) {
						switch ($GLOBALS['CONF_VARS']['SYS']['reverseProxyHeaderMultiValue']) {
							case 'last':
								$host = array_pop($host);
							break;
							case 'first':
								$host = array_shift($host);
							break;
							case 'none':
							default:
								$host = '';
							break;
						}
					}
					if ($host)	{
						$retVal = $host;
					}
				}
			break;
				// These are let through without modification
			case 'HTTP_REFERER':
			case 'HTTP_USER_AGENT':
			case 'HTTP_ACCEPT_ENCODING':
			case 'HTTP_ACCEPT_LANGUAGE':
			case 'REMOTE_HOST':
			case 'QUERY_STRING':
				$retVal = $_SERVER[$getEnvName];
			break;
			case 'DOCUMENT_ROOT':
				// Some CGI-versions (LA13CGI) and mod-rewrite rules on MODULE versions will deliver a 'wrong' DOCUMENT_ROOT (according to our description). Further various aliases/mod_rewrite rules can disturb this as well.
				// Therefore the DOCUMENT_ROOT is now always calculated as the SCRIPT_FILENAME minus the end part shared with SCRIPT_NAME.
				$SFN = self::GetEnv('SCRIPT_FILENAME');
				$SN_A = explode('/',strrev(self::GetEnv('SCRIPT_NAME')));
				$SFN_A = explode('/',strrev($SFN));
				$acc = array();
				foreach ($SN_A as $kk => $vv) {
					if (!strcmp($SFN_A[$kk],$vv))	{
						$acc[] = $vv;
					} else break;
				}
				$commonEnd=strrev(implode('/',$acc));
				if (strcmp($commonEnd,''))	{ $DR = substr($SFN,0,-(strlen($commonEnd)+1)); }
				$retVal = $DR;
			break;
			case 'HOST_ONLY':
				$p = explode(':',self::GetEnv('HTTP_HOST'));
				$retVal = $p[0];
			break;
			case 'PORT':
				$p = explode(':',self::GetEnv('HTTP_HOST'));
				$retVal = $p[1];
			break;
			case 'REQUEST_HOST':
				$retVal = (self::GetEnv('SSL') ? 'https://' : 'http://').
					self::GetEnv('HTTP_HOST');
			break;
			case 'REQUEST_URL':

				if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
					return $_SERVER['HTTP_X_REWRITE_URL'];
				} elseif (isset($_SERVER['REQUEST_URI'])) {
					return $_SERVER['REQUEST_URI'];
				} elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
					$requestUri = $_SERVER['ORIG_PATH_INFO'];
					if (!empty($_SERVER['QUERY_STRING'])) {
						$requestUri .= '?' . $_SERVER['QUERY_STRING'];
					}
					return $requestUri;
				}


				#$retVal = self::GetEnv('REQUEST_HOST').self::GetEnv('REQUEST_URI');
			break;
			case 'REQUEST_SCRIPT':
				$retVal = self::GetEnv('REQUEST_HOST').self::GetEnv('SCRIPT_NAME');
			break;
			case 'REQUEST_DIR':
				$retVal = self::GetEnv('REQUEST_HOST').path_part(self::GetEnv('SCRIPT_NAME'));
			break;
			case 'SITE_URL':
				if (defined('PATH_thisScript') && defined('PATH_site'))	{
					$lPath = substr(dirname(PATH_thisScript),strlen(PATH_site)).'/';
					$url = self::GetEnv('REQUEST_DIR');
					$siteUrl = substr($url,0,-strlen($lPath));
					if (substr($siteUrl,-1)!='/')	$siteUrl.='/';
					$retVal = $siteUrl;
				}
			break;
			case 'SITE_SCRIPT':
				$retVal = substr(self::GetEnv('REQUEST_URL'),strlen(self::GetEnv('SITE_URL')));
			break;
			case 'SSL':
				$proxySSL = trim($GLOBALS['CONF_VARS']['SYS']['reverseProxySSL']);
				if ($proxySSL == '*') {
					$proxySSL = $GLOBALS['CONF_VARS']['SYS']['reverseProxyIP'];
				}
				if (\Next\IP::Compare($_SERVER['REMOTE_ADDR'], $proxySSL))	{
					$retVal = true;
				} else {
					$retVal = $_SERVER['SSL_SESSION_ID'] || !strcasecmp($_SERVER['HTTPS'], 'on') || !strcmp($_SERVER['HTTPS'], '1') ? true : false;	// see http://bugs.typo3.org/view.php?id=3909
				}
			break;
			case '_ARRAY':
				$out = array();
					// Here, list ALL possible keys to this function for debug display.
				$envTestVars = trim_explode(',','
					HTTP_HOST,
					HOST_ONLY,
					PORT,
					PATH_INFO,
					QUERY_STRING,
					REQUEST_URI,
					HTTP_REFERER,
					REQUEST_HOST,
					REQUEST_URL,
					REQUEST_SCRIPT,
					REQUEST_DIR,
					SITE_URL,
					SITE_SCRIPT,
					SSL,
					REV_PROXY,
					SCRIPT_NAME,
					DOCUMENT_ROOT,
					SCRIPT_FILENAME,
					REMOTE_ADDR,
					REMOTE_HOST,
					HTTP_USER_AGENT,
					HTTP_ACCEPT_LANGUAGE');
				foreach ($envTestVars as $v) {
					$out[$v]=self::GetEnv($v);
				}
				reset($out);
				$retVal = $out;
			break;
		}
		return $retVal;
	}
}