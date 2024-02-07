<?php

/**
 * This file is part of the package mittwald/typo3_forum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

use Mittwald\Typo3Forum\Cache\CacheManager;
use Mittwald\Typo3Forum\Controller\AjaxController;
use Mittwald\Typo3Forum\Controller\ForumController;
use Mittwald\Typo3Forum\Controller\ModerationController;
use Mittwald\Typo3Forum\Controller\PostController;
use Mittwald\Typo3Forum\Controller\ReportController;
use Mittwald\Typo3Forum\Controller\StatsController;
use Mittwald\Typo3Forum\Controller\TagController;
use Mittwald\Typo3Forum\Controller\TopicController;
use Mittwald\Typo3Forum\Controller\UserController;
use Mittwald\Typo3Forum\Updates\PopulateTypo3ForumSlugs;
use Mittwald\Typo3Forum\Updates\PostsWithoutAuthorNameUpdate;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die('Access denied.');

call_user_func(static function () {
    ExtensionManagementUtility::addPageTSConfig(
        "@import 'EXT:typo3_forum/Configuration/PageTS/pageTS.typoscript'"
    );

    ExtensionUtility::configurePlugin(
        'Typo3Forum',
        'Pi1',
        [
            ForumController::class => 'index, show, markRead, showUnread',
            TopicController::class => 'show, new, create, solution, listLatest',
            PostController::class => 'show, new, create, edit, update, delete',
            UserController::class => 'showMyProfile, index, list, subscribe, favSubscribe, show, disableUser, unDisableUser, listNotifications, listMessages, createMessage, newMessage, listPosts',
            ReportController::class => 'newUserReport, newPostReport, createUserReport, createPostReport',
            ModerationController::class => 'indexReport, editReport, newReportComment, editTopic, updateTopic, deleteTopic, updateUserReportStatus, updatePostReportStatus, createUserReportComment, createPostReportComment',
            TagController::class => 'list, show, new, create, listUserTags, newUserTag, deleteUserTag',
        ],
        [
            ForumController::class => 'show, index, create, update, delete, markRead, showUnread',
            TopicController::class => 'create',
            PostController::class => 'new, create, edit, update, delete',
            UserController::class => 'showMyProfile, dashboard, subscribe, favSubscribe, listFavorites, listNotifications, listTopics, listMessages, createMessage,listPosts',
            ReportController::class => 'newUserReport, newPostReport, createUserReport, createPostReport',
            ModerationController::class => 'indexReport, updateTopic, deleteTopic, updateUserReportStatus, updatePostReportStatus, newReportComment, createUserReportComment, createPostReportComment',
            TagController::class => 'list, show, new, create, listUserTags, newUserTag, deleteUserTag',
        ]
    );

    ExtensionUtility::configurePlugin(
        'Typo3Forum',
        'Widget',
        [
            UserController::class => 'list',
            StatsController::class => 'list',
        ],
        [
            UserController::class => 'list',
        ]
    );

    ExtensionUtility::configurePlugin(
        'Typo3Forum',
        'Ajax',
        [
            ForumController::class => 'index',
            PostController::class => 'preview, addSupporter, removeSupporter',
            TagController::class => 'autoComplete',
            AjaxController::class => 'main, postSummary, loginbox, preview'
        ],
        [
            ForumController::class => 'index',
            PostController::class => 'preview, addSupporter, removeSupporter',
            AjaxController::class => 'main, postSummary, loginbox, preview',
        ]
    );

    # TCE-Main hook for clearing all typo3_forum caches
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][]
        = CacheManager::class . '->clearAll';

    if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['typo3forum_main'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['typo3forum_main'] = [];
    }

//    // TODO
//    $GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['typo3_forum'] = 'EXT:typo3_forum/Classes/Ajax/Dispatcher.php';
//
//    // TODO
//
//    // Connect signals to slots. Some parts of extbase suck, but the signal-slot
//    // pattern is really cool! :P
//    $signalSlotDispatcher = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\SignalSlot\Dispatcher');
//    $signalSlotDispatcher->connect(
//        'Mittwald\Typo3Forum\Domain\Model\Forum\Post',
//        'postCreated',
//        'Mittwald\Typo3Forum\Service\Notification\SubscriptionListener',
//        'onPostCreated'
//    );
//
//    $signalSlotDispatcher->connect(
//        'Mittwald\Typo3Forum\Domain\Model\Forum\Topic',
//        'topicCreated',
//        'Mittwald\Typo3Forum\Service\Notification\SubscriptionListener',
//        'onTopicCreated'
//    );
//    $signalSlotDispatcher->connect(
//        'TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper',
//        'afterMappingSingleRow',
//        'Mittwald\Typo3Forum\Service\SettingsHydrator',
//        'hydrateSettings'
//    );

    // Adding scheduler tasks
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Mittwald\Typo3Forum\Scheduler\Counter'] = [
        'extension' => 'typo3_forum',
        'title' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang.xml:tx_typo3forum_scheduler_counter_title',
        'description' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang.xml:tx_typo3forum_scheduler_counter_description',
        'additionalFields' => 'Mittwald\Typo3Forum\Scheduler\CounterAdditionalFieldProvider'
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Mittwald\Typo3Forum\Scheduler\ForumRead'] = [
        'extension' => 'typo3_forum',
        'title' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang.xml:tx_typo3forum_scheduler_forumRead_title',
        'description' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang.xml:tx_typo3forum_scheduler_forumRead_description',
        'additionalFields' => 'Mittwald\Typo3Forum\Scheduler\ForumReadAdditionalFieldProvider',
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Mittwald\Typo3Forum\Scheduler\Notification'] = [
        'extension' => 'typo3_forum',
        'title' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang.xml:tx_typo3forum_scheduler_notification_title',
        'description' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang.xml:tx_typo3forum_scheduler_notification_description',
        'additionalFields' => 'Mittwald\Typo3Forum\Scheduler\NotificationAdditionalFieldProvider',
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Mittwald\Typo3Forum\Scheduler\SessionResetter'] = [
        'extension' => 'typo3_forum',
        'title' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang.xml:tx_typo3forum_scheduler_sessionResetter_title',
        'description' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang.xml:tx_typo3forum_scheduler_sessionResetter_description',
        'additionalFields' => 'Mittwald\Typo3Forum\Scheduler\SessionResetterAdditionalFieldProvider',
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Mittwald\Typo3Forum\Scheduler\StatsSummary'] = [
        'extension' => 'typo3_forum',
        'title' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang.xml:tx_typo3forum_scheduler_statsSummary_title',
        'description' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang.xml:tx_typo3forum_scheduler_statsSummary_description',
        'additionalFields' => 'Mittwald\Typo3Forum\Scheduler\StatsSummaryAdditionalFieldProvider',
    ];

    // Register upgrade wizard
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['postsWithoutAuthorName']
        = PostsWithoutAuthorNameUpdate::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['populateTypo3ForumSlugs']
        = PopulateTypo3ForumSlugs::class;
});
