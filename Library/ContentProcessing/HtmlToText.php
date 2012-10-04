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
 * @subpackage ContentProcessing
 * @package    CMP3
 * @copyright  Copyright (c) 2008 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\ContentProcessing;



/**
 * Content processors which performs HTML to text conversation which tries to be more nice than strip_tags()
 *
 * STATUS: beta
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class HtmlToText extends ProcessorAbstract {


	/**
	 * Processes a content object
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface $objContent)
	{
		$content = $objContent->GetData();

		if (!$content) return;

		/************************************************************
		 * Modified 12/27/2007 by Charles Childers for use with Ent
		 ************************************************************/


		/************************************************************
		 Library to convert HTML into an approximate text equivalent
		 v1.0.4 update 11/10/2008 to convert HTML entities
		 *************************************************************

		 Please see http://www.howtocreate.co.uk/php/ for details
		 Please see http://www.howtocreate.co.uk/jslibs/termsOfUse.html
		 for terms and conditions of use

		 The reason this library was written was to convert HTML email
		 contents into a text based email content, where the rendering
		 does not have to be as accurate as with a text based browser.
		 However, there must be many more uses for it.

		 This library attempts to deal with non-standard HTML, but may
		 occasionally suffer from problems with pages that are not
		 properly written - most especially: Tags written as
		 <tagName attribute=somethingWithA"or'InItButNotSurroundedByQuotes>,
		 Closing </pre> or </textarea> tags without their corresponding
		 opening tags, Tags within <textarea> </textarea> tags, which
		 will be rendered, even though they should not be.

		 Conversion requires a lot of preg_replace statements, so it can
		 be quite slow with large HTML files.

		 ******
		 To use
		 ******

		 This library requires PHP 4+

		 To use this library, put the following line in your script
		 before the part that needs it:
		 require('PATH_TO_THIS_FILE/html2text.php');

		 To convert HTML/PHP to text:
		 $textVersion = html2text( $HTMLversion );

		 ************
		 Further info
		 ************

		 For the technically minded, this is the process I use for
		 converting HTML to approx text:

		 REMOVE php start and end tags
		 REMOVE <!-- -->
		 ensure HTML uses entities in the right places (like inside tags) so strip_tags works properly
		 <STYLE|SCRIPT|OPTION>
		 carefully remove everything between them
		 strip_tags except the important ones
		 replace all \s that are after the start or a </pre> and before <pre> or end with a single space
		 </TITLE|HR>
		 \n --------------------
		 <H1|H2|H3|H4|H5|H6|DIV|P|PRE>
		 \n\n
		 <SUP>
		 ^
		 <UL|OL|BR|DL|DT|TABLE|CAPTION|TR->(TH|TD)>
		 \n
		 <LI>
		 \n·
		 <DD>
		 \n\t
		 <TH|TD>
		 \t
		 <A|AREA href=(!javascript:&&!#)>
		 [LINK:hrefWithout#]
		 <IMG>
		 [IMG:alt]
		 <FORM>
		 [FORM:action]
		 <INPUT|TEXTAREA|BUTTON|SELECT>
		 [INPUT]
		 strip tags again, leaving nothing this time
		 un-htmlspecialchars
		 */


		//remove PHP if it exists
		while( substr_count( $content, '<'.'?' ) && substr_count( $content, '?'.'>' ) && strpos( $content, '?'.'>', strpos( $content, '<'.'?' ) ) > strpos( $content, '<'.'?' ) ) {
			$content = substr( $content, 0, strpos( $content, '<'.'?' ) ) . substr( $content, strpos( $content, '?'.'>', strpos( $content, '<'.'?' ) ) + 2 );
		}
		//remove comments
		while( substr_count( $content, '<!--' ) && substr_count( $content, '-->' ) && strpos( $content, '-->', strpos( $content, '<!--' ) ) > strpos( $content, '<!--' ) ) {
			$content = substr( $content, 0, strpos( $content, '<!--' ) ) . substr( $content, strpos( $content, '-->', strpos( $content, '<!--' ) ) + 3 );
		}
		//now make sure all HTML tags are correctly written (> not in between quotes)
		for( $x = 0, $goodStr = '', $is_open_tb = false, $is_open_sq = false, $is_open_dq = false; strlen( $chr = $content{$x} ); $x++ ) {
			//take each letter in turn and check if that character is permitted there
			switch( $chr ) {
				case '<':
					if( !$is_open_tb && strtolower( substr( $content, $x + 1, 5 ) ) == 'style' ) {
						$content = substr( $content, 0, $x ) . substr( $content, strpos( strtolower( $content ), '</style>', $x ) + 7 ); $chr = '';
					} elseif( !$is_open_tb && strtolower( substr( $content, $x + 1, 6 ) ) == 'script' ) {
						$content = substr( $content, 0, $x ) . substr( $content, strpos( strtolower( $content ), '</script>', $x ) + 8 ); $chr = '';
					} elseif( !$is_open_tb ) { $is_open_tb = true; } else { $chr = '&lt;'; }
					break;
				case '>':
					if( !$is_open_tb || $is_open_dq || $is_open_sq ) { $chr = '&gt;'; } else { $is_open_tb = false; }
					break;
				case '"':
					if( $is_open_tb && !$is_open_dq && !$is_open_sq ) { $is_open_dq = true; }
					elseif( $is_open_tb && $is_open_dq && !$is_open_sq ) { $is_open_dq = false; }
					else { $chr = '&quot;'; }
					break;
				case "'":
					if( $is_open_tb && !$is_open_dq && !$is_open_sq ) { $is_open_sq = true; }
					elseif( $is_open_tb && !$is_open_dq && $is_open_sq ) { $is_open_sq = false; }
			} $goodStr .= $chr;
		}
		//now that the page is valid (I hope) for strip_tags, strip all unwanted tags
		$goodStr = strip_tags( $goodStr, '<title><hr><h1><h2><h3><h4><h5><h6><div><p><pre><sup><ul><ol><br><dl><dt><table><caption><tr><li><dd><th><td><a><area><img><form><input><textarea><button><select><option>' );
		//strip extra whitespace except between <pre> and <textarea> tags
		$content = preg_split( "/<\/?pre[^>]*>/i", $goodStr );
		for( $x = 0; is_string( $content[$x] ); $x++ ) {
			if( $x % 2 ) {
				$content[$x] = '<pre>'.$content[$x].'</pre>';
			} else {
				$goodStr = preg_split( "/<\/?textarea[^>]*>/i", $content[$x] );
				for( $z = 0; is_string( $goodStr[$z] ); $z++ ) {
					if( $z % 2 ) {
						$goodStr[$z] = '<textarea>'.$goodStr[$z].'</textarea>';
					} else {
						$goodStr[$z] = preg_replace( "/\s+/", ' ', $goodStr[$z] );
					}
				}
				$content[$x] = implode('',$goodStr);
			}
		}
		$goodStr = implode('',$content);
		//remove all options from select inputs
		$goodStr = preg_replace( "/<option[^>]*>[^<]*/i", '', $goodStr );

		//replace all tags with their text equivalents
		$goodStr = preg_replace( "/<(\/title|hr)[^>]*>/i", "\n --------------------\n", $goodStr );
		$goodStr = preg_replace( "/<(h|div|p)[^>]*>/i", "\n\n", $goodStr );
		$goodStr = preg_replace( "/<sup[^>]*>/i", '^', $goodStr );
		$goodStr = preg_replace( "/<(ul|ol|br|dl|dt|table|caption|\/textarea|tr[^>]*>\s*<(td|th))[^>]*>/i", "\n", $goodStr );
		$goodStr = preg_replace( "/<li[^>]*>/i", "\n· ", $goodStr );
		$goodStr = preg_replace( "/<dd[^>]*>/i", "\n\t", $goodStr );
		$goodStr = preg_replace( "/<(th|td)[^>]*>/i", "\t", $goodStr );
		$goodStr = preg_replace( "/<a[^>]* href=(\"((?!\"|#|javascript:)[^\"#]*)(\"|#)|'((?!'|#|javascript:)[^'#]*)('|#)|((?!'|\"|>|#|javascript:)[^#\"'> ]*))[^>]*>/i", "", $goodStr );
		$goodStr = preg_replace( "/<img[^>]* alt=(\"([^\"]+)\"|'([^']+)'|([^\"'> ]+))[^>]*>/i", "[IMAGE: $2$3$4] ", $goodStr );
		$goodStr = preg_replace( "/<form[^>]* action=(\"([^\"]+)\"|'([^']+)'|([^\"'> ]+))[^>]*>/i", "\n[FORM: $2$3$4] ", $goodStr );
		$goodStr = preg_replace( "/<(input|textarea|button|select)[^>]*>/i", "[INPUT] ", $goodStr );
		//strip all remaining tags (mostly closing tags)
		$goodStr = strip_tags( $goodStr );
		//convert HTML entities
		$goodStr = strtr( $goodStr, array_flip( get_html_translation_table( HTML_ENTITIES ) ) );
		$goodStr = preg_replace( "/&#(\d+);/me", "chr('$1')", $goodStr );
		//make sure there are no more than 3 linebreaks in a row and trim whitespace
		$content =  preg_replace( "/^\n*|\n*$/", '', preg_replace( "/[ \t]+(\n|$)/", "$1", preg_replace( "/\n(\s*\n){2}/", "\n\n\n", preg_replace( "/\r\n?|\f/", "\n", str_replace( chr(160), ' ', $goodStr ) ) ) ) );


		$this->blnHasModified = true;

		$objContent->SetData($content);
	}

}









