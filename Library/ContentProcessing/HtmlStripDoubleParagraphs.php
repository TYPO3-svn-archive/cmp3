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
 * Content processors which change multiple <p> to a single <p> in HTML
 *
 * STATUS: beta
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class HtmlStripDoubleParagraphs extends ReplaceAbstract {

	
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
	
	
	/**
	 * The smlies images will be initialized once only.
	 * Running
	 *
	 * @param \Cmp3\Config\ConfigInterface $objConfig
	 * @return void
	 */
	public function __construct (\Cmp3\Config\ConfigInterface $objConfig)
	{
		$configArray = array();
		$configArray['str_replace.']['double_p_begin1']['search'] = '<p class="bodytext"><p class="bodytext">';
		$configArray['str_replace.']['double_p_begin1']['replace'] = '<p class="bodytext">';
		$configArray['str_replace.']['double_p_begin2']['search'] = '<p class="bodytext"><p>';
		$configArray['str_replace.']['double_p_begin2']['replace'] = '<p class="bodytext">';
		$configArray['str_replace.']['double_p_end']['search'] = '</p></p>';
		$configArray['str_replace.']['double_p_end']['replace'] = '</p>';

		$objConfig = new \Cmp3\Config\TypoScriptArray($configArray);

		parent::__construct ($objConfig);
	}

}









