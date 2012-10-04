<?php


namespace Cmp3\String;

/**
 *
 * @author	Andreas Schütte <schuette@bitmotion.de>
 */
class StringHtml_Test extends \TestCaseBase {





	/*********************************************************
	 *
	 * HTML tests
	 *
	 *********************************************************/




	public function test_Escape()
	{
		$fixture = '';
		$expected = '';
		$result = HTML::Escape($fixture);

		self::assertEquals ($expected, $result, 'The result of HTML::Escape() is not as expected!');


		$fixture = 'abc';
		$expected = 'abc';
		$result = HTML::Escape($fixture);

		self::assertEquals ($expected, $result, 'The result of HTML::Escape() is not as expected!');


		$fixture = '<div><a href="http://www.google.de/search?q=htmlspecialchars&ie=utf-8">htmlspecialchars</a></div>';
		$expected = '&lt;div&gt;&lt;a href=&quot;http://www.google.de/search?q=htmlspecialchars&amp;ie=utf-8&quot;&gt;htmlspecialchars&lt;/a&gt;&lt;/div&gt;';
		$result = HTML::Escape($fixture);

		self::assertEquals ($expected, $result, 'The result of HTML::Escape() is not as expected!');
	}


	public function test_Tag()
	{
		$fixture = '';
		$expected = '';
		$result = HTML::Tag($fixture, 'border="0"', '');

		self::assertEquals ($expected, $result, 'The result of HTML::Tag() is not as expected!');


		$fixture = 'table';
		$expected = '';
		$result = HTML::Tag($fixture, 'border="0"', '');

		self::assertEquals ($expected, $result, 'The result of HTML::Tag() is not as expected!');

		$fixture = 'table';
		$expected = '<table border="0">htmlcode for table</table>';
		$result = HTML::Tag($fixture, 'border="0"', 'htmlcode for table');

		self::assertEquals ($expected, $result, 'The result of HTML::Tag() is not as expected!');


		$fixture = 'table';
		$expected = '<table border="0">&lt;tr&gt;&lt;td&gt;&lt;/td&gt;&lt;/tr&gt;</table>';
		$result = HTML::Tag($fixture, 'border="0"', '<tr><td></td></tr>');

		self::assertEquals ($expected, $result, 'The result of HTML::Tag() is not as expected!');


		$fixture = 'table';
		$expected = '<table border="0"><tr><td></td></tr></table>';
		$result = HTML::Tag($fixture, 'border="0"', '<tr><td></td></tr>', false);

		self::assertEquals ($expected, $result, 'The result of HTML::Tag() is not as expected!');
	}




	public function test_GetBodyContent()
	{
		$fixture = '';
		$result = HTML::GetBodyContent($fixture);

		self::assertFalse ($result, 'The result of HTML::GetBodyContent() is not as expected!');


		$fixture = '<body><p>bodytext</p>';
		$result = HTML::GetBodyContent($fixture);

		self::assertFalse ($result, 'The result of HTML::GetBodyContent() is not as expected!');


		$fixture = '<p>bodytext</p></body>';
		$result = HTML::GetBodyContent($fixture);

		self::assertFalse ($result, 'The result of HTML::GetBodyContent() is not as expected!');


		$fixture = '<body><p>bodytext</p></body>';
		$expected = '<p>bodytext</p>';
		$result = HTML::GetBodyContent($fixture);

		self::assertEquals ($expected, $result, 'The result of HTML::GetBodyContent() is not as expected!');


		$fixture = '<tag><body><p>bodytext</p></body></tag>';
		$expected = '<p>bodytext</p>';
		$result = HTML::GetBodyContent($fixture);

		self::assertEquals ($expected, $result, 'The result of HTML::GetBodyContent() is not as expected!');
	}


	public function test_ExplodeAttributes()
	{
		$fixture = '';
		$expected = array();
		$result = HTML::ExplodeAttributes($fixture);

		self::assertEquals ($expected, $result, 'The result of HTML::ExplodeAttributes() is not as expected!');


//		$fixture = array();
//		$expected = array();
//		$result = HTML::ExplodeAttributes($fixture);
//
//		self::assertEquals ($expected, $result, 'The result of HTML::ExplodeAttributes() is not as expected!');


		$fixture = '<table border="1">';
		$expected = array(border => "1");
		$result = HTML::ExplodeAttributes($fixture);

		self::assertEquals ($expected, $result, 'The result of HTML::ExplodeAttributes() is not as expected!');


		$fixture = '<table border="1"> <a href="www.google.de">';
		$expected = array(border => "1", href => "www.google.de");
		$result = HTML::ExplodeAttributes($fixture);

		self::assertEquals ($expected, $result, 'The result of HTML::ExplodeAttributes() is not as expected!');
	}


	public function test_ImplodeAttributes()
	{
		$fixture = '';
		$expected = '';
		$result = HTML::ImplodeAttributes($fixture);

		self::assertEquals ($expected, $result, 'The result of HTML::ImplodeAttributes() is not as expected!');


		$fixture = 'border="1"';
		$expected = 'border="1"';
		$result = HTML::ImplodeAttributes($fixture);

		self::assertEquals ($expected, $result, 'The result of HTML::ImplodeAttributes() is not as expected!');


		$fixture = 'border="1" href="www.google.de"';
		$expected = 'border="1" href="www.google.de"';
		$result = HTML::ImplodeAttributes($fixture);

		self::assertEquals ($expected, $result, 'The result of HTML::ImplodeAttributes() is not as expected!');


		$fixture = array('border' => '1');
		$expected = ' border="1"';
		$result = HTML::ImplodeAttributes($fixture);

		self::assertEquals ($expected, $result, 'The result of HTML::ImplodeAttributes() is not as expected!');


		$fixture = array('border' => '1', 'href' => 'www.google.de');
		$expected = ' border="1" href="www.google.de"';
		$result = HTML::ImplodeAttributes($fixture);

		self::assertEquals ($expected, $result, 'The result of HTML::ImplodeAttributes() is not as expected!');


		$fixture = array('border' => '1', 'href' => 'www.google.de', 'style' => '<tr><td></td></tr>');
		$expected = ' border="1" href="www.google.de" style="&lt;tr&gt;&lt;td&gt;&lt;/td&gt;&lt;/tr&gt;"';
		$result = HTML::ImplodeAttributes($fixture);

		self::assertEquals ($expected, $result, 'The result of HTML::ImplodeAttributes() is not as expected!');


		$fixture = array('border' => '1', 'href' => 'www.google.de', 'style' => '<tr><td></td></tr>');
		$expected = ' border="1" href="www.google.de" style="<tr><td></td></tr>"';
		$result = HTML::ImplodeAttributes($fixture, false);

		self::assertEquals ($expected, $result, 'The result of HTML::ImplodeAttributes() is not as expected!');
	}
}