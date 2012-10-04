<?php


abstract class TestCaseBase extends PHPUnit_Framework_TestCase {


	/**
	 * @var boolean
	 */
	protected $backupGlobals = false;

	/**
	 * @var boolean
	 */
	protected $backupStaticAttributes = false;


	protected function GetLogger()
	{
		$strJobID = number_format(microtime(true), 2, '', '');
		$objLogWriter = new \Zend_Log_Writer_Stream(\Cmp3\Cmp3::$LogPath . $strJobID . '.log');
		$objLog = new \Cmp3\Log\Logger($objLogWriter);
		$objLog->SetJobID($strJobID);

		return $objLog;
	}






	public function GetDummyText ($intMaxLength=45, $strPrefix='')
	{
		static $strWordList;
		static $strWordListCount;

		if (!$strWordList) {
			include(PATH_fixture . 'wordlist.php');
			$strWordListCount = count($strWordList);
		}

		$strText = $strPrefix;
		$intLength = strlen($strText);

		while ($intLength < $intMaxLength) {
			$strNewWord = $strWordList[(rand(0, $strWordListCount-1))];
			$intNewWordLength = strlen($strNewWord) + 1;
			if (($intLength+$intNewWordLength) > $intMaxLength) {
				break;
			}
			$intLength += $intNewWordLength;
			$strText .= ' ' . $strNewWord;
		}

		return ucfirst(trim($strText));
	}




//------------------------------

	public function MakeWordList ()
	{
		$strWordList = explode("\n", file_get_contents(PATH_fixture . 'wordlist.txt'));
		$strWordListCount = count($strWordList);


		$out = <<<EOD
<?php

\$strWordList = array(

EOD;

		for ($i = 0; $i < 2000; $i++) {

			$out .= "'" . addslashes($strWordList[(rand(0, $strWordListCount-1))]) . "',\n";
		}
		$out .= ");\n\n";
		file_put_contents(PATH_fixture . 'wordlist.php', $out);
	}


	public function GetDummyText2 ($intMaxLength=45, $strPrefix='')
	{
		static $strWordList;
		static $strWordListCount;

		if (!$strWordList) {
			$strWordList = explode("\n", file_get_contents(PATH_fixture . 'wordlist.txt'));
			$strWordListCount = count($strWordList);
		}

		$strText = $strPrefix;
		$intLength = strlen($strText);

		while ($intLength < $intMaxLength) {
			$strNewWord = $strWordList[(rand(0, $strWordListCount-1))];
			$intNewWordLength = strlen($strNewWord) + 1;
			if (($intLength+$intNewWordLength) > $intMaxLength) {
				break;
			}
			$intLength += $intNewWordLength;
			$strText .= ' ' . $strNewWord;
		}

		return ucfirst(trim($strText));
	}



}




