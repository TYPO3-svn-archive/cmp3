<?php


namespace Cmp3\Source;

class ContentBox_Test extends \TestCaseBase {

	/**
	 *
	 */
	public function testRender01()
	{
        $configArray = array();
		require (PATH_fixture . 'Config_Source_ContentBox.php');
		$objConfig = new \Cmp3\Config\ArrayData($configArray);

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objProperties['Config'] = $objConfig;
		$objPage = new \Cmp3\Source\ContentBox($objProperties);

		$this->Render($objPage, $objConfig, __METHOD__);
	}


	/**
	 *
	 */
	public function testRender02()
	{
		require (PATH_fixture . 'Config_Source_ContentBox.php');
		$configArray['background'] = PATH_fixture . 'test-3.pdf';
		$objConfig = new \Cmp3\Config\ArrayData($configArray);

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objProperties['Config'] = $objConfig;
		$objPage = new \Cmp3\Source\ContentBox($objProperties);

		$this->Render($objPage, $objConfig, __METHOD__);
	}


	/**
	 * @param \Cmp3\Source\ContentBox $objPage
	 */
	protected function Render($objPage, $objConfig, $strName)
	{
		$strTargetFileName = PATH_output . \helper::cleanFilename($strName) . '.pdf';
		@unlink($strTargetFileName);

		$objFile = $objPage->GetContent()->GetDataFile();

		$this->assertTrue($objFile instanceof \Cmp3\Files\File);

		$this->assertTrue($objFile->Exists());
		$this->assertTrue($objFile->Size > 0);

		$objFile->SetDeleteOnDestruct(false);
		$objFile->Rename($strTargetFileName);
	}

}
