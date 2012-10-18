<?php


namespace Cmp3\ContentProcessing;



class Function_Test extends \TestCaseBase {

	protected $objConfig;


	protected function setUp()
	{
		$filepath = PATH_fixture . 'Config_ContentProcessing.txt';
		$configArray = \Cmp3\Config\TypoScriptParser::ParseFileAsArray($filepath);
		$this->objConfig = new \Cmp3\Config\TypoScriptArray($configArray);
	}


	/**
	 *
	 */
	public function testHtmlProcessing()
	{
		$strType = \Cmp3\Content\ContentType::HTML;

		$filepath = PATH_fixture . 'BaselineTypography.html';

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objContent = new \Cmp3\Content\Content($objProperties);
		$objContent->SetData(file_get_contents($filepath), $strType);

		$objConfig = $this->objConfig->GetProxy(__FUNCTION__ . '.processing');

		$objContentProcessing = ContentProcessing::Factory($objConfig);
		$objContentProcessing->Process($objContent);

		$this->assertEquals($objContent->Type, $strType);

		$this->assertNotContains('<body', $objContent->Data);
		$this->assertNotContains('</body', $objContent->Data);
		$this->assertContains('<div id="page"', $objContent->Data);
		$this->assertContains('</footer', $objContent->Data);
	}


}
