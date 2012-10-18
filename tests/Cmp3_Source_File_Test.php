<?php


namespace Cmp3\Source;


class File_Test extends \TestCaseBase {

	protected $objConfig;


	protected function setUp()
	{
		$filepath = PATH_fixture . 'Config_SourceFile.txt';
		$configArray = \Cmp3\Config\TypoScriptParser::ParseFileAsArray($filepath);
		$this->objConfig = new \Cmp3\Config\TypoScriptArray($configArray);
	}


	/**
	 *
	 */
	public function testText()
	{
		$objSourceConfig = $this->objConfig->GetProxy('source.test1.');
		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objProperties['Config'] = $objSourceConfig;
		$objSource = new Fetcher($objProperties);

		$objContent = $objSource->GetContent();

		$this->assertEquals($objContent->GetData(), 'TEST1');
		$this->assertEquals($objContent->Type, \Cmp3\Content\ContentType::TEXT);



		$objSourceConfig = $this->objConfig->GetProxy('source.test2.');
		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objProperties['Config'] = $objSourceConfig;
		$objSource = new Fetcher($objProperties);

		$objContent = $objSource->GetContent();

		$this->assertEquals($objContent->GetData(), 'TEST2');
		$this->assertEquals($objContent->Type, \Cmp3\Content\ContentType::TEXT);

	}


	/**
	 *
	 */
	public function testHtml()
	{
		$objSourceConfig = $this->objConfig->GetProxy('source.testhtml.');
		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objProperties['Config'] = $objSourceConfig;
		$objSource = new Fetcher($objProperties);

		$objContent = $objSource->GetContent();

		$this->assertTrue((boolean)strpos($objContent->GetData(), '<h1>baseline</h1>'));
		$this->assertEquals($objContent->Type, \Cmp3\Content\ContentType::HTML);
	}


}
