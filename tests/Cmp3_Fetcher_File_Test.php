<?php


namespace Cmp3\Fetcher;


class File_Test extends \TestCaseBase {



	/**
	 *
	 */
	public function testFileTEXT()
	{
		$tsConfString = '
			type = TEXT
			url = EXT:cmp3/tests/fixture/test1.txt
		';

		$configArray = \Cmp3\Config\TypoScriptParser::ParseAsArray($tsConfString);
		$objFetcherConfig = new \Cmp3\Config\TypoScriptArray($configArray);
		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objProperties['Config'] = $objFetcherConfig;
		$objFetcher = new File($objProperties);


		$objContent = $objFetcher->GetContent();

		$this->assertEquals($objContent->Type, \Cmp3\Content\ContentType::TEXT);
		$this->assertEquals($objContent->GetData(), 'TEST1');
	}


	/**
	 *
	 */
	public function testFileTEXT2()
	{
		$tsConfString = '
		url = EXT:cmp3/tests/fixture/test1.txt
		';

		$configArray = \Cmp3\Config\TypoScriptParser::ParseAsArray($tsConfString);
		$objFetcherConfig = new \Cmp3\Config\TypoScriptArray($configArray);
		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objProperties['Config'] = $objFetcherConfig;
		$objFetcher = new File($objProperties);


		$objContent = $objFetcher->GetContent();

		$this->assertEquals($objContent->Type, \Cmp3\Content\ContentType::TEXT);
		$this->assertEquals($objContent->GetData(), 'TEST1');
	}

}
