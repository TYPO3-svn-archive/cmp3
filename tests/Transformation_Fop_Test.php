<?php


namespace Cmp3\Transformation;



class Transformation_Fop_Test extends \TestCaseBase {


	/**
	 *
	 */
	public function testTwo()
	{
		$strTargetFileName = PATH_output . \helper::cleanFilename(__METHOD__) . '.pdf';
		@unlink($strTargetFileName);

		$xmlFilepath = PATH_fixture . 'rich-long.xml';
		$xslFilepath = PATH_fixture . 'rich_to_fo.xsl';

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objContent = new \Cmp3\Content\Content($objProperties);
		$objContent->SetData(file_get_contents($xmlFilepath), \Cmp3\Content\ContentType::XML);

		// fetch one record
		$configArray = array(
				'stylesheet' => $xslFilepath,
		);
		$objConfig = new \Cmp3\Config\TypoScriptArray($configArray);


		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objProperties['Config'] = $objConfig;
		$objContentProcessor = new Fop($objProperties);
		$objContentProcessor->Process($objContent);

		$this->assertEquals($objContent->Type, \Cmp3\Content\ContentType::PDF);

		$objFile = $objContent->File->Copy($strTargetFileName);
		$objFile->SetDeleteOnDestruct(false);

		$this->assertTrue($objFile->Exists());

		$this->assertNotContains('<body', $objFile->ReadContent());
	}


}
