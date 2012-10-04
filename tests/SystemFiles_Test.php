<?php


namespace Cmp3\System;

/**
 *
 * @author	Andreas Schütte <schuette@bitmotion.de>
 */
class FileFiles_Test extends \TestCaseBase {




	/*********************************************************
	 *
	 * Systemfiles tests
	 *
	 *********************************************************/




	public function test_Basename()
	{
		$fixture = '';
		$expected = '';
		$result = Files::Basename($fixture);

		self::assertEquals($expected, $result, 'The result of Basename is not as expected!');


		$fixture ='C:/Temp/bpl11500.pdf';
		$expected = 'bpl11500.pdf';
		$result = Files::Basename($fixture);

		self::assertEquals($expected, $result, 'The result of Basename is not as expected!');


		$fixture = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$expected = 'testfile.txt';
		$result = Files::Basename($fixture);

		self::assertEquals($expected, $result, 'The result of Basename is not as expected!');
	}


	public function test_Dirname()
	{
		$fixture = '';
		$expected = '';
		$result = Files::Dirname($fixture);

		self::assertEquals($expected, $result, 'The result of Dirname is not as expected!');


		$fixture ='C:/Temp/bpl11500.pdf';
		$expected = 'C:/Temp/';
		$result = Files::Dirname($fixture);

		self::assertEquals($expected, $result, 'The result of Dirname is not as expected!');


		$fixture =PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$expected = PATH_fixture . 'systemfiles_testfiles/';
		$result = Files::Dirname($fixture);

		self::assertEquals($expected, $result, 'The result of Dirname is not as expected!');
	}


	public function test_isIndexed()
	{
		#TODO test DAM

		$fixture = '';
		$result = Files::isIndexed($fixture);

		self::assertFalse($result, 'The result of isIndexed is not as expected!');


		$fixture = 'abc';
		$result = Files::isIndexed($fixture);

		self::assertFalse($result, 'The result of isIndexed is not as expected!');


		$fixture = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$result = Files::isIndexed($fixture);

		self::assertFalse($result, 'The result of isIndexed is not as expected!');
	}


	public function test_resolvepath()
	{
		#TODO what do we expect here?

#		$fixture = 'index.php';
#		$expected = PATH_site.'index.php';
#		$result = Files::ResolvePath($fixture);

#		self::assertEquals($expected, $result, 'The result of ResolvePath is not as expected!');


		$fixture = PATH_site.'index.php';
		$expected = PATH_site.'index.php';
		$result = Files::ResolvePath($fixture);

		self::assertEquals($expected, $result, 'The result of ResolvePath is not as expected!');


		$fixture = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$expected = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$result = Files::ResolvePath($fixture);

		self::assertEquals($expected, $result, 'The result of ResolvePath is not as expected!');
	}


	public function test_normalizepath()
	{
		$fixture = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$expected = PATH_fixtureSitePath . 'systemfiles_testfiles/testfile.txt';
		$result = Files::NormalizePath($fixture);

		self::assertEquals($expected, $result, 'The result of NormalizePath is not as expected!');


		$fixture = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$expected = PATH_fixtureSitePath . 'systemfiles_testfiles/testfile.txt';
		$result = Files::NormalizePath($fixture);

		self::assertEquals($expected, $result, 'The result of NormalizePath is not as expected!');


		$fixture = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$expected = PATH_fixtureSitePath . 'systemfiles_testfiles/testfile.txt';
		$result = Files::NormalizePath($fixture);

		self::assertEquals($expected, $result, 'The result of NormalizePath is not as expected!');
	}


	public function test_makefilepathabsolute()
	{
		$fixture = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$expected = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$result = Files::MakeFilePathAbsolute($fixture);

		self::assertEquals($expected, $result, 'The result of MakeFilePathAbsolute is not as expected!');


		$fixture = array('file_name' => 'testfile.txt', 'file_path_absolute' => PATH_fixture . 'systemfiles_testfiles/');
		$expected = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$result = Files::MakeFilePathAbsolute($fixture);

		self::assertEquals($expected, $result, 'The result of MakeFilePathAbsolute is not as expected!');


		$fixture = array('file_name' => 'testfile.txt', 'file_path' => PATH_fixture . 'systemfiles_testfiles/');
		$expected = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$result = Files::MakeFilePathAbsolute($fixture);

		self::assertEquals($expected, $result, 'The result of MakeFilePathAbsolute is not as expected!');
	}


