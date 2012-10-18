<?php


namespace Cmp3\Xslt;



class Processor2_Test extends \TestCaseBase {


	/**
	 * 
	 */
	public function testOne()
	{
		$xmlFilepath = PATH_fixture . 'person.xml';
		$xslFilepath = PATH_fixture . 'person.xsl';

		$xml = file_get_contents($xmlFilepath);
#FIXME this shouldn't be needed - see XML_XSLT2Processor using curwd()
		chdir(PATH_output);

		$objProcessor = new Processor2();
		$strContent = $objProcessor->Process($xml, $xslFilepath);

		$this->assertContains('<Person>', $strContent);
		$this->assertContains('<Gender>', $strContent);
		$this->assertContains('<FirstName>Yosi</FirstName>', $strContent);
	}


}
