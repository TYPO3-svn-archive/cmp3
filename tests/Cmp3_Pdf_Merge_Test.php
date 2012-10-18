<?php

namespace Cmp3\Pdf;

class Merge_Test extends \TestCaseBase {


	protected $files;
	protected $objFileArray;


	protected function setUp()
	{
		$this->files = array(
			PATH_fixture . 'test-1.pdf',
			PATH_fixture . 'test-2.pdf',
			PATH_fixture . 'test-3.pdf',
		);

		foreach($this->files as $strFile) {
			$this->objFileArray[] = new \Cmp3\Files\File($strFile);
		}
	}


	/**
	 *
	 */
	public function testRenderOne()
	{
		$objFileArray = array($this->objFileArray[0]);
		$objMerge = new Merge($objFileArray);

		$objFile1 = $objMerge->Render();

		$this->assertTrue($objFile1 instanceof \Cmp3\Files\File);

		$objFile2 = $objMerge->GetFile();
		$this->assertTrue($objFile2 instanceof \Cmp3\Files\File);

		$this->assertTrue($objFile1->Exists());
		$this->assertTrue($objFile1->Size > 0);

		$this->assertEquals($objFile1->AbsolutePath, $objFile2->AbsolutePath);

		$this->assertEquals($objFile1->AbsolutePath, $objFileArray[0]->AbsolutePath);

		#echo __FUNCTION__ . " PDF file: {$objFile1->AbsolutePath}\n";
	}


	/**
	 *
	 */
	public function testRenderTwo()
	{
		$objFileArray = array($this->objFileArray[0], $this->objFileArray[1]);
		$this->RenderMultiple($objFileArray, __METHOD__);
	}


	/**
	 *
	 */
	public function testRenderThree()
	{
		$objFileArray = $this->objFileArray;
		$this->RenderMultiple($objFileArray, __METHOD__);
	}


	/**
	 *
	 */
	protected function RenderMultiple($objFileArray, $strName)
	{
		$strTargetFileName = PATH_output . \helper::cleanFilename($strName) . '.pdf';
		@unlink($strTargetFileName);

		$objMerge = new Merge($objFileArray);

		$objFile1 = $objMerge->Render();

		$this->assertTrue($objFile1 instanceof \Cmp3\Files\File);

		$objFile2 = $objMerge->GetFile();
		$this->assertTrue($objFile2 instanceof \Cmp3\Files\File);

		$this->assertEquals($objFile1->AbsolutePath, $objFile2->AbsolutePath);

		$this->assertTrue($objFile1->Exists());
		$this->assertTrue($objFile1->Size > 0);

		$objFile1->SetDeleteOnDestruct(false);
		$objFile1->Rename($strTargetFileName);
		#echo $strName . " PDF file: {$objFile1->AbsolutePath}\n";
	}

	/**
	 *
	 */
	public function testRenderEmpty()
	{
		$objFileArray = array();

		try {
			$objMerge = new Merge($objFileArray);
		} catch (\Cmp3\Pdf\Exception $e) {
			$this->assertTrue($e instanceof \Cmp3\Pdf\Exception);
		}
	}


}
