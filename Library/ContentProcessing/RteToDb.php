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
 * Content processors which perform RTE HTML to TYPO3 DB content
 *
 * STATUS: alpha
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class RteToDb extends ProcessorAbstract {


	/**
	 * Keyword: "rte" means direction from db to rte, which is to HTML, "db" means direction from Rte to DB
	 * @var string
	 */
	protected $strConversionType = 'db';


	/**
	 * Processes a content object
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent Content to be processed
	 * @return void
	 * @throws Exception
	 */
	public function Process (\Cmp3\Content\ContentInterface $objContent)
	{
		if ($content = $objContent->GetData()) {
#FIXME use objDataRow or config
			$table = $this->objDataRow->TableName;
			$pid = $this->objDataRow->pid;
			$field = $this->config['field'];

		 	// "special" configuration - what is found at position 4 in the types configuration of a field from record, parsed into an array.
			$specConfDummy = array();

			$RTErelPath = 'uploads/rte/';


			if (!$table OR !$pid) {
				throw new Exception ('Table name or pid not set in objDataRow:'.$table.','.$pid);
			}

			require_once(PATH_t3lib.'/class.t3lib_befunc.php');

			// select right config array and merge special field conf
			$tsRteSetupArray = t3lib_BEfunc::RTEsetup($this->objConfig, $table, $field, $RTEtypeValDummy='');


			// Initialize transformation:
			require_once (PATH_t3lib.'class.t3lib_parsehtml_proc.php');
			$parseHTML = \t3lib_div::makeInstance('t3lib_parsehtml_proc');
			$parseHTML->init($table.':'.$field, $pid);
			$parseHTML->setRelPath($RTErelPath);


			// Perform transformation:
			// Keyword: "rte" means direction from db to rte, which is to HTML, "db" means direction from Rte to DB
			$content = $parseHTML->RTE_transform($content, $specConfDummy, $this->strConversionType, $tsRteSetupArray);

			$this->blnHasModified = true;

			$objContent->SetData($content);
		}
	}

}









