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
 * @subpackage String
 * @package    CMP3
 * @copyright  Copyright (c) 2008 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\String;




/**
 * An abstract utility class to handle string formatting.
 * All methods are statically available.
 *
 * STATUS: beta
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage String
 * @package    CMP3
 */
abstract class Format {




	/**
	 * Replace whitespaces with html entity &nbsp;
	 * This is handy to create output which is at least $intLength long in a row without linebreak.
	 *
	 * This will not give expected results with HTML content.
	 *
	 * @param string $strContent
	 * @param integer $intLength If set after n characters replacing stops. If blnForce is false (default) will not replace the last whitespace if the resulting string is longer than n (with the word afterwards).
	 * @param bollean $blnForce If set all whitespaces till n char will be replaced
	 * @return string
	 */
	public static function NonBreak($strContent, $intLength=0, $blnForce=false)
	{
		if ($intLength AND $blnForce) {
			$strBegin = substr($strContent, 0, $intLength);
			$strEnd = substr($strContent, $intLength);;
			return str_replace(' ', '&nbsp;', $strBegin).$strEnd;

		} else if ($intLength) {
			$strArray = explode(' ', $strContent);
			$charCount = 0;
			$strContent = '';
			foreach ($strArray as $key => $strPart) {
				$strContent .= $strPart;
				$charCount += strlen($strPart);
				if (array_key_exists($key+1, $strArray)) {
					if (($charCount+strlen($strArray[$key+1])+1) <= $intLength)
						$strContent .= '&nbsp;';
					else
						$strContent .= ' ';
				}

			}
			return $strContent;

		} else
			return str_replace(' ', '&nbsp;', $strContent);
	}



	/*****************************************
	 *
	 *  formating values respecting locale settings
	 *
	 *****************************************/




	const INTERVAL_SECOND = 1;
	const INTERVAL_MINUTE = 60;
	const INTERVAL_HOUR = 3600;
	const INTERVAL_DAY = 86400;
	const INTERVAL_WEEK = 604800;
	const INTERVAL_MONTH = 2592000; // 30 days
	const INTERVAL_YEAR = 31536000;


	/**
	 * Returns the "age" in seconds / minutes / hours / days / weeks / month / years of the number of $intSeconds inputted.
	 *
	 * @param integer		$intSeconds could be the difference of a certain timestamp and time()
	 * @param string $strMinInterval The minimum interval: s, m, h, d, w, m, y. If set to m the minumum age will be '1 minute'
	 * @return	string		Formatted time
	 */
	public static function FormatAge($intSeconds, $strMinInterval=self::INTERVAL_SECOND)
	{
		$strMinus='';
#TODO support DateTime object
		$intSeconds = intval($intSeconds);

		if ($intSeconds<0)	{
			$strMinus = '-';
			$intSeconds = abs($intSeconds);
		}

		$intSeconds = max($intSeconds, $strMinInterval);

		// SECONDS
		if ($intSeconds < self::INTERVAL_MINUTE)	{
			$strLabelName = 'lblSecond';

		// MINUTES
		} elseif ($intSeconds < self::INTERVAL_HOUR)	{
			$intSeconds = round ($intSeconds / self::INTERVAL_MINUTE);
			$strLabelName = 'lblMinute';

		// HOURS
		} elseif ($intSeconds < self::INTERVAL_DAY)	{
			$intSeconds = round ($intSeconds / self::INTERVAL_HOUR);
			$strLabelName = 'lblHour';

		// DAYS
		} elseif ($intSeconds < self::INTERVAL_WEEK)	{
			$intSeconds = round ($intSeconds / self::INTERVAL_DAY);
			$strLabelName = 'lblDay';

		// WEEKS
		} elseif ($intSeconds < self::INTERVAL_MONTH)	{
			$intSeconds = round ($intSeconds / self::INTERVAL_WEEK);
			$strLabelName = 'lblWeek';

		// MONTHS
		} elseif ($intSeconds < self::INTERVAL_YEAR)	{
			$intSeconds = round ($intSeconds / self::INTERVAL_MONTH);
			$strLabelName = 'lblMonth';

		// YEARS
		} else {
			$intSeconds = round ($intSeconds / self::INTERVAL_YEAR);
			$strLabelName = 'lblYear';
		}

		$intSeconds = intval($intSeconds);
		if ($intSeconds === 1) {
			$strLabel = txApplications::GetCurrent()->Translate($strLabelName);
		} else {
			$strLabel = txApplications::GetCurrent()->Translate($strLabelName.'s');
		}

		if ($strMinus) {
			$intSeconds =$intSeconds * -1;
		}
		return sprintf ($strLabel, $intSeconds);
	}


