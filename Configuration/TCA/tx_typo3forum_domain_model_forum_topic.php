<?php

return [
	'ctrl' => [
		'title' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic',
		'type' => 'type',
		'label' => 'subject',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'versioningWS' => true,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'delete' => 'deleted',
		'enablecolumns' => [
			'disabled' => 'hidden'
		],
		'iconfile' => 'EXT:typo3_forum/Resources/Public/Icons/Forum/Topic.png'
	],
	'interface' => [
		'showRecordFieldList' => 'hidden,type,subject,slug,posts,post_count,author,last_post,last_post_crdate,forum,target,question,solution,closed,sticky'
			.',criteria_options,tags,subscribers,fav_subscribers,readers'
	],
	'types' => [
		'0' => ['showitem' => 'hidden,type,subject,slug,posts,author,last_post,forum,question,solution,closed,sticky,criteria_options,tags,subscribers,fav_subscribers,readers'],
		'1' => ['showitem' => 'hidden,type,subject,slug,forum,last_post,target'],
	],
	'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    ['LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.default_value', 0]
                ],
                'default' => 0,
                'fieldWizard' => [
                    'selectIcons' => [
                        'disabled' => false,
                    ],
                ],
            ]
        ],
		't3ver_label' => [
			'displayCond' => 'FIELD:t3ver_label:REQ:true',
			'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
			'config' => [
				'type' => 'none',
				'cols' => 27
			],
		],
		'crdate' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.creationDate',
			'config' => [
				'type' => 'passthrough'
			],
		],
		'hidden' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
			'config' => [
				'type' => 'check'
			],
		],
		'type' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.type',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'maxitems' => 1,
				'minitems' => 1,
				'default' => 0,
				'items' => [
					['LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.type.0', 0],
					['LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.type.1', 1],
				],
			],
		],
		'subject' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.subject',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			],
		],
        'slug' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:pages.slug',
            'exclude' => 1,
            'config' => [
                'type' => 'slug',
                'generatorOptions' => [
                    'fields' => ['subject'],
                    'replacements' => [
                        '/' => '-'
                    ],
                ],
                'fallbackCharacter' => '-',
                'eval' => 'uniqueInPid',
            ],
        ],
		'posts' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.posts',
			'config' => [
				'type' => 'inline',
				'foreign_table' => 'tx_typo3forum_domain_model_forum_post',
				'foreign_field' => 'topic',
				'maxitems' => 9999,
				'appearance' => [
					'collapse' => 0,
					'newRecordLinkPosition' => 'bottom',
				],
			],
		],
		'post_count' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.post_count',
			'config' => [
				'type' => 'none'
			],
		],
		'author' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.author',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'fe_users',
				'maxitems' => 1
			],
		],
		'last_post' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.last_post',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_typo3forum_domain_model_forum_post',
				'minitems' => 1,
				'maxitems' => 1,
			],
		],
		'last_post_crdate' => [
			'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.last_post_crdate',
			'config' => [
				'type' => 'none'
			],
		],
		'is_solved' => [
			'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.is_solved',
			'config' => [
				'type' => 'none'
			],
		],
		'solution' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.solution',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_typo3forum_domain_model_forum_post',
				'maxitems' => 1,
				'items' => [
					['-', '0'],
				],
			],
		],
		'forum' => [
			'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_forum',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_typo3forum_domain_model_forum_forum',
				'maxitems' => 1
			],
		],
		'closed' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.closed',
			'config' => [
				'type' => 'check'
			],
		],
		'sticky' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.sticky',
			'config' => [
				'type' => 'check'
			],
		],
		'question' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.question',
			'config' => [
				'type' => 'check'
			],
		],
		'criteria_options' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_criteria_options',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectMultipleSideBySide',
				'size' => 10,
				'maxitems' => 99999,
				'foreign_table' => 'tx_typo3forum_domain_model_forum_criteria_options',
				'MM' => 'tx_typo3forum_domain_model_forum_criteria_topic_options'
			],
		],
		'tags' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.tags',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectMultipleSideBySide',
				'size' => 10,
				'maxitems' => 99999,
				'foreign_table' => 'tx_typo3forum_domain_model_forum_tag',
				'MM' => 'tx_typo3forum_domain_model_forum_tag_topic'
			],
		],
		'subscribers' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.subscribers',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectMultipleSideBySide',
				'foreign_table' => 'fe_users',
				'MM' => 'tx_typo3forum_domain_model_user_topicsubscription',
				'MM_opposite_field' => 'tx_typo3forum_topic_subscriptions',
				'maxitems' => 9999,
				'size' => 10
			],
		],
		'fav_subscribers' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.fav_subscribers',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectMultipleSideBySide',
				'foreign_table' => 'fe_users',
				'MM' => 'tx_typo3forum_domain_model_user_topicfavsubscription',
				'MM_opposite_field' => 'tx_typo3forum_topic_favsubscriptions',
				'maxitems' => 9999,
				'size' => 10
			],
		],
		'target' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.target',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_typo3forum_domain_model_forum_topic',
				'minitems' => 1,
				'maxitems' => 1,
			],
		],
		'readers' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.readers',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingleBox',
				'foreign_table' => 'fe_users',
				'MM' => 'tx_typo3forum_domain_model_user_readtopic',
				'MM_opposite_field' => 'tx_typo3forum_read_topics',
				'size' => 10
			],
		],
	],
];
