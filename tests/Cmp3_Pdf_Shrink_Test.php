<?php

namespace Cmp3\Pdf;

class Shrink_Test extends \TestCaseBase {


	protected $file;
	protected $objFile;


	protected function setUp()
	{
		$this->file = PATH_fixture . 'test-300dpi.pdf';
		$this->objFile= new \Cmp3\Files\File($this->file);
	}


	/**
	 *
	 */
	public function testRenderScreen()
	{
		$objShrink = new Shrink($this->objFile);

		$objFileResult = $objShrink->Render();

		$objFileResult->SetDeleteOnDestruct(false);

		$this->assertTrue($objFileResult instanceof \Cmp3\Files\File);

		$this->assertTrue($objFileResult->Exists());
		$this->assertTrue($objFileResult->Size > 0);
		$this->assertTrue($objFileResult->Size <  $this->objFile->Size);

		$strTargetFileName = PATH_output . \helper::cleanFilename(__METHOD__) . '.pdf';
		$objFileResult->Copy($strTargetFileName);

		#echo __FUNCTION__ . " PDF file: {$objFileResult->AbsolutePath}\n";
	}


	/**
	 *
	 */
	public function testRenderEbook()
	{
		$objShrink = new Shrink($this->objFile);
		$objShrink->SetQuality(Shrink::QualityEbook);

		$objFileResult = $objShrink->Render();

		$objFileResult->SetDeleteOnDestruct(false);

		$this->assertTrue($objFileResult instanceof \Cmp3\Files\File);

		$this->assertTrue($objFileResult->Exists());
		$this->assertTrue($objFileResult->Size > 0);
		$this->assertTrue($objFileResult->Size <  $this->objFile->Size);

		$strTargetFileName = PATH_output . \helper::cleanFilename(__METHOD__) . '.pdf';
		$objFileResult->Copy($strTargetFileName);

		#echo __FUNCTION__ . " PDF file: {$objFileResult->AbsolutePath}\n";
	}


	/**
	 *
	 */
	public function zzz_testRenderEmpty()
	{
		$objFile = null;

		try {
			$objShrink = new Shrink($objFile);
		} catch (\Cmp3\Pdf\Exception $e) {
			$this->assertTrue($e instanceof \Cmp3\Pdf\Exception);
		}
	}


}
