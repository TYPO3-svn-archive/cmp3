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
 * @subpackage Css
 * @package    CMP3
 * @copyright  Copyright (c) 2012 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */

namespace Cmp3\Css;



/*
 * TODO
 *
 * -  * selector
 * -  cascading selectors: div p
 * - margin, margin-left
 */



/**
 * Parses CSS and can give styles for a given css selector.
 *
 * So what is this good for?
 * Let's say you want to make things like colors, fonts, margins, ... configurable.
 * Instead of inventing properties/options for it just use CSS for that.
 *
 * Maybe this is not a good idea, maybe it is. You have to use this in some way in your application.
 * CMP3 itself doesn't use it - for now.
 *
 * STATUS: beta
 *
 *
 * Supported css selectors to get styles:
 *
 * any specific rule which matches exactly
 * p
 * p.x
 * .x
 * p#y
 * p:link
 *
 * properties from '*' definition will be used as default styles
 *
 * Not (yet) supported:
 * div p
 * div div p - when div p is defined
 * div.x *
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage PDF
 * @package    CMP3
 *
 * parts of the code:
 *
 * License
 * Copyright (c) 2010, Tijs Verkoyen. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * This software is provided by the author "as is" and any express or implied warranties, including, but not limited to, the implied warranties of merchantability and fitness for a particular purpose are disclaimed. In no event shall the author be liable for any direct, indirect, incidental, special, exemplary, or consequential damages (including, but not limited to, procurement of substitute goods or services; loss of use, data, or profits; or business interruption) however caused and on any theory of liability, whether in contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of this software, even if advised of the possibility of such damage.
 *
 * @author		Tijs Verkoyen <php-css-to-inline-styles@verkoyen.eu>
 * @version		1.0.3
 * @copyright	Copyright (c) 2010, Tijs Verkoyen. All rights reserved.
 * @license		BSD License
 */
class Css
{


	/**
	 * The processed CSS rules
	 *
	 * @var	array
	 */
	protected $cssRules;


	/**
	 * Creates an instance, you could set the HTML and CSS here, or load it later.
	 *
	 * @return	void
	 * @param	string $css	The CSS to use
	 */
	public function __construct($css)
	{
		$this->processCSS(strtolower((string) $css));
	}



	/**
	 * Returns the internal array with parsed css
	 * Please don't use when not necessary
	 *
	 * @return array
	 */
	public function GetRules()
	{
		return $this->cssRules;
	}


	/**
	 * Returns all CSS as string
	 *
	 * @return string
	 */
	public function GetAll()
	{
		$result = "";
		foreach ($this->cssRules as $values) {
			$result .= $values['selector'] . " {\n";
			foreach ($values['properties'] as $key => $value) {
				$result .= "  $key: $value;\n";
			}
			$result .= "}\n\n";
		}
		return $result;
	}


	/**
	 * Get the value for a css property
	 *
	 * @param string $selector
	 * @param string $property
	 * @return string
	 */
	public function GetProperty($selector, $property)
	{
		$selector = strtolower($selector);
		$property = strtolower($property);

		$properties = $this->GetProperties($selector);

		return $properties[$property];
	}


	/**
	 * get css properties for a given selector
	 *
	 * @todo add caching
	 *
	 * @param string $selector
	 * @return array
	 */
	public function GetProperties($selector)
	{
		$debug = false;

		$selector = strtolower($selector);

		list($tag, $pseudo) = explode(":", $selector);
		list($tag, $class) = explode(".", $tag);
		list($tag, $id) = explode("#", $tag);


		if ($debug) echo  "\nquery selector: $tag #$id .$class :$pseudo\n";

		$result = array();
		foreach ($this->cssRules as $values) {

			$_selector = $values['selector'];
			$properties = $values['properties'];

			if ($_selector == '*') {
				$result = array_merge($result, $properties);
				if ($debug) echo  "match *\n";
				continue;
			}

			if ($selector == $_selector) {
				$result = array_merge($result, $properties);
				if ($debug) echo  "exact match: $selector\n";
				continue;
			}

			list($_tag, $_pseudo) = explode(":", $_selector);
			list($_tag, $_class) = explode(".", $_tag);
			list($_tag, $_id) = explode("#", $_tag);


			if ($debug) echo  "css selector: $_tag #$_id .$_class :$_pseudo";
			$tagmatch = (strcmp($tag, $_tag) == 0) | (strlen($_tag) == 0);
			$pseudomatch = (strcmp($pseudo, $_pseudo) == 0) | (strlen($_pseudo) == 0);
			$classmatch = (strcmp($class, $_class) == 0) | (strlen($_class) == 0);
			$idmatch = (strcmp($id, $_id) == 0);

			if ($tagmatch & $pseudomatch & $classmatch & $idmatch) {
				$result = array_merge($result, $properties);
				if ($debug) echo  " - match";
			}
			if ($debug) echo  "\n";
		}
		return $result;
	}


