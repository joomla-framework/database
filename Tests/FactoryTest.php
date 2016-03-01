<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Tests;

use Joomla\Http\HttpFactory;

/**
 * Test class for Joomla\Http\HttpFactory.
 *
 * @since  1.0
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Tests the getHttp method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Http\HttpFactory::getHttp
	 * @since   1.0
	 */
	public function testGetHttp()
	{
		$this->assertInstanceOf(
			'Joomla\\Http\\Http',
			HttpFactory::getHttp()
		);
	}

	/**
	 * Tests the getHttp method to ensure only arrays or ArrayAccess objects are allowed
	 *
	 * @return  void
	 *
	 * @covers             Joomla\Http\HttpFactory::getHttp
	 * @expectedException  \InvalidArgumentException
	 */
	public function testGetHttpDisallowsNonArrayObjects()
	{
		HttpFactory::getHttp(new \stdClass);
	}

	/**
	 * Tests the getHttp method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Http\HttpFactory::getHttp
	 * @expectedException RuntimeException
	 * @since   1.1.4
	 */
	public function testGetHttpException()
	{
		$this->assertInstanceOf(
			'Joomla\\Http\\Http',
			HttpFactory::getHttp(array(), array())
		);
	}

	/**
	 * Tests the getAvailableDriver method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Http\HttpFactory::getAvailableDriver
	 * @since   1.0
	 */
	public function testGetAvailableDriver()
	{
		$this->assertInstanceOf(
			'Joomla\\Http\\TransportInterface',
			HttpFactory::getAvailableDriver(array(), null)
		);

		$this->assertFalse(
			HttpFactory::getAvailableDriver(array(), array()),
			'Passing an empty array should return false due to there being no adapters to test'
		);

		$this->assertFalse(
			HttpFactory::getAvailableDriver(array(), array('fopen')),
			'A false should be returned if a class is not present or supported'
		);

		include_once __DIR__ . '/stubs/DummyTransport.php';

		$this->assertFalse(
			HttpFactory::getAvailableDriver(array(), array('DummyTransport')),
			'Passing an empty array should return false due to there being no adapters to test'
		);
	}

	/**
	 * Tests the getAvailableDriver method to ensure only arrays or ArrayAccess objects are allowed
	 *
	 * @return  void
	 *
	 * @covers             Joomla\Http\HttpFactory::getAvailableDriver
	 * @expectedException  \InvalidArgumentException
	 */
	public function testGetAvailableDriverDisallowsNonArrayObjects()
	{
		HttpFactory::getAvailableDriver(new \stdClass);
	}

	/**
	 * Tests the getHttpTransports method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Http\HttpFactory::getHttpTransports
	 * @since   1.1.4
	 */
	public function testGetHttpTransports()
	{
		$transports = array('Stream', 'Socket', 'Curl');
		sort($transports);

		$this->assertEquals(
			$transports,
			HttpFactory::getHttpTransports()
		);
	}
}
