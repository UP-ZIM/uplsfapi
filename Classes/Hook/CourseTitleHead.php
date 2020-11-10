<?php

namespace UniPotsdam\Lsfapi\Hook;

/**
     * --------------------------------------------------------------
	 * This file is part of the package UniPotsdam\Orcid.
     * copyright 2020 by University Potsdam
     * https://www.uni-potsdam.de/
     *
     * Project: Orcid Extension
	 * User: Anuj Sharma (asharma@uni-potsdam.de)
     *
     * --------------------------------------------------------------
     */

use \TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use \TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;


/**
 * Contains a preview rendering for the page module of CType="uporcidext"
 */
class CourseTitleHead implements PageLayoutViewDrawItemHookInterface
{

   /**
    * Preprocesses the preview rendering of a content element of type "My new content element"
    *
    * @param \TYPO3\CMS\Backend\View\PageLayoutView $parentObject Calling parent object
    * @param bool $drawItem Whether to draw the item using the default functionality
    * @param string $headerContent Header content
    * @param string $itemContent Item content
    * @param array $row Record row of tt_content
    *
    * @return void
    */
    public function preProcess(
      PageLayoutView &$parentObject,
      &$drawItem,
      &$headerContent,
      &$itemContent,
      array &$row
   ){

   //    if (TYPO3_MODE === 'FE') {
   //       if (isset($GLOBALS['TSFE']->config['config']['language'])) {
   //           return $GLOBALS['TSFE']->config['config']['language'];
   //       }
   //   } elseif (strlen($GLOBALS['BE_USER']->uc['lang']) > 0) {
   //       return $GLOBALS['BE_USER']->uc['lang'];
   //   }
   //   return 'en'; 
     
      //Initialize query to add Course data in tx_lsfcoursedata table
      $quBuildFilter = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_lsfcoursefilter');
      $crsArr = $quBuildFilter->select('courseId','courseName','semester')->from('tx_lsfcoursefilter')->where($quBuildFilter->expr()->eq('uid', $quBuildFilter->createNamedParameter($row['lsfcoursedata'])))->execute();
      $crsArr = $crsArr->fetchAll();

      if ($row['CType'] === 'lsfextcourse') {

          $itemContent .= '<Strong>PULS Kursinformationen</Strong>';
          $itemContent .= '<p>'.$crsArr[0]['courseName'].' ('.$crsArr[0]['semester'].')</p>';

          $drawItem = false;
      }
   }
}