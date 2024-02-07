<?php

/**
 * This file is part of the package mittwald/typo3_forum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die('Access denied.');

call_user_func(static function () {
    $flexFormPath = 'FILE:EXT:typo3_forum/Configuration/FlexForms/';

    ExtensionUtility::registerPlugin('Typo3Forum', 'Pi1', 'typo3_forum');
    ExtensionUtility::registerPlugin('Typo3Forum', 'Widget', 'typo3_forum Widgets');

    $pluginSignature = strtolower(GeneralUtility::underscoredToUpperCamelCase('typo3_forum')) . '_pi1';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';

    ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, $flexFormPath . 'Pi1.xml');

    $pluginSignature = strtolower(GeneralUtility::underscoredToUpperCamelCase('typo3_forum')) . '_widget';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';

    ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, $flexFormPath . 'Widgets.xml');
});
