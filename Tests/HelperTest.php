<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests;

use Joomla\Filesystem\Helper;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Helper.
 *
 * @since  1.0
 */
class HelperTest extends TestCase
{
	/**
	 * Test remotefsize method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testRemotefsize()
	{
		$this->assertFalse(
			Helper::remotefsize('http://www.joomla.o'),
			'Line:' . __LINE__ . ' for an invalid remote file path, false should be returned.'
		);

		$this->assertTrue(
			is_numeric(Helper::remotefsize('https://www.joomla.org')),
			'Line:' . __LINE__ . ' for a valid remote file, returned size should be numeric.'
		);

		$this->assertFalse(
			Helper::remotefsize('ftppp://ftp.mozilla.org/index.html'),
			'Line:' . __LINE__ . ' for an invalid remote file path, false should be returned.'
		);

		// Find a more reliable FTP server to test with
		if (false)
		{
			$this->assertTrue(
				is_numeric(Helper::remotefsize('ftp://ftp.mozilla.org/index.html')),
				'Line:' . __LINE__ . ' for a valid remote file, returned size should be numeric.'
			);
		}
	}

	/**
	 * Test ftpChmod method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testFtpChmod()
	{
		$this->assertFalse(
			Helper::ftpChmod('ftp://ftppp.mozilla.org/index.html', 0777),
			'Line:' . __LINE__ . ' for an invalid remote file, false should be returned.'
		);

		$this->assertFalse(
			Helper::ftpChmod('ftp://ftp.mozilla.org/index.html', 0777),
			'Line:' . __LINE__ . ' for an inaccessible remote file, false should be returned.'
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testGetWriteModes().
	 *
	 * @return void
	 */
	public function testGetWriteModes()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Test getSupported method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testGetSupported()
	{
		$this->assertTrue(
			\in_array('String', Helper::getSupported()),
			'Line:' . __LINE__ . ' Joomla Streams must contain String.'
		);

		$registeredStreams = stream_get_wrappers();

		$this->assertEquals(
			\count(array_diff($registeredStreams, Helper::getSupported())),
			0,
			'Line:' . __LINE__ . ' getSupported should contains default streams.'
		);
	}

	/**
	 * Test getTransports method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testGetTransports()
	{
		$registeredTransports = stream_get_transports();

		$this->assertEquals(
			\count(array_diff($registeredTransports, Helper::getTransports())),
			0,
			'Line:' . __LINE__ . ' getTransports should contains default transports.'
		);
	}

	/**
	 * Test getFilters method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testGetFilters()
	{
		$registeredFilters = stream_get_filters();

		$this->assertEquals(
			\count(array_diff($registeredFilters, Helper::getFilters())),
			0,
			'Line:' . __LINE__ . ' getFilters should contains default filters.'
		);
	}

	/**
	 * Test getJStreams mthod.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testGetJStreams()
	{
		$streams = Helper::getJStreams();

		$this->assertTrue(\in_array('StringWrapper', Helper::getJStreams()));
	}

	/**
	 * Test
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 * @covers  Joomla\Filesystem\Helper::isJoomlaStream
	 */
	public function testIsJoomlaStream()
	{
		$this->assertTrue(
			Helper::isJoomlaStream('String'),
			'Line:' . __LINE__ . ' String must be a Joomla Stream.'
		);

		$this->assertFalse(
			Helper::isJoomlaStream('unknown'),
			'Line:' . __LINE__ . ' Unkwon is not a Joomla Stream.'
		);
	}
}
