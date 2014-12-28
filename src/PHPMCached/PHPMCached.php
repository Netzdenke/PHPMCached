<?php

/*
 * This file is part of the PHPMCached package.
 *
 * (c) Hahn und Herden Netzdenke GbR <info@netzdenke.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPMCached;

/**
 * PHPMCached main class
 */
class PHPMCached {

	/**
	 * Expirations
	 * @const int
	 */
	const EXPIRATION_INFINITY = 0;
	const EXPIRATION_HOUR = 60;
	const EXPIRATION_DAY = 86400;
	const EXPIRATION_WEEK = 604800;
	const EXPIRATION_YEAR = 31556926;

	/**
	 * PHPMCached instance
	 * @var \PHPMCached\PHPMCached
	 */
	private static $instance;

	/**
	 * Memcached instance
	 * @var \Memcached
	 */
	protected $memcached;

	/**
	 * Application key
	 * @var string
	 */
	protected $appKey = 'app';

	/**
	 * Get instance
	 * @param string $appKey
	 * @return \PHPMCached\PHPMCached
	 */
	public static function getInstance($appKey = null) {
		if (!self::$instance instanceof PHPMCached) {
			self::$instance = new PHPMCached($appKey);
		}

		return self::$instance;
	}

	/**
	 * Init adapter
	 * @param string $appKey
	 */
	private function __construct($appKey) {
		$this->memcached = new \Memcached();

		if ($appKey !== null) {
			$this->appKey = $appKey;
		}
	}

	/**
	 * Add memcached server
	 * @param string $host
	 * @param int $port
	 * @return bool
	 */
	public function addServer($host, $port = 11211) {
		return $this->memcached->addServer($host, $port);
	}

	/**
	 * Set an entry
	 * @param string $key
	 * @param mixed $value
	 * @param string|null $cacheGroup
	 * @param int $expiration
	 * @return bool
	 */
	public function set($key, $value, $cacheGroup = null, $expiration = self::EXPIRATION_INFINITY) {
		if ($expiration !== self::EXPIRATION_INFINITY) {
			$expiration = time() + (int) $expiration;
		}
		
		$status = $this->memcached->set($key, serialize($value), $expiration);

		if ($cacheGroup !== null) {
			$groupKey = $this->getCacheGroupKey($cacheGroup);

			$groupEntry = $this->get($groupKey);
			if (!is_array($groupEntry)) {
				$groupEntry = array();
			}
			$groupEntry[] = $key;

			$this->memcached->set($groupKey, serialize($groupEntry), 0);
		}

		return $status;
	}

	/**
	 * Get an entry
	 * @param string $key
	 * @return bool|mixed
	 */
	public function get($key) {
		$entry = unserialize($this->memcached->get($key));
		if ($this->memcached->getResultCode() === \Memcached::RES_NOTFOUND) {
			return false;
		}

		return $entry;
	}

	/**
	 * Delete an entry
	 * @param string $key
	 */
	public function delete($key) {
		$this->memcached->delete($key);
	}

	/**
	 * Flush all existing items at the server
	 * @return bool
	 */
	public function flush() {
		return $this->memcached->flush();
	}

	/**
	 * Delete all entries in a cache group
	 * @param string $cacheGroup
	 */
	public function deleteCacheGroup($cacheGroup) {
		$key = $this->getCacheGroupKey($cacheGroup);
		$cacheKeys = $this->get($key);
		if (is_array($cacheKeys)) {
			foreach ($cacheKeys as $cacheKey) {
				$this->delete($cacheKey);
			}
		}
	}

	/**
	 * Get cache key
	 * @param string $identifier
	 * @param array $params
	 * @return string
	 */
	public function getCacheKey($identifier, $params = array()) {
		return $this->appKey . '::ck::' . (string) $identifier . '::' . md5(json_encode($params));
	}

	/**
	 * Get cache key for cache group
	 * @param string $cacheGroup
	 * @return string
	 */
	protected function getCacheGroupKey($cacheGroup) {
		return $this->appKey . '::cg::' . (string) $cacheGroup;
	}

}
