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
    ExtensionManagementUtility::addLLrefForTCAdescr(
        'tx_typo3forum_domain_model_format_textparser',
        'EXT:typo3_forum/Resources/Private/Language/locallang_csh_format_textparser.xml'
    );
    ExtensionManagementUtility::addLLrefForTCAdescr(
        'tx_typo3forum_domain_model_forum_access',
        'EXT:typo3_forum/Resources/Private/Language/locallang_csh_forum_access.xml'
    );
    ExtensionManagementUtility::addLLrefForTCAdescr(
        'tx_typo3forum_domain_model_forum_attachment',
        'EXT:typo3_forum/Resources/Private/Language/locallang_csh_forum_attachment.xml'
    );
    ExtensionManagementUtility::addLLrefForTCAdescr(
        'tx_typo3forum_domain_model_forum_forum',
        'EXT:typo3_forum/Resources/Private/Language/locallang_csh_forum_forum.xml'
    );
    ExtensionManagementUtility::addLLrefForTCAdescr(
        'tx_typo3forum_domain_model_forum_post',
        'EXT:typo3_forum/Resources/Private/Language/locallang_csh_forum_post.xml'
    );
    ExtensionManagementUtility::addLLrefForTCAdescr(
        'tx_typo3forum_domain_model_forum_topic',
        'EXT:typo3_forum/Resources/Private/Language/locallang_csh_forum_topic.xml'
    );
    ExtensionManagementUtility::addLLrefForTCAdescr(
        'tx_typo3forum_domain_model_user_userfield_userfield',
        'EXT:typo3_forum/Resources/Private/Language/locallang_csh_user_userfield_userfield.xml'
    );
    ExtensionManagementUtility::addLLrefForTCAdescr(
        'tx_typo3forum_domain_model_user_userfield_value',
        'EXT:typo3_forum/Resources/Private/Language/locallang_csh_user_userfield_value.xml'
    );
});
