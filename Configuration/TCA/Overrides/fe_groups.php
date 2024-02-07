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
    $tempColumns = [
        'tx_typo3forum_user_mod' => [
            'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:fe_groups.user_mod',
            'config' => [
                'type' => 'check',
            ],
        ],
    ];

    ExtensionManagementUtility::addTCAcolumns(
        'fe_groups',
        $tempColumns
    );

    $GLOBALS['TCA']['fe_groups']['types']['Mittwald\Typo3Forum\Domain\Model\User\FrontendUserGroup']
        = $GLOBALS['TCA']['fe_groups']['types']['0'];

    ExtensionManagementUtility::addTcaSelectItem(
        'fe_groups',
        'tx_extbase_type',
        [
            'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:fe_groups.tx_extbase_type.typo3_forum',
            'Mittwald\Typo3Forum\Domain\Model\User\FrontendUserGroup',
        ]
    );

    $GLOBALS['TCA']['fe_groups']['types']['Mittwald\Typo3Forum\Domain\Model\User\FrontendUserGroup']['showitem'] .=
        ',--div--;LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:fe_users.tx_typo3forum.tab.settings,' .
        'tx_typo3forum_user_mod';
});
