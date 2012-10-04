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
 * An abstract utility class to handle string/html output.
 * All methods are statically available.
 *
 * STATUS: alpha
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage String
 * @package    CMP3
 */
abstract class HTML {



	/**
	 * escape content for output in HTML (htmlspecialchars)
	 *
	 * @param string $strText text string to perform html escaping
	 * @return string the html escaped string
	 */
	public static function Escape($strText) {
		return htmlspecialchars($strText, ENT_COMPAT, 'utf8');
	}


	/**
	 * Decodes HTML entities like &nbsp; &gt; &lt; &quot; &amp; &uuml; back to normal characters
	 *
	 * @param string $strText text string to perform entity decoding
	 * @return string the decoded string
	 */
	public static function EntityDecode($strText) {
		return html_entity_decode($strText, ENT_QUOTES, 'utf8');
	}


	/**
	 * small helper to create a tag
	 *
	 * @param string $tagName
	 * @param mixed $attributes
	 * @param string $content
	 * @param boolean $hsc
	 * @return string HTML content
	 */
	public static function Tag($tagName, $attributes, $content, $hsc=true) {

		if ($content=='') {
			return '';
		}

		$bag = '<'.$tagName . ($attributes ? ' '.self::ImplodeAttributes($attributes) : '') . '>';
		$bag .=  ($hsc ? self::Escape($content) : $content);
		$bag .=  '</'.$tagName.'>';

		return $bag;

	}





	/***************************************
	 *
	 *   Truncate
	 *
	 ***************************************/



	/**
	 * Truncates the string to a given length, adding elipses (if needed).
	 * The shortened string is trimmed and the elipses are prepended with whitespace: "abc ..."
	 * The minimum length of the truncated string is 6
	 *
	 * Compared \Cmp3\String\String::Truncate this respects HTML tags and entities.
	 *
	 * @param string $strString string to truncate
	 * @param integer $intMaxLength the maximum possible length of the string to return (including length of the elipse)
	 * @return string the full string or the truncated string with eplise
	 */
	public static function Truncate($strText, $intMaxLength, $strPostfix = ' ...')
	{
		$strText = self::_crop($strText, max(6, $intMaxLength), $strPostfix, true);

		return $strText;
	}


	/**
	 * Truncates the string to a given length, adding elipses (if needed).
	 * In comparison to Truncate() this function tries to cut at wound boundaries.
	 *
	 * Compared \Cmp3\String\String::Truncate this respects HTML tags and entities.
	 *
	 * @todo unit test
	 * @param string $strString string to truncate
	 * @param integer $intMaxLength the maximum possible length of the string to return (including length of the elipse)
	 * @return string the full string or the truncated string with eplise
	 */
	public static function TruncateSmart($strText, $intMaxLength)
	{
		$strText = self::_crop($strText, max(6, $intMaxLength), ' ...', true);

		return $strText;
	}


