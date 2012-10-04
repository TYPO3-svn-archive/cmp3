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
 * @subpackage Transformation
 * @package    CMP3
 * @copyright  Copyright (c) 2012 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Transformation;

#FIXME fix or remove

/**
 *
 *
 * STATUS: beta
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Transformation
 * @package    CMP3
 *
 */
class Xslt1 extends TransformerAbstract {


	/**
	 * Processes the transformation
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface &$objContent)
	{
		$strContent = $objContent->GetData();

		if (!$this->objConfig->hasProperty('chain.')) {
			throw new Excpetion(__METHOD__ . " Configuration has no 'chain.'");
		}

		$objConfigIterator = new \Cmp3\Config\ConfigIterator ($this->objConfig, 'chain.');
		foreach($objConfigIterator as $key => $objConfig) {

			$xslFileConfig = $objConfig->GetValue('stylesheet');

			if (!$xslFileConfig) {
				throw new Exception(__METHOD__ . " stylesheet not defined");
			}

			$xslFilepath = \Cmp3\System\Env::ResolvePath($xslFileConfig);
			if (!file_exists($xslFilepath)) {
				throw new Exception(__CLASS__ . " Couldn't find style sheet: $xslFilepath ($xslFileConfig)");
			}

			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Transform content with stylesheet: ' . $xslFilepath);

			$objProcessor = new \Cmp3\Xslt\Processor1();
			$strContent = $objProcessor->Process($strContent, $xslFilepath);


			$objContent->SetData($strContent);

		}
	}

}









