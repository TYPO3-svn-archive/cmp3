<?php


namespace Cmp3\String;

/**
 *
 * @author	Andreas Schütte <schuette@bitmotion.de>
 * @author	Rene Fritz <r.fritz@bitmotion.de>
 */
class StringString_Test extends \TestCaseBase {


	private $strEncoding = 'utf-8';
	private $strOrigInternalEncoding;
	private $strOrigRegexEncoding;



	public function setUp()
	{
		if (function_exists('mb_internal_encoding')) {
			$this->strOrigEncoding = mb_internal_encoding();
			mb_internal_encoding($this->strEncoding);
		}
		if (function_exists('mb_regex_encoding')) {
			$this->strOrigRegexEncoding = mb_regex_encoding();
			mb_regex_encoding($this->strEncoding);
		}
	}

	public function tearDown()
	{
		if (function_exists('mb_internal_encoding')) {
			mb_internal_encoding($this->strOrigEncoding);
		}
		if (function_exists('mb_regex_encoding')) {
			mb_regex_encoding($this->strOrigRegexEncoding);
		}
	}

	/*********************************************************
	 *
	 * String tests
	 *
	 *********************************************************/


	/**
	 * data provider for invalid String tests
	 *
	 * @see ReturnsLastCharacterOfStringTest
	 */
	public function dataProviderForInvalidStringTests()
	{
		return array(
			'throws exception if bool true is given' => array(TRUE),
			'throws exception if bool false is given' => array(FALSE),
			'throws exception if array is given' => array(array('foo')),
			'throws exception if object is given' => array(new \stdClass()),
			'throws exception if integer is given' => array(12),
			'throws exception if null is given' => array(null),
			'throws exception if boolean is given' => array(3245.456),
		);
	}


	/**
	 * data provider for ReturnsFirstCharacterOfStringTest
	 *
	 * @see ReturnsFirstCharacterOfStringTest
	 */
	public function dataProviderForReturnsFirstCharacterOfString()
	{
		return array(
			'returns same if one ASCII char is given' => array('!', '!'),
			'returns same if one multibyte  char is given' => array('Ü', 'Ü'),
			'returns first char of ASCII string' => array('GJGKvkvjkvjkV', 'G'),
			'returns first char of UTF-8 string, when first char is multibyte' => array('ÄJGKvkvjkvjkV', 'Ä'),
			'returns first char of UTF-8 string, when other chars are multibyte' => array('JßGsßäöKvkvjkvjkV', 'J'),
			'returns empty string, when empty string is given' => array('', ''),
		);
	}

	/**
	 *
	 * @test
	 * @dataProvider dataProviderForReturnsFirstCharacterOfString
	 */
	public function FirstCharacter_ReturnsFirstCharacterOfStringTest($string, $expected)
	{
		$result = String::FirstCharacter($string);

		self::assertEquals ($expected, $result, 'The result of String::FirstCharacter() is not as expected!');
	}


	/**
	 *
	 * @test
	 * @dataProvider dataProviderForInvalidStringTests
	 * @expectedException \Cmp3\WrongParameterException
	 */
	public function FirstCharacter_ReturnsExceptionForInvalidStringTest($string)
	{
		$result = String::FirstCharacter($string);
	}


	/**
	 * data provider for ReturnsLastCharacterOfStringTest
	 *
	 * @see ReturnsLastCharacterOfStringTest
	 */
	public function dataProviderForReturnsLastCharacterOfString()
	{
		return array(
			'returns same if one ASCII char is given' => array('!', '!'),
			'returns same if one multibyte  char is given' => array('Ü', 'Ü'),
			'returns last char of ASCII string' => array('GJGKvkvjkvjkV', 'V'),
			'returns last char of UTF-8 string, when last char is multibyte' => array('JGKvkvjkvjkVÄ', 'Ä'),
			'returns last char of UTF-8 string, when other chars are multibyte' => array('JßGsßäöKvkvjkvjkäV', 'V'),
			'returns empty string, when empty string is given' => array('', ''),
		);
	}


	/**
	 *
	 * @test
	 * @dataProvider dataProviderForReturnsLastCharacterOfString
	 */
	public function LastCharacter_ReturnsLastCharacterOfStringTest($string, $expected)
	{
		$result = String::LastCharacter($string);
		self::assertEquals ($expected, $result, 'The result of String::LastCharacter() is not as expected!');
	}


	/**
	 *
	 * @test
	 * @dataProvider dataProviderForInvalidStringTests
	 * @expectedException \Cmp3\WrongParameterException
	 */
	public function LastCharacter_ReturnsExceptionForInvalidStringTest($string)
	{
		$result = String::LastCharacter($string);
	}


