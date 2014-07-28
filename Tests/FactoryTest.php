<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
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
	 * @covers Joomla\Http\HttpFactory::getHttp
	 * @since   1.0
	 */
	public function testGetHttp()
	{
		$this->assertThat(
			HttpFactory::getHttp(),
			$this->isInstanceOf('Joomla\\Http\\Http')
		);
	}

	/**
	 * Tests the getHttp method.
	 *
	 * @return  void
	 *
	 * @covers Joomla\Http\HttpFactory::getHttp
	 * @expectedException RuntimeException
	 * @since   1.0
	 */
	public function testGetHttpException()
	{
		$this->assertThat(
			HttpFactory::getHttp(array(), array()),
			$this->isInstanceOf('Joomla\\Http\\Http')
		);
	}

	/**
	 * Tests the getAvailableDriver method.
	 *
	 * @return  void
	 *
	 * @covers Joomla\Http\HttpFactory::getAvailableDriver
	 * @since   1.0
	 */
	public function testGetAvailableDriver()
	{
		$this->assertInstanceOf(
			'Joomla\\Http\\TransportInterface',
			HttpFactory::getAvailableDriver(array(), null)
		);

		$this->assertThat(
			HttpFactory::getAvailableDriver(array(), array()),
			$this->isFalse(),
			'Passing an empty array should return false due to there being no adapters to test'
		);

		$this->assertThat(
			HttpFactory::getAvailableDriver(array(), array('fopen')),
			$this->isFalse(),
			'A false should be returned if a class is not present or supported'
		);

		include_once __DIR__ . '/stubs/DummyTransport.php';

		$this->assertThat(
			HttpFactory::getAvailableDriver(array(), array('DummyTransport')),
			$this->isFalse(),
			'Passing an empty array should return false due to there being no adapters to test'
		);
	}

	/**
	 * Tests the getHttpTransports method.
	 *
	 * @return  void
	 *
	 * @covers Joomla\Http\HttpFactory::getHttpTransports
	 * @since   1.0
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
