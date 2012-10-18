<?php


namespace Cmp3\Uri;



class UriEncode_Test extends \TestCaseBase {

    /**
     * data provider for StripTags
     *
     */
    public function dataProviderForAllUris()
    {
        return array(
            'String' => array('leet1337'),
            'Number' => array(1337),
            'Boolean' => array(true),
            'NULL' => array(NULL),
            'Empty - Over and Out' => array('')
        );
    }



    /**
     *
     * @see Bookmark
     * @dataProvider dataProviderForAllUris
     */
    public function test_UriEncode_Bookmark($fixture)
    {
        $expected = 'MEBKM:TITLE:' . $fixture . ';URL:' . $fixture . ';;';
        $result = UriEncode::Bookmark($fixture, $fixture);

        self::assertEquals ($expected, $result, 'The result of UriEncode::Bookmark() is not as expected!');
    }

    /**
     *
     * @see Contact
     * @dataProvider dataProviderForAllUris
     */
    public function test_UriEncode_Contact($fixture)
    {
        $expected = 'MECARD:N:' . $fixture . ';ADR:' . $fixture . ';TEL:' . $fixture . ';EMAIL:' . $fixture . ';;';
        $result = UriEncode::Contact($fixture, $fixture, $fixture, $fixture);

        self::assertEquals ($expected, $result, 'The result of UriEncode::Contact() is not as expected!');
    }

    /**
     *
     * @see Content
     * @dataProvider dataProviderForAllUris
     */
    public function test_UriEncode_Content($fixture)
    {
        $expected = 'CNTS:TYPE:' . $fixture . ';LNG:' . $fixture . ';BODY:' . $fixture . ';;';
        $result = UriEncode::Content($fixture, $fixture, $fixture);

        self::assertEquals ($expected, $result, 'The result of UriEncode::Content() is not as expected!');
    }

    /**
     *
     * @see Email
     * @dataProvider dataProviderForAllUris
     */
    public function test_UriEncode_Email($fixture)
    {
        $expected = 'MATMSG:TO:' . $fixture . ';SUB:' . $fixture . ';BODY:' . $fixture . ';;';
        $result = UriEncode::Email($fixture, $fixture, $fixture);

        self::assertEquals ($expected, $result, 'The result of UriEncode::Email() is not as expected!');
    }

    /**
     *
     * @see Phone
     * @dataProvider dataProviderForAllUris
     */
    public function test_UriEncode_Phone($fixture)
    {
        $expected = 'TEL:' . $fixture;
        $result = UriEncode::Phone($fixture);

        self::assertEquals ($expected, $result, 'The result of UriEncode::Phone() is not as expected!');
    }

    /**
     *
     * @see Geo
     * @dataProvider dataProviderForAllUris
     */
    public function test_UriEncode_Geo($fixture)
    {
        $expected = 'GEO:' . $fixture . ',' . $fixture . ',' . $fixture;
        $result = UriEncode::Geo($fixture,$fixture,$fixture);

        self::assertEquals ($expected, $result, 'The result of UriEncode::Geo() is not as expected!');
    }

    /**
     *
     * @see Sms
     * @dataProvider dataProviderForAllUris
     */
    public function test_UriEncode_Sms($fixture)
    {
        $expected = 'SMSTO:' . $fixture . ':' . $fixture . '';
        $result = UriEncode::Sms($fixture,$fixture,$fixture);

        self::assertEquals ($expected, $result, 'The result of UriEncode::Sms() is not as expected!');
    }

    /**
     *
     * @see Url
     * @dataProvider dataProviderForAllUris
     */
    public function test_UriEncode_Url($fixture)
    {
        $expected = 'http://' . $fixture . '';
        $result = UriEncode::Url($fixture);

        self::assertEquals ($expected, $result, 'The result of UriEncode::Url() is not as expected!');
    }

    /**
     *
     * @see Wifi
     * @dataProvider dataProviderForAllUris
     */
    public function test_UriEncode_Wifi($fixture)
    {
        $expected = 'WIFI:T:' . $fixture . ';S' . $fixture . ';' . $fixture . ';;';
        $result = UriEncode::Wifi($fixture,$fixture,$fixture);

        self::assertEquals ($expected, $result, 'The result of UriEncode::Wifi() is not as expected!');
    }





}
