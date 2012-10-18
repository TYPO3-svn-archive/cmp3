<?php


namespace Cmp3\Content;



class Function_Test extends \TestCaseBase {


	/**
	 * @return void
	 */
	public function testTextString()
	{
		$strType = ContentType::TEXT;
		$strContent = 'Schubidu';

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objContent = new Content($objProperties);
		$objContent->SetData($strContent, $strType);

		$this->assertEquals($objContent->Type,    $strType);
		$this->assertEquals($objContent->Data, $strContent);
		$this->assertEquals($objContent->GetData(), $strContent);
	}


	/**
	 * @return void
	 */
	public function testMeta()
	{
		$strType = ContentType::TEXT;
		$strContent = 'Schubidu';

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objContent = new Content($objProperties);
		$objContent->SetData($strContent, $strType);
		$objContent->Meta->BaseUrl = $strContent;
		$objContent->Meta->something_else = $strContent;


		$this->assertEquals($objContent->Type,    $strType);
		$this->assertEquals($objContent->Data, $strContent);
		$this->assertEquals($objContent->Meta->BaseUrl, $strContent);
		$this->assertEquals($objContent->Meta->something_else, $strContent);
	}


	/**
	 * @return void
	 */
	public function testDetData()
	{
		$strType = ContentType::TEXT;
		$strContent = 'Schubidu';

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objContent = new Content($objProperties);
		$objContent->SetData($strContent, $strType);

		$this->assertEquals($objContent->Type,    $strType);

		$objContent->SetData('Something');

		$this->assertEquals($objContent->Type,    $strType);

		$strType = ContentType::HTML;
		$objContent->SetData('<p>Something</p>', $strType);

		$this->assertEquals($objContent->Type,    $strType);

		$strType = ContentType::TEXT;
		$objContent->Type = $strType;

		$this->assertEquals($objContent->Type,    $strType);
	}


	/**
	 * @return void
	 */
	public function testTextFile()
	{
		$strType = ContentType::TEXT;
		$filepath = PATH_fixture . 'test1.txt';
		$strContent = file_get_contents($filepath);

		$objContentFile = new \Cmp3\Files\File($filepath);

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objContent = new Content($objProperties);
		$objContent->SetData($objContentFile, $strType);

		$this->assertEquals($objContent->Type,    $strType);
		$this->assertEquals($objContent->Data, $strContent);
		$this->assertEquals($objContent->GetData(), $strContent);
		$this->assertEquals($objContent->GetDataFile(), $objContent->File);
	}


	/**
	 *
	 */
	public function testTextStringAsFile()
	{
		$strType = ContentType::TEXT;
		$strContent = 'Schubidu';

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objContent = new Content($objProperties);
		$objContent->SetData($strContent, $strType);

		$this->assertEquals($objContent->Type,    $strType);
		$this->assertEquals($objContent->GetDataFile()->ReadContent(), $strContent);
	}


	/**
	 *
	 */
	public function testHtmlString()
	{
		$strType = ContentType::HTML;
		$strContent = '<p>Schubidu</p>';

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objContent = new Content($objProperties);
		$objContent->SetData($strContent, $strType);

		$this->assertEquals($objContent->Type,    $strType);
		$this->assertEquals($objContent->Data, $strContent);
		$this->assertEquals($objContent->GetData(), $strContent);
	}


	/**
	 * @expectedException \Cmp3\Exception
	 */
	public function testGetDataException()
	{
		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objContent = new Content($objProperties);
		$objContent->SetData(null,null);

		$objContent->GetData();
	}


	/**
	 * @expectedException \Cmp3\Exception
	 */
	public function testGetDataFileException()
	{
		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objContent = new Content($objProperties);
		$objContent->SetData(null,null);

		$objContent->GetDataFile();
	}


	/**
	 * @expectedException \Cmp3\UndefinedGetPropertyException
	 */
	public function testGetterException()
	{
		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objContent = new Content($objProperties);
		$objContent->SetData(null,null);

		$bla = $objContent->Schubidu;
	}


	/**
	 * @expectedException \Cmp3\UndefinedSetPropertyException
	 */
	public function testSetterException()
	{
		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objContent = new Content($objProperties);
		$objContent->SetData(null,null);

		$objContent->Schubidu = 1234;
	}
}
