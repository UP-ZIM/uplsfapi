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

    /**
     * Default PageTS for lsfapi
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
        $extensionKey,
        'Configuration/TsConfig/Page/All.tsconfig',
        'LSF Data'
    );

    //Add plugin icons and text in Typical Page Content Opiton
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
        array(
            'PULS Kursinformationen',
            'lsfextcourse',
            'EXT:' . $extensionKey . '/ext_icon.png'
        ),
        'CType',
        $extensionKey
    );
});