	/**
	 * data provider for ReturnsLastCharacterOfStringTest
	 *
	 * @see ReturnsLastCharacterOfStringTest
	 */
	public function dataProviderForAppendColonAppendsColonToString()
	{
		return array(
			'appends colon to string if it has none' => array('abc', 'abc:'),
			'appends colon to multibyte string if it has none' => array('äâè', 'äâè:'),
			'returns same if string has a colon' => array('abc:', 'abc:'),
			'returns same if string has multiple colons' => array('abc::', 'abc::'),
			'returns colon if empty string is given' => array('', ':'),
			'returns colon if colon is given' => array(':', ':'),
		);
	}


	/**
	 *
	 * @test
	 * @dataProvider dataProviderForAppendColonAppendsColonToString
	 */
	public function AppendColon_AppendsColonToStringTest($string, $expected)
	{
		$result = String::AppendColon($string);

		self::assertEquals ($expected, $result, 'The result of String::AppendColon() is not as expected!');
	}


	/**
	 *
	 * @test
	 * @dataProvider dataProviderForInvalidStringTests
	 * @expectedException \Cmp3\WrongParameterException
	 */
	public function AppendColon_ReturnsExceptionForInvalidStringsTest($string)
	{
		$result = String::AppendColon($string);
	}


	/**
	 * data provider for ReturnsFirstCharacterOfStringTest
	 *
	 * @see ReturnsFirstCharacterOfStringTest
	 */
	public function dataProviderForStripColonFromString()
	{
		return array(
			'strips colon to string if it has none' => array('abc:', 'abc'),
			'strips colon to multibyte string if it has none' => array('äâè:', 'äâè'),
			'returns same if string has no colon' => array('abc', 'abc'),
			'returns one colon less if more than one given' => array('abc::', 'abc:'),
			'returns empty string if only a colon is given' => array(':', ''),
			'returns same if string is empty' => array('', ''),
		);
	}


	/**
	 *
	 * @test
	 * @dataProvider dataProviderForStripColonFromString
	 */
	public function StripColon_StipsColonFromStringTest($string, $expected)
	{
		$result = String::StripColon($string);

		self::assertEquals ($expected, $result, 'The result of String::StripColon() is not as expected!');
	}


	/**
	 *
	 * @test
	 * @dataProvider dataProviderForInvalidStringTests
	 * @expectedException \Cmp3\WrongParameterException
	 */
	public function StripColon_ReturnsExceptionForInvalidStringsTest($string)
	{
		$result = String::StripColon($string);
	}


	/**
	 * data provider for ReturnsTruncatedStringTest
	 *
	 * @see Truncate_ReturnsTruncatedStringTest
	 */
	public function dataProviderForTruncatedString()
	{
		return array(
			'truncate string' => array('abcdefgh',6 , 'abc...'),
			'truncate string2' => array('äbcdöfgü',6 , 'äbc...'),
			'truncate string3' => array('abc def gh',7 , 'abc ...'),
			'truncate string4' => array('abcdefgh',2 , 'abc...'),
			'truncate string5' => array('abcdefgh',-6 , '...fgh'),
			'truncate string6' => array('äbcdöfgü', -6 , '...fgü'),
			'truncate string7' => array('abc def gh',10 , 'abc def gh'),
			'truncate string8' => array('abc def gh',-10 , 'abc def gh'),
			'truncate string9' => array('abcd ef ghi',-10 , '... ef ghi'),
			'truncate string10' => array('abcdefgh',-2 , '...fgh'),
			'truncate string13' => array('abcdefgh',"-2" , '...fgh'),
			'truncate string11' => array('abcd ef ghi', True , 'abc...'),
			'truncate string12' => array('abcd ef ghi', array("","") , 'abc...'),
			'truncate string14' => array('abcdefgh', NULL , 'abc...'),
			'truncate string15' => array('abcdefgh', FALSE , 'abc...'),
			'returns empty string if given string is empty' => array('',6, ''),
		);
	}


	/**
	 *
	 * @test
	 * @dataProvider dataProviderForTruncatedString
	 */
	public function Truncate_ReturnsTruncatedStringTest($string, $int, $expected)
	{
		$result = String::Truncate($string, $int);

		self::assertEquals ($expected, $result, 'The result of String::Truncate() is not as expected!');
	}



	/**
	 *
	 * @test
	 * @dataProvider dataProviderForInvalidStringTests
	 * @expectedException \Cmp3\WrongParameterException
	 */
	public function Truncate_ReturnsExceptionForInvalidStringsTest($string)
	{
		$result = String::Truncate($string, 6);
	}


