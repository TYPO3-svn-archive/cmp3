<?php



class Pdf_Overlay_Test extends TestCaseBase {


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
	public function testRenderOneBottomTop()
	{
		$objFileArray = array($this->objFileArray[0]);
		$objOverlay = new \Cmp3\Pdf\Overlay($objFileArray);

		$objFile1 = $objOverlay->Render();

		$objFile1->SetDeleteOnDestruct(false);

		$this->assertTrue($objFile1 instanceof \Cmp3\Files\File);

		$objFile2 = $objOverlay->GetFile();
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
	public function testRenderTwoBottomTop()
	{
		$objFileArray = array($this->objFileArray[0], $this->objFileArray[1]);
		$this->RenderMultiple($objFileArray, \Cmp3\Pdf\Overlay::FIRST_IS_BOTTOM, __METHOD__);
	}


	/**
	 *
	 */
	public function testRenderThreeBottomTop()
	{
		$objFileArray = $this->objFileArray;
		$this->RenderMultiple($objFileArray, \Cmp3\Pdf\Overlay::FIRST_IS_BOTTOM, __METHOD__);
	}


	/**
	 *
	 */
	public function testRenderOneTopBottom()
	{
		$objFileArray = array($this->objFileArray[0]);
		$objOverlay = new \Cmp3\Pdf\Overlay($objFileArray);

		$objFile1 = $objOverlay->Render(\Cmp3\Pdf\Overlay::FIRST_IS_TOP);

		$objFile1->SetDeleteOnDestruct(false);

		$this->assertTrue($objFile1 instanceof \Cmp3\Files\File);

		$objFile2 = $objOverlay->GetFile();
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
	public function testRenderTwoTopBottom()
	{
		$objFileArray = array($this->objFileArray[0], $this->objFileArray[1]);
		$this->RenderMultiple($objFileArray, \Cmp3\Pdf\Overlay::FIRST_IS_TOP, __METHOD__);
	}


	/**
	 *
	 */
	public function testRenderThreeTopBottom()
	{
		$objFileArray = $this->objFileArray;
		$this->RenderMultiple($objFileArray, \Cmp3\Pdf\Overlay::FIRST_IS_TOP, __METHOD__);
	}


	/**
	 *
	 */
	protected function RenderMultiple($objFileArray, $Mode, $strName)
	{
		$strTargetFileName = PATH_output . \helper::cleanFilename($strName) . '.pdf';
		@unlink($strTargetFileName);

		$objOverlay = new \Cmp3\Pdf\Overlay($objFileArray);

		$objFile1 = $objOverlay->Render($Mode);

		$objFile1->SetDeleteOnDestruct(false);

		$this->assertTrue($objFile1 instanceof \Cmp3\Files\File);

		$objFile2 = $objOverlay->GetFile();
		$this->assertTrue($objFile2 instanceof \Cmp3\Files\File);

		$this->assertEquals($objFile1->AbsolutePath, $objFile2->AbsolutePath);

		$this->assertTrue($objFile1->Exists());
		$this->assertTrue($objFile1->Size > 0);

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
			$objOverlay = new \Cmp3\Pdf\Overlay($objFileArray);
		} catch (\Cmp3\Pdf\Exception $e) {
			$this->assertTrue($e instanceof \Cmp3\Pdf\Exception);
		}
	}


}
