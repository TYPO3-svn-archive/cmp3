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

#TODO use mb_string only

/**
 * An abstract utility class to handle string manipulation.
 * All methods are statically available.
 *
 * STATUS: beta - added mb_string support which is not well tested
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage String
 * @package    CMP3
 */
abstract class String {


	/**
	 * Replaces all linefeed variants LF, CR, CRLF to LF (chr(10))
	 *
	 * @param string $strString
	 * @return string
	 */
	public static function NormalizeLinefeed($strString)
	{
		$strString = str_replace("\r\n", "\n", $strString);
		$strString = str_replace("\r", "\n", $strString);

		return $strString;
	}


	/**
	 * Replaces all repeating linefeeds with a single linefeed.
	 * Spaces between two linefeeds will be ignored.
	 *
	 * @param string $strString
	 * @return string
	 */
	public static function RemoveRepeatingLinefeeds($strString)
	{
		$strString = self::NormalizeLinefeed($strString);
		$strString = preg_replace("#\n( *\n){1,}#", "\n", $strString);

		return $strString;
	}


	/**
	 * Remove Invisible Characters
	 *
	 * Every control character except newline (dec 10), carriage return (dec 13), and horizontal tab (dec 09) will be removed
	 *
	 * A fixed space (160) will be replaced with a normal space
	 *
	 * @param	string
	 * @return	string
	 */
	public static function StripInvisible($strText)
	{
		static $non_displayables;

		$strNBSP = utf8_encode(chr(160));
		$strText = str_replace($strNBSP, ' ', $strText);

		if (!isset($non_displayables)) {
			// every control character except newline (dec 10), carriage return (dec 13), and horizontal tab (dec 09),
			$non_displayables = array(
				'/%0[0-8bcef]/', // url encoded 00-08, 11, 12, 14, 15
				'/%1[0-9a-f]/', // url encoded 16-31
				'/[\x00-\x08]/', // 00-08
				'/\x0b/', '/\x0c/', // 11, 12
				'/[\x0e-\x1f]/' // 14-31
			);
		}

		do {
			$cleaned = $strText;
			$strText = preg_replace($non_displayables, '', $strText);
		} while ($cleaned != $strText);

		return $strText;
	}


	/**
	 * Returns the first character of a given string, or null if the given
	 * string is null.
	 *
	 * @param string $strString
	 * @return string the first character, or null
	 */
	public static function FirstCharacter($strString)
	{
		if (!is_string($strString)) {
			throw new \Cmp3\WrongParameterException("Function expects string as argument. " . gettype($strString) . " given");
		}

		if (extension_loaded('mbstring')) {
			if (mb_strlen($strString) > 0)
				return mb_substr($strString, 0, 1);
			else {
				if (strlen($strString) > 0)
					return substr($strString, 0, 1);
			}
		}
		return null;
	}


	/**
	 * Returns the last character of a given string, or null if the given
	 * string is null.
	 *
	 * @param string $strString
	 * @return string the last character, or null
	 */
	public static function LastCharacter($strString)
	{
		if (!is_string($strString)) {
			throw new \Cmp3\WrongParameterException("Function expects string as argument. " . gettype($strString) . " given");
		}

		if (extension_loaded('mbstring')) {
			$intLength = mb_strlen($strString);
			if ($intLength > 0)
				return mb_substr($strString, $intLength - 1);
		} else {
			$intLength = strlen($strString);
			if ($intLength > 0)
				return substr($strString, $intLength - 1);
		}


		return null;
	}


	/**
	 * Checks if a string begins with a given prefix (just other string)
	 *
	 * @param string $strString
	 * @param string $strPrefix
	 * @return booelan
	 */
	public static function BeginsWith($strString, $strPrefix)
	{
		return (strncmp($strString, $strPrefix, strlen($strPrefix)) == 0);
	}


	/**
	 * Returns a string without a given prefix.
	 *
	 * @param string $strString
	 * @param string $strPrefix
	 * @return string
	 */
	public static function StripPrefix($strString, $strPrefix)
	{
		$intPrefixLength = strlen($strPrefix);
		if (strncmp($strString, $strPrefix, $intPrefixLength) == 0) {
			return substr($strString, $intPrefixLength);
		}
		return $strString;
	}


