<?php

/**
 * This file is part of the package mittwald/typo3_forum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

$EM_CONF['typo3_forum'] = [
    'title'          => 'typo3_forum',
    'description'    => 'Forum extension',
    'category'       => 'plugin',
    'author'         => 'Mittwald CM Service',
    'author_email'   => 'support@mittwald.de',
    'author_company' => 'Mittwald CM Service',
    'state'          => 'stable',
    'createDirs'     => 'typo3temp/typo3_forum,typo3temp/typo3_forum/gravatar, typo3temp/typo3_forum/workflowstatus',
    'modify_tables'  => 'fe_users',

    // NOTE: DO NOT CHANGE this version number manually.
    // This is done by the build-release.sh script.
    'version'        => '1.0-dev',
    'constraints'    => [
        'depends' => [
            'typo3'              => '8.7.0 - 9.5.99',
            'static_info_tables' => '',
            'php'                => '7.1',
        ],
    ],
];
