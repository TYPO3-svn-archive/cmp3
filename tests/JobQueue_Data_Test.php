<?php


namespace Cmp3;


class JobQueue_Data_Test extends \TestCaseBase {

	protected $objConfig;


	protected function setUp()
	{
		$filepath = PATH_fixture . 'Config_JobQueue_Data.txt';
		$configArray = \Cmp3\Config\TSParser::ParseFileAsArray($filepath);
		$this->objConfig = new \Cmp3\Config\TypoScriptArray($configArray);
	}


	/**
	 *
	 */
	public function testXPathDataMerge()
	{
		$strTargetFileName = PATH_output . \helper::cleanFilename(__METHOD__) . '.xml';
		@unlink($strTargetFileName);

		$filepath = PATH_fixture . 'person2.xml';

		$objQueue = new Job\Queue;
		$objJob = $objQueue->CreateJob(__FUNCTION__, $this->objConfig);

		$objJob->AddData('extraData', file_get_contents($filepath));

		$objQueue->RunJob($objJob);

		while (!$objQueue->isJobFinished($objJob)) {
			// this will never happen because the queue is not asynchronous
			sleep(1);
		}

		$objResult = $objQueue->GetResult($objJob);

		$strContent = (string)$objResult->Content->Data;

		file_put_contents($strTargetFileName, $strContent);

		$this->assertContains('<hPerson>', $strContent);
		$this->assertContains('<FirstName>Yosi</FirstName>', $strContent);
		$this->assertContains('<hName>Carol</hName>', $strContent);
		$this->assertContains('<hName>Jim</hName>', $strContent);
		$this->assertContains('<hAge>10</hAge>', $strContent);
	}

}
