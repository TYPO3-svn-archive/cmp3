<?php


namespace Cmp3\Xslt;



class RichToFo_Test extends \TestCaseBase {


	/**
	 *
	 */
	public function testOne()
	{
		$xmlFilepath = PATH_fixture . 'rich.xml';
		$xslFilepath = PATH_fixture . 'rich_to_fo.xsl';
		#$xslFilepath = PATH_cmp3 . 'xsl/rich_to_fo/rich_to_fo.xsl';

		$xml = file_get_contents($xmlFilepath);

		$objProcessor = new Processor1();
		$strContent = $objProcessor->Process($xml, $xslFilepath);


		$strDestinationPath = PATH_output . \helper::cleanFilename(__METHOD__) . '.xml';
		file_put_contents($strDestinationPath, $strContent);

		$this->assertContains('<fo:layout-master-set>', $strContent);
		$this->assertContains('role="rich:p">Sed scelerisque', $strContent);
		$this->assertContains('</fo:root>', $strContent);
	}


	/**
	 *
	 */
	public function testTwo()
	{
		$xmlFilepath = PATH_fixture . 'rich-long.xml';
		$xslFilepath = PATH_fixture . 'rich_to_fo.xsl';

		$xml = file_get_contents($xmlFilepath);

		$objProcessor = new Processor1();
		$strContent = $objProcessor->Process($xml, $xslFilepath);


		$strDestinationPath = PATH_output . \helper::cleanFilename(__METHOD__) . '.xml';
		file_put_contents($strDestinationPath, $strContent);

		$this->assertContains('<fo:layout-master-set>', $strContent);
		$this->assertContains('role="rich:b">offset text typically styled in bold</fo:inline>', $strContent);
		$this->assertContains('</fo:root>', $strContent);
	}


}
