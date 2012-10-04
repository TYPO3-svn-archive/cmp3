<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE == 'BE')	{

	// add module after 'File'
	if (!isset($TBE_MODULES['txcmp3M1'])) {
		$temp_TBE_MODULES = array();
		foreach ($TBE_MODULES as $key => $val) {
			if ($key === 'file') {
				$temp_TBE_MODULES[$key] = $val;
				$temp_TBE_MODULES['txcmp3M1'] = $val;
			} else {
				$temp_TBE_MODULES[$key] = $val;
			}
		}
		$TBE_MODULES = $temp_TBE_MODULES;
		unset($temp_TBE_MODULES);
	}

	t3lib_extMgm::addModulePath('txcmp3M1', t3lib_extMgm::extPath($_EXTKEY) . 'mod_main/');


	# t3lib_extMgm::addModule('_CLI_txcmp3Mtests','','',t3lib_extMgm::extPath($_EXTKEY).'tests/');
}
?>