	/**
	 * integer formatting function depending on the current locale
	 *
	 * @param string $value value to print
	 * @return string
	 */
	public static function FormatInteger($value)
	{
		if (class_exists('NumberFormatter', false)) {
			static $fmt;
#FIXME
			if (!$fmt) $fmt = new NumberFormatter( txApplications::GetCurrent()->Locale->GetLocale(), NumberFormatter::DECIMAL, NumberFormatter::GROUPING_USED );

			$strString = $fmt->format (intval($value));

		} else {
			$strString = intval($value);
		}

		return $strString;
	}


	/**
	 * float formatting function depending on the current locale
	 *
	 * @param string $value value to print
	 * @return string
	 */
	public static function FormatFloat($value)
	{
		if (class_exists('NumberFormatter', false)) {
			static $fmt;

			if (!$fmt) $fmt = new NumberFormatter( txApplications::GetCurrent()->Locale->GetLocale(), NumberFormatter::DECIMAL, NumberFormatter::TYPE_DOUBLE );

			$strString = $fmt->format ((double)$value);

		} else {
			$strString = (string)$value;
		}

		return $strString;
	}


	/**
	 * currency formatting function depending on the current locale
	 *
	 * @param string $value value to print
	 * @param string $strCurrency Currency iso code: EUR, USD, ...
	 * @return string
	 */
	public static function FormatCurrency($value, $strCurrency )
	{
		if (class_exists('NumberFormatter', false)) {
			static $fmt;

			if (!$fmt) $fmt = new NumberFormatter( txApplications::GetCurrent()->Locale->GetLocale(), NumberFormatter::CURRENCY );

			$strString = $fmt->formatCurrency ( floatval($value), $strCurrency);

		} else {
			$strString = number_format ( floatval($value), 2) . ' ' . $strCurrency;
		}

		return $strString;
	}


	/**
	 * day formatting function depending on the current locale
	 *
	 * Output examples:
	 *
	 * Montag
	 *
	 * @param string $value value to print
	 * @return string
	 * @todo support datetime object
	 * @todo support timezone?
	 */
	public static function FormatDay( $value )
	{

		if (class_exists('IntlDateFormatter', false)) {
			static $fmt;

			if (!$fmt) $fmt = new IntlDateFormatter( txApplications::GetCurrent()->Locale->GetLocale(), IntlDateFormatter::FULL  , IntlDateFormatter::NONE ,date_default_timezone_get() );
 			$fmt->setPattern('EEEE');
			$strString = $fmt->format ((integer)$value);

		} else {
			$strString = strftime ('%A', $value );
		}

		return $strString;
	}


	/**
	 * date formatting function depending on the current locale
	 *
	 * Output examples:
	 *
	 * 12/13/52
	 *
	 * @param string $value value to print
	 * @return string
	 * @todo support datetime object
	 * @todo support timezone?
	 */
	public static function FormatDate( $value )
	{
		if (class_exists('IntlDateFormatter', false)) {
			static $fmt;

			if (!$fmt) $fmt = new IntlDateFormatter( txApplications::GetCurrent()->Locale->GetLocale(), IntlDateFormatter::SHORT  , IntlDateFormatter::NONE ,date_default_timezone_get() );


			$strString = $fmt->format ((integer)$value);

		} else {
			if (txApplications::GetCurrent()->Locale->GetLanguage() == 'de') {
				$strString = strftime ('%d.%m.%Y', $value );
			} else {
				$strString = strftime ('%m-%d-%Y', $value );
			}
		}

		return $strString;
	}