	public function test_TruncateSmart()
	{
		$fixture = 'abcdefgh';
		$expected = 'abcdefgh';
		$result = String::TruncateSmart($fixture, 8);

		self::assertEquals ($expected, $result, 'The result of String::TruncateSmart() is not as expected!');

		$fixture = 'abcdefgh';
		$expected = 'abc...';
		$result = String::TruncateSmart($fixture, 6);

		self::assertEquals ($expected, $result, 'The result of String::TruncateSmart() is not as expected!');


		$fixture = 'äbcdöfgü';
		$expected = 'äbc...';
		$result = String::TruncateSmart($fixture, 6);

		self::assertEquals ($expected, $result, 'The result of String::TruncateSmart() is not as expected!');


		$fixture = 'abc def gh';
		$expected = 'abc ...';
		$result = String::TruncateSmart($fixture, 7);

		self::assertEquals ($expected, $result, 'The result of String::TruncateSmart() is not as expected!');


		$fixture = 'abcdef ghijklmnopqrstuvwxyz';
		$expected = 'abcdef ghijklmn...';
		$result = String::TruncateSmart($fixture, 18);

		self::assertEquals ($expected, $result, 'The result of String::TruncateSmart() is not as expected!');


		$fixture = 'abcdefg hijklmnopqrstuvwxyz';
		$expected = 'abcdefg ...';
		$result = String::TruncateSmart($fixture, 13);

		self::assertEquals ($expected, $result, 'The result of String::TruncateSmart() is not as expected!');

		$fixture = 'abcdefgh';
		$expected = '...fgh';
		$result = String::TruncateSmart($fixture, -6);

		self::assertEquals ($expected, $result, 'The result of String::TruncateSmart() is not as expected!');


		$fixture = 'äbcdöfgü';
		$expected = '...fgü';
		$result = String::TruncateSmart($fixture, -6);

		self::assertEquals ($expected, $result, 'The result of String::TruncateSmart() is not as expected!');


		$fixture = 'abc def gh';
		$expected = '... gh';
		$result = String::TruncateSmart($fixture, -7);

		self::assertEquals ($expected, $result, 'The result of String::TruncateSmart() is not as expected!');


		$fixture = 'abc def gh';
		$expected = '...def gh';
		$result = String::TruncateSmart($fixture, -9);

		self::assertEquals ($expected, $result, 'The result of String::TruncateSmart() is not as expected!');


		$fixture = 'zabc def gh';
		$expected = '... def gh';
		$result = String::TruncateSmart($fixture, -10);

		self::assertEquals ($expected, $result, 'The result of String::TruncateSmart() is not as expected!');


		$fixture = 'abcdefghijklmnopqrs tuvwxyz';
		$expected = '... tuvwxyz';
		$result = String::TruncateSmart($fixture, -18);

		self::assertEquals ($expected, $result, 'The result of String::TruncateSmart() is not as expected!');


		$fixture = 'abcdefghijklmnopqrs tuvwxyz';
		$expected = '... tuvwxyz';
		$result = String::TruncateSmart($fixture, -13);

		self::assertEquals ($expected, $result, 'The result of String::TruncateSmart() is not as expected!');

	}


	public function test_XmlEscape()
	{
		$fixture = '';
		$expected = '';
		$result = String::XmlEscape($fixture);

		self::assertEquals ($expected, $result, 'The result of String::XmlEscape() is not as expected!');


		$fixture = 'abc';
		$expected = 'abc';
		$result = String::XmlEscape($fixture);

		self::assertEquals ($expected, $result, 'The result of String::XmlEscape() is not as expected!');

		$fixture = '<abc';
		$expected = '<![CDATA[<abc]]>';
		$result = String::XmlEscape($fixture);

		self::assertEquals ($expected, $result, 'The result of String::XmlEscape() is not as expected!');

		$fixture = 'ab&c';
		$expected = '<![CDATA[ab&c]]>';
		$result = String::XmlEscape($fixture);

		self::assertEquals ($expected, $result, 'The result of String::XmlEscape() is not as expected!');
	}


	public function test_WordsFromUnderscore()
	{
		$fixture = '';
		$expected = '';
		$result = String::WordsFromUnderscore($fixture);

		self::assertEquals ($expected, $result, 'The result of String::WordsFromUnderscore() is not as expected!');


		$fixture = 'abc';
		$expected = 'Abc';
		$result = String::WordsFromUnderscore($fixture);

		self::assertEquals ($expected, $result, 'The result of String::WordsFromUnderscore() is not as expected!');


		$fixture = 'abc def gh';
		$expected = 'Abc Def Gh';
		$result = String::WordsFromUnderscore($fixture);

		self::assertEquals ($expected, $result, 'The result of String::WordsFromUnderscore() is not as expected!');


		$fixture = 'abc_def gh';
		$expected = 'Abc Def Gh';
		$result = String::WordsFromUnderscore($fixture);

		self::assertEquals ($expected, $result, 'The result of String::WordsFromUnderscore() is not as expected!');


		$fixture = 'ABC_DEF GH_';
		$expected = 'ABC DEF GH';
		$result = String::WordsFromUnderscore($fixture);

		self::assertEquals ($expected, $result, 'The result of String::WordsFromUnderscore() is not as expected!');
	}


