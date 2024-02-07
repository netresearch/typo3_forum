<?php

/**
 * This file is part of the package mittwald/typo3_forum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die('Access denied.');

call_user_func(static function () {
    ExtensionManagementUtility::addStaticFile(
        'typo3_forum',
        'Configuration/TypoScript',
        'typo3_forum'
    );

    ExtensionManagementUtility::addStaticFile(
        'typo3_forum',
        'Configuration/TypoScript/Bootstrap',
        'typo3_forum Bootstrap Template'
    );
});
