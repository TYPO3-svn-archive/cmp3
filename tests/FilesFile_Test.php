<?php


namespace Cmp3\Files;

/**
 *
 * @author	Andreas Schütte <schuette@bitmotion.de>
 * @author	Rene Fritz <r.fritz@bitmotion.de>
 */
class File_Test extends \TestCaseBase {



	/*********************************************************
	 *
	 * Files tests
	 *
	 *********************************************************/


#TODO test __get(), __set()


	public function test_FieldExists()
	{
		$field = array('dataarray',
						'dataobject',
						'dataraw',
						'datafields',
						'hash',
						'status',
						'name',
						'downloadname',
						'title',
						'absolutepath',
						'path',
						'relativepath',
						'absolutedirname',
						'dirname',
						'relativedirname',
						'mtime',
						'tstamp',
						'ctime',
						'crdate',
						'inode',
						'size',
						'owner',
						'perms',
						'iswritable',
						'writable',
						'isreadable',
						'readable',
						'hidden',
						'deleted',
						'mimetype',
						'mimebasetype',
						'mimesubtype',
						'mediatype',
						'type',
						'extension',
						'suffix',
						'Hash',
						'Status',
						'Name',
						'DownloadName',
						'Title',
						'AbsolutePath',
						'Path',
						'RelativePath',
						'AbsoluteDirname',
						'Dirname',
						'RelativeDirname',
						'Mtime',
						'Tstamp',
						'Ctime',
						'Crdate',
						'Inode',
						'Size',
						'Owner',
						'Perms',
						'isWritable',
						'Writable',
						'isReadable',
						'Readable',
						'Hidden',
						'Deleted',
						'MimeType',
						'MimeBasetype',
						'MimeSubtype',
						'MediaType',
						'Type',
						'Extension',
						'Suffix',
				);  //end of array

		foreach ($field as $value)
		{
			$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt', array('testvalue'));
			$result = $fixture->FieldExists($value);

			self::assertTrue($result, 'The result of File::FieldExists is not as expected!');
		}

	}


	public function test_Exists()
	{
		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$result = $fixture->Exists();

		self::assertTrue($result, 'The result of File::Exists is not as expected!');


		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$result = $fixture->Exists();

		self::assertTrue($result, 'The result of File::Exists is not as expected!');


		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/notexistingfile.dat');
		$result = $fixture->Exists();

		self::assertFalse($result, 'The result of File::Exists is not as expected!');
	}


	public function test_Getid()
	{
		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$expected = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$result = $fixture->GetID();

		self::assertEquals($expected, $result, 'The result of File::GetID is not as expected!');


		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$expected = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$result = $fixture->GetID();

		self::assertEquals($expected, $result, 'The result of File::GetID is not as expected!');


		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/notexistingfile.dat');
		$expected = PATH_fixture . 'systemfiles_testfiles/notexistingfile.dat';
		$result = $fixture->GetID();

		self::assertEquals($expected, $result, 'The result of File::GetID is not as expected!');
	}


