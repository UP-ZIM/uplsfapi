<?php
namespace UniPotsdam\Lsfapi\ViewHelpers;
/*
 * This file is part of the package Potsdam\Orcid.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;

class GetlecturescheduleallViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper {
    
    //initaliz atttribute for viewhelper tag of html template 
    // public function initializeArguments()
    // {
    //     $this->registerArgument('uid', 'string', 'uid use to get orcid id', true);
    //     $this->registerArgument('orcidstyle', 'string', 'Apply Citation Style on Orcid data', true);
    // }

    //Render Function for Orcid Data
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {

        // $extConf    = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['lsfapi']);
        // $lsfurl     =   $extConf['inputApiurl'];
        // $lsftoken   =   $extConf['inputAccesstoken'];

        // $curl = curl_init();
        // $data = array(
        //     'semester' => '0'
        // );
        // $auth_data = json_encode(array("condition" => $data));
        // $headers = array(
        //     'Content-Type:application/json',
        //     'Authorization:Bearer '.$lsftoken
        // );

        // curl_setopt($curl, CURLOPT_POST, 1);
        // curl_setopt($curl, CURLOPT_POSTFIELDS, $auth_data);
        // curl_setopt($curl, CURLOPT_URL, $lsfurl.'/getLectureScheduleAll');
        // curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($curl, CURLOPT_POST, true);
        // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        // $result = curl_exec($curl);
        // if(!$result){die("Connection Failure");}
        // curl_close($curl);
        
        // self::taskMsQuery($result);

         //Course Schedule Query to insert and update data in tx_lsfscheduledata      
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_lsfscheduledata');
        $tabledata = $queryBuilder->select('allschedule_data')->from('tx_lsfscheduledata')->execute();

        while ($row = $tabledata->fetch()) {
            $schdul_data = json_decode($row['allschedule_data'], true);            
        }
        foreach ($schdul_data as $schdul_data_key => $schdul_data_value) {
            $childnodes = $schdul_data_value['rootNode']['Tree']['childNodes'];   
            foreach ($childnodes as $catalogs_key => $catalogs_value) {
                $lvlone_courses = $catalogs_value['childNode']['courses'];
                if($lvlone_courses != null){
                    // echo $lvlone_courses['course']['courseId'];
                    // echo $lvlone_courses['course']['courseName'];
                    // echo $lvlone_courses['course']['semesterSC'];
                    // echo $lvlone_courses['course']['semester'];
                    // echo $lvlone_courses['course']['sws'];
                }
                // echo $catalogs_value['childNode']['headerName'];
                // echo '<br>---------------Level 1 -----------------<br>';
                $lvlone_nodes = $catalogs_value['childNodes'];
                foreach ($lvlone_nodes as $lvlone_node_key => $lvlone_node_value) {
                    if ($lvlone_node_value['courses'] != null) {
                        self::filterDataloop($lvlone_node_value);
                    }
                    
                    $lvltwo_nodes = $lvlone_node_value['childNodes'];
                    foreach ($lvltwo_nodes as $lvltwo_node_key => $lvltwo_node_value) {
                        $lvltwo_courses = $lvltwo_node_value['childNode'];
                        if ($lvltwo_courses['courses'] != null) {
                            self::filterDataloop($lvltwo_courses);
                        }
                        
                        $lvlthree_nodes = $lvltwo_node_value['childNodes'];
                        foreach ($lvlthree_nodes as $lvlthree_node_key => $lvlthree_node_val) {
                            $lvlthree_courses = $lvlthree_node_val['childNode'];
                            if($lvlthree_courses['courses'] != null){
                                self::filterDataloop($lvlthree_courses);
                            }

                            $lvltfour_nodes = $lvlthree_node_val['childNodes'];
                            foreach ($lvltfour_nodes as $lvltfour_node_key => $lvltfour_node_val) {
                                $lvltfour_courses = $lvltfour_node_val['childNode'];
                                if($lvltfour_courses['courses'] != null){
                                    self::filterDataloop($lvltfour_courses);
                                }

                                $lvltfive_nodes = $lvltfour_node_val['childNodes'];
                                foreach ($lvltfive_nodes as $lvltfive_node_key => $lvltfive_node_val) {
                                    $lvltfive_courses = $lvltfive_node_val['childNode'];
                                    if($lvltfive_courses['courses'] != null){
                                        self::filterDataloop($lvltfive_courses);
                                    }

                                    $lvltsix_nodes = $lvltfive_node_val['childNodes'];
                                    foreach ($lvltsix_nodes as $lvltsix_node_key => $lvltsix_node_val) {
                                        $lvltsix_courses = $lvltsix_node_val['childNode'];
                                        if($lvltsix_courses['courses'] != null){
                                            self::filterDataloop($lvltsix_courses);
                                        }

                                        $lvltseven_nodes = $lvltsix_node_val['childNodes'];
                                        foreach ($lvltseven_nodes as $lvltseven_node_key => $lvltseven_node_val) {
                                            $lvltseven_courses = $lvltseven_node_val['childNode'];
                                            if($lvltseven_courses['courses'] != null){
                                                self::filterDataloop($lvltseven_courses);
                                            }
                                        }
                                    }
                                }
                                // echo '<br><pre>';
                                // print_r($lvltfour_node_val);
                                // echo '</pre><br>';
                            }
                        }
                    }
                    
                }
                
            }
            
        }

        return true;

    }

    //foreach loop to execute insert query with tx_lsfcoursefilter
    public static function filterDataloop($content){

        $cnt_courses = $content['courses'];
        if (array_key_exists(0,$cnt_courses['course'])) {
            foreach ($cnt_courses['course'] as $cnt_course_key => $cnt_course_val) {
                $hid    = $content['headerId'];
                $hname  = $content['headerName'];
                $crsid  = $cnt_course_val['courseId'];
                $cname  = $cnt_course_val['courseName'];
                $ctype  = $cnt_course_val['courseType'];
                $semsc  = $cnt_course_val['semesterSC'];
                $sem    = $cnt_course_val['semester'];
                $sws    = $cnt_course_val['sws'];
                /* echo $crsid;
                echo '</pre><br>'; */
                self::nodeDataQuery($hid, $hname, $crsid, $cname, $ctype, $semsc, $sem, $sws);
            }
        }else{
            $hid    = $content['headerId'];
            $hname  = $content['headerName'];
            $crsid  = $cnt_courses['course']['courseId'];
            $cname  = $cnt_courses['course']['courseName'];
            $ctype  = $cnt_courses['course']['courseType'];
            $semsc  = $cnt_courses['course']['semesterSC'];
            $sem    = $cnt_courses['course']['semester'];
            $sws    = $cnt_courses['course']['sws'];
            /* echo $crsid;
            echo '</pre><br>'; */
            self::nodeDataQuery($hid, $hname, $crsid, $cname, $ctype, $semsc, $sem, $sws);
        }
        
    }


    //Insert data in tx_lsfcoursefilter
    public static function nodeDataQuery($hid, $hname, $crsid, $cname, $ctype, $semsc, $sem, $sws){

        //Get all Course filter table data
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_lsfcoursefilter');
        $oid = $queryBuilder->select('uid','courseId','headerId','headerName')->from('tx_lsfcoursefilter')->where($queryBuilder->expr()->eq('courseId', $queryBuilder->createNamedParameter($crsid)))->execute();
        $row = $oid->fetch();
        if($row['courseId'] == $crsid && $row['headerName']==$hname){
            echo 'Header name is equal';
            echo '<br>';
            // $queryBuilder
            // ->update('tx_lsfcoursefilter')
            // ->where(
            //     $queryBuilder->expr()->eq('courseId', $queryBuilder->createNamedParameter($crsid)),
            //     $queryBuilder->expr()->eq('headerId', $queryBuilder->createNamedParameter($hid))
            // )
            // ->set('headerId', $hid)
            // ->set('headerName', html_entity_decode($hname))
            // ->set('courseName', html_entity_decode($cname))
            // ->set('courseType', $ctype)
            // ->set('semesterSC', $semsc)
            // ->set('semester', $sem)
            // ->set('title', html_entity_decode($cname).' | '.html_entity_decode($hname))
            // ->set('sws', $sem)
            // ->set('tstamp', time())
            // ->execute();
            
        } else{
            echo 'Header name is not equal';
            echo '<br>';
            //Insert Orcid Id in tx_orcid table
            //$queryBuilder->insert('tx_lsfcoursefilter')->values(['headerId' => $hid, 'headerName' => $hname, 'courseId' => $crsid,'courseName' => $cname, 'courseType' => $ctype, 'semesterSC' => $semsc, 'semester' => $sem, 'sws' => $sws, 'title' => html_entity_decode($cname).' | '.html_entity_decode($hname), 'crdate'=>time()])->execute();
        }
    }

    // Insert and update Query for Lsf Schedule data
    public static function taskMsQuery($content){
        
        //Course Schedule Query to insert and update data in tx_lsfscheduledata      
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_lsfscheduledata');
        $count = $queryBuilder->count('uid')->from('tx_lsfscheduledata')->execute()->fetchColumn(0);
        if($count != 0){
            $queryBuilder
            ->update('tx_lsfscheduledata')
            ->set('allschedule_data', $content)
            ->execute();
        }else{
            $queryBuilder->insert('tx_lsfscheduledata')->values(['allschedule_data' => $content])->execute();
        }
    }

}
