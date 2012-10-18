<?php


namespace Cmp3\ContentProcessing;



class Function2_Test extends \TestCaseBase {


	/**
	 *
	 */
	public function testHtmlExtractBody()
	{
		$strType = \Cmp3\Content\ContentType::HTML;

		$filepath = PATH_fixture . 'BaselineTypography.html';

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();
		$objContent = new \Cmp3\Content\Content($objProperties);
		$objContent->SetData(file_get_contents($filepath), $strType);

		$objContentProcessor = new HtmlExtractBody();
		$objContentProcessor->Process($objContent);

		$this->assertEquals($objContent->Type,    $strType);

		$this->assertNotContains('<body', $objContent->Data);
		$this->assertNotContains('</body', $objContent->Data);
		$this->assertContains('<div id="page"', $objContent->Data);
		$this->assertContains('</footer', $objContent->Data);
	}




	/**
	 * data provider for StripTags
	 *
	 * @see StripsAllTags
	 */
	public function dataProviderForStripsAllTags()
	{
		return array(
			'Bodytag (open and closed)' => array('<body>hallo</body>', 'hallo'),
            'Bodytag (o) + Boldtag (o&c) + br/' => array('<body>hallo<b>hallo</b><br />', 'hallohallo'),
			'hr br br/' => array('<hr>asdf<br><br />', 'asdf'),
			'Fictive tag &lt;/&gt;, <> and &lt;/leet&gt;' => array('hallo</>hallo<><leet>hallo</leet>', 'hallohallohallo'),
            'Empty - StripTags over and done' => array('', '')
		);
	}

	/**
	 *
	 * @dataProvider dataProviderForStripsAllTags
	 */
	public function testStripTags_StripsAllTags($string, $expected)
    {
	    $strType = \Cmp3\Content\ContentType::HTML;

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();

        // two times same object
		$objContent = new \Cmp3\Content\Content($objProperties);
		$objContent->SetData($string, $strType);

        // first object: StripTags()
		$objContentProcessor = new StripTags();
		$objContentProcessor->Process($objContent);

        // identical?
		self::assertEquals ($expected, $objContent->GetData(), 'The result of StripTags is not as expected!');
	}



	/**
	 * data provider for HtmlEntityDecode
	 *
	 * @see HtmlEntityDecode
	 */
	public function dataProviderForAllHtmlEntityDecode()
	{
		return array(
			'&amp;auml; -> ä' => array('&auml;', 'ä'),
			'&amp;Ouml; -> Ö' => array('&Ouml;', 'Ö'),
			'Fictive &amp;leet;' => array('&leet;', '&leet;'),
			'&amp;hearts; -> ♥' => array('&hearts;', '♥'),
			'Empty - HtmlEntityDecode over and done' => array('', '')
		);
	}

	/**
	 *
	 * @dataProvider dataProviderForAllHtmlEntityDecode
	 */
	public function testHtmlEntityDecode_HtmlAllEntityDecode($string, $expected)
    {
	    $strType = \Cmp3\Content\ContentType::HTML;

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();

        // two times same object
		$objContent = new \Cmp3\Content\Content($objProperties);
		$objContent->SetData($string, $strType);

        // first object: StripTags()
		$objContentProcessor = new HtmlEntityDecode();
		$objContentProcessor->Process($objContent);

        // identical?
		self::assertEquals ($expected, $objContent->GetData(), 'The result of HtmlEntityDecode is not as expected!');
	}



	/**
	 * data provider for HtmlToText
	 *
	 * @see HtmlToText
	 */
	public function dataProviderForTestHtmlToText()
	{
		return array(
			'HTML Tag' => array('<body></body>index.php', 'index.php'),
			'PHP' => array('<?php $test = \'Hello World\';?>:)', ':)'),
			'HTML Quote' => array('<!-- Quote -->Test', 'Test'),
			'HTML JS' => array('<script type="text/javascript">alert("Fenster wurde geschlossen")</script>Test', 'Test'),
#            'True' => array(TRUE, TRUE),
			'Empty - HtmlToText over and done' => array('', '')
		);
	}

	/**
	 *
	 * @dataProvider dataProviderForTestHtmlToText()
	 */
	public function testHtmlToText_TestHtmlToText($string, $expected)
    {
	    $strType = \Cmp3\Content\ContentType::HTML;

		$objProperties = array();
		$objProperties['Logger'] = $this->GetLogger();

		$objContent = new \Cmp3\Content\Content($objProperties);
		$objContent->SetData($string, $strType);

		$objContentProcessor = new HtmlToText();
		$objContentProcessor->Process($objContent);

		self::assertEquals ($expected, $objContent->GetData(), 'The result of HtmlToText is not as expected!');
	}



    /**
     * data provider for BBCodeToHtml
     *
     * @see BBCodeToHtml
     */
    public function dataProviderForTestBBCodeToHtml()
    {
        return array(
            'Bold' => array('[b]Bold[/b]', '<b>Bold</b>'),
            'URL' => array('[url=http://google.de/]Google[/url]', '<a href="http://google.de/">Google</a>'),
#            'True' => array(TRUE, TRUE),
            'Empty - BBCodeToHtml Over and Out' => array('', '')
        );
    }

    /**
     *
     * @dataProvider dataProviderForTestBBCodeToHtml()
     */
    public function testBBCodeToHtml_TestBBCodeToHtml($string, $expected)
    {
        $strType = \Cmp3\Content\ContentType::HTML;

        $objProperties = array();
        $objProperties['Logger'] = $this->GetLogger();

        $objContent = new \Cmp3\Content\Content($objProperties);
        $objContent->SetData($string, $strType);;

        $objContentProcessor = new BBCodeToHtml();
        $objContentProcessor->Process($objContent);

        self::assertEquals ($expected, $objContent->GetData(), 'The result of BBCodeToHtml is not as expected!');
    }

    /**
     *
     * @expectedException \Cmp3\ContentProcessing\Exception
     */
    public function testBBCodeToHtml_TestBBCodeToHtml_Exception()
    {
    	$fixture = true;

        $strType = \Cmp3\Content\ContentType::HTML;

        $objProperties = array();
        $objProperties['Logger'] = $this->GetLogger();

        $objContent = new \Cmp3\Content\Content($objProperties);
        $objContent->SetData($fixture, $strType);

        $objContentProcessor = new BBCodeToHtml();
        $objContentProcessor->Process($objContent);
    }

	## Add more tests here
	## see Library/ContentProcessing

}