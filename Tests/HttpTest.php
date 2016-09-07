<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Tests;

use Joomla\Http\Http;
use Joomla\Uri\Uri;
use Joomla\Test\TestHelper;

/**
 * Test class for Joomla\Http\Http.
 *
 * @since  1.0
 */
class HttpTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var    array  Options for the Http object.
	 * @since  1.0
	 */
	protected $options;

	/**
	 * @var    Transport  Mock transport object.
	 * @since  1.0
	 */
	protected $transport;

	/**
	 * @var    Http  Object under test.
	 * @since  1.0
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		parent::setUp();

		static $classNumber = 1;
		$this->options = array();

		$this->transport = $this->getMockBuilder('Joomla\Http\TransportInterface')
			->setConstructorArgs(array($this->options))
			->getMock();

		$this->object = new Http($this->options, $this->transport);
	}

	/**
	 * Tests the constructor to ensure only arrays or ArrayAccess objects are allowed
	 *
	 * @return  void
	 *
	 * @expectedException  \InvalidArgumentException
	 */
	public function testConstructorDisallowsNonArrayObjects()
	{
		new Http(new \stdClass);
	}

	/**
	 * Tests the getOption method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetOption()
	{
		$this->object->setOption('testKey', 'testValue');

		$value = TestHelper::getValue($this->object, 'options');

		$this->assertThat(
			$value['testKey'],
			$this->equalTo('testValue')
		);
	}

	/**
	 * Tests the setOption method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetOption()
	{
		TestHelper::setValue(
			$this->object, 'options', array(
				'testKey' => 'testValue'
			)
		);

		$this->assertThat(
			$this->object->getOption('testKey'),
			$this->equalTo('testValue')
		);
	}

	/**
	 * Tests the options method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testOptions()
	{
		$this->transport->expects($this->once())
			->method('request')
			->with('OPTIONS', new Uri('http://example.com'), null, array('testHeader'))
			->will($this->returnValue('ReturnString'));

		$this->assertThat(
			$this->object->options('http://example.com', array('testHeader')),
			$this->equalTo('ReturnString')
		);
	}

	/**
	 * Tests the head method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testHead()
	{
		// Set header option
		$this->object->setOption('headers', array('option' => 'optionHeader'));

		$this->transport->expects($this->once())
			->method('request')
			->with('HEAD', new Uri('http://example.com'), null, array('test' => 'testHeader', 'option' => 'optionHeader'))
			->will($this->returnValue('ReturnString'));

		$this->assertThat(
			$this->object->head('http://example.com', array('test' => 'testHeader')),
			$this->equalTo('ReturnString')
		);
	}

	/**
	 * Tests the get method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGet()
	{
		// Set timeout option
		$this->object->setOption('timeout', 100);

		$this->transport->expects($this->once())
			->method('request')
			->with('GET', new Uri('http://example.com'), null, array('testHeader'), 100)
			->will($this->returnValue('ReturnString'));

		$this->assertThat(
			$this->object->get('http://example.com', array('testHeader')),
			$this->equalTo('ReturnString')
		);
	}

	/**
	 * Tests the get method with an injected Uri object
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetWithUri()
	{
		// Set timeout option
		$this->object->setOption('timeout', 100);

		$this->transport->expects($this->once())
			->method('request')
			->with('GET', new Uri('http://example.com'), null, array('testHeader'), 100)
			->will($this->returnValue('ReturnString'));

		$this->assertThat(
			$this->object->get(new Uri('http://example.com'), array('testHeader')),
			$this->equalTo('ReturnString')
		);
	}

	/**
	 * Tests the get method with an invalid object for the URL
	 *
	 * @return  void
	 *
	 * @since   1.0
	 *
	 * @expectedException  \InvalidArgumentException
	 * @expectedExceptionMessage  A string or UriInterface object must be provided, a "array" was provided.
	 */
	public function testGetWithInvalidUrl()
	{
		// Set timeout option
		$this->object->setOption('timeout', 100);

		$this->assertThat(
			$this->object->get(array(), array('testHeader')),
			$this->equalTo('ReturnString')
		);
	}

	/**
	 * Tests the post method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testPost()
	{
		$this->transport->expects($this->once())
			->method('request')
			->with('POST', new Uri('http://example.com'), array('key' => 'value'), array('testHeader'))
			->will($this->returnValue('ReturnString'));

		$this->assertThat(
			$this->object->post('http://example.com', array('key' => 'value'), array('testHeader')),
			$this->equalTo('ReturnString')
		);
	}

	/**
	 * Tests the put method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testPut()
	{
		$this->transport->expects($this->once())
			->method('request')
			->with('PUT', new Uri('http://example.com'), array('key' => 'value'), array('testHeader'))
			->will($this->returnValue('ReturnString'));

		$this->assertThat(
			$this->object->put('http://example.com', array('key' => 'value'), array('testHeader')),
			$this->equalTo('ReturnString')
		);
	}

	/**
	 * Tests the delete method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testDelete()
	{
		$this->transport->expects($this->once())
			->method('request')
			->with('DELETE', new Uri('http://example.com'), null, array('testHeader'))
			->will($this->returnValue('ReturnString'));

		$this->assertThat(
			$this->object->delete('http://example.com', array('testHeader')),
			$this->equalTo('ReturnString')
		);
	}

	/**
	 * Tests the trace method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testTrace()
	{
		$this->transport->expects($this->once())
			->method('request')
			->with('TRACE', new Uri('http://example.com'), null, array('testHeader'))
			->will($this->returnValue('ReturnString'));

		$this->assertThat(
			$this->object->trace('http://example.com', array('testHeader')),
			$this->equalTo('ReturnString')
		);
	}

	/**
	 * Tests the patch method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testPatch()
	{
		$this->transport->expects($this->once())
			->method('request')
			->with('PATCH', new Uri('http://example.com'), array('key' => 'value'), array('testHeader'))
			->will($this->returnValue('ReturnString'));

		$this->assertThat(
			$this->object->patch('http://example.com', array('key' => 'value'), array('testHeader')),
			$this->equalTo('ReturnString')
		);
	}
}
