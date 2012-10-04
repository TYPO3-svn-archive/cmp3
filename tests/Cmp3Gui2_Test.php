<?php


namespace Cmp3;


class Cmp3Gui2_Test extends \TestCaseBase {

	protected $objConfig;

	protected $objControlArray;

	protected function setUp()
	{
		$filepath = PATH_fixture . 'Config_Cmp3Gui2.txt';
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
					$objControl->Value = $this->GetDummyText();

					$this->objControlArray[] = $objControl;
			}
		}
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

		$strContent = $objFile->ReadContent();

		$this->assertNotContains('<body', $strContent);
		$this->assertContains('<field name="uid"', $strContent);
		$this->assertContains('<field name="web_address"', $strContent);
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

		// create a generic meta record
		$objDataRowMeta = new \Cmp3\Data\MetaGeneric('dummyTableName', 'EN');

		// add all pseudo fields
		foreach ($this->objControlArray as $objControl) {
			$objField = new \Cmp3\Data\Field($objControl->Name, $objControl->Config->GetValue('meta.type'), $objControl->Config->GetValue('meta.format'), $objControl->Config->GetValue('meta.meta'), $objControl->Value);
			$objDataRowMeta->AddField($objField);
		}

		// add test fields
		$objField = new \Cmp3\Data\Field('print_url', 'text', 'line', null, 'http://www.google.de');
		$objDataRowMeta->AddField($objField);
		$objField = new \Cmp3\Data\Field('title', 'text', 'line', null, $this->GetDummyText(30));
		$objDataRowMeta->AddField($objField);
		$objField = new \Cmp3\Data\Field('subtitle', 'text', 'line', null, $this->GetDummyText(40));
		$objDataRowMeta->AddField($objField);
		$objField = new \Cmp3\Data\Field('description', 'text', 'line', null, $this->GetDummyText(600));
		$objDataRowMeta->AddField($objField);
		$objField = new \Cmp3\Data\Field('short_description', 'text', 'line', null, $this->GetDummyText(380));
		$objDataRowMeta->AddField($objField);
		$objField = new \Cmp3\Data\Field('pros', 'text', 'line', null, $this->GetDummyText(380));
		$objDataRowMeta->AddField($objField);

		// make xml out of it
		$objDummyConfig = new \Cmp3\Config\ArrayData();

		$objProperties = array();
		$objProperties['Logger'] = $objJob->Logger;
		$objProperties['Config'] = $objDummyConfig;
		$objProperties['DataRowMetaArray'] = array($objDataRowMeta);

		$objDataRowMetaXml = new \Cmp3\Source\DataRowMetaXml($objProperties);

		//--------------------------------------


		$strTargetFileName = PATH_output . \helper::cleanFilename(__METHOD__) . '_extraData.xml';
		@unlink($strTargetFileName);
		file_put_contents($strTargetFileName, (string)$objDataRowMetaXml->GetContent()->Data);


		$objJob->AddData('recordID', '34');
		$objJob->AddData('extraData', $objDataRowMetaXml->GetContent()->Data);

		//--------------------------------------

		$objQueue->RunJob($objJob);

		while (!$objQueue->isJobFinished($objJob)) {
			// this will never happen because the queue is not asynchronous
			sleep(1);
		}

		$objResult = $objQueue->GetResult($objJob);

		return $objResult;
	}
}
