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
 * Content processors which removes bb code
 *
 * STATUS: beta
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class StripBBCode extends ProcessorAbstract {


	/**
	 * Processes a content object
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface $objContent)
	{
		if ($content = $objContent->GetData()) {

			# Formatierungen
			$content = preg_replace('#\[b\](.*)\[/b\]#isU', "$1", $content);
			$content = preg_replace('#\[i\](.*)\[/i\]#isU', "$1", $content);
			$content = preg_replace('#\[u\](.*)\[/u\]#isU', "$1", $content);
			$content = preg_replace('#\[color=(.*)\](.*)\[/color\]#isU', "$2", $content);
			$content = preg_replace('#\[size=(8|10|12)\](.*)\[/size\]#isU', "$2", $content);

			# Links
			$content = preg_replace('#\[url\](.*)\[/url\]#isU', "$1", $content);
			$content = preg_replace('#\[url=(.*)\](.*)\[/url\]#isU', "$2", $content);

			# Grafiken
			$content = preg_replace('#\[img\](.*)\[/img\]#isU', "", $content);

			# Zitate
			$content = preg_replace('#\[quote\](.*)\[/quote\]#isU', "$1", $content);

			# Quelltext
			$content = preg_replace('#\[code\](.*)\[/code\]#isU', "$1", $content);

			# Listen
			$content = preg_replace('#\[list\](.*)\[/list\]#isU', "$1", $content);
			$content = preg_replace('#\[list=(1|a)\](.*)\[/list\]#isU', "$2", $content);
			$content = preg_replace("#^\[\*\](.*)$#mU", "$1>", $content);

			# email
			$content = preg_replace("#\[email=(.*)\](.*)\[\/email\]#Usi", "\\2", $content);

			$this->blnHasModified = true;

			$objContent->SetData($content);
		}
	}

}









