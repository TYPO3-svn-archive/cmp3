<?php


namespace Cmp3\Source;


class Content_Test extends \TestCaseBase {


	/**
	 *
	 */
	public function testText()
	{
		$strType = \Cmp3\Content\ContentType::TEXT;
		$strContent = 'Schubidu';

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objContent = new \Cmp3\Content\Content($objProperties);
		$objContent->SetData($strContent, $strType);

		$objSourceConfig = new \Cmp3\Config\ArrayData();

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objProperties['Config'] = $objSourceConfig;
		$objProperties['Content'] = $objContent;
		$objSource = new \Cmp3\Source\Content($objProperties);

		$objContent = $objSource->GetContent();

		$this->assertEquals($objContent->Type,    $strType);
		$this->assertEquals($objContent->Data, $strContent);
		$this->assertEquals($objContent->GetData(), $strContent);
	}



}
