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
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\ContentProcessing;


/**
 * Content processors which adds css styles to html header
 *
 * STATUS: beta
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class HtmlAddStyles extends ProcessorAbstract {


	/**
	 * adds css styles to html header
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface $objContent)
	{
		if ($content = $objContent->GetData()) {

			$strInsert1 = '';
			if ($arrProperties = $this->objConfig->GetProperties('stylesheets.')) {
				foreach ($arrProperties as $strFilePath) {

					$urlArray = parse_url($strFilePath);

					if ($urlArray['scheme']==='EXT') {
						try {
							$strFilePath = \tx_cmp3::ResolvePath($strFilePath);
						} catch (\Exception $e) {
						}
						$strFilePath = 'file://'.\Cmp3\System\Files::MakeFilePathAbsolute($strFilePath);

					} elseif (!$urlArray['scheme']) {
						$strFilePath = 'file://'.\Cmp3\System\Files::MakeFilePathAbsolute($strFilePath);
					}
					$strInsert1 .= "\n" . '<link rel="stylesheet" media="all" href="' . htmlspecialchars($strFilePath) . '" />';
				}
			}

			$strInsert2 = '';
			if ($arrProperties = $this->objConfig->GetProperties('inline.')) {
				foreach ($arrProperties as $strCss) {
					$strInsert2 .= "\n" . $strCss;
				}
				$strInsert2 = '
				<style type="text/css" media="all">
				' . $strInsert2 . '
				</style>';

			}

			$search  = '#</head>#i';
			$replace = '
'. $strInsert1 .'
'. $strInsert2 .'
</head>';

			$content = preg_replace($search, $replace, $content);

			$this->blnHasModified = true;

			$objContent->SetData($content);
		}
	}

}