	/**
	 * found here: http://www.bennadel.com/blog/2365-Calculating-CSS-Selector-Specificity-Using-ColdFusion.htm
	 *
	 * I roughly calculate the numeric specificity of a CSS selector.
	 * CAUTION: This algorithm doesn't know how to take into account character escape sequences.
	 *
	 *
	 * @param string $selector
	 * @return number
	 */
	protected function calculateCSSSpecifity($selector) {


		// Before we start parsing the selector, we're gonna try to
		// strip out characters that will making pattern matching more
		// difficult.

		// Strip out wild-card matches - these don't contribute to
		// a selector specificity.
		$selector = str_replace( "*", "", $selector );

		// Strip out any quoted values - these will only be in the
		// attribute selectors (and don't contribute to our
		// specificity calculation).
		$selector = preg_replace( '#"[^"]*"#', "", $selector );
		$selector = preg_replace( "#'[^']*'#", "", $selector );

		// Now that we've stripped out the quoted values, let's strip
		// out any content within the attribute selectors.
		$selector = preg_replace( "#\[[^\]]*\]#", "[]", $selector );

		// Strip out any special child and descendant selectors as
		// these don't really contribute to specificity.
		$selector = preg_replace( "#[>+~]+#", " ", $selector );

		// Strip out any "function calls"; these will be for complex
		// selectors like :not() and :eq(). We're gonna do this in a
		// loop so that we can simplify the replace and handle nested
		// groups of parenthesis.
		while (strpos( $selector, "(" ) !== false){

			// Strip out the smallest parenthesis.
			$selector = preg_replace( "#\([^)]*\)#", "", $selector );
		}

		// Now that we've stripped off any parenthesis, our pseudo-
		// elements and pseudo-classes should all be in a uniform.
		// However, pseudo-elements and pseudo-classes actually have
		// different specifity than each other. To make things simple,
		// let's convert pseudo-classes (which have high specificity)
		// into mock classes.
		$selector = preg_replace(
				"#:(first-child|last-child|link|visited|hover|active|focus|lang)#",
				".pseudo",
				$selector
			);

		// Now that we've removed the pseudo-classes, the only
		// constructs that start with ":" should be the pseudo-
		// elements. Let's replace these with mock elements. Notice
		// that we are injecting a space before the element name.
		$selector = preg_replace( "#:[\w-]+#", " pseudo", $selector );

		// Now that we've cleaned up the selector, we can count the
		// number of key elements within the selector.

		// Count the number of ID selectors. These are the selectors
		// with the highest specificity.
		$idCount = preg_match_all( "/#[\w-]+/", $selector, $matches);

		// Count the number of classes, attributes, and pseudo-
		// classes. Remember, we converted our pseudo-classes to be
		// mock classes (.pseudo).
		$classCount = preg_match_all( "#\.[\w_-]+|\[\]#", $selector, $matches );

		// Count the number of elements and pseudo-elements. Remember,
		// we converted our pseudo-selements to be mock elements
		// (pseudo).
		$elementCount = preg_match_all( "#(^|\s)[\w_-]+#", $selector, $matches );

		// Now that we have our count of the various parts of the
		// selector, we can calculate the specificity by concatenating
		// the parts (as strings), and then converting to a number -
		// the number will be the specificity of the selector.
		return intval( $idCount . $classCount . $elementCount );
	}


