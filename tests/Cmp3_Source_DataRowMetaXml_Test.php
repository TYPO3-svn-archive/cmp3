<?php


namespace Cmp3\Source;


class DataRowMetaXml_Test extends \TestCaseBase {


	/**
	 *
	 */
	public function testDataRowMetaXml()
	{
		// fetch one record
		$configArray = array(
				'table' => 'tt_content',
				'id' => '34,35,36,37',
				);
		$objSourceConfig = new \Cmp3\Config\TypoScriptArray($configArray);


		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objProperties['Config'] = $objSourceConfig;

		$objQuery = new Typo3Query($objProperties);
		$objDataRowMetaArray = $objQuery->QueryArray();

		//------------------------------------------------

		$objConfig = new \Cmp3\Config\ArrayData();

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objProperties['Config'] = $objConfig;
		$objProperties['DataRowMetaArray'] = $objDataRowMetaArray;

		$objDataRowMetaXml = new DataRowMetaXml($objProperties);

		$objContent = $objDataRowMetaXml->GetContent();

		$this->assertEquals($objContent->Type, \Cmp3\Content\ContentType::CMP3XML);

		$strXml = $objContent->GetData()->saveXML();

		$this->assertContains('<cmp3document', $strXml);
		$this->assertContains('<field name="uid" type="int" format="text">', $strXml);
		$this->assertContains('<value>34</value>', $strXml);
	}
}
