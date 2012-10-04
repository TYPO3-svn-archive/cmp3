<?php


namespace Cmp3;


class DataMetaTypo3_Test extends \TestCaseBase {


	/**
	 *
	 */
	public function testSimple()
	{
		$filepath = PATH_fixture . 'tt_content_text_simple.xml';
		$mixFieldsArray = \helper::xmlstring2array(file_get_contents($filepath));

		$objDataRow = new Data\Row('tt_content', $mixFieldsArray);
		$objDataRowMeta = new Data\MetaTypo3($objDataRow);

		$this->assertEquals($objDataRowMeta->Title, 'LLL:EXT:cms/locallang_tca.xml:tt_content');
		$this->assertEquals($objDataRowMeta->Type, 'text');
		$this->assertEquals($objDataRowMeta->TableName, 'tt_content');

		$this->assertEquals($objDataRowMeta->DataRow->TableName, 'tt_content');
		$this->assertEquals($objDataRowMeta->DataRow->DataArray['uid'], 8);
		$this->assertEquals($objDataRowMeta->DataRow->uid, 8);
		$this->assertArrayHasKey('bodytext', $objDataRowMeta->DataRow->DataArray);
		$this->assertContains('bodytext', $objDataRowMeta->DataRow->DataFields);
	}


	/**
	 *
	 */
	public function testFieldDefinition()
	{
		$filepath = PATH_fixture . 'tt_content_text_simple.xml';
		$mixFieldsArray = \helper::xmlstring2array(file_get_contents($filepath));

		$objDataRow = new Data\Row('tt_content', $mixFieldsArray);
		$objDataRowMeta = new Data\MetaTypo3($objDataRow);

		$objFieldDef = $objDataRowMeta->GetFieldDefinition('bodytext');

		$this->assertEquals($objFieldDef->Name, 'bodytext');
		$this->assertEquals($objFieldDef->Type, 'text');
		$this->assertEquals($objFieldDef->Format, 'typo3_rte');
		$this->assertEquals($objFieldDef->Type, 'text');
		$this->assertNotEquals($objFieldDef->Content, '');
	}


	/**
	 *
	 */
	public function testTable()
	{
		global $TCA;
		\Cmp3\Typo3\TcaTools::LoadTca('tt_content');

		$TCA['tt_content']['columns']['technical_details'] = array (
			'exclude' => 0,
			'label' => 'LLL:EXT:kmp_product/locallang_db.xml:tx_kmpproduct_product.technical_details',
			'config' => array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 4,
					'_VALIGN' => 'middle',
					'table' => array(
						'notNewRecords' => 1,
						'enableByTypeConfig' => 1,
						'type' => 'script',
						'title' => 'LLL:EXT:cms/locallang_ttc.xml:bodytext.W.table',
						'icon' => 'wizard_table.gif',
						'script' => 'wizard_table.php',
						'params' => array(
							'xmlOutput' => 0,
						),
					),
				),
			)
		);

		$filepath = PATH_fixture . 'tt_content_text_simple.xml';
		$mixFieldsArray = \helper::xmlstring2array(file_get_contents($filepath));

		$objDataRow = new Data\Row('tt_content', $mixFieldsArray);
		$objDataRowMeta = new Data\MetaTypo3($objDataRow);

		$objFieldDef = $objDataRowMeta->GetFieldDefinition('technical_details');

		$this->assertEquals($objFieldDef->Name, 'technical_details');
		$this->assertEquals($objFieldDef->Type, 'text');
		$this->assertEquals($objFieldDef->Format, 'typo3_table');
		$this->assertEquals($objFieldDef->Type, 'text');
		$this->assertNotEquals($objFieldDef->Content, '');
		$this->assertContains('|', $objFieldDef->Content);
	}

}