	/**
	 * Process the loaded CSS
	 *
	 * @param string CSS
	 * @return	void
	 */
	protected function processCSS($css)
	{
		// remove newlines
		$css = str_replace(array("\r", "\n"), '', $css);

		// replace double quotes by single quotes
		$css = str_replace('"', '\'', $css);

		// remove comments
		$css = preg_replace('|/\*.*?\*/|', '', $css);

		// remove spaces
		$css = preg_replace('/\s\s+/', ' ', $css);

		// rules are splitted by }
		$rules = (array) explode('}', $css);

		// init var
		$i = 1;

		// loop rules
		foreach($rules as $rule)
		{
			// split into chunks
			$chunks = explode('{', $rule);

			// invalid rule?
			if(!isset($chunks[1])) continue;

			// set the selectors
			$selectors = trim($chunks[0]);

			// get cssProperties
			$cssProperties = trim($chunks[1]);

			// split multiple selectors
			$selectors = (array) explode(',', $selectors);

			// loop selectors
			foreach($selectors as $selector)
			{
				// cleanup
				$selector = trim($selector);

				// build an array for each selector
				$ruleSet = array();

				// store selector
				$ruleSet['selector'] = $selector;

				// process the properties
				$ruleSet['properties'] = $this->processCSSProperties($cssProperties);

				// calculate specifity
				$ruleSet['specifity'] = $this->calculateCSSSpecifity($selector);

				// add into global rules
				$this->cssRules[] = $ruleSet;
			}

			// increment
			$i++;
		}

		// sort based on specifity
		if(!empty($this->cssRules)) usort($this->cssRules, array($this, 'sortOnSpecifity'));
	}


	/**
	 * Process the CSS-properties
	 *
	 * @return	array
	 * @param	string $propertyString
	 */
	protected function processCSSProperties($propertyString)
	{
		// split into chunks
		$properties = (array) explode(';', $propertyString);

		// init var
		$pairs = array();

		// loop properties
		foreach($properties as $property)
		{
			// split into chunks
			$chunks = (array) explode(':', $property, 2);

			// validate
			if(!isset($chunks[1])) continue;

			// add to pairs array
			$pairs[trim($chunks[0])] = trim($chunks[1]);
		}

		// sort the pairs
		ksort($pairs);

		// return
		return $pairs;
	}


	/**
	 * Sort an array on the specifity element
	 *
	 * @return	int
	 * @param	array $e1	The first element
	 * @param	array $e2	The second element
	 */
	protected static function sortOnSpecifity($e1, $e2)
	{
		// validate
		if(!isset($e1['specifity']) || !isset($e2['specifity'])) return 0;

		// lower
		if($e1['specifity'] < $e2['specifity']) return -1;

		// higher
		if($e1['specifity'] > $e2['specifity']) return 1;

		// fallback
		return 0;
	}


}

/*

$strCss = '

* { line-height: 110%; }
body {
  color: #000;
  font-family: dejavusans;
  font-size: 10pt;
  line-height: 120%;
  top:220mm;
  left:120mm;
  width:70mm;
  height:40mm;
}
img {
  top:220mm;
  left:70mm;
  width:30mm;
  height:30mm;
}
p {
  color: #000;
  font-family: dejavusans;
  font-size: 11pt;
  line-height: 120%;
  margin:0 0 0.3em 0;
}
div p.a {
  color: #fff;
}
p.a {
  color: #fe0;
  margin:0 0 1em 0;
}
.a {
  color: #ff0;
  margin:0 0 1em 0;
}
.b {
  color: #fff;
  margin:0 0 1em 0;
}
p.b {
  color: #f0a;
  margin:0 0 1em 0;
}
';

error_reporting(E_ALL ^ (E_NOTICE));

$css = new Css($strCss);





echo "\n";
var_export($css->GetRules());

echo "\n";
echo $css->GetAll();


echo "\n";
echo "color\n";
echo 'body: ' . $css->GetProperty('body', 'color');
echo "\n";
echo 'p: ' . $css->GetProperty('p', 'color');
echo "\n";
echo 'p.a: ' . $css->GetProperty('p.a', 'color');
echo "\n";
echo '.a: ' . $css->GetProperty('.a', 'color');
echo "\n";
echo '.b: ' . $css->GetProperty('.b', 'color');
echo "\n";
echo 'p.b: ' . $css->GetProperty('p.b', 'color');
echo "\n";
echo 'div.b: ' . $css->GetProperty('div.b', 'color');
echo "\n";
echo 'div p.b: ' . $css->GetProperty('div p.b', 'color');
echo "\n";


echo "\n";
echo "font-size\n";
echo 'p: ' . $css->GetProperty('p', 'font-size');
echo "\n";
echo 'p.a: ' . $css->GetProperty('p.a', 'font-size');
echo "\n";


echo "\n";
echo "\n";
echo 'body: ' . var_export($css->GetProperties('body'),true);
echo "\n";
echo 'p: ' . var_export($css->GetProperties('p'),true);
echo "\n";
echo 'p.a: ' . var_export($css->GetProperties('p.a'),true);
echo "\n";
echo '.a: ' . var_export($css->GetProperties('.a'),true);
echo "\n";

*/