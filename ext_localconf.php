<?php
defined('TYPO3_MODE') || die();



$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
// use same identifier as used in TSconfig for icon
$iconRegistry->registerIcon(
    // use same identifier as used in TSconfig for icon
    'lsfapi-ext',
    \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
    // font-awesome identifier ('external-link-square')
    ['source' => 'EXT:'.$_EXTKEY .'/ext_icon.png']
 );

 if ($_EXTKEY=="lsfapi" && TYPO3_MODE === 'BE' )   {
    $pageRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
    $pageRenderer->loadRequireJsModule('TYPO3/CMS/Lsfapi/LsfJs');
    
  }
/***************
 * Register Scheduler task for Lsf Data
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][UniPotsdam\Lsfapi\Task\Coursedata::class] = array(
    'extension' => $_EXTKEY,
    'title' => 'LSF Course Data',
    'description' => 'Get LSF data',
 );


 /***************
 * Register Scheduler task for Lsf Data
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][UniPotsdam\Lsfapi\Task\Scheduledata::class] = array(
    'extension' => $_EXTKEY,
    'title' => 'LSF Schedule Data',
    'description' => 'Get LSF Schedule data',
 );

 $TYPO3_CONF_VARS['BE']['defaultUserTSconfig'] .= '
    setup.override.titleLen= 300
';
if ($_EXTKEY=="lsfapi" )   {
//Pre Process layout on backend option of LSF Plugin
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['lsfdata'] = UniPotsdam\Lsfapi\Hook\CourseTitleHead::class;

}

/***************
 * PageTS
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $_EXTKEY . '/Configuration/TsConfig/Page/All.tsconfig">');
