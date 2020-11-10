<?php

namespace UniPotsdam\Lsfapi\Hook;

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Extbase\Object\ObjectManager;

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
 
class DataHandler{

    /**
 * Index Action
 *
 * @return string
 */
    public function processDatamap_postProcessFieldArray($status, $table, $id, array &$fieldArray, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {  
        
        //Initialize LSF Api url 
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['lsfapi']);
        $lsfurl     =   $extConf['inputApiurl'];
        $lsftoken   =   $extConf['inputAccesstoken'];

       //Initialize query to get Course data from tx_lsfcoursedata table
       $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
       $CntData = $queryBuilder->select('lsfcoursedata', 'CType')->from('tt_content')->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($id)))->execute();
       $CntRowData = $CntData->fetchAll();
       $CntRowData = $CntRowData[0];

        //Initialize query to get Orcid data from tx_orcid table
       $qurBuilderCrs = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_lsfcoursedata');
       $allCrs = $qurBuilderCrs->select('course_id')->from('tx_lsfcoursedata')->execute();
       $allCrsRow = $allCrs->fetchAll();


       //Initialize query to add Course data in tx_lsfcoursedata table
       $quBuildFilter = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_lsfcoursefilter');
       $crsArr = $quBuildFilter->select('courseId')->from('tx_lsfcoursefilter')->where($quBuildFilter->expr()->eq('uid', $quBuildFilter->createNamedParameter($CntRowData['lsfcoursedata'])))->execute();
       $crsArr = $crsArr->fetchAll();
       $crsArr = $crsArr[0];

       if($CntRowData['CType'] == 'lsfextcourse'){
            $this->crsCurl($lsfurl, $lsftoken, $allCrsRow, $crsArr['courseId']);
       }elseif ($fieldArray['CType'] == 'lsfextcourse') {
            $this->crsCurl($lsfurl, $lsftoken, $allCrsRow, $crsArr['courseId']);
       }
        
        
    }
     
    //Function for CURL to get data through Api
    protected function crsCurl($url, $token, $allRow, $id){

        if (array_search($id  , array_column($allRow, 'course_id')) != true){

            echo 'This id is not exist in Course data table <br>';
            $ch = curl_init();
            $data = array(
                'courseId' => $id
            );
            $auth_data = json_encode(array("condition" => $data));
            $headers = array(
                'Content-Type:application/json',
                'Authorization:Bearer '.$token
            );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $auth_data);
            curl_setopt($ch, CURLOPT_URL, $url.'/getCourseData');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

            $rst = curl_exec($ch);
            $urlRes = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if($urlRes == 200){
            //Function to Insert and Update Query for Course Data
                $this->msQuery($id, $rst);
                $this->succFlashMessage('Course Id is valid ', ' ', FlashMessage::OK);
            }else{
                $this->succFlashMessage('Sever error: '.$urlRes, ' ', FlashMessage::ERROR);
            }   
            
        }else{
            $this->succFlashMessage('Course Id is valid ', ' ', FlashMessage::OK);
        }
        

    }

    // Insert and update Query for LSF data
    public function msQuery($resid, $content){

        //Create querbuilder variable for tx_lsfcoursedata table
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_lsfcoursedata');
        $oid = $queryBuilder->select('uid','course_id')->from('tx_lsfcoursedata')->where($queryBuilder->expr()->eq('course_id', $queryBuilder->createNamedParameter($resid)))->execute();
        $row = $oid->fetch();
        if($row['course_id'] == $resid){
            $queryBuilder
            ->update('tx_lsfcoursedata')
            ->where(
                $queryBuilder->expr()->eq('course_id', $queryBuilder->createNamedParameter($resid))
            )
            ->set('course_data', $content)
            ->set('tstamp', time())
            ->execute();
        } else{
            //Insert Course data in tx_lsfcoursedata table
            $queryBuilder->insert('tx_lsfcoursedata')->values(['course_id' => $resid,'course_data' => $content, 'crdate'=>time()])->execute();
        }
    }

    /**
     * @param string $messageTitle
     * @param string $messageText
     * @param int $severity
     */
    protected function succFlashMessage($messageTitle, $messageText, $severity = FlashMessage::OK)
    {
        $message = GeneralUtility::makeInstance(
            FlashMessage::class,
            $messageText,
            $messageTitle,
            $severity,
            true
        );

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $flashMessageService = $objectManager->get(FlashMessageService::class);
        $messageQueue = $flashMessageService->getMessageQueueByIdentifier();
        $messageQueue->addMessage($message);
    }

    /**
     * @param string $messageTitle
     * @param string $messageText
     * @param int $severity
     */
    protected function errorflashMessage($messageTitle, $messageText, $severity = FlashMessage::ERROR)
    {
        $message = GeneralUtility::makeInstance(
            FlashMessage::class,
            $messageText,
            $messageTitle,
            $severity,
            true
        );

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $flashMessageService = $objectManager->get(FlashMessageService::class);
        $messageQueue = $flashMessageService->getMessageQueueByIdentifier();
        $messageQueue->addMessage($message);
    }
    

}