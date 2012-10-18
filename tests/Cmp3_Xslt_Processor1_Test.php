<?php


namespace Cmp3\Xslt;



class Processor1_Test extends \TestCaseBase {


	/**
	 *
	 */
	public function testOne()
	{
		$xmlFilepath = PATH_fixture . 'person.xml';
		$xslFilepath = PATH_fixture . 'person.xsl';

		$xml = file_get_contents($xmlFilepath);

		$objProcessor = new Processor1();
		$strContent = $objProcessor->Process($xml, $xslFilepath);


		$this->assertContains('<Person>', $strContent);
		$this->assertContains('<Gender>', $strContent);
		$this->assertContains('<FirstName>Yosi</FirstName>', $strContent);
	}


}
