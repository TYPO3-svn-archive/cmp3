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
 * Content processors which performs bb to html code replacement
 *
 * STATUS: beta
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class BBCodeToHtml extends ProcessorAbstract {


	/**
	 * performs bb to html code replacement
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface $objContent)
	{
		if ($content = $objContent->GetData()) {

			if (!is_string($content)) {
				throw new Exception('String expected in ' . __METHOD__);
			}

			# Formatierungen
			$content = preg_replace('#\[b\](.*)\[/b\]#isU', "<b>$1</b>", $content);
			$content = preg_replace('#\[i\](.*)\[/i\]#isU', "<i>$1</i>", $content);
			$content = preg_replace('#\[u\](.*)\[/u\]#isU', "<u>$1</u>", $content);
			$content = preg_replace('#\[color=(.*)\](.*)\[/color\]#isU', "<span style=\"color: $1\">$2</span>", $content);
			$content = preg_replace('#\[size=(8|10|12)\](.*)\[/size\]#isU', "<span style=\"font-size: $1 pt\">$2</span>", $content);

			# Links
			$content = preg_replace('#\[url\](.*)\[/url\]#isU', "<a href=\"$1\">$1</a>", $content);
			$content = preg_replace('#\[url=(.*)\](.*)\[/url\]#isU', "<a href=\"$1\">$2</a>", $content);

			# Grafiken
			$content = preg_replace('#\[img\](.*)\[/img\]#isU', "<img src=\"$1\" alt=\"$1\" />", $content);

			# Zitate
			$content = preg_replace('#\[quote\](.*)\[/quote\]#isU', "<blockquote>$1</blockquote>", $content);

			# Quelltext
			$content = preg_replace('#\[code\](.*)\[/code\]#isU', "<div class=\"code\">$1</div>", $content);

			# Listen
			$content = preg_replace('#\[list\](.*)\[/list\]#isU', "<ul>$1</ul>", $content);
			$content = preg_replace('#\[list=(1|a)\](.*)\[/list\]#isU', "<ol>$2</ol>", $content);
			$content = preg_replace("#^\[\*\](.*)$#mU", "<li>$1</li>", $content);

			# email
			$content = preg_replace("#\[email=(.*)\](.*)\[\/email\]#Usi", "<a href=\"mailto:\\1\">\\2</a>", $content);

			$this->blnHasModified = true;

			$objContent->SetData($content);
		}
	}

}









