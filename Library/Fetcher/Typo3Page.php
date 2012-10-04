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
 * @subpackage Files
 * @package    CMP3
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */




namespace Cmp3\Fetcher;



/**
 * {@inheritdoc}
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Fetcher
 * @package    CMP3
 */
class Typo3Page extends Url { #Fetcher_Abstract {


	// @todo we're not necessarily render HTML
	protected $strContentType = \Cmp3\Content\ContentType::HTML;



	/**
	 * {@inheritdoc}
	 */
	public function GetContent()
	{
		if (!$this->objConfig->id) {
			throw new \Cmp3\UndefinedConfigurationException('id', __CLASS__);
		}

		$this->strBaseUrl = $GLOBALS['TSFE']->baseUrlWrap('');

		$strParameterArray = \Cmp3\Uri\Url::ExplodeParameter($this->objConfig->parameter);
		if ($this->objConfig->type) {
			$strParameterArray['type'] = $this->objConfig->type;
		}

		$this->strUrl = $GLOBALS['TSFE']->cObj->getTypoLink_URL($this->objConfig->id, $strParameterArray);
		$this->strUrl = \Cmp3\Uri\Url::MakeAbsoluteUrl($this->strUrl);

		return parent::GetContent();

	}

}
