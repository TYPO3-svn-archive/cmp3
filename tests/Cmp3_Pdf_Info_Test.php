<?php

namespace Cmp3\Pdf;

class Info_Test extends \TestCaseBase {


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
	public function testInfo1()
	{
		$objInfo = new Info;

		$objFile = $this->objFileArray[0];
		$objInfoData = $objInfo->Get($objFile);

		$this->assertTrue($objInfoData instanceof \Cmp3\Pdf\InfoData);

		$this->assertFalse($objInfoData->Optimized);

		$this->assertEquals($objInfoData->Version, '1.4');

		$this->assertEquals($objInfoData->PageFormat, 'A4');

		$this->assertEquals($objInfoData->Pages, 1);

	}


}