	public function test_CamelCaseFromUnderscore()
	{
		$fixture = '';
		$expected = '';
		$result = String::CamelCaseFromUnderscore($fixture);

		self::assertEquals ($expected, $result, 'The result of String::CamelCaseFromUnderscore() is not as expected!');


		$fixture = 'abc';
		$expected = 'Abc';
		$result = String::CamelCaseFromUnderscore($fixture);

		self::assertEquals ($expected, $result, 'The result of String::CamelCaseFromUnderscore() is not as expected!');


		$fixture = 'abc def gh';
		$expected = 'Abc def gh';
		$result = String::CamelCaseFromUnderscore($fixture);

		self::assertEquals ($expected, $result, 'The result of String::CamelCaseFromUnderscore() is not as expected!');


		$fixture = 'abc_def gh';
		$expected = 'AbcDef gh';
		$result = String::CamelCaseFromUnderscore($fixture);

		self::assertEquals ($expected, $result, 'The result of String::CamelCaseFromUnderscore() is not as expected!');


		$fixture = 'ABC_DEF GH_';
		$expected = 'AbcDef gh';
		$result = String::CamelCaseFromUnderscore($fixture);

		self::assertEquals ($expected, $result, 'The result of String::CamelCaseFromUnderscore() is not as expected!');
	}


	public function test_SmallCamelCaseFromUnderscore()
	{
		$fixture = '';
		$expected = '';
		$result = String::SmallCamelCaseFromUnderscore($fixture);

		self::assertEquals ($expected, $result, 'The result of String::SmallCamelCaseFromUnderscore() is not as expected!');


		$fixture = 'abc';
		$expected = 'abc';
		$result = String::SmallCamelCaseFromUnderscore($fixture);

		self::assertEquals ($expected, $result, 'The result of String::SmallCamelCaseFromUnderscore() is not as expected!');


		$fixture = 'abc def gh';
		$expected = 'abc def gh';
		$result = String::SmallCamelCaseFromUnderscore($fixture);

		self::assertEquals ($expected, $result, 'The result of String::SmallCamelCaseFromUnderscore() is not as expected!');


		$fixture = 'abc_def gh';
		$expected = 'abcDef gh';
		$result = String::SmallCamelCaseFromUnderscore($fixture);

		self::assertEquals ($expected, $result, 'The result of String::SmallCamelCaseFromUnderscore() is not as expected!');


		$fixture = 'ABC_DEF GH_';
		$expected = 'abcDef gh';
		$result = String::SmallCamelCaseFromUnderscore($fixture);

		self::assertEquals ($expected, $result, 'The result of String::SmallCamelCaseFromUnderscore() is not as expected!');
	}


	public function test_WordsFromCamelCase()
	{
		$fixture = '';
		$expected = '';
		$result = String::WordsFromCamelCase($fixture);

		self::assertEquals ($expected, $result, 'The result of String::WordsFromCamelCase() is not as expected!');


		$fixture = 'abc';
		$expected = 'abc';
		$result = String::WordsFromCamelCase($fixture);

		self::assertEquals ($expected, $result, 'The result of String::WordsFromCamelCase() is not as expected!');


		$fixture = 'AbcDefGh';
		$expected = 'Abc Def Gh';
		$result = String::WordsFromCamelCase($fixture);

		self::assertEquals ($expected, $result, 'The result of String::WordsFromCamelCase() is not as expected!');


		$fixture = 'AbcDef Gh';
		$expected = 'Abc Def Gh';
		$result = String::WordsFromCamelCase($fixture);

		self::assertEquals ($expected, $result, 'The result of String::WordsFromCamelCase() is not as expected!');


		$fixture = 'AbcDef GhIjk';
		$expected = 'Abc Def Gh Ijk';
		$result = String::WordsFromCamelCase($fixture);

		self::assertEquals ($expected, $result, 'The result of String::WordsFromCamelCase() is not as expected!');
	}


	public function test_UnderscoreFromCamelCase()
	{
		$fixture = '';
		$expected = '';
		$result = String::UnderscoreFromCamelCase($fixture);

		self::assertEquals ($expected, $result, 'The result of String::UnderscoreFromCamelCase() is not as expected!');


		$fixture = 'abc';
		$expected = 'abc';
		$result = String::UnderscoreFromCamelCase($fixture);

		self::assertEquals ($expected, $result, 'The result of String::UnderscoreFromCamelCase() is not as expected!');


		$fixture = 'AbcDefGh';
		$expected = 'abc_def_gh';
		$result = String::UnderscoreFromCamelCase($fixture);

		self::assertEquals ($expected, $result, 'The result of String::UnderscoreFromCamelCase() is not as expected!');
	}
}

