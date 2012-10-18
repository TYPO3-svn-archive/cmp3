<?php


namespace Cmp3;

class Converter_Test extends \TestCaseBase {

	protected $objConfig;


	protected function setUp()
	{
		$filepath = PATH_fixture . 'Config_Converter.txt';
		$configArray = \Cmp3\Config\TypoScriptParser::ParseFileAsArray($filepath);
		$this->objConfig = new \Cmp3\Config\TypoScriptArray($configArray);
	}

	/**
	 *
	 */
	public function testMpdf()
	{
		$this->Render('\\Cmp3\\Converter\\Mpdf', __METHOD__);
	}

	/**
	 *
	 */
	public function testWebkit()
	{
		$this->Render('\\Cmp3\\Converter\\Webkit', __METHOD__);
	}


	/**
	 *
	 */
	protected function Render($strConverterClass, $strName, $strConfigPath='converter.test1')
	{
		$strTargetFileName = PATH_output . \helper::cleanFilename($strName) . '.pdf';
		@unlink($strTargetFileName);

		$strType = \Cmp3\Content\ContentType::HTML;
		$filepath = PATH_fixture . 'BaselineTypography.html';

		$objContentFile = new \Cmp3\Files\File($filepath);

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objContent = new \Cmp3\Content\Content($objProperties);
		$objContent->SetData($objContentFile, $strType);


		// now we create the PDF which also fetches and processes the data if needed
		$objPDF = new $strConverterClass($this->objConfig->GetProxy($strConfigPath));
        /* @var $objPDF \Cmp3\Converter\ConverterInterface */
		$objPDF->Process($objContent);


		$this->assertEquals($objContent->Type, \Cmp3\Content\ContentType::PDF);

		$objFile = $objContent->File->Copy($strTargetFileName);
		$objFile->SetDeleteOnDestruct(false);

		$this->assertTrue($objFile->Exists());
		$this->assertNotContains('<body', $objFile->ReadContent());
	}

}