	public function test_makefilepathwebrelative()
	{
		#TODO what do we expect here?
#		$fixture = '';
#		$expected = '';
#		$result = Files::MakeFilePathWebRelative($fixture);

#		self::assertEquals($expected, $result, 'The result of MakeFilePathWebRelative is not as expected!');


		$fixture = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$expected = PATH_fixtureSitePath . 'systemfiles_testfiles/testfile.txt';
		$result = Files::MakeFilePathWebRelative($fixture);

		self::assertEquals($expected, $result, 'The result of MakeFilePathWebRelative is not as expected!');


		$fixture = PATH_site.'index.php';
		$expected = 'index.php';
		$result = Files::MakeFilePathWebRelative($fixture);

		self::assertEquals($expected, $result, 'The result of MakeFilePathWebRelative is not as expected!');

#FIXME
#		$fixture = '/var/www/schuette/notexistingpath';
#		$expected = '/var/www/schuette/notexistingpath';
#		$result = Files::MakeFilePathWebRelative($fixture);

#		self::assertEquals($expected, $result, 'The result of MakeFilePathWebRelative is not as expected!');


		$fixture = array('file_name' => 'testfile.txt', 'file_path_absolute' => PATH_fixture . 'systemfiles_testfiles/');
		$expected = PATH_fixtureSitePath . 'systemfiles_testfiles/testfile.txt';
		$result = Files::MakeFilePathWebRelative($fixture);

		self::assertEquals($expected, $result, 'The result of MakeFilePathWebRelative is not as expected!');


		$fixture = array('file_name' => 'testfile.txt', 'file_path' => PATH_fixture . 'systemfiles_testfiles/');
		$expected = PATH_fixtureSitePath . 'systemfiles_testfiles/testfile.txt';
		$result = Files::MakeFilePathWebRelative($fixture);

		self::assertEquals($expected, $result, 'The result of MakeFilePathWebRelative is not as expected!');
	}


	public function test_makefilenameclean()
	{
		$fixture = 'index@/@|@?@"@*@:@<@>.php';
		$expected = 'index@_@_@_@_@_@_@_@_.php';
		$result = Files::MakeFileNameClean($fixture);

		self::assertEquals($expected, $result, 'The result of MakeFileNameClean is not as expected!');

		#TODO does that make sense?
		$fixture = '.';
		$expected = '._';
		$result = Files::MakeFileNameClean($fixture);

		self::assertEquals($expected, $result, 'The result of MakeFileNameClean is not as expected!');


		$fixture = '..';
		$expected = '.._';
		$result = Files::MakeFileNameClean($fixture);

		self::assertEquals($expected, $result, 'The result of MakeFileNameClean is not as expected!');


		//['TYPO3_CONF_VARS']['SYS']['maxFileNameLength'] == 60
		$fixture = 'verylongfilenamewithmorethensixtychars_verylongfilenamewithmorethensixtychars.dat';
		$expected = 'verylongfilenamewithmorethensixtychars_verylongfilenamew.dat';
		$result = Files::MakeFileNameClean($fixture, true);

		self::assertEquals($expected, $result, 'The result of MakeFileNameClean is not as expected!');


		//['TYPO3_CONF_VARS']['SYS']['maxFileNameLength'] == 60
		$fixture = 'verylongfilenamewithmorethensixtychars_verylongfilenamewithmorethensixtychars.dat';
		$expected = 'verylongfilenamewithmorethensixtychars_verylongfilenamewithmorethensixtychars.dat';
		$result = Files::MakeFileNameClean($fixture, false);

		self::assertEquals($expected, $result, 'The result of MakeFileNameClean is not as expected!');
	}


