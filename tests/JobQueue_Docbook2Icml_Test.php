<?php


namespace Cmp3;


class JobQueue_Docbook2Icml_Test extends \TestCaseBase {

	protected $objConfig;


	protected function setUp()
	{
		$filepath = PATH_fixture . 'Config_JobQueue_Docbook2Icml.txt';
		$configArray = \Cmp3\Config\TSParser::ParseFileAsArray($filepath);
		$this->objConfig = new \Cmp3\Config\TypoScriptArray($configArray);
	}

	/**
	 *
	 */
	public function testDB2ICML1()
	{
		Job\Job::$throwExceptions = true;

		$objQueue = new \Cmp3\Job\Queue();
		$objJob = $objQueue->CreateJob(__FUNCTION__, $this->objConfig);

		$objQueue->RunJob($objJob);

		while (!$objQueue->isJobFinished($objJob)) {
			// this will never happen because the queue is not asynchronous
			sleep(1);
#FIXME			
			break;
		}

		$objResult = $objQueue->GetResult($objJob);

		$strDestinationPath = PATH_output . \helper::cleanFilename(__METHOD__) . '.xml';
		$objFile = $objResult->Content->File->Copy($strDestinationPath);
		$objFile->SetDeleteOnDestruct(false);

		$this->assertTrue($objFile->Exists());
#TODO this will fail currently		$this->assertEquals($objResult->Content->Type, \Cmp3\Content\ContentType::XML);
		$this->assertNotContains('<body', $objFile->ReadContent());
	}



}
