<?php

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE) {
	if (!defined ('PATH_cmp3')) {
		define('PATH_cmp3', t3lib_extMgm::extPath('cmp3'));
	}

	if (!defined ('PATH_cmp3_siteRel')) {
		define('PATH_cmp3_siteRel', t3lib_extMgm::siteRelPath('cmp3'));
	}

} else {
	if (!defined ('PATH_cmp3')) {
		define('PATH_cmp3', dirname(__FILE__).'/');
	}

	if (!defined ('PATH_cmp3_siteRel')) {
		define('PATH_cmp3_siteRel', preg_replace('#^' . preg_quote(PATH_site) . '#', '', PATH_cmp3));
	}
}


if (!class_exists('\Cmp3\Autoload', false)) {

	#require_once(PATH_cmp3.'next/ext_localconf.php');

	include_once(PATH_cmp3.'Library/public_functions.php');

	// we don't use autoloader here because the autloader might be initialized with wrong path - if so the whole system might be broken
	require_once(PATH_cmp3.'Library/Typo3/TcaTools.php');
	require_once(PATH_cmp3.'Library/System/System.php');
	\tx_cmp3::RegisterPath('cmp3', PATH_cmp3);

	require_once(PATH_cmp3.'Library/Autoloader.php');

	\Cmp3\Autoloader::SetBasePath();
	\Cmp3\Autoloader::RegisterAutoload();
	// initialize library paths - needed for Zend stuff
	\Cmp3\Autoloader::AddIncludePath(PATH_cmp3.'Library/');

	\Cmp3\Autoloader::RegisterFile('TestCaseBase', PATH_cmp3.'/tests/TestCaseBase.php');


	// include autoloading registry
	include(PATH_cmp3.'autoload-setup.php');

	\tx_cmp3::Init();
}




if (TYPO3_MODE) {
	include(PATH_cmp3.'system/typo3/ext_localconf.php');
}




?>