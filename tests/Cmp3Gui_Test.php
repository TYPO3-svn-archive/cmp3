<?php


namespace Cmp3;


use Cmp3\Composer\Exception;

class Cmp3Gui_Test extends \TestCaseBase {

	protected $objConfig;

	protected $objControlArray;

	protected function setUp()
	{
		$filepath = PATH_fixture . 'Config_Cmp3Gui.txt';
		$configArray = \Cmp3\Config\TSParser::ParseFileAsArray($filepath);
		$this->objConfig = new \Cmp3\Config\TypoScriptArray($configArray);



		/*
		 * type of the field
		 *
		 * Types are the more raw presentations of data which reflects not necessarily the specific data meaning.
		 *
		 * text     - string type of any length or format
		 * datetime - format is %Y-%m-%dT%H:%M:%S%z, the W3C format. (xs:datetime)
		 * date     - %Y-%m-%d (xs:date)
		 * time     - %H:%M:%S%z (xs:time)
		 * int      - integer, could be negative
		 * float    - floating number of any precision
		 * blob     - binary data
		 *
		 *
		 * Format defines the specific data meaning.
		 * Example: For the type integer the format could be datetime
		 *
		 * line      - string of any length but only one line. Example: header
		 * multiline - string of any length and multiple lines with no further formating instructions like &lt;b&gt;
		 * rich      - string of any length and multiple lines with formating instructions - todo to be defined
		 * datetime  - field defines date and time
		 * date      - just date
		 * time      - a time
		 * int       - integer, could be negative
		 * float     - floating number of any precision
		 * images    - todo
		 *
		 *
		 * This defines the meaning of fields in relation to the record
		 *
		 * header  - defines that the field is the header of the record
		 * body    - the field holds the body text of the record
		 * todo more?
		 */

		if ($arrProperties = $this->objConfig->GetProperties('options')) {

			foreach ($arrProperties as $key => $strClass) {

					if (is_array($strClass)) {
						continue;
					}

					if ( $this->objConfig->hasProperty('options.'.$key.'.')) {
						$objConfigControl =  $this->objConfig->GetProxy('options.'.$key.'.');
					} else {
						// dummy
						$objConfigControl = new \Cmp3\Config\ArrayData();
					}
					$objControl = new \ArrayObject();
					$objControl->Config = $objConfigControl;

					if ($arrProperties = $objConfigControl->GetProperties('properties')) {
						foreach ($arrProperties as $strPropertyName => $strPropertyValue) {
							$objControl->$strPropertyName = $strPropertyValue;
						}
					}

					$objControl->Name = $key;
					$objControl->Value = md5(time());

					$this->objControlArray[] = $objControl;
			}
		}
	}


	/**
	 *
	 */
	public function testOne()
	{
		// fetch one record
		$configArray = array(
				'table' => 'tt_content',
				'id' => '34',
				);
		$objConfig = new \Cmp3\Config\TypoScriptArray($configArray);

		$objProperties = array();
		$objProperties['Logger'] = $objJob->Logger;
		$objProperties['Config'] = $objConfig;

		$objQuery = new \Cmp3\Source\Typo3Query($objProperties);
		$objQueryDataRowMeta = $objQuery->QuerySingle();

		//------------------------------------------------

		// create a generic meta record
		$objDataRowMeta = new \Cmp3\Data\MetaGeneric('tt_content');

		//------------------------------------------------

		// add all fields to the generic meta record
		$strFieldNameArray = $objQueryDataRowMeta->DataFields;
		foreach ($strFieldNameArray as $strFieldName) {
			$objDataField = $objQueryDataRowMeta->GetFieldDefinition($strFieldName);
			$objDataRowMeta->AddField($objDataField);
		}

		//------------------------------------------------

		// add test fields

		$objField = new \Cmp3\Data\Field('test_url', 'text', 'line', null, 'http://www.google.de');
		$objDataRowMeta->AddField($objField);


		//------------------------------------------------

		// add all pseudo fields
		foreach ($this->objControlArray as $objControl) {
			$objField = new \Cmp3\Data\Field($objControl->Name, $objControl->Config->GetValue('meta.type'), $objControl->Config->GetValue('meta.format'), $objControl->Config->GetValue('meta.meta'), $objControl->Value);
			$objDataRowMeta->AddField($objField);
		}

		//------------------------------------------------

		// make xml out of it
		$objConfig = new \Cmp3\Config\ArrayData();
		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objProperties['Config'] = $objConfig;
		$objProperties['DataRowMetaArray'] = array($objDataRowMeta);

		$objDataRowMetaXml = new \Cmp3\Source\DataRowMetaXml($objProperties);

		$objContent = $objDataRowMetaXml->GetContent();

		$this->assertEquals($objContent->Type, \Cmp3\Content\ContentType::CMP3XML);

		$strXml = $objContent->GetData()->saveXML();

		$strDestinationPath = PATH_output . \helper::cleanFilename(__METHOD__) . '.xml';
		file_put_contents($strDestinationPath, $strXml);

		//------------------------------------------------

#error_log($strXml);

		$this->assertContains('<field name="uid" type="int" format="text">', $strXml);
		$this->assertContains('<value>34</value>', $strXml);
		$this->assertContains('<field name="freitext_produkt" type="text" format="multiline">', $strXml);
	}











