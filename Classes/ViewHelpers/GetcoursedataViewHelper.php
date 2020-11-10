<?php
namespace UniPotsdam\Lsfapi\ViewHelpers;
/*
 * This file is part of the package Potsdam\Orcid.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use UniPotsdam\Lsfapi\Hook\CreatePulslink;

class GetcoursedataViewHelper extends AbstractViewHelper {

    public function initializeArguments()
    {
        $this->registerArgument('id', 'string', 'Course id to get Course data', true);
    }

    
     
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $extConf    = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['lsfapi']);
        $lsfurl     =   $extConf['inputApiurl'];
        $lsftoken   =   $extConf['inputAccesstoken'];
        $pulsUrl   =   $extConf['inputCourseurl'];
        $crsUid    = $arguments['id'];
        $languageAspect = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\Context::class)->getAspect('language');
        $sys_language_uid = $languageAspect->getId();

        
        if($sys_language_uid == 1){
            $langurl = 'LLL:EXT:lsfapi/Resources/Private/Language/locallang.xlf:';
        }elseif ($sys_language_uid == 2) {
            $langurl = 'LLL:EXT:lsfapi/Resources/Private/Language/locallang_de.xlf:';
        }

        //Initialize query to get Course data from tx_lsfcoursedata table
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_lsfcoursedata');
        $allcourse = $queryBuilder->select('course_id')->from('tx_lsfcoursedata')->execute();
        $all_cors_row = $allcourse->fetchAll();

        //Initialize query to add Course data in tx_lsfcoursedata table
        $quBuildFilter = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_lsfcoursefilter');
        $crsArr = $quBuildFilter->select('courseId')->from('tx_lsfcoursefilter')->orderBy('courseId')->execute();
        $crsArr = $crsArr->fetchAll();
        $id = self::getCrsId($quBuildFilter, $crsUid);
        
        $tabledata = $queryBuilder->select('course_data')->from('tx_lsfcoursedata')->where($queryBuilder->expr()->eq('course_id', $queryBuilder->createNamedParameter($id)))->execute();
        while ($row = $tabledata->fetch()) {
            $orcid_data = json_decode($row['course_data'], true);            
        }

        // echo "<pre><br>";
        // print_r($orcid_data);
        // echo "</pre><br>";

        $html = self::convertHtml($orcid_data, $id, $pulsUrl, $langurl);

        // self::insertAllCourseData($crsArr, $all_cors_row, $lsfurl, $lsftoken);

        return $html;
    }

    //Function used to convert array to html
    public static function convertHtml($data, $crsId, $url, $langurl){

        //Call CreatePulslink class from Hook
        $linkClass = new CreatePulslink();
        $pulsLink = $linkClass->pulsLink($url,$crsId);

        //Create Html for frontend With Course Data
        $html = '<div class="lsf-course">';

        //language variable
        $crsType = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($langurl.'front.lyt.crstype');
        $semester = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($langurl.'front.lyt.semester');
        $regPeriod = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($langurl.'front.lyt.reg_period');
        $lang = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($langurl.'front.lyt.language');
        $facl = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($langurl.'front.lyt.facility');
        $audi = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($langurl.'front.lyt.audience');
        $moreInfo = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($langurl.'front.lyt.more_info');
        $botComment = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($langurl.'front.lyt.bot_comment');
        $botExam    = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($langurl.'front.lyt.bot_examination');
        $botLitera   = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($langurl.'front.lyt.bot_literature');
        
        //Course Data
        foreach ($data as $courseDatakey => $courseDatavalue) {
            foreach ($courseDatavalue as $courseKey => $courseValue) {
                if (array_key_exists(0,$courseValue)){
                    $html .= "<h2 class='course_heading'>".html_entity_decode($courseValue[0]['courseName'],ENT_HTML401 | ENT_HTML5, "UTF-8")."</h2>";
                    $html .= "<div class='course_left'><ul><li><b>".$crsType.": </b>".$courseValue[0]['courseType']."</li>";
                    $html .= "<li><b>".$semester.": </b>".$courseValue[0]['semester']."</li>";
                    $html .= "<li><b>".$regPeriod.": </b>".$courseValue[0]['enrolmentBegin'].' - '.$courseValue[0]['enrolmentEnd']."</li></ul></div>";
                    $html .= "<div class='course_right'><ul><li><b>".$lang.": </b>".$courseValue[0]['language']."</li>";
                    $html .= "<li><b>".$facl.": </b>".$courseValue[0]['facilityName']."</li>";
                    $html .= "<li><b>".$audi.": </b>".$courseValue[0]['audience']."</li></ul></div>";
                    $html .= "<a href='".$pulsLink."' class='crsmore up-external-link'>".$moreInfo."</a>";
                    $events = $courseValue[0]['events'];
                    $comment = $courseValue[0]['comment'];
                    $examination = $courseValue[0]['examination'];
                    $literature = $courseValue[0]['literature'];
                }else {
                    $html .= "<h2 class='course_heading'>".html_entity_decode($courseValue['courseName'],ENT_HTML5, "UTF-8")."</h2>";
                    $html .= "<div class='course_left'><ul>";
                    $html .= "<li><b>".$crsType.": </b>".$courseValue['courseType']."</li>";
                    $html .= "<li><b>".$semester.": </b>".$courseValue['semester']."</li>";
                    $html .= "<li><b>".$regPeriod.": </b>".$courseValue['enrolmentBegin'].'-'.$courseValue['enrolmentEnd']."</li></ul></div>";
                    $html .= "<div class='course_right'><ul><li><b>".$lang.": </b>".$courseValue['language']."</li>";
                    $html .= "<li><b>".$facl.": </b>".$courseValue['facilityName']."</li>";
                    $html .= "<li><b>".$audi.": </b>".$courseValue['audience']."</li></ul></div>";
                    $html .= "<a href='".$pulsLink."' class='crsmore up-external-link'>".$moreInfo."</a>";
                    $events = $courseValue['events'];
                    $comment = $courseValue['comment'];
                    $examination = $courseValue['examination'];
                    $literature = $courseValue['literature'];
                }
                
            }
            
        }
        $html .= '</div>';
        
        // echo "<pre><br>";
        // print_r($events);
        // echo "</pre><br>";
        $html .= self::eventHtml($events, $langurl);
        $html .= '<div class="lsf-other-cnt"><div class="up-accordion-container" id="accordion-'.$crsId.'">
                    <div class="up-accordion">';
        if($comment != null){
            $html .= '<div class="up-accordion-item odd" id="accordion-crs-comment">';
            $html .= '<div class="up-accordion-item-header"><p><h3>'.$botComment.'</h3><span class="up-icon up-indicator"></span></p></div>';
            $html .= '<div class="up-accordion-item-content" style="display:none;">'.$comment.'</div></div>';
        }
        if($examination != null){
            $html .= '<div class="up-accordion-item odd" id="accordion-crs-exam">';
            $html .= '<div class="up-accordion-item-header"><h3>'.$botExam.'</h3><span class="up-icon up-indicator"></span></div>';
            $html .= '<div class="up-accordion-item-content" style="display:none;">'.$examination.'</div></div>';
        }
        if ($literature != null) {
            $html .= '<div class="up-accordion-item odd" id="accordion-crs-liter">';
            $html .= '<div class="up-accordion-item-header"><h3>'.$botLitera.'</h3><span class="up-icon up-indicator"></span></div>';
            $html .= '<div class="up-accordion-item-content" style="display:none;">'.$literature.'</div></div>';
        }
        $html .= '</div></div></div>';

        

        return $html;
    }

    //Create 
    public static function eventHtml($events, $langurl){        
        //language variable
        $day        = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($langurl.'front.lyt.day');
        $time       = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($langurl.'front.lyt.time');
        $frequ      = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($langurl.'front.lyt.frequ');
        $duration   = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($langurl.'front.lyt.duration');
        $room       = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($langurl.'front.lyt.room');
        $location   = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($langurl.'front.lyt.location');
        $lecturer   = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($langurl.'front.lyt.lecturer');

        //Course Events
        foreach ($events as $events_key => $events_val) {  
            
                     
            if(array_key_exists(0,$events_val)){
                $groups = self::groupSort($events_val);
                // echo '<br><pre> sfsdf';
                // print_r($groups);
                // echo '</pre><br>'; 
                foreach ($groups as $groupsKey => $groupsVal) { 
                    $groupTitle = $groupsKey + 1;
                    $html .= '<h3 class="lsf-grp-title"> Group '.$groupTitle.'</h3>';
                    
                    foreach ($groupsVal as $groupKey => $groupVal) {
                        //if ($groupVal['group'] !== 'Alle Gruppen') {
                            $html .= '<div class="table-container lsf-course-event" role="table" aria-label="Destinations">';
                            // $html .= '<div class="flex-table header" role="rowgroup">';
                            // $html .= '<div class="flex-row first" role="columnheader">'.$day.' / '.$time.' / '.$frequ.'</div>';
                            // $html .= '<div class="flex-row" role="columnheader">'.$duration.'</div>';
                            // $html .= '<div class="flex-row" role="columnheader">'.$room.' / '.$location.'</div>';
                            // $html .= '<div class="flex-row" role="columnheader">'.$lecturer.'</div></div>';
                            $html .= '<div class="flex-table row" role="rowgroup">';
                            $html .= '<div class="flex-row first" role="cell"><div class="colheader">'.$day.' / '.$time.' / '.$frequ.'</div><div class="coltext">'.$groupVal['daySC'].' / '.$groupVal['startTime'].' - '.$groupVal['endTime'].' / '.$groupVal['rhythmSC'].'</div></div>';
                            $html .= '<div class="flex-row" role="cell"><div class="colheader">'.$duration.'</div><div class="coltext">'.$groupVal['startDate'].' - '.$groupVal['endDate'].'</div></div>';
                            $roomSC = str_replace("_",".",$groupVal['roomSc']);
                            $html .= '<div class="flex-row" role="cell"><div class="colheader">'.$room.' / '.$location.'</div><div class="coltext">'.$roomSC.' / '.$groupVal['location'].'</div></div>';
                            $lec = $groupVal['lecturers']['lecturer'];
                            if (array_key_exists(0,$lec)){
                                $lec_full_name =  $lec[0]['lecturerTitle'].' '.$lec[0]['lecturerFirstname'].' '.$lec[0]['lecturerLastname'];
                                $html .= '<div class="flex-row" role="cell"><div class="colheader">'.$lecturer.'</div><div class="coltext">'.$lec_full_name.'</div></div></div>';
                            }else{
                                $html .= '<div class="flex-row" role="cell"><div class="colheader">'.$lecturer.'</div><div class="coltext">'.$lec['lecturerTitle'].' '.$lec['lecturerFirstname'].' '.$lec['lecturerLastname'].'</div></div></div>';
                            }
                            
                            $html .= '</div>';
                        //}
                    }
                }
            }else{
                $html .= '<div class="table-container lsf-course-event" role="table" aria-label="Destinations">';
                // $html .= '<div class="flex-table header" role="rowgroup">';
                // $html .= '<div class="flex-row first" role="columnheader">'.$day.' / '.$time.' / '.$frequ.'</div>';
                // $html .= '<div class="flex-row" role="columnheader">'.$duration.'</div>';
                // $html .= '<div class="flex-row" role="columnheader">'.$room.' / '.$location.'</div>';
                // $html .= '<div class="flex-row" role="columnheader">'.$lecturer.'</div></div>';
            
                $html .= '<div class="flex-table row" role="rowgroup">';
                $html .= '<div class="flex-row first" role="cell"><div class="colheader">'.$day.' / '.$time.' / '.$frequ.'</div><div class="coltext">'.$events_val['daySC'].' / '.$events_val['startTime'].' - '.$events_val['endTime'].'/'.$events_val['rhythmSC'].'</div></div>';
                $html .= '<div class="flex-row" role="cell"><div class="colheader">'.$duration.'</div><div class="coltext">'.$events_val['startDate'].' - '.$events_val['endDate'].'</div></div>';
                $roomSC = str_replace("_",".",$events_val['roomSc']);
                $html .= '<div class="flex-row" role="cell"><div class="colheader">'.$room.' / '.$location.'</div><div class="coltext">'.$roomSC.' / '.$events_val['location'].'</div></div>';
                $lec = $events_val['lecturers']['lecturer'];
                if (array_key_exists(0,$lec)){
                    $lec_full_name =  $lec[0]['lecturerTitle'].' '.$lec[0]['lecturerFirstname'].' '.$lec[0]['lecturerLastname'];
                    $html .= '<div class="flex-row" role="cell"><div class="colheader">'.$lecturer.'</div><div class="coltext">'.$lec_full_name.'</div></div></div>';
                }else{
                    $html .= '<div class="flex-row" role="cell"><div class="colheader">'.$lecturer.'</div><div class="coltext">'.$lec['lecturerTitle'].' '.$lec['lecturerFirstname'].' '.$lec['lecturerLastname'].'</div></div></div>';
                }
                $html .= '</div>';
            }
            
        }

        return $html;
    }

    //Get Course id 
    public static function getCrsId($querbuilder, $uid){
        $tabledata = $querbuilder->select('courseId')->from('tx_lsfcoursefilter')->where($querbuilder->expr()->eq('uid', $querbuilder->createNamedParameter($uid)))->execute();
        $crsArr = $tabledata->fetchAll();
        $crsId = array_column($crsArr, 'courseId');
        return $crsId[0];

    }

    //Sort Group row according to group id
    protected function groupSort($value){
        $tempArr = array_unique(array_column($value, 'groupId'));
        $groups = array();
        foreach ($tempArr as $tempArrKey => $tempArrValue) {
            $groupAccID = array();
            foreach ($value as $groupkey => $groupvalue) {
                if( $tempArrValue == $groupvalue['groupId'] && $groupvalue['group'] !== 'Alle Gruppen'){
                    array_push($groupAccID, $groupvalue);
                }elseif($groupvalue['group'] === 'Alle Gruppen' && $tempArrValue != 0){
                    array_push($groupAccID, $groupvalue);
                }
            }
            if (!empty($groupAccID)) {
                array_push($groups, $groupAccID);
            }
        }
        
        // echo '<br><pre>';
        // print_r($groups);
        // echo '</pre><br>'; 
        return $groups;
    }
    
    /**
     * Get the current language
     */
    protected function getLanguage() {
        if (TYPO3_MODE === 'FE') {
            if (isset($GLOBALS['TSFE']->config['config']['language'])) {
                return $GLOBALS['TSFE']->config['config']['language'];
            }
        } elseif (strlen($GLOBALS['BE_USER']->uc['lang']) > 0) {
            return $GLOBALS['BE_USER']->uc['lang'];
        }
        return 'en'; //default
    }
}