	/**
	 * date formatting function depending on the current locale
	 *
	 * Output examples:
	 *
	 * January 12, 1952
	 *
	 * @param string $value value to print
	 * @return string
	 * @todo support datetime object
	 */
	public static function FormatDateLong( $value )
	{
		if (class_exists('IntlDateFormatter', false)) {
			static $fmt;

			if (!$fmt) $fmt = new IntlDateFormatter( txApplications::GetCurrent()->Locale->GetLocale(), IntlDateFormatter::LONG, IntlDateFormatter::NONE ,date_default_timezone_get() );


			$strString = $fmt->format ((integer)$value);

		} else {
			if (txApplications::GetCurrent()->Locale->GetLanguage() == 'de') {
				$strString = strftime ('%d.%m.%y', $value );
			} else {
				$strString = strftime ('%m-%d-%y', $value );
			}
		}

		return $strString;
	}


	/**
	 * date formatting function depending on the current locale
	 *
	 * Output examples:
	 *
	 * Montag, 2. November 2009
	 *
	 * @param string $value value to print
	 * @return string
	 * @todo support datetime object
	 */
	public static function FormatDateFull( $value )
	{
		if (class_exists('IntlDateFormatter', false)) {
			static $fmt;

			if (!$fmt) $fmt = new IntlDateFormatter( txApplications::GetCurrent()->Locale->GetLocale(), IntlDateFormatter::FULL, IntlDateFormatter::NONE ,date_default_timezone_get() );
			// Pattern: EEEE, d. MMMM yyyy
			// 			Montag, 2. November 2009

			$strString = $fmt->format ((integer)$value);

		} else {
			if (txApplications::GetCurrent()->Locale->GetLanguage() == 'de') {
				$strString = strftime ('%A, %d. %B %Y', $value );
			} else {
				$strString = strftime ('%A, %d %B %Y', $value );
			}
		}

		return $strString;
	}


	/**
	 * date formatting function depending on the current locale
	 *
	 * Output examples:
	 *
	 * January 12, 1952 11:32pm
	 *
	 * @param string $value value to print
	 * @return string
	 * @todo support datetime object
	 */
	public static function FormatDateTimeLong( $value )
	{
		if (class_exists('IntlDateFormatter', false)) {
			static $fmt;

			if (!$fmt) $fmt = new IntlDateFormatter( txApplications::GetCurrent()->Locale->GetLocale(), IntlDateFormatter::LONG, IntlDateFormatter::SHORT ,date_default_timezone_get() );


			$strString = $fmt->format ((integer)$value);

		} else {
			if (txApplications::GetCurrent()->Locale->GetLanguage() == 'de') {
				$strString = strftime ('%d.%m.%y %H:%M', $value );
			} else {
				$strString = strftime ('%m-%d-%y %H:%M', $value );
			}
		}

		return $strString;
	}


	/**
	 * date formatting function depending on the current locale
	 *
	 * Output examples:
	 *
	 * Montag, 2. November 2009 11:32pm
	 *
	 * @param string $value value to print
	 * @return string
	 * @todo support datetime object
	 */
	public static function FormatDateTimeFull( $value )
	{
		if (class_exists('IntlDateFormatter', false)) {
			static $fmt;

			if (!$fmt) $fmt = new IntlDateFormatter( txApplications::GetCurrent()->Locale->GetLocale(), IntlDateFormatter::FULL, IntlDateFormatter::SHORT ,date_default_timezone_get() );
			// Pattern: EEEE, d. MMMM yyyy
			// 			Montag, 2. November 2009

			$strString = $fmt->format ((integer)$value);

		} else {
			if (txApplications::GetCurrent()->Locale->GetLanguage() == 'de') {
				$strString = strftime ('%A, %d. %B %Y %H:%M', $value );
			} else {
				$strString = strftime ('%A, %d %B %Y %H:%M', $value );
			}
		}

		return $strString;
	}