	/**
	 * Truncates the string to a given length, adding elipses (if needed).
	 * The shortened string is trimmed and the elipses are prepended: "abc..."
	 * The minimum length of the truncated string is 6
	 *
	 * @param string $strString string to truncate
	 * @param integer $intMaxLength the maximum possible length of the string to return (including length of the ellipses). Negative value shorten the beginning and prepend ellipses.
	 * @param string $strEllipses string to append/prepend
	 * @return string the full string or the truncated string with ellipses
	 */
	public static function Truncate($strText, $intMaxLength, $strEllipses='...')
	{
		if (!is_string($strText)) {
			throw new \Cmp3\WrongParameterException("Function expects string as argument. " . gettype($strText) . " given");
		}

		$blnReverse = false;
		if ($intMaxLength < 0) {
			$blnReverse = true;
		}
		$intMaxLength = max(6, abs($intMaxLength));

		if (extension_loaded('mbstring')) {
			if (mb_strlen($strText) > $intMaxLength) {

				if ($blnReverse) {
					$strTrimmedTextWithEllipses = $strEllipses . mb_substr($strText, -($intMaxLength - mb_strlen($strEllipses)));
				} else {
					$strTrimmedTextWithEllipses = mb_substr($strText, 0, $intMaxLength - mb_strlen($strEllipses)) . $strEllipses;
				}
				return $strTrimmedTextWithEllipses;
			}
		} else {
			if (strlen($strText) > $intMaxLength) {

				if ($blnReverse) {
					$strTrimmedTextWithEllipses = $strEllipses . substr($strText, -($intMaxLength - strlen($strEllipses)));
				} else {
					$strTrimmedTextWithEllipses = substr($strText, 0, $intMaxLength - strlen($strEllipses)) . $strEllipses;
				}

				return $strTrimmedTextWithEllipses;
			}
		}

		return $strText;
	}


	/**
	 * Truncates the string to a given length, adding elipses (if needed).
	 * In comparison to Truncate() this function tries to cut at word boundaries.
	 *
	 * @param string $strString string to truncate
	 * @param integer $intMaxLength the maximum possible length of the string to return (including length of the ellipses). Negative value shorten the beginning and prepend ellipses.
	 * @param string $strEllipses string to append/prepend
	 * @return string the full string or the truncated string with ellipses
	 */
	public static function TruncateSmart($strText, $intMaxLength, $strEllipses='...')
	{
		if (extension_loaded('mbstring')) {
			$funcStrLen = 'mb_strlen';
			$funcSubStr = 'mb_substr';
			$funcStrPos = 'mb_strpos';
			$funcStrRPos = 'mb_strrpos';
		} else {
			$funcStrLen = 'strlen';
			$funcSubStr = 'substr';
			$funcStrPos = 'strpos';
			$funcStrRPos = 'strrpos';
		}

		$blnReverse = false;
		if ($intMaxLength < 0) {
			$blnReverse = true;
		}
		$intMaxLength = abs($intMaxLength);


		if ($funcStrLen($strText) > $intMaxLength) {

			if ($blnReverse) {
				$strText = $funcSubStr($strText, -($intMaxLength - $funcStrLen($strEllipses)));

				$pos = $funcStrPos($strText, ' ');

				if ($pos !== FALSE AND $pos < intval($funcStrLen($strText) * 0.6)) {
					$strText = $strEllipses . $funcSubStr($strText, $pos);
				} else {
					$strText = $strEllipses . $strText;
				}
			} else {

				$strText = $funcSubStr($strText, 0, $intMaxLength - $funcStrLen($strEllipses));

				$pos = $funcStrRPos($strText, ' ');

				if ($pos > intval($funcStrLen($strText) * 0.6)) {
					$strText = $funcSubStr($strText, 0, $pos) . ' ' . $strEllipses;
				} else {
					$strText = $strText . $strEllipses;
				}
			}

			return $strText;
		}


		return $strText;
	}

