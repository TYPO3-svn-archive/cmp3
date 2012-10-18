<?php


namespace Cmp3\Data;


class Row_Test extends \TestCaseBase {


	/**
	 * 
	 */
	public function testSimple()
	{
		$filepath = PATH_fixture . 'tt_content_text_simple.xml';
		$mixFieldsArray = \helper::xmlstring2array(file_get_contents($filepath));


		$objDataRow = new Row('tt_content', $mixFieldsArray);

		$this->assertEquals($objDataRow->TableName,    'tt_content');
		$this->assertEquals($objDataRow->DataArray['uid'], 8);
		$this->assertEquals($objDataRow->uid, 8);
		$this->assertArrayHasKey('bodytext', $objDataRow->DataArray);
		$this->assertTrue((false!==array_search('bodytext', $objDataRow->DataFields)));
	}



}
