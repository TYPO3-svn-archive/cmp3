<?php

namespace Cmp3\Pdf;

class Pdf_Processor_Test extends TestCaseBase {


	/**
	 *
	 */
	public function zzztestRender01()
	{
		#TODO
	}


	/**
	 *
	 */
	protected function Render($objPagesArray, $objConfig, $strName)
	{
		// now we create the PDF which also fetches and processes the data if needed
		$objPDF = new Processor($objPagesArray, $objConfig);
		$objFile = $objPDF->Render();

		$this->assertTrue($objFile instanceof \Cmp3\Files\File);

		$this->assertTrue($objFile->Exists());
		$this->assertTrue($objFile->Size > 0);

		$objFile->Rename($strTargetFileName);
	}

}