	/**
	 * Implements the stdWrap property "cropHTML" which is a modified "substr" function allowing to limit a string length
	 * to a certain number of chars (from either start or end of string) and having a pre/postfix applied if the string
	 * really was cropped.
	 *
	 * Compared \Cmp3\String\String::Truncate it respects HTML tags and entities.
	 *
	 * @param string  $content	The string to perform the operation on
	 * @param integer $chars Max number of chars of the string. Negative value means cropping from end of string.
	 * @param string  $replacementForEllipsis	The pre/postfix string to apply if cropping occurs.
	 * @param boolean $crop2space If set then crop will be applied at nearest space.
	 * @return	string
	 * @see tslib_content::stdWrap() in TYPO3 4.3
	 * @todo needs to be tested - might be crap
	 */
	protected function _crop($content, $chars, $replacementForEllipsis='', $crop2space=false)
	{
		// Split $content into an array (even items in the array are outside the tags, odd numbers are tag-blocks).
		$tags= 'a|b|blockquote|body|div|em|font|form|h1|h2|h3|h4|h5|h6|i|li|map|ol|option|p|pre|sub|sup|select|span|strong|table|thead|tbody|tfoot|td|textarea|tr|u|ul|br|hr|img|input|area|link';
		// todo We should not crop inside <script> tags.
		$tagsRegEx = "
			(
				(?:
					<!--.*?-->					# a comment
				)
				|
				</?(?:\s*" . $tags . ")+			# opening tag ('<tag') or closing tag ('</tag')
				(?:
					(?:
						\s+\w+					# EITHER spaces, followed by word characters (attribute names)
						(?:
							\s*=?\s*			# equals
							(?>
								\"[^\"]*\"			# attribute values in double-quotes
								|
								'[^]*'			# attribute values in single-quotes
								|
								[^'\">\s]+		# plain attribute values
							)
						)?
					)+\s*
					|							# OR only spaces
					\s*
				)
				/?>								# closing the tag with '>' or '/>'
			)";
		$splittedContent = preg_split('%' . $tagsRegEx . '%xs', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

		// Reverse array if we are cropping from right.
		if ($chars < 0) {
			$splittedContent = array_reverse($splittedContent);
		}

		// Crop the text (chars of tag-blocks are not counted).
		$strLen = 0;
		$croppedOffset = NULL; // This is the offset of the content item which was cropped.
		for ($offset = 0; $offset < count($splittedContent); $offset++) {
			if ($offset%2 === 0) {
				// html_entity_decode() supports the most often used charsets, but not all TYPO3 supports. This only may cause
				// problems if you use entities AND an unsupported charset. Entities will then be counted as several single characters.
				//$thisStrLen = $GLOBALS['TSFE']->csConvObj->strlen($GLOBALS['TSFE']->renderCharset, html_entity_decode($splittedContent[$offset],ENT_COMPAT,$GLOBALS['TSFE']->renderCharset));


				#RF-
				$strString = html_entity_decode($splittedContent[$offset], ENT_COMPAT, 'utf-8');
				if (extension_loaded('mbstring')) {
					$thisStrLen = mb_strlen($strString);
				} else {
					$thisStrLen = strlen($strString);
				}


				if (($strLen + $thisStrLen > abs($chars))) {
					$croppedOffset = $offset;
					$cropPosition = abs($chars) - $strLen;
					if ($crop2space) {
						$cropRegEx = $chars < 0 ? '#(?<=\s).{0,' . $cropPosition . '}$#ui' : '#^.{0,' . $cropPosition . '}(?=\s)#ui';
					} else {
						// The snippets "&[^&\s;]{2,7};" in the RegEx below represents entities.
						$cropRegEx = $chars < 0 ? '#(.(?![^&\s]{2,7};)|(&[^&\s;]{2,7};)){0,' . $cropPosition . '}$#ui' : '#^(.(?![^&\s]{2,7};)|(&[^&\s;]{2,7};)){0,' . $cropPosition . '}#ui';
					}
					if (preg_match($cropRegEx, $splittedContent[$offset], $croppedMatch)) {
						$splittedContent[$offset] = $croppedMatch[0];
					}
					break;
				} else {
					$strLen += $thisStrLen;
				}
			}
		}

		// Close cropped tags.
		$closingTags = array();
		if($croppedOffset !== NULL) {
			$tagName = '';
			$openingTagRegEx = '#^<(\w+)(?:\s|>)#u';
			$closingTagRegEx = '#^</(\w+)(?:\s|>)#u';
			for ($offset=$croppedOffset-1; $offset >= 0; $offset = $offset-2) {
				if (preg_match('&/>$&', $splittedContent[$offset])) {
					// Ignore empty element tags (e.g. <br />).
					continue;
				}
				preg_match($chars < 0 ? $closingTagRegEx : $openingTagRegEx, $splittedContent[$offset], $matches);
				$tagName = isset($matches[1]) ? $matches[1] : NULL;
				if ($tagName !== NULL) {
					// Seek for the closing (or opening) tag.
					$seekingTagName = '';
					for ($seekingOffset = $offset + 2; $seekingOffset < count($splittedContent); $seekingOffset = $seekingOffset + 2) {
						preg_match($chars < 0 ? $openingTagRegEx : $closingTagRegEx, $splittedContent[$seekingOffset], $matches);
						$seekingTagName = isset($matches[1]) ? $matches[1] : NULL;
						if ($tagName === $seekingTagName) { // We found a matching tag.
							// Add closing tag only if it occurs after the cropped content item.
							if ($seekingOffset > $croppedOffset) {
								$closingTags[] = $splittedContent[$seekingOffset];
							}
							break;
						}
					}
				}
			}
			// Drop the cropped items of the content array. The $closingTags will be added later on again.
			array_splice($splittedContent, $croppedOffset + 1);
		}

		$splittedContent = array_merge($splittedContent, array($croppedOffset !== NULL ? $replacementForEllipsis : ''), $closingTags);

		// Reverse array once again if we are cropping from the end.
		if ($chars < 0) {
			$splittedContent = array_reverse($splittedContent);
		}

		return implode('', $splittedContent);
	}



	/***************************************
	 *
	 *   Tools - Extract from HTML
	 *
	 ***************************************/




	/**
	 * Returns the content of a <body> tag
	 *
	 * @param string $strContent
	 * @return string
	 */
	public static function GetBodyContent ($strContent)
	{
		if (preg_match('#<body[^>]*>(.*)</body>#is', $strContent, $match))
			return $match[1];

		return false;
	}


	/**
	 * Strip all HTML code including <style> and <script> tags and it's content
	 *
	 * @param string $strContent
	 * @return string
	 */
	public static function Strip ($strContent)
	{
		$strContent = preg_replace('#<style[^>]*>.*?</style>#is', '', $strContent);
		$strContent = preg_replace('#<script[^>]*>.*?</script>#is', '', $strContent);
		return strip_tags($strContent);
	}


	/**
	 * Purifies HTML.
	 * Useful to get trusted, XSS free HTML out of untrusted sources
	 * and to get clean and valid XHTML out of broken or incomplete HTML
	 *
	 * @param string $strHtml To be cleaned HTML
	 * @param array $arrConfig Additional configuration for the purifier @see http://htmlpurifier.org/live/configdoc/plain.html
	 * @throws UnexpectedValueException
	 * @return string Safe and clean HTML
	 */
	public static function Purify($strHtml, array $arrConfig = array())
	{
		static $objPurifier = NULL, $objConfig = NULL;

		if ($objPurifier === NULL) {
			require(PATH_txnext . 'library/htmlpurifier/HTMLPurifier.standalone.php'); //@todo use autoloader?
			$objConfig = HTMLPurifier_Config::createDefault();
			$objConfig->set('Core.Encoding', \tx_cmp3::$System->EncodingType);
			$objConfig->set('Cache.SerializerPath', \tx_cmp3::ResolvePath('PATH_site') . 'typo3temp/cache');
			$objConfig->set('Attr.EnableID', TRUE);

			// allow rel attribute with arbitrary contents
			$objConfig->set('HTML.DefinitionRev', 1);
			$mixDefinitions = $objConfig->getHTMLDefinition(TRUE);
			if ($mixDefinitions) {
				$mixDefinitions->addAttribute('a', 'rel', 'CDATA');
			}

			// @todo more things useful to configure?
			$objPurifier = new HTMLPurifier($objConfig);
		}

		$objAdditionalConfig = NULL;
		if (!empty($arrConfig)) {
			if (!is_array($arrConfig)) {
				throw new UnexpectedValueException('Purifier configuration has to be an array but was of type: ' . gettype($arrConfig));
			}
			$objAdditionalConfig = clone $objConfig;
			foreach ($arrConfig as $strConfigName => $strConfigValue) {
				$objAdditionalConfig->set($strConfigName, $strConfigValue);
			}
		}

		return $objPurifier->purify($strHtml, $objAdditionalConfig);
	}




	/***************************************
	 *
	 *   Tools - Attributes
	 *
	 ***************************************/


	/**
	 * Explode a string into a HTML tags attributes and it's values
	 *
	 * @param 	string 	$attributeString HTML tag or it's attributes
	 * @return array Attribute/value pairs
	 */
	public static function ExplodeAttributes($attributeString)
	{
		if (!is_array($attributeString)) {
			$attributes = array();
			$attributeMatches = array();
			preg_match_all('# *([\w]+)="([^"]*)"#', $attributeString, $attributeMatches);

			if(count($attributeMatches[1])) {
				foreach($attributeMatches[2] as $name => $value) {
					$attributeMatches[2][$name] = htmlspecialchars_decode($value);
				}
				$attributes = array_combine($attributeMatches[1], $attributeMatches[2]);
			}
		}
		return $attributes;
	}


	/**
	 * Implode an array into a string to be used in HTML tags as attributes and it's values
	 *
	 * @param 	array 	$attributes Attribute name/value pairs
	 * @param 	boolean $hsc If set (default) all values will be htmlspecialchars()
	 * @return string 	attributes
	 */
	public static function ImplodeAttributes($attributes, $hsc=true)
	{
		if (is_array($attributes)) {
			$attributeString = '';
			foreach($attributes as $name => $value) {
				$attributeString .= ' '.$name.'="'.($hsc ? htmlspecialchars($value) : $value).'"';
			}
		} else {
			$attributeString = $attributes;
		}
		return $attributeString;
	}


	/**
	 * Explode a string with CSS styles into an array with property - value pairs
	 *
	 * @param 	string 	$strStyles CSS styles
	 * @return array Property/value pairs
	 */
	public static function ExplodeStyles($strStyles)
	{
		if (is_array($strStyles)) {
			return $strStyles;
		}

		$strStyleArray = array();
		$strStyles = trim_explode(';', $strStyles);
		foreach ($strStyles as $strStyle) {
			list($strProperty, $strValue) = trim_explode(':', $strStyle);
			$strStyleArray[$strProperty] = $strValue;
		}
		return $strStyleArray;
	}


	/**
	 * Implode a CSS styles array with property - value pairs to a string
	 *
	 * @param 	array 	$strStyleArray CSS styles as Property/value pairs
	 * @return string CSS styles as string
	 */
	public static function ImplodeStyles($strStyleArray)
	{
		if (is_string($strStyleArray)) {
			return $strStyleArray;
		}

		$strStyleArray2 = array();
		foreach ($strStyleArray as $strProperty => $strValue) {
			$strStyleArray2[] = $strProperty . ':' . $strValue;
		}
		return implode(';', $strStyleArray2);
	}
}







