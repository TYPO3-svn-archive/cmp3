<?php


namespace Cmp3\Source;


class FileProcessing_Test extends \TestCaseBase {

	protected $objConfig;


	protected function setUp()
	{
		$filepath = PATH_fixture . 'Config_SourceFileProcessing.txt';
		$configArray = \Cmp3\Config\TypoScriptParser::ParseFileAsArray($filepath);
		$this->objConfig = new \Cmp3\Config\TypoScriptArray($configArray);
	}


	/**
	 *
	 */
	public function testHtmlProcessing()
	{
		$objSourceConfig = $this->objConfig->GetProxy('source.' . __FUNCTION__);
		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objProperties['Config'] = $objSourceConfig;
		$objSource = new Fetcher($objProperties);

		$objContent = $objSource->GetContent();

		file_put_contents(PATH_output . \helper::cleanFilename(__METHOD__) . '.html', $objContent->GetData());

		$this->assertEquals($objContent->Type, \Cmp3\Content\ContentType::HTML);

		$this->assertContains('<h1>baseline</h1>', $objContent->GetData());
		$this->assertNotContains('<body', $objContent->GetData());
	}



	/**
	 *TODO
	 */
	public function TODO_testHTML2Docbook()
	{
		$objSourceConfig = $this->objConfig->GetProxy('source.testHtmlTransformation.');
		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objProperties['Config'] = $objSourceConfig;
		$objSource = new Fetcher($objProperties);

		$objContent = $objSource->GetContent();

		file_put_contents(PATH_output . \helper::cleanFilename(__METHOD__) . '.xml', $objContent->GetData());

		$this->assertEquals($objContent->Type, \Cmp3\Content\ContentType::DOCBOOK);

		$this->assertContains('<h1>baseline</h1>', $objContent->GetData());
		$this->assertNotContains('<article', $objContent->GetData());
	}

}
