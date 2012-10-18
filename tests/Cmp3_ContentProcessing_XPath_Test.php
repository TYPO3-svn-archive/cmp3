<?php


namespace Cmp3\ContentProcessing;



class XPath_Test extends \TestCaseBase {

	protected $objConfig;

	protected $objContent;


	protected function setUp()
	{
		$filepath = PATH_fixture . 'Config_ContentProcessing_XPath.txt';
		$configArray = \Cmp3\Config\TypoScriptParser::ParseFileAsArray($filepath);
		$this->objConfig = new \Cmp3\Config\TypoScriptArray($configArray);

		$filepath = PATH_fixture . 'tt_content_text_simple.xml';
		$objXml = \Cmp3\XML\Tools::MakeXmlDom(file_get_contents($filepath));

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$this->objContent = new \Cmp3\Content\Content($objProperties);
		$this->objContent->SetData($objXml, \Cmp3\Content\ContentType::XML);
	}



	/**
	 *
	 */
	public function testQRCode()
	{
		$filepath = PATH_site . 'typo3temp/pics/0f58813181c28ae7dbdfd8126f61e0bd.png';
		@unlink ($filepath);

		$objConfig = $this->objConfig->GetProxy(__FUNCTION__);
		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objProperties['Config'] = $objConfig;
		$objContentProcessor = new XPath_QRCode($objProperties);
		$objContentProcessor->Process($this->objContent);

		$this->assertNotContains('<url>http://www.google.de</url>', (string)$this->objContent->Data);
		$this->assertContains('<url>' . $filepath . '</url>', (string)$this->objContent->Data);
		$this->assertTrue(file_exists($filepath));
	}


}
