<?php


namespace Cmp3\Source;


class Typo3_Test extends \TestCaseBase {

	protected $objConfig;


	protected function setUp()
	{
		$filepath = PATH_fixture . 'Config_SourceTypo3.txt';
		$configArray = \Cmp3\Config\TypoScriptParser::ParseFileAsArray($filepath);
		$this->objConfig = new \Cmp3\Config\TypoScriptArray($configArray);
	}


	/**
	 *
	 */
	public function testNews()
	{
		$objSourceConfig = $this->objConfig->GetProxy('source.' . __FUNCTION__);
		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objProperties['Config'] = $objSourceConfig;
		$objSource = new Typo3Xml($objProperties);

		$objContent = $objSource->GetContent();

		$this->assertEquals($objContent->Type, \Cmp3\Content\ContentType::CMP3XML);

		$strContent = $objContent->GetData()->saveXML();
		$strDestinationPath = PATH_output . \helper::cleanFilename(__METHOD__) . '.xml';
		file_put_contents($strDestinationPath, $strContent);

		$this->assertContains('<cmp3document', $strContent);

		$this->assertContains('format="typo3_rte"', $strContent);
	}


	/**
	 *
	 */
	public function testNewsProcessed()
	{
		$objSourceConfig = $this->objConfig->GetProxy('source.' . __FUNCTION__);
		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objProperties['Config'] = $objSourceConfig;
		$objSource = new Typo3Xml($objProperties);

		$objContent = $objSource->GetContent();

		$this->assertEquals($objContent->Type, \Cmp3\Content\ContentType::CMP3XML);

		$strContent = $objContent->GetData()->saveXML();
		$strDestinationPath = PATH_output . \helper::cleanFilename(__METHOD__) . '.xml';
		file_put_contents($strDestinationPath, $strContent);

		$this->assertContains('<cmp3document', $strContent);

		$this->assertNotContains('format="typo3_rte"', $strContent);
		$this->assertContains('format="rich"', $strContent);
	}



	/**
	 *
	 */
	public function testContent()
	{
		$objSourceConfig = $this->objConfig->GetProxy('source.' . __FUNCTION__);
		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objProperties['Config'] = $objSourceConfig;
		$objSource = new Typo3Xml($objProperties);

		$objContent = $objSource->GetContent();

		$this->assertEquals($objContent->Type, \Cmp3\Content\ContentType::CMP3XML);

		$strContent = $objContent->GetData()->saveXML();
		$strDestinationPath = PATH_output . \helper::cleanFilename(__METHOD__) . '.xml';
		file_put_contents($strDestinationPath, $strContent);

		$this->assertContains('<cmp3document', $strContent);

		$this->assertNotContains('format="typo3_rte"', $strContent);
	}


}
