<?php
/**
 * Part of the Joomla Framework Mediawiki Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Mediawiki\Tests;

use Joomla\Registry\Registry;
use Joomla\Mediawiki\Http;
use Joomla\Uri\Uri;
use Joomla\Http\Transport\Stream;

/**
 * Test class for Http.
 *
 * @since  1.0
 */
class HttpTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var    Registry  Options for the Mediawiki object.
	 * @since  1.0
	 */
	protected $options;

	/**
	 * @var    Http  Mock client object.
	 * @since  1.0
	 */
	protected $transport;

	/**
	 * @var    Http  Object under test.
	 * @since  1.0
	 */
	protected $object;

	/**
	 * @var    string  Sample xml string.
	 * @since  1.0
	 */
	protected $sampleString = '<a><b></b><c></c></a>';

	/**
	 * @var    string  Sample xml error message.
	 * @since  1.0
	 */
	protected $errorString = '<message>Generic Error</message>';

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	protected function setUp()
	{
		$this->options = new Registry;

		$errorLevel = error_reporting();
		error_reporting($errorLevel & ~E_DEPRECATED);

		$this->transport = $this->getMock('Joomla\\Http\\Transport\\Stream', array('request'), array($this->options), 'CustomTransport', false);

		error_reporting($errorLevel);

		$this->object = new Http($this->options, $this->transport);
	}

	/**
	 * Tests the get method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testGet()
	{
		$uri = new Uri('https://example.com/gettest');

		$this->transport->expects($this->once())
			->method('request')
			->with('GET', $uri)
			->will($this->returnValue('requestResponse'));

		$this->assertThat(
			$this->object->get('https://example.com/gettest'),
			$this->equalTo('requestResponse')
		);
	}

	/**
	 * Tests the post method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testPost()
	{
		$uri = new Uri('https://example.com/gettest');

		$this->transport->expects($this->once())
			->method('request')
			->with('POST', $uri, array())
			->will($this->returnValue('requestResponse'));

		$this->assertThat(
			$this->object->post('https://example.com/gettest', array()),
			$this->equalTo('requestResponse')
		);
	}
}
