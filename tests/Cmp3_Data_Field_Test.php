<?php


namespace Cmp3\Data;


class Field_Test extends \TestCaseBase {


	/**
	 * 
	 * @return void
	 */
	public function testSimple()
	{
		$strName = uniqid();
		$strType = uniqid();
		$strFormat = uniqid();
		$strMeta = uniqid();
		$strContent = uniqid();

		$objDataField = new Field($strName, $strType, $strFormat, $strMeta, $strContent);

		$this->assertEquals($objDataField->Name,    $strName);
		$this->assertEquals($objDataField->Type,    $strType);
		$this->assertEquals($objDataField->Format,  $strFormat);
		$this->assertEquals($objDataField->Meta,    $strMeta);
		$this->assertEquals($objDataField->Content, $strContent);
	}


}
