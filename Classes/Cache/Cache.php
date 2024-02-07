<?php

namespace Mittwald\Typo3Forum\Cache;

/*                                                                      *
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

use TYPO3\CMS\Core\SingletonInterface;

/**
 *
 * This class provides access to the TYPO3 caching framework to the typo3_forum components.
 * Basically, this class is just a very thin wrapper around the TYPO3 caching framework.
 * It encapsulated creation and retrieval of the appropriate caches and can be very
 * easily obtained using dependency injection.
 *
 *
 */
class Cache implements SingletonInterface
{
    const CACHE_NAME = 'typo3forum_main';

    /**
     * @var \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface
     */
    protected ?\TYPO3\CMS\Core\Cache\Frontend\FrontendInterface $cacheInstance = null;

    /**
     * @var \TYPO3\CMS\Core\Cache\CacheManager
     */
    protected \TYPO3\CMS\Core\Cache\CacheManager $cacheManager;

    /**
     * Constructor.
     *
     * @param \TYPO3\CMS\Core\Cache\CacheManager $cacheManager
     */
    public function __construct(\TYPO3\CMS\Core\Cache\CacheManager $cacheManager)
    {
        $this->cacheManager  = $cacheManager;
        $this->cacheInstance = $this->cacheManager->getCache(self::CACHE_NAME);
    }

    public function has($identifier)
    {
        return $this->cacheInstance->has($identifier);
    }

    public function get($identifier)
    {
        return $this->cacheInstance->get($identifier);
    }

    public function set($identifier, $value, array $tags = [], $lifetime = null)
    {
        $this->cacheInstance->set(
            $identifier,
            $value,
            $tags,
            $lifetime
        );
    }

    public function flush()
    {
        $this->cacheInstance->flush();
    }
}
