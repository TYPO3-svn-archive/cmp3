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
 * @subpackage Uri
 * @package    CMP3
 * @copyright  Copyright (c) 2012 Rene Fritz <r.fritz@colorcube.de>
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\Uri;



/**
 * Helps to encode uri's used in qr codes for examples
 *
 * @see https://code.google.com/p/zxing/wiki/BarcodeContents
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Uri
 * @package    CMP3
 */
abstract class UriEncode {


	/**
	 * Bookmark code
	 *
	 * @param string $title
	 * @param string $url
	 */
	public static function Bookmark($title = null, $url = null)
	{
		return "MEBKM:TITLE:{$title};URL:{$url};;";
	}

	/**
	 * MECARD code
	 *
	 * @param string $name
	 * @param string $address
	 * @param string $phone
	 * @param string $email
	 */
	public static function Contact($name = null, $address = null, $phone = null, $email = null)
	{
		return "MECARD:N:{$name};ADR:{$address};TEL:{$phone};EMAIL:{$email};;";
	}

	/**
	 * Create code with GIF, JPG, etc.
	 *
	 * @param string $type
	 * @param string $size
	 * @param string $content
	 */
	public static function Content($type = null, $size = null, $content = null)
	{
		return "CNTS:TYPE:{$type};LNG:{$size};BODY:{$content};;";
	}

	/**
	 * Email address code
	 *
	 * @param string $email
	 * @param string $subject
	 * @param string $message
	 */
	public static function Email($email = null, $subject = null, $message = null)
	{
		return "MATMSG:TO:{$email};SUB:{$subject};BODY:{$message};;";
	}

	/**
	 * Geo location code
	 *
	 * @param string $lat
	 * @param string $lon
	 * @param string $height
	 */
	public static function Geo($lat = null, $lon = null, $height = null)
	{
		return "GEO:{$lat},{$lon},{$height}";
	}

	/**
	 * Telephone number code
	 *
	 * @param string $phone
	 */
	public static function Phone($phone = null)
	{
		return "TEL:{$phone}";
	}

	/**
	 * SMS code
	 *
	 * @param string $phone
	 * @param string $text
	 */
	public static function Sms($phone = null, $text = null)
	{
		return "SMSTO:{$phone}:{$text}";
	}

	/**
	 * URL code
	 *
	 * @param string $url
	 */
	public static function Url($url = null)
	{
		return preg_match("#^https?\:\/\/#", $url) ? $url : "http://{$url}";
	}

	/**
	 * Wifi code
	 *
	 * @param string $type
	 * @param string $ssid
	 * @param string $password
	 */
	public static function Wifi($type = null, $ssid = null, $password = null)
	{
		return "WIFI:T:{$type};S{$ssid};{$password};;";
	}

}

