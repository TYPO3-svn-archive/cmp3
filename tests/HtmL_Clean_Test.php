<?php


namespace Cmp3;



class HtmL_Clean_Test extends \TestCaseBase {


	/**
	 *
	 */
	public function testWord()
	{
		$htmlFilepath = PATH_fixture . 'WordPasteGarbage.html';

		$html = file_get_contents($htmlFilepath);

		$strContent = Xml\Tools::CleanHtml($html);


		$strDestinationPath = PATH_output . \helper::cleanFilename(__METHOD__) . '.html';
		file_put_contents($strDestinationPath, $strContent);

		$this->assertNotContains('mso-', $strContent);
		$this->assertNotContains('tab-stops', $strContent);
		$this->assertNotContains('style="font: 7pt&amp;quot;"', $strContent);
		# $this->assertContains('lang="', $strContent);
	}


}