	/**
	 *
	 */
	public function testXML()
	{
		$strTargetFileName = PATH_output . \helper::cleanFilename(__METHOD__) . '.xml';
		@unlink($strTargetFileName);

		$objResult = $this->RunJob(__FUNCTION__);

		$objContent = $objResult->Content;

		$this->assertEquals($objContent->Type, \Cmp3\Content\ContentType::CMP3XML);

		$objFile = $objContent->File->Copy($strTargetFileName);
		$objFile->SetDeleteOnDestruct(false);

		$this->assertTrue($objFile->Exists());
		$this->assertNotContains('<body', $objFile->ReadContent());
	}



	/**
	 *
	 */
	public function testFO()
	{
		$strTargetFileName = PATH_output . \helper::cleanFilename(__METHOD__) . '.xml';
		@unlink($strTargetFileName);

		$objResult = $this->RunJob(__FUNCTION__);

		$objContent = $objResult->Content;

#FIXME content processor does not change type we need to fix that or remove type completely
#		$this->assertEquals($objContent->Type, \Cmp3\Content\ContentType::XML);

		$objFile = $objContent->File->Copy($strTargetFileName);
		$objFile->SetDeleteOnDestruct(false);

		$this->assertTrue($objFile->Exists());
		$this->assertNotContains('<body', $objFile->ReadContent());
	}



	/**
	 *
	 */
	public function testPDF()
	{
		$strTargetFileName = PATH_output . \helper::cleanFilename(__METHOD__) . '.pdf';
		@unlink($strTargetFileName);

		$objResult = $this->RunJob(__FUNCTION__);

		$objContent = $objResult->Content;

		$this->assertEquals($objContent->Type, \Cmp3\Content\ContentType::PDF);

		$objFile = $objContent->File->Copy($strTargetFileName);
		$objFile->SetDeleteOnDestruct(false);

		$this->assertTrue($objFile->Exists());
		$this->assertNotContains('<body', $objFile->ReadContent());
	}



	/**
	 *
	 */
	protected function RunJob($strJobName)
	{
		$objConfig = $this->objConfig->GetProxy('cmp3');

		$objQueue = new \Cmp3\Job\Queue;
		$objJob = $objQueue->CreateJob($strJobName, $objConfig);


		//------------------------------------------------

		// fetch one record
		$configArray = array(
				'table' => 'tt_content',
				'id' => '34',
		);
		$objConfig = new \Cmp3\Config\TypoScriptArray($configArray);

		$objProperties = array();
		$objProperties['Logger'] = $objJob->Logger;
		$objProperties['Config'] = $objConfig;

		$objQuery = new \Cmp3\Source\Typo3Query($objProperties);
		$objQuery->Logger = $objJob->Logger;
		$objQueryDataRowMeta = $objQuery->QuerySingle();

		//------------------------------------------------

		// create a generic meta record
		$objDataRowMeta = new \Cmp3\Data\MetaGeneric($objQueryDataRowMeta->TableName, $objQueryDataRowMeta->Language);

		//------------------------------------------------

		// add all fields to the generic meta record
		$strFieldNameArray = $objQueryDataRowMeta->DataFields;
		foreach ($strFieldNameArray as $strFieldName) {
			$objDataField = $objQueryDataRowMeta->GetFieldDefinition($strFieldName);
			$objDataRowMeta->AddField($objDataField);
		}

		//------------------------------------------------

		// add test fields

		$objField = new \Cmp3\Data\Field('test_url', 'text', 'line', null, 'http://www.google.de');
		$objDataRowMeta->AddField($objField);

		//------------------------------------------------

		// add all pseudo fields
		foreach ($this->objControlArray as $objControl) {
			$objField = new \Cmp3\Data\Field($objControl->Name, $objControl->Config->GetValue('meta.type'), $objControl->Config->GetValue('meta.format'), $objControl->Config->GetValue('meta.meta'), $objControl->Value);
			$objDataRowMeta->AddField($objField);
		}

		//------------------------------------------------

		// make xml out of it
		$objDummyConfig = new \Cmp3\Config\ArrayData();

		$objProperties = array();
		$objProperties['Logger'] = $objJob->Logger;
		$objProperties['Config'] = $objDummyConfig;
		$objProperties['DataRowMetaArray'] = array($objDataRowMeta);

		$objDataRowMetaXml = new \Cmp3\Source\DataRowMetaXml($objProperties);

		//--------------------------------------

		$objJob->Prepare();

		// create source object
		$objJob->TaskArray[0]->Source = $objDataRowMetaXml;

		//--------------------------------------

		$objQueue->RunJob($objJob);

		while (!$objQueue->isJobFinished($objJob)) {
			// this will never happen because the queue is not asynchronous
			sleep(1);
		}

		$objResult = $objQueue->GetResult($objJob);

		if ($objResult->hasErrors) {
			throw new Exception($objResult->ErrorMessage);
		}

		return $objResult;
	}
}
