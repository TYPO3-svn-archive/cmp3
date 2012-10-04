<?php

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


// Register task
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_cmp3_task'] = array(
		'extension'        => $_EXTKEY,
		'title'            => 'LLL:EXT:' . $_EXTKEY . '/locallang.xml:tx_cmp3_task.name',
		'description'      => 'LLL:EXT:' . $_EXTKEY . '/locallang.xml:tx_cmp3_task.description',
		'additionalFields' => '',
);

?>