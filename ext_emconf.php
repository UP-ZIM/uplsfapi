<?php

/**
 * Extension Manager/Repository config file for ext "upcoursesapi".
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'lsfapi',
    'description' => 'API for Courses of University potsdam',
    'category' => 'plugin',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99',
            'fluid_styled_content' => '9.5.0-9.5.99',
            'rte_ckeditor' => '9.5.0-9.5.99'
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'UniPotsdam\\Lsfapi\\' => 'Classes'
        ],
    ],
    'state' => 'beta',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Anuj Sharma',
    'author_email' => 'asharma@uni-potsdam.de',
    'author_company' => 'uni-potsdam.de',
    'version' => '1.0.0',
];