	public function test_Gethash()
	{
		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$expected = md5_file(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$result = $fixture->GetHash();

		self::assertEquals($expected, $result, 'The result of File::GetHash is not as expected!');


		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$expected = md5_file(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$result = $fixture->GetHash();

		self::assertEquals($expected, $result, 'The result of File::GetHash is not as expected!');
	}


	public function test_Gettype()
	{
		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$expected = 'txt';
		$result = $fixture->GetType();

		self::assertEquals($expected, $result, 'The result of File::GetType is not as expected!');


		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testimage.bmp');
		$expected = 'bmp';
		$result = $fixture->GetType();

		self::assertEquals($expected, $result, 'The result of File::GetType is not as expected!');


		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testaudio.wav');
		$expected = 'wav';
		$result = $fixture->GetType();

		self::assertEquals($expected, $result, 'The result of File::GetType is not as expected!');
	}


	public function test_Getmimetype()
	{
		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$expected = 'text/plain';
		$result = $fixture->GetMimeType();

		self::assertEquals($expected, $result, 'The result of File::GetMimeType is not as expected!');


		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testimage.bmp');
		$expected = 'image/x-ms-bmp';
		$result = $fixture->GetMimeType();

		self::assertEquals($expected, $result, 'The result of File::GetMimeType is not as expected!');


		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testaudio.wav');
		$expected = 'audio/x-wav';
		$result = $fixture->GetMimeType();

		self::assertEquals($expected, $result, 'The result of File::GetMimeType is not as expected!');
	}


	public function test_Getdownloadname()
	{
		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$expected = 'testfile.txt';
		$result = $fixture->GetDownloadName();

		self::assertEquals($expected, $result, 'The result of File::GetDownloadName is not as expected!');


		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testimage.bmp');
		$expected = 'testimage.bmp';
		$result = $fixture->GetDownloadName();

		self::assertEquals($expected, $result, 'The result of File::GetDownloadName is not as expected!');
	}


	public function test_Getpathwebrelative()
	{
		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$expected = PATH_fixtureSitePath . 'systemfiles_testfiles/testfile.txt';
		$result = $fixture->GetPathWebRelative();

		self::assertEquals($expected, $result, 'The result of File::GetPathWebRelative is not as expected!');


		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$expected = PATH_fixtureSitePath . 'systemfiles_testfiles/testfile.txt';
		$result = $fixture->GetPathWebRelative();

		self::assertEquals($expected, $result, 'The result of File::GetPathWebRelative is not as expected!');
	}


	public function test_Getpathabsolute()
	{
		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$expected = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$result = $fixture->GetPathAbsolute();

		self::assertEquals($expected, $result, 'The result of File::GetPathAbsolute is not as expected!');


		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$expected = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$result = $fixture->GetPathAbsolute();

		self::assertEquals($expected, $result, 'The result of File::GetPathAbsolute is not as expected!');

	}

#FIXME
	public function zzz_test_Geturl()
	{
		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$expected = $this->fixtureBaseUrl . PATH_fixtureSitePath . 'systemfiles_testfiles/testfile.txt';
		$result = $fixture->GetURL();

		self::assertEquals($expected, $result, 'The result of File::GetURL is not as expected!');


		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$expected = $this->fixtureBaseUrl . PATH_fixtureSitePath . 'systemfiles_testfiles/testfile.txt';
		$result = $fixture->GetURL();

		self::assertEquals($expected, $result, 'The result of File::GetURL is not as expected!');
	}

	#FIXME
	public function zzz_test_Getdownloadurl()
	{
		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$expected = $this->fixtureBaseUrl . PATH_fixtureSitePath . 'systemfiles_testfiles/testfile.txt';
		$result = $fixture->GetDownloadURL();

		self::assertEquals($expected, $result, 'The result of File::GetDownloadURL is not as expected!');


		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$expected = $this->fixtureBaseUrl . PATH_fixtureSitePath . 'systemfiles_testfiles/testfile.txt';
		$result = $fixture->GetDownloadURL();

		self::assertEquals($expected, $result, 'The result of File::GetDownloadURL is not as expected!');
	}

#TODO test FileDownload


//	public function test_download()
//	{
//		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
//		$expected = new FileDownload;
//		$result = $fixture->Download();
//
//		self::assertEquals($expected, $result, 'The result of File::Download is not as expected!');
//	}



	public function test_Touch()
	{
		$fixture = PATH_output . 'systemfiles_testfiles.txt';
		@unlink($fixture);
		$objFile = new File(PATH_output . 'systemfiles_testfiles.txt');
		$result = $objFile->Touch();


		if ($objFile->GetLastErrorCode()) {
			echo $objFile->GetLastErrorMessage().'<br />';
		}

		self::assertTrue($result, 'The result of File::Touch is not as expected!');

		self::assertTrue(file_exists($fixture), 'The result of File::Touch is not as expected!');

		self::assertTrue(is_file($fixture), 'The result of File::Touch is not as expected!');


		$expected = 1237796558;
		$result = $objFile->Touch($expected);


		self::assertTrue($result, 'The result of File::Touch is not as expected!');

		$result = $objFile->mtime;

		self::assertEquals($expected, $result, 'The result of File::Touch is not as expected!');

		$expected = 123;
		$expected2 = 456;
		$result = $objFile->Touch($expected, $expected2);

		self::assertTrue($result, 'The result of File::Touch is not as expected!');

		$result = $objFile->mtime;
		self::assertEquals($expected, $result, 'The result of File::Touch is not as expected!');
		$result = $objFile->atime;
		if ($result > 0) // atime doesn't work on all (file)systems
			self::assertEquals($expected2, $result, 'The result of File::Touch is not as expected!');

	}


	public function test_Delete()
	{
		$fixture = PATH_output . 'systemfiles_testfiles.txt';
		@unlink($fixture);
		$objFile = new File(PATH_output . 'systemfiles_testfiles.txt');
		$result = $objFile->Touch();


		$result = $objFile->Delete();

		if ($objFile->GetLastErrorCode()) {
			echo $objFile->GetLastErrorMessage().'<br />';
		}

		self::assertTrue($result, 'The result of File::Delete is not as expected!');

		$result = $objFile->Delete();

		self::assertFalse($result, 'The result of File::Delete is not as expected!');
	}


	public function test_Rename()
	{
		$fixture = PATH_output . 'systemfiles_testfiles.txt';
		@unlink($fixture);
		$objFile = new File($fixture);
		$result = $objFile->Touch();

		$expected = 'systemfiles_testfiles.png';
		$result = $objFile->Rename($expected);

		if ($objFile->GetLastErrorCode()) {
			echo $objFile->GetLastErrorMessage().'<br />';
		}

		self::assertTrue($result, 'The result of File::Rename is not as expected!');


		$result = $objFile->AbsolutePath;
		self::assertEquals(PATH_output . $expected, $result, 'The result of File::Rename is not as expected!');

		$result = $objFile->Suffix;
		self::assertEquals('png', $result, 'The result of File::Rename is not as expected!');


#FIXME
#		rmdir(PATH_output . 'bla');
#		$expected = 'bla/systemfiles_testfiles.png';
#		$result = $objFile->Rename($expected);

#		self::assertFalse($result, 'The result of File::Rename is not as expected!');


		mkdir(PATH_output . 'bla');
		$expected = PATH_output . 'bla/systemfiles_testfiles.png';
		$result = $objFile->Rename($expected);
		$objFile->Delete();
		rmdir(PATH_output . 'bla');

		self::assertTrue($result, 'The result of File::Rename is not as expected!');
	}


	public function test_Move()
	{
		$fixture = PATH_output . 'systemfiles_testfiles.txt';
		@unlink($fixture);
		$objFile = new File(PATH_output . 'systemfiles_testfiles.txt');
		$result = $objFile->Touch();

		$expected = 'systemfiles_testfiles.png';
		$result = $objFile->Move($expected);
		self::assertFalse($result, 'The result of File::Move is not as expected!');


		$expected = 'bla/systemfiles_testfiles.png';
		$result = $objFile->Move($expected);

		self::assertFalse($result, 'The result of File::Move is not as expected!');


		mkdir(PATH_output . 'bla');
		$expected = PATH_output . 'bla/';
		$result = $objFile->Move($expected);
		if ($objFile->GetLastErrorCode()) {
			echo $objFile->GetLastErrorMessage().'<br />';
		}
		$objFile->Delete();
		rmdir(PATH_output . 'bla');



		self::assertTrue($result, 'The result of File::Move is not as expected!');
	}


	public function test_Copy()
	{
		$fixture = PATH_output . 'systemfiles_testfiles.txt';
		@unlink($fixture);
		$objFile = new File(PATH_output . 'systemfiles_testfiles.txt');
		$result = $objFile->Touch();

		$expected = 'systemfiles_testfiles.png';
		@unlink(PATH_output . $expected);
		$result = $objFile->Copy($expected);

		if ($objFile->GetLastErrorCode()) {
			echo $objFile->GetLastErrorMessage().'<br />';
		}

		self::assertType('\Cmp3\Files\File', $result, 'The result of File::Copy is not as expected!');
		self::assertTrue(@is_file($fixture), 'The result of File::Copy is not as expected!');
		self::assertTrue(@is_file(PATH_output . $expected), 'The result of File::Copy is not as expected!');

		$result->Delete();


		@rmdir(PATH_output . 'tx_cmp3_bla');
		$expected = PATH_output . 'tx_cmp3_bla/systemfiles_testfiles.png';
		$result = $objFile->Copy($expected);

		self::assertFalse($result, 'The result of File::Copy is not as expected!');
		self::assertTrue(@is_file($fixture), 'The result of File::Copy is not as expected!');
		self::assertFalse(@is_file(PATH_output . $expected), 'The result of File::Copy is not as expected!');


		mkdir(PATH_output . 'tx_cmp3_bla');
		$expected = PATH_output . 'tx_cmp3_bla/';

		$result = $objFile->Copy($expected);

		self::assertType('\Cmp3\Files\File', $result, 'The result of File::Copy is not as expected!');
		self::assertEquals('systemfiles_testfiles.txt', $result->Name, 'The result of File::Copy is not as expected!');
		self::assertEquals(PATH_output . 'tx_cmp3_bla/', $result->AbsoluteDirname, 'The result of File::Copy is not as expected!');
		self::assertTrue(@is_file($fixture), 'The result of File::Copy is not as expected!');

		$result->Delete();

		$expected = PATH_output . 'tx_cmp3_bla/systemfiles_testfiles.png';
		$result = $objFile->Copy($expected);

		self::assertType('\Cmp3\Files\File', $result, 'The result of File::Copy is not as expected!');
		self::assertEquals('systemfiles_testfiles.png', $result->Name, 'The result of File::Copy is not as expected!');
		self::assertEquals(PATH_output . 'tx_cmp3_bla/', $result->AbsoluteDirname, 'The result of File::Copy is not as expected!');
		self::assertTrue(@is_file($fixture), 'The result of File::Copy is not as expected!');

		$result->Delete();

		$objFile->Delete();
		rmdir(PATH_output . 'tx_cmp3_bla');
	}



	#TODO ReadContent
	#TODO WriteContent



	public function test_Getsizeformatted()
	{
		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testaudio.wav');
		$expected = '45 KB';
		$result = $fixture->GetSizeFormatted();

		self::assertEquals($expected, $result, 'The result of File::GetSizeFormatted is not as expected!');


		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$expected = '42 B';
		$result = $fixture->GetSizeFormatted();

		self::assertEquals($expected, $result, 'The result of File::GetSizeFormatted is not as expected!');


		$fixture = new File(PATH_fixture . 'systemfiles_testfiles/testimage.bmp');
		$expected = '1.4 MB';
		$result = $fixture->GetSizeFormatted();

		self::assertEquals($expected, $result, 'The result of File::GetSizeFormatted is not as expected!');
	}
}