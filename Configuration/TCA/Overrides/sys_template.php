<?php
defined('TYPO3_MODE') || die();

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

call_user_func(function()
{
    /**
     * Temporary variables
     */
    $extensionKey = 'lsfapi';

  
// Add an entry in the static template list found in sys_templates for static TS
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $extensionKey,
      'Configuration/TypoScript',
      'LSF Data'
  );
    // Created input fields for LSF API plugin
    $temporaryColumns = array (
        'lsfcoursedata' => array (
          'label' => 'LLL:EXT:lsfapi/Resources/Private/Language/locallang.xlf:course.fieldname',
          'exclude' => true,
          'l10n_mode' => 'exclude',
          'l10n_display' => 'hideDiff',
          'config' => array (
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_lsfcoursefilter',
                'maxitems' => 1,
                'minitems' => 1,
                'size' => 1,
                'suggestOptions' => [
                    'default' => [
                        'maxItemsInResultList' => 30,
                        'searchWholePhrase' => 1,
                        'receiverClass' => 'UniPotsdam\\Lsfapi\\Hook\\SuggestWizard'
                    ]
                ],
          )
        ),
    );

  //add LSF API Couse id in tt_content table 
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'tt_content',
    $temporaryColumns
    );      
    
    $GLOBALS['TCA']['tx_lsfcoursefilter'] = [
        'ctrl' => [
            'label' => 'courseName',
            'label_userFunc' => UniPotsdam\Lsfapi\Tca\CourseTitle::class . '->title',
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'title' => 'Puls Courses',
            'cruser_id' => 'cruser_id',
            'dividers2tabs' => true,
            'versioningWS' => true,
            'sortby' => 'sorting',
            'origUid' => 'courseId',
            'default_sortby' => 'courseName ASC',
            'delete' => 'deleted',
            'versioningWS' => true,
            'origUid' => 't3_origuid',
            'type' => 'record_type',
            'descriptionColumn' => 'rowDescription',
            'hideAtCopy' => true,
            'prependAtCopy' => 'LLL:EXT:lang/locallang_general.xlf:LGL.prependAtCopy',
            'copyAfterDuplFields' => 'colPos,sys_language_uid',
            'useColumnsForDefaultValues' => 'colPos,sys_language_uid,record_type',
            'shadowColumnsForNewPlaceholders' => 'colPos',
            'transOrigPointerField' => 'l18n_parent',
            'translationSource' => 'l10n_source',
            'transOrigDiffSourceField' => 'l18n_diffsource',
            'languageField' => 'sys_language_uid',         
            'typeicon_classes' => [
                'default' => 'lsfapi-ext'
            ],
            'searchFields' =>  'courseName',
            'iconfile' => 'EXT:' . $extensionKey . '/Resources/Public/Icons/ext_icon.png'
        ],
        'interface' => [
            'showRecordFieldList' => 'hidden, courseName',
        ],
        'types' => [
            '0' => [
                'showitem' => 'courseName'
            ],
        ],
        'columns' => [
            'title' => [
                'label' => 'fillter for course',
                'config' => [
                    'type' => 'inline',
                    'renderType' => 'selectSingle',
                    'foreign_table' => 'tx_lsfcoursefilter',
                    'foreign_field' => 'courseName',
                    'foreign_table_where' => 'ORDER BY sys_language.courseName',
                    'appearance' => [
                        'collapseAll' => 1,
                        'useSortable' => 1,
                        'newRecordLinkAddTitle' => 0,
                        'levelLinksPosition' => 'top',
                        'showSynchronizationLink' => 0,
                        'showAllLocalizationLink' => 0,
                        'elementBrowserEnabled ' => 1,
                        'showPossibleLocalizationRecords' => 1,
                    ],
                ],
            ],
        ],
    ];

    $GLOBALS['TCA']['tt_content']['types']['lsfextcourse'] = [
      'showitem' => '--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,header,'.
              'lsfcoursedata;;;1,'.
            '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
            --palette--;;appearanceLinks,' .
        '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
            --palette--;;language,'.
        '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,' .
        '--palette--;;hidden,' .
        '--palette--;;access,'.
        '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,'
      ];

     
});
