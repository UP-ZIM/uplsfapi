<?php
namespace UniPotsdam\Lsfapi\Tca;
use TYPO3\CMS\Backend\Utility\BackendUtility;

class CourseTitle{
    public function title(&$parameters)
    {
        $record = BackendUtility::getRecord($parameters['table'], $parameters['row']['uid']);
        $newTitle = $record['courseName'];
        $newTitle .= ' (' . substr(strip_tags($record['semester']), 0, 30) . ')';
        $parameters['title'] = $newTitle;
    }

} 