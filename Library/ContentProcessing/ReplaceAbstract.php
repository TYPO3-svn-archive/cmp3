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
 * Content processors which strip CSS sytles from HTML
 *
 * STATUS: beta
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class ReplaceAbstract extends ProcessorAbstract {

	protected $configArray = array();

	/*
	protected $configArray = array(
			'preg_replace.' => array(
				'stylesheet_link.' => array(
					'search' => '#<link .*rel="stylesheet"[^>]*>#i',
					'replace' => '',
				),
				'stylesheet_inline.' => array(
					'search' => '#<style type="text/css".*?</style>#is',
					'replace' => '',
				)
			)
		);
	*/


	/**
	 * Processes a content object
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface $objContent)
	{
		$this->blnHasModified = false;

		if ($content = $objContent->GetData()) {

			if ($arrProperties = $this->configArray['preg_replace.']) {

				foreach ($arrProperties as $searchReplace) {
					$contentRet = preg_replace($searchReplace['search'], $searchReplace['replace'], $content);
					if ($contentRet!==null) {
						$content = $contentRet;
						$this->blnHasModified = true;
					}
				}
			}

			if ($this->objLogger) $this->objLogger->LogData('htmlProcessing content after:', (substr($content,0,500) . "\n\n ... \n\n" . substr($content,-500)), \Cmp3\Log\Logger::DEBUG);

			if ($this->blnHasModified) {
				$objContent->SetData($content);
			}
		}

	}

}









