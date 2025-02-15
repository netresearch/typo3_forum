<?php

namespace Mittwald\Typo3Forum\Scheduler;

/*                                                                    - *
 *  COPYRIGHT NOTICE                                                    *
 *                                                                      *
 *  (c) 2015 Mittwald CM Service GmbH & Co KG                           *
 *           All rights reserved                                        *
 *                                                                      *
 *  This script is part of the TYPO3 project. The TYPO3 project is      *
 *  free software; you can redistribute it and/or modify                *
 *  it under the terms of the GNU General Public License as published   *
 *  by the Free Software Foundation; either version 2 of the License,   *
 *  or (at your option) any later version.                              *
 *                                                                      *
 *  The GNU General Public License can be found at                      *
 *  http://www.gnu.org/copyleft/gpl.html.                               *
 *                                                                      *
 *  This script is distributed in the hope that it will be useful,      *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of      *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the       *
 *  GNU General Public License for more details.                        *
 *                                                                      *
 *  This copyright notice MUST APPEAR in all copies of the script!      *
 *                                                                      */

use Mittwald\Typo3Forum\Domain\Model\User\FrontendUser;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * A turtle with maths skills checks all counter columns of any user and update them on a error.
 *
 * @author  Ruven Fehling <r.fehling@mittwald.de>
 * @package  TYPO3
 * @subpackage  typo3_forum
 */
class Counter extends AbstractDatabaseTask
{

    /**
     * @var int
     */
    protected $forumPid;

    /**
     * @var int
     */
    protected $userPid;

    /**
     * @var array
     */
    private $settings;


    /**
     * @return int
     */
    public function getForumPid()
    {
        return $this->forumPid;
    }

    /**
     * @return int
     */
    public function getUserPid()
    {
        return $this->userPid;
    }

    /**
     * @param int $forumPid
     */
    public function setForumPid($forumPid)
    {
        $this->forumPid = $forumPid;
    }

    /**
     * @param int $userPid
     */
    public function setUserPid($userPid)
    {
        $this->userPid = $userPid;
    }