	public function test_pathbasename()
	{
		$fixture = '';
		$expected = '';
		$result = Files::PathBasename($fixture);

		self::assertEquals($expected, $result, 'The result of PathBasename is not as expected!');


		$fixture = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$expected = 'testfile.txt';
		$result = Files::PathBasename($fixture);

		self::assertEquals($expected, $result, 'The result of PathBasename is not as expected!');


		$fixture = PATH_fixture . 'systemfiles_testfiles/';
		$expected = 'systemfiles_testfiles';
		$result = Files::PathBasename($fixture);

		self::assertEquals($expected, $result, 'The result of PathBasename is not as expected!');


		$fixture = PATH_fixture . 'systemfiles_testfiles';
		$expected = 'systemfiles_testfiles';
		$result = Files::PathBasename($fixture);

		self::assertEquals($expected, $result, 'The result of PathBasename is not as expected!');
	}

	//@todo
	public function test_makepathrelative()
	{
		#TODO what do we expect here?
#		$fixture = '';
#		$expected = '';
#		$result = Files::MakePathRelative($fixture);

#		self::assertEquals($expected, $result, 'The result of MakePathRelative is not as expected!');


		$fixture = PATH_fixture . 'systemfiles_testfiles/';
		$expected = PATH_fixtureSitePath . 'systemfiles_testfiles/';
		$result = Files::MakePathRelative($fixture);

		self::assertEquals($expected, $result, 'The result of MakePathRelative is not as expected!');
	}


	public function test_makepathabsolute()
	{
		$fixture = '';
		$expected = '';
		$result = Files::MakePathAbsolute($fixture);

		self::assertEquals($expected, $result, 'The result of MakePathAbsolute is not as expected!');


		$fixture = array('dir_name' => 'schuette', 'dir_path_absolute' => PATH_fixture . 'systemfiles_testfiles/');
		$expected = PATH_fixture . 'systemfiles_testfiles/';
		$result = Files::MakePathAbsolute($fixture);

		self::assertEquals($expected, $result, 'The result of MakePathAbsolute is not as expected!');


		$fixture = array('file_path_absolute' => PATH_fixture . 'systemfiles_testfiles/');
		$expected = PATH_fixture . 'systemfiles_testfiles/';
		$result = Files::MakePathAbsolute($fixture);

		self::assertEquals($expected, $result, 'The result of MakePathAbsolute is not as expected!');


		$fixture = array('file_path' => PATH_fixture . 'systemfiles_testfiles/');
		$expected = PATH_fixture . 'systemfiles_testfiles/';
		$result = Files::MakePathAbsolute($fixture);

		self::assertEquals($expected, $result, 'The result of MakePathAbsolute is not as expected!');
	}


	public function test_makepathclean()
	{
		$fixture = '';
		$expected = '';
		$result = Files::MakePathClean($fixture);

		self::assertEquals($expected, $result, 'The result of MakePathClean is not as expected!');


		$fixture = '/var/www/schuette/nextTesting/ext/next_tests/tests/fixtures/systemfiles_testfiles';
		$expected = '/var/www/schuette/nextTesting/ext/next_tests/tests/fixtures/systemfiles_testfiles/';
		$result = Files::MakePathClean($fixture);

		self::assertEquals($expected, $result, 'The result of MakePathClean is not as expected!');


		$fixture = '/var/www/schuette/nextTesting/ext/next_tests/./';
		$expected = '/var/www/schuette/nextTesting/ext/next_tests/';
		$result = Files::MakePathClean($fixture);

		self::assertEquals($expected, $result, 'The result of MakePathClean is not as expected!');


		$fixture = '/var//www//schuette//nextTesting//ext//next_tests/./../';
		$expected = '/var/www/schuette/nextTesting/ext/';
		$result = Files::MakePathClean($fixture);

		self::assertEquals($expected, $result, 'The result of MakePathClean is not as expected!');
	}


//	public function test_compilepathinfo()
//	{
//		$fixture = '';
//		$expected = '';
//		$result = Files::CompilePathInfo($fixture);
//
//		self::assertFalse($result, 'The result of CompilePathInfo is not as expected!');
//
//
//		$fixture = PATH_fixture . 'systemfiles_testfiles/';
//		//$fixture = 'var/www/schuette/nextTesting/ext/next_tests';
//		$expected = array(
//						'__type' => 'dir',
//					    '__exists' => '1',
//					    '__protected' => '',
//					    '__protected_type' => '',
//					    'dir_ctime' => filectime($fixture),
//					    'dir_mtime' => filemtime($fixture),
//					    'dir_size' => filesize($fixture),
//					    'dir_type' => 'dir',
//					    'dir_owner' => fileowner($fixture),
//					    'dir_perms' => fileperms($fixture),
//					    'dir_writable' => '1',
//					    'dir_readable' => '1',
//					    'mount_id' => '',
//					    'mount_path' => null,
//					    'mount_name' => '',
//					    'mount_type' => '',
//					    'web_nonweb' => 'web',
//					    'web_sys' => 'web',
//					    'dir_accessable' => '',
//					    'dir_name' => 'systemfiles_testfiles',
//					    'dir_title' => 'systemfiles_testfiles',
//					    'dir_path_absolute' => $fixture,
//					    'dir_path_relative' => PATH_fixture . 'systemfiles_testfiles/',
//					    'dir_path' => PATH_fixture . 'systemfiles_testfiles/',
//					    'dir_path_normalized' => PATH_fixture . 'systemfiles_testfiles/',
//					    'dir_path_from_mount' => PATH_fixture . 'systemfiles_testfiles/'
//				    ); //end of array
//		$result = Files::CompilePathInfo($fixture);
//
//		print '<pre>';
//		print_r ($result);
//		print '</pre>';
//		print '<pre>';
//		print_r ($expected);
//		print '</pre>';
//		print 'result'.$result['__protected_type'];
//		print '<br />';
//		debug(_highlight_control_chars  ($result['mount_path']));
//		debug(_highlight_control_chars  ($expected['mount_path']));
//
//		self::assertEquals($expected, $result, 'The result of CompilePathInfo is not as expected!');
//	}


