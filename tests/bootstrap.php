<?php



#phpinfo();


error_reporting(E_ALL ^ E_NOTICE);

// this is needed - it seems we're not running in global context
global $TYPO3_CONF_VARS;
global $TYPO3_LOADED_EXT;
global $TT;
global $typo_db;
global $typo_db_username;
global $typo_db_password;
global $typo_db_host;
global $TYPO3_DB;
global $BE_USER;
global $MCONF;
global $BACK_PATH;
global $EXEC_TIME;
global $SIM_ACCESS_TIME;
global $PAGES_TYPES;
global $TCA;
global $_EXTKEY;

$strExtensionPath = realpath(__DIR__ . '/../').'/';
$_EXTKEY = basename($strExtensionPath);

// configure module
define('TYPO3_MOD_PATH', "../typo3conf/ext/$_EXTKEY/" . basename(__DIR__) . '/');
$BACK_PATH='../../../../typo3/';

$MCONF['name']='_CLI_txcmp3Mtests';
$MCONF['shy']=true;
$MCONF['access']='user,group';

// Defining circumstances for CLI mode:
define('TYPO3_cliMode', TRUE);

#define ('TYPO3_PROCEED_IF_NO_USER', true);


// Defining PATH_thisScript here: Must be the ABSOLUTE path of this script
define('PATH_thisScript',__FILE__);


chdir(__DIR__);

// initialize module
#require(dirname(PATH_thisScript).'/'.$BACK_PATH.'init.php');
# this will not work because we're not in global scope and need to patch init.php

require_once('init.php');


chdir(__DIR__);

// define some test paths
define ('PATH_fixture', __DIR__.'/fixture/');
define ('PATH_fixtureSitePath', t3lib_extMgm::siteRelPath('cmp3') . 'tests/fixture/');
define ('PATH_output', __DIR__.'/output/');



#echo __DIR__."\n";
#echo $strExtensionPath."\n";
#echo $_EXTKEY."\n";


require_once('helper.php');

#include('TestCaseBase.php');
\Cmp3\Autoloader::RegisterFile('TestCaseBase', __DIR__.'/TestCaseBase.php');


$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;

error_log(__FILE__ . __LINE__);


