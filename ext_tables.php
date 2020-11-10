<?php

defined('TYPO3_MODE') || die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $_EXTKEY,
    'lsfapiext',
    'LSF Data API'
);

//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['lsfdata'] = 'UniPotsdam\\Lsfapi\\Hook\\DataHandler';