	public function test_getfile()
	{
		$fixture = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$expected = new \Cmp3\Files\File($fixture);
		$result = Files::GetFile($fixture);

		self::assertEquals($expected, $result, 'The result of GetFile is not as expected!');

	}


	public function test_findfileinpath()
	{
#TODO 		FindFileInPath returns file name instead of content now
		$fixture_filename = 'testfile.txt';
		$fixture_path = PATH_fixture . 'systemfiles_testfiles/';
		$expected = $fixture_path.$fixture_filename;
		$result = Files::FindFileInPath($fixture_filename, $fixture_path);

		self::assertEquals($expected, $result, 'The result of FindFileInPath is not as expected!');


		$fixture_filename = 'notexisting.txt';
		$fixture_path = PATH_fixture . 'systemfiles_testfiles/';
		$result = Files::FindFileInPath($fixture_filename, $fixture_path);

		self::assertFalse($result, 'The result of FindFileInPath is not as expected!');


		$fixture_filename = 'testfile.txt';
		$fixture_path = PATH_fixture . 'systemfiles_testfiles/';
		$expected = $fixture_path.$fixture_filename;
		$result = Files::FindFileInPath($fixture_filename, $fixture_path);

		self::assertEquals($expected, $result, 'The result of FindFileInPath is not as expected!');
	}