	public static function TruncateHtml($text, $length, $suffix = '&hellip;', $isHTML = true)
	{
		$strText = strip_tags($text);
		$intLengthText = strlen($strText);

		if ($intLengthText <= $length) {
			return $text;
		}

		$i = 0;
		$simpleTags = array('br' => true, 'hr' => true, 'input' => true, 'image' => true, 'link' => true, 'meta' => true);
		$tags = array();
		if ($isHTML) {
			preg_match_all('/<[^>]+>([^<]*)/', $text, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
			foreach ($m as $o) {
				if ($o[0][1] - $i >= $length)
					break;
				$t = substr(strtok($o[0][0], " \t\n\r\0\x0B>"), 1);
				// test if the tag is unpaired, then we mustn't save them
				if ($t[0] != '/' && (!isset($simpleTags[$t])))
					$tags[] = $t;
				elseif (end($tags) == substr($t, 1))
					array_pop($tags);
				$i += $o[1][1] - $o[0][1];
			}
		}

		// output without closing tags
		$output = substr($text, 0, $length = min(strlen($text), $length + $i));

		// closing tags
		$output2 = (count($tags = array_reverse($tags)) ? '</' . implode('></', $tags) . '>' : '');

		// Find last space or HTML tag (solving problem with last space in HTML tag eg. <span class="new">)
		$pos = (int) end(end(preg_split('/<.*>| /', $output, -1, PREG_SPLIT_OFFSET_CAPTURE)));

		// Append closing tags to output
		$output.=$output2;

		// Get everything until last space
		$one = substr($output, 0, $pos);

		// Get the rest
		$two = substr($output, $pos, (strlen($output) - $pos));

		// Extract all tags from the last bit
		preg_match_all('/<(.*?)>/s', $two, $tags);
		// Add suffix if needed
		if (strlen($text) > $length) {
			$one .= $suffix;
		}
		// Re-attach tags
		$output = $one . implode($tags[0]);

		return $output;
	}


	/**
	 * Returns true if string is appended with ':'
	 *
	 * @param string $strString
	 * @return string
	 */
	public static function hasColon($strString)
	{
		return preg_match('#:$#', $strString) ? true : false;
	}


	/**
	 * Appended ':' will be removed from the string
	 *
	 * @param string $strString
	 * @return string
	 */
	public static function StripColon($strString)
	{
		if (!is_string($strString)) {
			throw new \Cmp3\WrongParameterException("Function expects string as argument. " . gettype($strString) . " given");
		}

		return preg_replace('#:$#', '', $strString);
	}


	/**
	 * A colon ':' will be appended to the string if not yet exist
	 *
	 * @param string $strString
	 * @return string
	 */
	public static function AppendColon($strString)
	{
		if (!is_string($strString)) {
			throw new \Cmp3\WrongParameterException("Function expects string as argument. " . gettype($strString) . " given");
		}

		return preg_replace('#:$#', '', $strString) . ':';
	}


	/**
	 * Appended '.' will be removed from the string
	 *
	 * @param string $strString
	 * @return string
	 */
	public static function StripDot($strString)
	{
		return preg_replace('#\.$#', '', $strString);
	}


	/**
	 * Prepended dots ('.') will be removed from the string
	 * along with eventually following spaces
	 *
	 * @param string $strString
	 * @return string
	 */
	public static function StripLeadingDots($strString)
	{
		return preg_replace('#^[\.]+[\s]*#', '', $strString);
	}


	/**
	 * Appended '/' or '\' will be removed from the string
	 *
	 * @param string $strString
	 * @return string
	 */
	public static function StripSlash($strString)
	{
		return preg_replace('#[\\\/]+$#', '', $strString);
	}


	/**
	 * Prepended '/' or '\' will be removed from the string
	 *
	 * @param string $strString
	 * @return string
	 */
	public static function StripLeadingSlash($strString)
	{
		return preg_replace('#^[\\/]+#', '', $strString);
	}


	/**
	 * Creates a clean comma list like
	 * value, value two, another, value, last
	 *
	 * The passed string might have commas already and/or newlines.
	 * Spaces will be fixed.
	 *
	 * @param string $strString
	 * @return string
	 */
	public static function CommaList($strString)
	{
		$strString = self::StripInvisible($strString);
		$strString = implode("\n", trim_explode(',', $strString));
		$strString = self::RemoveRepeatingLinefeeds($strString);
		$strString = implode(", ", trim_explode("\n", $strString));

		return $strString;
	}



	/***************************************
	 *
	 *   Convert
	 *
	 ***************************************/





	/**
	 * Takes UTF-8 data and tries to represent it in US-ASCII characters like Unidecode().
	 * Also any character except " -_a-zA-Z0-9" will be removed.
	 * Finally the string will be converted to CamelCase.
	 *
	 *
	 * @param string $strString
	 * @param string $strPreserveCharacters Characters which should not be removed (regex definition)
	 * @return string
	 */
	public static function AnyToCamelCase($strString, $strPreserveCharacters=' \-_a-zA-Z0-9')
	{
		return \Cmp3\String\String::CamelCaseFromUnderscore(str_replace(array(' ', '-'), '_', \Cmp3\String\String::Simplify($strString, $strPreserveCharacters)));
	}



	/***************************************
	 *
	 *   Escape
	 *
	 ***************************************/

	/**
	 * Escapes the string so that it can be safely used in as an Xml Node (basically, adding CDATA if needed)
	 *
	 * @param string $strString string to escape
	 * @return string the XML Node-safe String
	 */
	public static function XmlEscape($strString)
	{
		if ((strpos($strString, '<') !== false) ||
				(strpos($strString, '&') !== false)) {
			$strString = str_replace(']]>', ']]]]><![CDATA[>', $strString);
			$strString = sprintf('<![CDATA[%s]]>', $strString);
		}

		return $strString;
	}




	/***************************************
	 *
	 *   Camel Case etc (ASCII only)
	 *
	 ************************************** */


	/**
	 * Convert string with underscores to words with spaces and first char as uppercase.
	 * (ASCII only)
	 *
	 * @param string $strName string to convert
	 * @return string
	 */
	public static function WordsFromUnderscore($strName)
	{
		$strToReturn = trim(str_replace('_', ' ', $strName));
		if (strtolower($strToReturn) == $strToReturn)
			return ucwords($strToReturn);
		return $strToReturn;
	}


	/**
	 * Convert string with underscores to camel case words with no spaces.
	 * (ASCII only)
	 *
	 * @param string $strName string to convert
	 * @return string
	 */
	public static function CamelCaseFromUnderscore($strName)
	{
		$strToReturn = '';

		// If entire underscore string is all uppercase, force to all lowercase
		// (mixed case and all lowercase can remain as is)
		if ($strName == strtoupper($strName))
			$strName = strtolower($strName);

		while (($intPosition = strpos($strName, "_")) !== false) {
			// Use 'ucfirst' to create camelcasing
			$strName = ucfirst($strName);
			if ($intPosition == 0) {
				$strName = substr($strName, 1);
			} else {
				$strToReturn .= substr($strName, 0, $intPosition);
				$strName = substr($strName, $intPosition + 1);
			}
		}

		$strToReturn .= ucfirst($strName);
		return $strToReturn;
	}


	/**
	 * Convert string with underscores to camel case words with no spaces.
	 * (ASCII only)
	 *
	 * @param string $strName string to convert
	 * @return string
	 */
	public static function SmallCamelCaseFromUnderscore($strName)
	{
		if (strlen($strName) == 0) {
			return '';
		} else {
			$strName = self::CamelCaseFromUnderscore($strName);
			$strName{0} = strtolower($strName{0});
			return $strName;
		}
	}


	/**
	 * Convert string with camel case words with no string with spaces.
	 * (ASCII only)
	 *
	 * @param string $strName string to convert
	 * @return string
	 */
	public static function WordsFromCamelCase($strName)
	{
		if (strlen($strName) == 0)
			return '';

		$strToReturn = self::FirstCharacter($strName);

		for ($intIndex = 1; $intIndex < strlen($strName); $intIndex++) {
			// Get the current character we're examining
			$strChar = substr($strName, $intIndex, 1);

			// Get the character previous to this
			$strPrevChar = substr($strName, $intIndex - 1, 1);

			// If the previous char is a space
			if ($strPrevChar == ' ')
			// Add the char
				$strToReturn .= $strChar;

			// If a digit, and the previous character is NOT a digit
			else if ((ord($strChar) >= ord('A')) &&
					(ord($strChar) <= ord('Z')))
			// Add a Space
				$strToReturn .= ' ' . $strChar;

			// If a digit, and the previous character is NOT a digit
			else if ((ord($strChar) >= ord('0')) &&
					(ord($strChar) <= ord('9')) &&
					((ord($strPrevChar) < ord('0')) ||
					(ord($strPrevChar) > ord('9'))))
			// Add a space
				$strToReturn .= ' ' . $strChar;

			// If a letter, and the previous character is a digit
			else if ((ord(strtolower($strChar)) >= ord('a')) &&
					(ord(strtolower($strChar)) <= ord('z')) &&
					(ord($strPrevChar) >= ord('0')) &&
					(ord($strPrevChar) <= ord('9')))
			// Add a space
				$strToReturn .= ' ' . $strChar;

			// Otherwise
			else
			// Don't add a space
				$strToReturn .= $strChar;
		}

		return $strToReturn;
	}


	/**
	 * Convert string with camel case words with no string with underscores.
	 * (ASCII only)
	 *
	 * @param string $strName string to convert
	 * @return string
	 */
	public static function UnderscoreFromCamelCase($strName)
	{
		if (strlen($strName) == 0)
			return '';

		$strToReturn = $strName{0};

		$strNameLen = strlen($strName);

		for ($intIndex = 1; $intIndex < $strNameLen; $intIndex++) {
			$strChar = $strName{$intIndex};
			if (ctype_upper($strChar))
				$strToReturn .= '_' . $strChar;
			else
				$strToReturn .= $strChar;
		}

		return strtolower($strToReturn);
	}


	/**
	 * Convert string with camel case words with no string with underscores.
	 * (ASCII only)
	 *
	 * @param string $strName string to convert
	 * @return string
	 */
	public static function isUpperCase($strChar)
	{
		return ctype_upper($strChar);
	}

}

