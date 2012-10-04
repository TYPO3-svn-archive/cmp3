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
 * @subpackage Content
 * @package    CMP3
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Source;



/**
 * {@inheritdoc}
 *
 * This source handles content retrival from TYPO3
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Source
 * @package    CMP3
 */
class Typo3Xml extends SourceAbstract {



	/**
	 * This is the xml document the source should add nodes to
	 * @var \Cmp3\Xml\DocumentCmp3
	 */
	protected $objDocument;


	/**
	 * Constructor
	 */
	public function Construct()
	{
		$this->objDocument = new \Cmp3\Xml\DocumentCmp3();
	}


	/**
	 * This actually retrieves the content and sets objContent
	 *
	 * @return void
	 */
	protected function FetchContent()
	{
		$strSelectionArray = $this->objConfig->GetProperties('fetcher.selection');

		foreach ($strSelectionArray as $strSelectionName) {
			$this->GetSelection($strSelectionName);
		}

		$objProperties = array();
		$objProperties['Logger'] = $this->objLogger;
		$this->objContent = new \Cmp3\Content\Content($objProperties);
		$this->objContent->SetData($this->objDocument, \Cmp3\Content\ContentType::CMP3XML);
	}


	/**
	 * Query records and generate xml document
	 *
	 * @param string $strSelectionName
	 * @param object $objParentRecord
	 */
	protected function GetSelection($strSelectionName, $objParentRecord = null)
	{
		$objQueryConfig = $this->objConfig->GetProxy('fetcher.queries.' . $strSelectionName . '.');

		$strSubqueriesArray = trim_explode(',', $objQueryConfig->subqueries);

		$strTableName = $objQueryConfig->GetValue('table');

		$strQueryClass = $this->objConfig->GetValue('fetcher', '\\Cmp3\\Source\\Typo3Query');


		$objProperties = array();
		$objProperties['Logger'] = $this->objLogger;
		$objProperties['Config'] = $objQueryConfig;
		$objProperties['ParentRecord'] = $objParentRecord;
		$objQuery = new $strQueryClass($objProperties);
		/* @var  $objQuery Typo3Query */
		$objDataRowArray = $objQuery->QueryArray();


		if ($objDataRowArray) {
			if ($strTableName == 'pages') {
				$strNodeType = \Cmp3\Xml\DocumentNodeType::PAGE;
				$strNodeSubtype = \Cmp3\Xml\DocumentNodeSubtype::NONE;
			} else {
				$strNodeType = \Cmp3\Xml\DocumentNodeType::GROUP;
				$strNodeSubtype = \Cmp3\Xml\DocumentNodeSubtype::RECORDS;
			}

			#TODO here is the place where plugin records would be resolved to render product or news records

			// add <node type="page" subtype="none">
			// or <node type="group" subtype="records">

			$this->objDocument->AddNode($strNodeType, $strNodeSubtype);
		}


		/* @var $objDataRowMeta \Cmp3\Data\MetaTypo3 */
		foreach ($objDataRowArray as $objDataRowMeta) {

			# TODO check for insert records CE type
			# see getShortcutUids
			# in general it would be possible to fetch tt_news or any other record type instead of tt_content

			/*
			if ($objDataRowMeta->TableName =='tt_content'
					AND $objDataRowMeta->DataRow->CType =='shortcut'
					AND ($uidList = $this->getShortcutUids($objDataRowMeta->DataRow->records))) {

				$objShortcutDataRowArray = array();
				foreach ($uidList as $shortcutUid) {

					try {

						// FIXME we need to use Typo3Query so we get right record object back

						$record = \Next\DataQuery_ttcontent::LoadByUid($shortcutUid);

						if ($this->languageID AND $this->languageID != $record->sys_language_uid) {
							$record = \Next\DataQuery_ttcontent_l10n_rev::LoadByUid($shortcutUid);
						}

						$objShortcutDataRowArray[] = $record;

					} catch (Exception $objExc) {
						// the record might be deleted so we ignore this
						continue;
					}
				}
				foreach ($objShortcutDataRowArray as $objShortcutDataRowMeta) {
					$objRecordRenderXml = new \Cmp3\Xml\RecordRender($objShortcutDataRowMeta);
					$this->objDocument->AddRecord($objRecordRenderXml->GetXml());
				}
				continue;
			}
			*/

			$objRecordRenderXml = new \Cmp3\Xml\RecordRender($objDataRowMeta);
			$this->objDocument->AddRecord($objRecordRenderXml->GetXml());


			foreach ($strSubqueriesArray as $strSubquery) {
				// perform subquery
				$this->GetSelection($strSubquery, $objDataRowMeta);
			}
		}
	}






	/**************************
	 *
	 * Tools
	 *
	 **************************/


	/**
	 * Explodes the item list and returns a comma list for tt_content records
	 *
	 * @param	string		Item list
	 * @return	void
	 * @todo use next function for this?
	 */
	function getShortcutUids($itemlist)
	{
		$uidList = false;

		if ((string)trim($itemlist) != '')	{
			$tempItemArray = trim_explode(',', $itemlist);
			foreach($tempItemArray as $key => $val)	{

					// Extract table name and id. This is un the formular [tablename]_[id] where table name MIGHT contain "_", hence the reversion of the string!
				$val = strrev($val);
				$parts = explode('_',$val,2);
				$theID = strrev($parts[0]);

					// Check that the id IS an integer:
				if (t3lib_div::testInt($theID))	{
						// Get the table name: If a part of the exploded string, use that. Otherwise if the id number is LESS than zero, use the second table, otherwise the first table
					$theTable = trim($parts[1]) ? strrev(trim($parts[1])) : '';
						// If the ID is not blank and the table name is among the names in the inputted tableList, then proceed:
					if ((string)$theID!='' && $theID && $theTable == 'tt_content')	{
							// Get ID as the right value:
						$theID = intval($theID);
							// Register ID/table name in internal arrays:
						$uidList[] = $theID;
					}
				}
			}
		}
		return $uidList;
	}

}