    /**
     * @return void
     */
    public function setSettings()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var ConfigurationManagerInterface $configurationManager */
        $configurationManager = $objectManager->get(ConfigurationManagerInterface::class);
        $this->settings = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $this->settings = $this->settings['plugin.']['tx_typo3forum.']['settings.'];
    }

    /**
     * @return bool
     */
    public function execute()
    {
        if ($this->getForumPid() == false || $this->getUserPid() == false) {
            return false;
        }
        $this->setSettings();

        $this->updateTopic();
        $this->updateForum();
        $this->updateUser();
        return true;
    }


    /**
     * @return void
     */
    private function updateTopic()
    {
		$queryBuilder = $this->getQueryBuilderForTable('tx_typo3forum_domain_model_forum_post');
		$statement = $queryBuilder
			->select('post.topic')
			->addSelectLiteral($queryBuilder->expr()->count('post.uid', 'post_count'))
			->from('tx_typo3forum_domain_model_forum_post', 'post')
			->join('post', 'tx_typo3forum_domain_model_forum_topic', 'topic', $queryBuilder->expr()->eq('post.topic', 'topic.uid'))
			->where($queryBuilder->expr()->eq('post.pid', $queryBuilder->createNamedParameter($this->getForumPid(), \PDO::PARAM_INT)))
			->addGroupBy('post.topic')
			->execute();

		$updateTopicConnection = $this->getConnectionPool()->getConnectionForTable('tx_typo3forum_domain_model_forum_topic');

		while ($topic = $statement->fetch()) {
			$updateTopicConnection->update(
				'tx_typo3forum_domain_model_forum_topic',
				['post_count' => (int)$topic['post_count']],
				['uid' => (int)$topic['topic']],
				[\PDO::PARAM_INT, \PDO::PARAM_INT]
			);
		}
    }

	private function updateForum() {

		$queryBuilderTopic = $this->getQueryBuilderForTable('tx_typo3forum_domain_model_forum_topic');
		$statement = $queryBuilderTopic
			->select('forum')
			->addSelectLiteral($queryBuilderTopic->expr()->count('uid', 'topic_count'))
			->addSelectLiteral($queryBuilderTopic->expr()->sum('post_count', 'post_count'))
			->from('tx_typo3forum_domain_model_forum_topic')
			->where(
				$queryBuilderTopic->expr()->eq(
					'pid',
					$queryBuilderTopic->createNamedParameter($this->getForumPid(), \PDO::PARAM_INT)
				)
			)
			->groupBy('forum')
			->execute();

		$postAndTopicCountPerForum = [];

		while ($forum = $statement->fetch()) {
			$postAndTopicCountPerForum[(int)$forum['forum']] = [
				'topic_count' => (int)$forum['topic_count'],
				'post_count' => (int)$forum['post_count']
			];
		}

		$queryBuilderForum = $this->getQueryBuilderForTable('tx_typo3forum_domain_model_forum_forum');
		$statement = $queryBuilderForum
			->select('uid')
			->from('tx_typo3forum_domain_model_forum_forum')
			->where(
				$queryBuilderForum->expr()->eq(
					'pid',
					$queryBuilderForum->createNamedParameter($this->getForumPid(), \PDO::PARAM_INT)
				)
			)
			->execute();

		$updateForumConnection = $this->getConnectionPool()->getConnectionForTable('tx_typo3forum_domain_model_forum_forum');

		while ($forum = $statement->fetch()) {
			$forumUid = (int)$forum['uid'];
			$updateForumConnection->update(
				'tx_typo3forum_domain_model_forum_forum',
				[
					'topic_count' => (int)$postAndTopicCountPerForum[$forumUid]['topic_count'],
					'post_count' => (int)$postAndTopicCountPerForum[$forumUid]['post_count']
				],
				['uid' => $forumUid],
				[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT]
			);
		}
	}

    /**
     * @return void
     */
    private function updateUser()
    {
        $forumPid = (int)$this->getForumPid();
        $userUpdate = [];
        $rankScore = $this->settings['rankScore.'];

        //Find any post_count
        $queryBuilder = $this->getQueryBuilderForTable('tx_typo3forum_domain_model_forum_post');
        $queryBuilder->from('tx_typo3forum_domain_model_forum_post', 'post');
        $queryBuilder->select('post.author');
        $queryBuilder->addSelectLiteral($queryBuilder->expr()->count('post.uid', 'counter'));
        $queryBuilder->andWhere(
            $queryBuilder->expr()->gt('post.author', 0),
            $queryBuilder->expr()->eq('post.pid', $queryBuilder->createNamedParameter($forumPid, \PDO::PARAM_INT))
        );
        $queryBuilder->addGroupBy('post.author');
        $result = $queryBuilder->execute();

        while ($row = $result->fetch()) {
            $userUpdate[$row['author']]['post_count'] = $row['counter'];
        }


        //Find any topic count
        $queryBuilder = $this->getQueryBuilderForTable('tx_typo3forum_domain_model_forum_topic');
        $queryBuilder->from('tx_typo3forum_domain_model_forum_topic', 'topic');
        $queryBuilder->select('topic.author');
        $queryBuilder->addSelectLiteral($queryBuilder->expr()->count('topic.uid', 'counter'));
        $queryBuilder->andWhere(
            $queryBuilder->expr()->gt('topic.author', 0),
            $queryBuilder->expr()->eq('topic.pid', $queryBuilder->createNamedParameter($forumPid, \PDO::PARAM_INT))
        );
        $queryBuilder->addGroupBy('topic.author');
        $result = $queryBuilder->execute();


        while ($row = $result->fetch()) {
            $userUpdate[$row['author']]['topic_count'] = $row['counter'];
        }

        // Find any question topic count
        $queryBuilder = $this->getQueryBuilderForTable('tx_typo3forum_domain_model_forum_topic');
        $queryBuilder->from('tx_typo3forum_domain_model_forum_topic', 'topic');
        $queryBuilder->select('topic.author');
        $queryBuilder->addSelectLiteral($queryBuilder->expr()->count('topic.uid', 'counter'));
        $queryBuilder->andWhere(
            $queryBuilder->expr()->gt('topic.author', 0),
            $queryBuilder->expr()->eq('topic.question', true),
            $queryBuilder->expr()->eq('topic.pid', $queryBuilder->createNamedParameter($forumPid, \PDO::PARAM_INT))
        );
        $queryBuilder->addGroupBy('topic.author');
        $result = $queryBuilder->execute();

        while ($row = $result->fetch()) {
            $userUpdate[$row['author']]['question_count'] = $row['counter'];
        }

        // Find any favorite count
        $queryBuilder = $this->getQueryBuilderForTable('tx_typo3forum_domain_model_user_topicfavsubscription');
        $queryBuilder->from('tx_typo3forum_domain_model_user_topicfavsubscription', 'subscription');
        $queryBuilder->join(
            'subscription',
            'tx_typo3forum_domain_model_forum_topic',
            'topic',
            $queryBuilder->expr()->eq('subscription.uid_foreign', 'topic.uid')
        );
        $queryBuilder->select('topic.author');
        $queryBuilder->addSelectLiteral($queryBuilder->expr()->count('*', 'counter'));
        $queryBuilder->addGroupBy('subscription.uid_local');
        $queryBuilder->addGroupBy('topic.author');
        $result = $queryBuilder->execute();


        while ($row = $result->fetch()) {
            $userUpdate[$row['author']]['favorite_count'] = $row['counter'];
        }

        //Supported Post User X got
        $queryBuilder = $this->getQueryBuilderForTable('tx_typo3forum_domain_model_user_supportpost');
        $queryBuilder->from('tx_typo3forum_domain_model_user_supportpost', 'support');
        $queryBuilder->join(
            'support',
            'tx_typo3forum_domain_model_forum_post',
            'post',
            $queryBuilder->expr()->eq('support.uid_foreign', 'post.uid')
        );
        $queryBuilder->select('post.author');
        $queryBuilder->addSelectLiteral($queryBuilder->expr()->count('*', 'counter'));
        $queryBuilder->addGroupBy('post.author');
        $result = $queryBuilder->execute();


        while ($row = $result->fetch()) {
            $userUpdate[$row['author']]['support_count'] = $row['counter'];
        }


        $queryBuilder = $this->getQueryBuilderForTable('tx_typo3forum_domain_model_user_supportpost');
        $queryBuilder->from('tx_typo3forum_domain_model_user_supportpost', 'support');
        $queryBuilder->select('support.uid_local');
        $queryBuilder->addSelectLiteral($queryBuilder->expr()->count('*', 'counter'));
        $queryBuilder->addGroupBy('support.uid_local');
        $result = $queryBuilder->execute();

        while ($row = $result->fetch()) {
            $userUpdate[$row['uid_local']]['markSupport_count'] = $row['counter'];
        }

        //Find all users with their current rank
        $queryBuilder = $this->getQueryBuilderForTable('fe_users');
        $queryBuilder->from('fe_users', 'user');
        $queryBuilder->select('user.uid AS author', 'user.tx_typo3forum_rank');
        $queryBuilder->andWhere(
            $queryBuilder->expr()->eq(
                'user.pid',
                $queryBuilder->createNamedParameter($this->getUserPid(), \PDO::PARAM_INT)
            ),
            $queryBuilder->expr()->eq(
                'user.tx_extbase_type',
                $queryBuilder->createNamedParameter(FrontendUser::class, \PDO::PARAM_STR)
            )
        );
        $result = $queryBuilder->execute();


        while ($row = $result->fetch()) {
            $userUpdate[$row['author']]['rank'] = $row['tx_typo3forum_rank'];
        }

        //Find all ranks
        $rankArray = [];


        $queryBuilder = $this->getQueryBuilderForTable('tx_typo3forum_domain_model_user_rank');
        $queryBuilder->from('tx_typo3forum_domain_model_user_rank', 'rank');
        $queryBuilder->select('rank.uid', 'rank.point_limit');
        $queryBuilder->andWhere(
            $queryBuilder->expr()->eq(
                'rank.pid',
                $queryBuilder->createNamedParameter($this->getUserPid(), \PDO::PARAM_INT)
            )
        );
        $queryBuilder->addOrderBy('point_limit', 'ASC');
        $result = $queryBuilder->execute();

        while ($row = $result->fetch()) {
            $rankArray[$row['uid']] = $row;
        }

		$updateFeUserConnection = $this->getConnectionPool()->getConnectionForTable('fe_users');

        //Now check this giant array
        foreach ($userUpdate as $userUid => $array) {
            $points = 0;
            $points = $points + (int)$array['post_count'] * (int)$rankScore['newPost'];
            $points = $points + (int)$array['markSupport_count'] * (int)$rankScore['markHelpful'];
            $points = $points + (int)$array['favorite_count'] * (int)$rankScore['gotFavorite'];
            $points = $points + (int)$array['support_count'] * (int)$rankScore['gotHelpful'];

            $lastPointLimit = 0;
            $lastRankUid = 0;

            foreach ($rankArray as $key => $rank) {
                if ($points >= $lastPointLimit && $points < $rank['point_limit']) {
                    $array['rank'] = $rank['uid'];
                }
                $lastPointLimit = $rank['point_limit'];
                $lastRankUid = $rank['uid'];
            }
            if ($lastRankUid > 0 && $points >= $lastPointLimit) {
                $array['rank'] = $lastRankUid;
            }

			$updateFeUserConnection->update(
				'fe_users',
				[
					'tx_typo3forum_post_count' => (int)$array['post_count'],
					'tx_typo3forum_topic_count' => (int)$array['topic_count'],
					'tx_typo3forum_question_count' => (int)$array['question_count'],
					'tx_typo3forum_helpful_count' => (int)$array['support_count'],
					'tx_typo3forum_points' => (int)$points,
					'tx_typo3forum_rank' => (int)$array['rank'],
				],
				['uid' => (int)$userUid],
				[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT]
			);
        }


        //At last, update the rank count
        $queryBuilder = $this->getQueryBuilderForTable('fe_users');
        $queryBuilder->from('fe_users', 'user');
        $queryBuilder->select('user.tx_typo3forum_rank');
        $queryBuilder->addSelectLiteral(
            $queryBuilder->expr()->count('*', 'counter')
        );
        $queryBuilder->andWhere(
            $queryBuilder->expr()->eq(
                'user.pid',
                $queryBuilder->createNamedParameter($this->getUserPid(), \PDO::PARAM_INT)
            ),
            $queryBuilder->expr()->eq(
                'user.tx_extbase_type',
                $queryBuilder->createNamedParameter(FrontendUser::class, \PDO::PARAM_STR)
            )
        );
        $queryBuilder->addGroupBy('tx_typo3forum_rank');
        $result = $queryBuilder->execute();

		$updateRankConnection = $this->getConnectionPool()->getConnectionForTable('tx_typo3forum_domain_model_user_rank');
        while ($row = $result->fetch()) {
			$updateRankConnection->update(
				'tx_typo3forum_domain_model_user_rank',
				['user_count' => (int)$row['counter']],
				['uid' => (int)$row['tx_typo3forum_rank']],
				[\PDO::PARAM_INT, \PDO::PARAM_INT]
			);
        }
    }
}
