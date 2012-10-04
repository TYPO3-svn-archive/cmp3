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
 * content processors which do a marker replacement
 *
 * STATUS: beta
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class Marker extends ProcessorAbstract {



	/**
	 * Processes a content object
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @return void
	 */
	public function Process (\Cmp3\Content\ContentInterface $objContent)
	{
		if ($this->objDataRow AND $content = $objContent->GetData()) {

			if (preg_match_all('/###([^#]+)###/m', $content, $matches))  {

				if ($this->objLogger) $this->objLogger->Debug('Try to replace ' . count($matches[1]) . ' marker');

				foreach ($matches[1] as $field) {

					try {
						$content = str_replace('###'.$field.'###', $this->objDataRow->$field, $content);

						if ($this->objLogger) $this->objLogger->Debug('Replaced marker ###' . $field . '### with: ' . substr($this->objDataRow->$field, 0 , 50));

					} catch ( \Cmp3\UndefinedPropertyException $e ) {
						// field not found for marker
						// we do nothing because marker could be substituted somewhere else
					}
				}

				$this->blnHasModified = true;

				$objContent->SetData($content);
			}
		}
	}

}









