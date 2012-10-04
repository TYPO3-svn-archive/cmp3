<?php


namespace Cmp3\ContentProcessing;



class ContentProcessor_Test extends \TestCaseBase {


	/**
	 *
	 */
	public function testHtmlExtractBody()
	{
		$strType = \Cmp3\Content\ContentType::HTML;

		$filepath = PATH_fixture . 'BaselineTypography.html';

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objContent = new \Cmp3\Content\Content($objProperties);
		$objContent->SetData(file_get_contents($filepath), $strType);

		$objContentProcessor = new HtmlExtractBody();
		$objContentProcessor->Process($objContent);

		$this->assertEquals($objContent->Type,    $strType);

		$this->assertNotContains('<body', $objContent->Data);
		$this->assertNotContains('</body', $objContent->Data);
		$this->assertContains('<div id="page"', $objContent->Data);
		$this->assertContains('</footer', $objContent->Data);
	}


}
