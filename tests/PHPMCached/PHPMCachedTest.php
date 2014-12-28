<?php

/*
 * This file is part of the PHPMCached package.
 *
 * (c) Hahn und Herden Netzdenke GbR <info@netzdenke.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Test class
 */
class PHPMCachedTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var \PHPMCached\PHPMCached
	 */
	protected $object;

	/**
	 * Init test
	 */
	protected function setUp() {
		$this->object = \PHPMCached\PHPMCached::getInstance('test');
	}

	/**
	 * Test -> Add a server to memcached adapter
	 */
	public function testAddServer() {
		$status = $this->object->addServer('127.0.0.1');
		$this->assertTrue($status);
	}

	/**
	 * Test -> Flush the memcached server
	 */
	public function testFlush() {
		$status = $this->object->flush();
		$this->assertTrue($status);
	}

	/**
	 * Test -> Set a value
	 */
	public function testSetValue() {
		$cacheKey = $this->object->getCacheKey('testA');
		$status = $this->object->set($cacheKey, 'testA');
		$this->assertTrue($status);

		$value = $this->object->get($cacheKey);
		$this->assertEquals('testA', $value);
	}

	/**
	 * Test -> Set and delete a value
	 */
	public function testDeleteValue() {
		$cacheKey = $this->object->getCacheKey('testB');
		$status = $this->object->set($cacheKey, 'testB');
		$this->assertTrue($status);

		$value = $this->object->get($cacheKey);
		$this->assertEquals('testB', $value);

		$this->object->delete($cacheKey);
		$this->assertFalse($this->object->get($cacheKey));
	}

	/**
	 * Test -> Test cache groups
	 */
	public function testCacheGroups() {
		$cacheKeyC = $this->object->getCacheKey('testC');
		$cacheKeyD = $this->object->getCacheKey('testD');
		$statusC = $this->object->set($cacheKeyC, 'testC', 'test');
		$statusD = $this->object->set($cacheKeyD, 'testD', 'test');
		$this->assertTrue($statusC);
		$this->assertTrue($statusD);

		$valueC = $this->object->get($cacheKeyC);
		$valueD = $this->object->get($cacheKeyD);
		$this->assertEquals('testC', $valueC);
		$this->assertEquals('testD', $valueD);

		$this->object->deleteCacheGroup('test');
		$this->assertFalse($this->object->get($cacheKeyC));
		$this->assertFalse($this->object->get($cacheKeyD));
	}

}
