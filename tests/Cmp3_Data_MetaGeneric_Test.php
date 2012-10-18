<?php


namespace Cmp3\Data;


class MetaGeneric_Test extends \TestCaseBase {


	/**
	 *
	 */
	public function testRecordRender()
	{
		$objDataRowMeta = new MetaGeneric('dummy');

		$objField = new \Cmp3\Data\Field('freitext_produkt', 'text', 'multiline', '', md5(time()));
		$objDataRowMeta->AddField($objField);
		$objField = new \Cmp3\Data\Field('nochwas', 'text', 'line', '', md5(time()));
		$objDataRowMeta->AddField($objField);

		$objRecordRenderXml = new \Cmp3\Xml\RecordRender($objDataRowMeta);
		$objXml = $objRecordRenderXml->GetXml();

		$strXml = $objXml->saveXML();

		$this->assertContains('<field name="freitext_produkt" type="text" format="multiline">', $strXml);
		$this->assertContains('<field name="nochwas" type="text" format="line">', $strXml);
	}
}
