<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Filesystem\Helper;

/**
 * Test class for Helper.
 *
 * @since  1.0
 */
class FilesystemHelperTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test...
	 *
	 * @todo Implement testRemotefsize().
	 *
	 * @return void
	 */
	public function testRemotefsize()
	{
		$this->assertFalse(
			Helper::remotefsize('http://www.joomla.o'),
			'Line:' . __LINE__ . ' for an invalid remote file path, false should be returned.'
		);

		$this->assertTrue(
			is_numeric(Helper::remotefsize('http://www.joomla.org')),
			'Line:' . __LINE__ . ' for a valid remote file, returned size should be numeric.'
		);

		$this->assertFalse(
			Helper::remotefsize('ftppp://ftp.mozilla.org/index.html'),
			'Line:' . __LINE__ . ' for an invalid remote file path, false should be returned.'
		);

		$this->assertTrue(
			is_numeric(Helper::remotefsize('ftp://ftp.mozilla.org/index.html')),
			'Line:' . __LINE__ . ' for a valid remote file, returned size should be numeric.'
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testFtpChmod().
	 *
	 * @return void
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
	 * Test...
	 *
	 * @todo Implement testGetSupported().
	 *
	 * @return void
	 */
	public function testGetSupported()
	{
		$this->assertTrue(
			in_array('String', Helper::getSupported()),
			'Line:' . __LINE__ . ' Joomla Streams must contain String.'
		);

		$registeredStreams = stream_get_wrappers();

		$this->assertEquals(
			count(array_diff($registeredStreams, Helper::getSupported())),
			0,
			'Line:' . __LINE__ . ' getSupported should contains default streams.'
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testGetTransports().
	 *
	 * @return void
	 */
	public function testGetTransports()
	{
		$registeredTransports = stream_get_transports();

		$this->assertEquals(
			count(array_diff($registeredTransports, Helper::getTransports())),
			0,
			'Line:' . __LINE__ . ' getTransports should contains default transports.'
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testGetFilters().
	 *
	 * @return void
	 */
	public function testGetFilters()
	{
		$registeredFilters = stream_get_filters();

		$this->assertEquals(
			count(array_diff($registeredFilters, Helper::getFilters())),
			0,
			'Line:' . __LINE__ . ' getFilters should contains default filters.'
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Filesystem\Helper::getJStreams
	 *
	 * @return void
	 */
	public function testGetJStreams()
	{
		$streams = Helper::getJStreams();

		$this->assertTrue(
			in_array('String', $streams),
			'Line:' . __LINE__ . ' Joomla Streams must contain String.'
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Filesystem\Helper::isJoomlaStream
	 *
	 * @return void
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