	public function test_calchash()
	{
		$fixture = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$expected = md5_file($fixture);
		$result = Files::CalcHash($fixture);

		self::assertEquals($expected, $result, 'The result of CalcHash is not as expected!');


		$fixture = array('file_name' => 'testfile.txt', 'file_path_absolute' => PATH_fixture . 'systemfiles_testfiles/');
		$expected = md5_file(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$result = Files::CalcHash($fixture);

		self::assertEquals($expected, $result, 'The result of CalcHash is not as expected!');


		$fixture = array('file_name' => 'testfile.txt', 'file_path' => PATH_fixture . 'systemfiles_testfiles/');
		$expected = md5_file(PATH_fixture . 'systemfiles_testfiles/testfile.txt');
		$result = Files::CalcHash($fixture);

		self::assertEquals($expected, $result, 'The result of CalcHash is not as expected!');
	}





	public function test_maketitlefromfilename()
	{
		$fixture = '';
		$expected = '';
		$result = Files::MakeTitleFromFilename($fixture);

		self::assertEquals($expected, $result, 'The result of MakeTitleFromFilename is not as expected!');


		$fixture = 'testfile.txt';
		$expected = 'testfile';
		$result = Files::MakeTitleFromFilename($fixture);

		self::assertEquals($expected, $result, 'The result of MakeTitleFromFilename is not as expected!');


		$fixture = 'test_file.txt';
		$expected = 'test file';
		$result = Files::MakeTitleFromFilename($fixture);

		self::assertEquals($expected, $result, 'The result of MakeTitleFromFilename is not as expected!');


		$fixture = 'testFile.txt';
		$expected = 'test File';
		$result = Files::MakeTitleFromFilename($fixture);

		self::assertEquals($expected, $result, 'The result of MakeTitleFromFilename is not as expected!');


		$fixture = 'test%20file.txt';
		$expected = 'test file';
		$result = Files::MakeTitleFromFilename($fixture);

		self::assertEquals($expected, $result, 'The result of MakeTitleFromFilename is not as expected!');
	}

#TODO
	public function zzz_test_detectfilemimetype()
	{
		$fixture = PATH_fixture . 'systemfiles_testfiles/testfile.txt';
		$expected = array(
						'mime_type' => 'text/plain',
						'mime_basetype' => 'text',
						'mime_subtype' => 'plain',
						'file_type' => 'txt',
						'media_type' => '1',
						'media_type_string' => 'text'
					);
		$result = Files::DetectFileMimeType($fixture);

		self::assertEquals($expected, $result, 'The result of DetectFileMimeType is not as expected!');


		$fixture = PATH_fixture . 'systemfiles_testfiles/testimage.bmp';
		$expected = array(
						'mime_type' => 'image/x-ms-bmp',
						'mime_basetype' => 'image',
						'mime_subtype' => 'x-ms-bmp',
						'file_type' => 'bmp',
						'media_type' => '2',
						'media_type_string' => 'image'
					);
		$result = Files::DetectFileMimeType($fixture);

		self::assertEquals($expected, $result, 'The result of DetectFileMimeType is not as expected!');


		$fixture = PATH_fixture . 'systemfiles_testfiles/testaudio.wav';
		$expected = array(
						'mime_type' => 'audio/x-wav',
						'mime_basetype' => 'audio',
						'mime_subtype' => 'x-wav',
						'file_type' => 'wav',
						'media_type' => '3',
						'media_type_string' => 'audio'
					);
		$result = Files::DetectFileMimeType($fixture);

		self::assertEquals($expected, $result, 'The result of DetectFileMimeType is not as expected!');


		$fixture = PATH_fixture . 'systemfiles_testfiles/arial.ttf';
		$expected = array(
						'mime_type' => 'application/octet-stream',
						'mime_basetype' => 'application',
						'mime_subtype' => 'octet-stream',
						'file_type' => 'ttf',
					);
		$result = Files::DetectFileMimeType($fixture);

		self::assertEquals($expected, $result, 'The result of DetectFileMimeType is not as expected!');
	}

#TODO
	public function zzz_test_getmimetypeforsuffix()
	{
//		print '<pre>';
//		print_r ($GLOBALS['T3_VAR']['ext']['dam']['file2mediaCode']);
//		print '</pre>';


		$fixture = '';
		$expected = 'application/octet-stream';
		$result = Files::GetMimeTypeForSuffix($fixture);

		self::assertEquals($expected, $result, 'The result of GetMimeTypeForSuffix is not as expected!');


		$fixture = 'rar';
		$expected = 'application/octet-stream';
		$result = Files::GetMimeTypeForSuffix($fixture);

		self::assertEquals($expected, $result, 'The result of GetMimeTypeForSuffix is not as expected!');


		$fixture = 'pdf';
		$expected = 'application/pdf';
		$result = Files::GetMimeTypeForSuffix($fixture);

		self::assertEquals($expected, $result, 'The result of GetMimeTypeForSuffix is not as expected!');


		$fixture = '3ds';
		$expected = 'application/octet-stream';
		$result = Files::GetMimeTypeForSuffix($fixture);

		self::assertEquals($expected, $result, 'The result of GetMimeTypeForSuffix is not as expected!');


		$fixture = 'gtar';
		$expected = 'application/x-gtar';
		$result = Files::GetMimeTypeForSuffix($fixture);

		self::assertEquals($expected, $result, 'The result of GetMimeTypeForSuffix is not as expected!');


		$fixture = 'pm';
		$expected = 'application/x-perl';
		$result = Files::GetMimeTypeForSuffix($fixture);

		self::assertEquals($expected, $result, 'The result of GetMimeTypeForSuffix is not as expected!');

	}
}

