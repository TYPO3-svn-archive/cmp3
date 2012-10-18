<?php

namespace Cmp3\Pdf;

class Impose_Test extends \TestCaseBase {



	/**
	 *
	 */
	public function testRender3Pages()
	{

		$strTargetFileName = PATH_output . \helper::cleanFilename(__METHOD__) . '.pdf';
		@unlink($strTargetFileName);

		$objFile = new \Cmp3\Files\File(PATH_fixture . 'test-3-pages.pdf');


		$objOverlay = new Impose($objFile);
		$objOverlay->SetPlan(PATH_fixture . 'make_double_page_lua.plan', true);

		$objFile1 = $objOverlay->Render();

		$objFile1->SetDeleteOnDestruct(false);

		$this->assertTrue($objFile1 instanceof \Cmp3\Files\File);

		$objFile2 = $objOverlay->GetFile();
		$this->assertTrue($objFile2 instanceof \Cmp3\Files\File);

		$this->assertEquals($objFile1->AbsolutePath, $objFile2->AbsolutePath);

		$this->assertTrue($objFile1->Exists());
		$this->assertTrue($objFile1->Size > 0);

		$objFile1->Rename($strTargetFileName);

	}



}
