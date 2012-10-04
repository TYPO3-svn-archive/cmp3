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
 * Content processors which perform  str_replace and preg_replace.
 * Configuration is done with TypoScript style setup.
 *
 * STATUS: beta
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class Replace extends ProcessorAbstract {



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


			if ($this->objLogger) $this->objLogger->LogData('Replace content before:', (substr($content,0,500) . "\n\n ... \n\n" . substr($content,-500)), \Cmp3\Log\Logger::DEBUG);

			if ($arrProperties = $this->objConfig->GetProperties('str_replace')) {

				if ($this->objLogger) $this->objLogger->LogData('Replace setup:', $arrProperties, \Cmp3\Log\Logger::DEBUG);

				foreach ($arrProperties as $searchReplace) {
					if ($searchReplace['search']) {
						if ($this->config['str_replace_expand']) {
							$content = str_replace($this->stringExpandDangerous($searchReplace['search']), $this->stringExpandDangerous($searchReplace['replace']), $content);
						} else {
							$content = str_replace($searchReplace['search'], $searchReplace['replace'], $content);
						}
					}
				}
				$this->blnHasModified = true;
			}

			if ($arrProperties = $this->objConfig->GetProperties('preg_replace')) {

				if ($this->objLogger) $this->objLogger->LogData('PregReplace setup:', $arrProperties, \Cmp3\Log\Logger::DEBUG);

				foreach ($arrProperties as $searchReplace) {
					if ($searchReplace['search']) {
						if ($this->config['preg_replace_expand_replace']) {
							$strReplace = $this->stringExpandDangerous($searchReplace['replace']);
						} else {
							$strReplace = $searchReplace['replace'];
						}
						$contentRet = preg_replace($searchReplace['search'], $strReplace, $content);
						if ($contentRet!==null) {
							$content = $contentRet;
							$this->blnHasModified = true;
						}
					}
				}
			}

			if ($this->objLogger) $this->objLogger->LogData('htmlProcessing content after:', (substr($content,0,500) . "\n\n ... \n\n" . substr($content,-500)), \Cmp3\Log\Logger::DEBUG);

			if ($this->blnHasModified) {
				$objContent->SetData($content);
			}
		}

	}


	/**
	 *
	 * @param string $subject
	 * @throws Exception
	 */
	protected function stringExpandDangerous($subject) {

		$delim = '__ASDFZXCV1324ZXCV__';  // button mashing...

		// built the eval code
		$statement = "return <<<$delim\n" . $subject . "\n$delim;\n";

		// execute statement, saving output to $result variable
		$result = eval($statement);

		// if eval() returned FALSE, throw a custom exception
		if ($result === false)
			throw new Exception($statement);

		// return variable expanded string
		return $result;
	}

}









