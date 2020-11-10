<?php
namespace UniPotsdam\Lsfapi\Task;

/*
 * This file is part of the package UniPotsdam\Orcid. 
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;

class Coursedata extends \TYPO3\CMS\Scheduler\Task\AbstractTask {
    public function execute() {
        $extConf    = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['lsfapi']);
        $lsfurl     =   $extConf['inputApiurl'];
        $lsftoken   =   $extConf['inputAccesstoken'];
        
        //Initialize query to get Course data from tx_lsfcoursedata table
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_lsfcoursedata');
        $allcourse = $queryBuilder->select('course_id')->from('tx_lsfcoursedata')->execute();
        $all_crs_row = $allcourse->fetchAll();

        //Initialize query to add Course data in tx_lsfcoursedata table
        $quBuildFilter = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_lsfcoursefilter');
        $crsArr = $quBuildFilter->select('courseId')->from('tx_lsfcoursefilter')->orderBy('courseId')->execute();
        $crsArr = $crsArr->fetchAll();

        $crs_ids = array_column($all_crs_row, 'course_id');
        foreach ($crs_ids as $crs_id_key => $crs_id){        
            $curl = curl_init();
            $data = array(
                'courseId' => $crs_id
            );
            $auth_data = json_encode(array("condition" => $data));
            $headers = array(
                'Content-Type:application/json',
                'Authorization:Bearer '.$lsftoken
            );

            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $auth_data);
            curl_setopt($curl, CURLOPT_URL, $lsfurl.'/getCourseData');
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

            $result = curl_exec($curl);
            //if(!$result){die("Connection Failure");}
            curl_close($curl);
            self::msQuery($id, $result);
        }
        self::insertAllCourseData($crsArr, $all_cors_row, $lsfurl, $lsftoken);
        return true;
    }


    // Insert and update Query for course data
    public static function msQuery($resid, $content){

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

    public static function insertAllCourseData($crsArr, $all_cors_row, $lsfurl, $lsftoken){
        $crsArr = array_map("unserialize",
        array_unique(array_map("serialize", $crsArr)));
        foreach ($crsArr as $crsArr_key => $crsID) {

            //Course id condition if is availabel in tx_lsfcoursedata table then skip api function
            if(is_array($all_cors_row) && array_search($crsID['courseId'], array_column($all_cors_row, 'course_id')) != TRUE){     
                
                $curl = curl_init();
                $data = array(
                    'courseId' => $crsID['courseId']
                );
                $auth_data = json_encode(array("condition" => $data));
                $headers = array(
                    'Content-Type:application/json',
                    'Authorization:Bearer '.$lsftoken
                );

                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $auth_data);
                curl_setopt($curl, CURLOPT_URL, $lsfurl.'/getCourseData');
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

                $result = curl_exec($curl);
                curl_close($curl);
                self::msQuery($crsID['courseId'], $result);
            
            }

        }
    
}

}