	/**
	 * date and time formatting function depending on the current locale
	 *
	 * Output examples:
	 *
	 * 12/13/52 3:30pm
	 *
	 * @param string $value value to print
	 * @return string
	 * @todo support datetime object
	 */
	public static function FormatDateTime( $value )
	{

		if (class_exists('IntlDateFormatter', false)) {
			static $fmt;

			if (!$fmt) $fmt = new IntlDateFormatter( txApplications::GetCurrent()->Locale->GetLocale(), IntlDateFormatter::SHORT, IntlDateFormatter::SHORT ,date_default_timezone_get() );


			$strString = $fmt->format ((integer)$value);

		} else {
			if (txApplications::GetCurrent()->Locale->GetLanguage() == 'de') {
				$strString = strftime ('%d.%m.%y %H:%M', $value );
			} else {
				$strString = strftime ('%m-%d-%y %H:%M', $value );
			}
		}

		return $strString;
	}


	/**
	 * time formatting function depending on the current locale
	 *
	 * Output examples:
	 *
	 * 11:32pm
	 * 23:32
	 *
	 * @param string $value value to print
	 * @return string
	 * @todo support datetime object
	 */
	public static function FormatTime( $value )
	{
		if (class_exists('IntlDateFormatter', false)) {
			static $fmt;

			if (!$fmt) $fmt = new IntlDateFormatter( txApplications::GetCurrent()->Locale->GetLocale(), IntlDateFormatter::NONE, IntlDateFormatter::SHORT ,date_default_timezone_get() );


			$strString = $fmt->format ((integer)$value);

		} else {
			$strString = strftime ('%H:%M', $value );
		}

		return $strString;
	}


	/**
	 * time formatting function depending on the current locale
	 *
	 * Output examples:
	 *
	 * 11:32:43pm
	 * 23:32:43
	 *
	 * @param string $value value to print
	 * @return string
	 * @todo support datetime object
	 */
	public static function FormatTimeSeconds( $value )
	{
		if (class_exists('IntlDateFormatter', false)) {
			static $fmt;

			if (!$fmt) $fmt = new IntlDateFormatter( txApplications::GetCurrent()->Locale->GetLocale(), IntlDateFormatter::NONE, IntlDateFormatter::LONG ,date_default_timezone_get() );


			$strString = $fmt->format ((integer)$value);

		} else {
			$strString = strftime ('%H:%M:%S', $value );
		}

		return $strString;
	}


	/**
	 * Formats the input integer $sizeInBytes as bytes/kilobytes/megabytes (B/KB/MB/GB)
	 *
	 * @param	integer		Number of bytes to format.
	 * @return	string		Formatted representation of the byte number, for output.
	 */
	public static function FormatFileSize($sizeInBytes)
	{
		if ($sizeInBytes>900)	{
			if ($sizeInBytes>900000000)	{	// GB
				$val = $sizeInBytes/(1024*1024*1024);
				return number_format($val, (($val<20)?1:0), '.', '').' GB';
			}
			elseif ($sizeInBytes>900000)	{	// MB
				$val = $sizeInBytes/(1024*1024);
				return number_format($val, (($val<20)?1:0), '.', '').' MB';
			} else {	// KB
				$val = $sizeInBytes/(1024);
				return number_format($val, (($val<20)?1:0), '.', '').' KB';
			}
		} else {	// Bytes
			return $sizeInBytes.' B';
		}
	}

}







