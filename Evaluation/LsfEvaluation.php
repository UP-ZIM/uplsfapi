<?php
namespace UniPotsdam\Orcid\Evaluation;

/**
 * Class for field value validation/evaluation to be used in 'eval' of TCA
 */

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Extbase\Object\ObjectManager;


 class LsfEvaluation{

    /**
     * JavaScript code for client side validation/evaluation
     *
     * @return string JavaScript code for client side validation/evaluation
     */
    

    public function evaluateFieldValue($value, $is_in, &$set)
    {
        
        //Initialize query to get Orcid data from tx_orcid table
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_lsfcoursedata');
        
        //Get all data from tx_orcid_workdata table
        $allcourse = $queryBuilder->select('course_id')->from('tx_lsfcoursedata')->execute();
        $all_cors_row = $allcourse->fetchAll();

        //Initialize query to add Course data in tx_lsfcoursedata table
        $quBuildFilter = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_lsfcoursefilter');
        $crsArr = $quBuildFilter->select('courseId')->from('tx_lsfcoursefilter')->orderBy('courseId')->execute();
        $crsArr = $crsArr->fetchAll();
        $id = $this->getCrsId($quBuildFilter, $crsUid);


        

        /* if (array_search($value  , array_column($all_cors_row, 'course_id')) != true){
			
            $apiurl = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['lsfapi']);
            $ch = curl_init();
            $data = array(
                'courseId' => $id
            );
            $auth_data = json_encode(array("condition" => $data));
            $headers = array(
                'Content-Type:application/json',
                'Authorization:Bearer '.$lsftoken
            );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $auth_data);
            curl_setopt($ch, CURLOPT_URL, $apiurl['inputApiurl'].'/getCourseData');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

            $rst = curl_exec($ch);
            $urlRes = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if($urlRes != 200){
                $this->flashMessage('Invalid field value', 'Please Select valid Course', FlashMessage::ERROR);
                $set = false; //do not save value
            }else{

                //Insert Query for Course Data
                $this->msQuery($value, $rst);
                
                $this->succFlashMessage('Input value is correct', 'Valid Course id', FlashMessage::OK);
                
            }
        }else{
            $this->succFlashMessage('Input value is correct', 'Valid Course id', FlashMessage::OK);
        } */
        
        print_r($value);
        
        return $value;        
    }

    /**
     * Server-side validation/evaluation on opening the record
     *
     * @param array $parameters Array with key 'value' containing the field value from the database
     * @return string Evaluated field value
     */
    public function deevaluateFieldValue(array $parameters) 
    {
        return $parameters['value'];
    }

    /**
     * @param string $messageTitle
     * @param string $messageText
     * @param int $severity
     */
    protected function flashMessage($messageTitle, $messageText, $severity = FlashMessage::ERROR)
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

    protected function getCrsId($querbuilder, $uid){
        $tabledata = $querbuilder->select('courseId')->from('tx_lsfcoursefilter')->where($querbuilder->expr()->eq('uid', $querbuilder->createNamedParameter($uid)))->execute();
        $crsArr = $tabledata->fetchAll();
        $crsId = array_column($crsArr, 'courseId');
        return $crsId[0];

    }

    // Insert and update Query for orcid data
    protected function msQuery($resid, $content){

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
            ->execute();
        } else{
            //Insert Course data in tx_lsfcoursedata table
            $queryBuilder->insert('tx_lsfcoursedata')->values(['course_id' => $resid,'course_data' => $content])->execute();
        }
    }

 }