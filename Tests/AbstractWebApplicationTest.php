<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application\Tests;

use Joomla\Application\Web\WebClient;
use Joomla\Test\TestHelper;

/**
 * Test class for Joomla\Application\AbstractWebApplication.
 */
class AbstractWebApplicationTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Value for test host.
	 *
	 * @var  string
	 */
	const TEST_HTTP_HOST = 'mydomain.com';

	/**
	 * Value for test user agent.
	 *
	 * @var  string
	 */
	const TEST_USER_AGENT = 'Mozilla/5.0';

	/**
	 * Value for test user agent.
	 *
	 * @var  string
	 */
	const TEST_REQUEST_URI = '/index.php';

	/**
	 * List of sent headers for inspection. array($string, $replace, $code).
	 *
	 * @var  array
	 */
	private static $headers = array();

	/**
	 * Data for detectRequestUri method.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getDetectRequestUriData()
	{
		return array(
			// HTTPS, PHP_SELF, REQUEST_URI, HTTP_HOST, SCRIPT_NAME, QUERY_STRING, (resulting uri)
			array(null, '/j/index.php', '/j/index.php?foo=bar', 'joom.la:3', '/j/index.php', '', 'http://joom.la:3/j/index.php?foo=bar'),
			array('on', '/j/index.php', '/j/index.php?foo=bar', 'joom.la:3', '/j/index.php', '', 'https://joom.la:3/j/index.php?foo=bar'),
			array(null, '', '', 'joom.la:3', '/j/index.php', '', 'http://joom.la:3/j/index.php'),
			array(null, '', '', 'joom.la:3', '/j/index.php', 'foo=bar', 'http://joom.la:3/j/index.php?foo=bar'),
		);
	}

	/**
	 * Data for testRedirectWithUrl method.
	 *
	 * @return  array
	 */
	public function getRedirectData()
	{
		return array(
			// Note: url, (expected result)
			'with_leading_slash' => array('/foo', 'http://' . self::TEST_HTTP_HOST . '/foo'),
			'without_leading_slash' => array('foo', 'http://' . self::TEST_HTTP_HOST . '/foo'),
		);
	}

	/**
	 * Mock to send a header to the client.
	 *
	 * @param   string   $string   The header string.
	 * @param   boolean  $replace  The optional replace parameter indicates whether the header should
	 *                             replace a previous similar header, or add a second header of the same type.
	 * @param   integer  $code     Forces the HTTP response code to the specified value. Note that
	 *                             this parameter only has an effect if the string is not empty.
	 *
	 * @return  void
	 */
	public static function mockHeader($string, $replace = true, $code = null)
	{
		self::$headers[] = array($string, $replace, $code);
	}

	/**
	 * @testdox  Tests the constructor creates default object instances
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::__construct
	 */
	public function test__constructDefaultBehaviour()
	{
		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication');

		// Validate default objects are created
		$this->assertAttributeInstanceOf('Joomla\Input\Input', 'input', $object);
		$this->assertAttributeInstanceOf('Joomla\Registry\Registry', 'config', $object);
		$this->assertAttributeInstanceOf('Joomla\Application\Web\WebClient', 'client', $object);

		// Validate default configuration data is written
		$executionDateTime = new \DateTime($object->get('execution.datetime'));

		$this->assertSame(date('Y'), $executionDateTime->format('Y'));
	}

	/**
	 * @testdox  Tests the correct objects are stored when injected
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::__construct
	 * @uses    Joomla\Application\AbstractApplication::get
	 */
	public function test__constructDependencyInjection()
	{
		$mockInput  = $this->getMock('Joomla\Input\Input', array('get', 'getString'), array(), '', true, true, true, false, true);
		$mockConfig = $this->getMock('Joomla\Registry\Registry', array('get', 'set'), array(), '', true, true, true, false, true);
		$mockClient = $this->getMock('Joomla\Application\Web\WebClient');

		// Mock the Input object internals
		$mockServerInput = $this->getMock('Joomla\Input\Input', array('get', 'set'), array(array('HTTP_HOST' => self::TEST_HTTP_HOST)), '', true, true, true, false, true);

		$inputInternals = array(
			'server' => $mockServerInput
		);

		TestHelper::setValue($mockInput, 'inputs', $inputInternals);

		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication', array($mockInput, $mockConfig, $mockClient));

		$this->assertAttributeSame($mockInput, 'input', $object);
		$this->assertAttributeSame($mockConfig, 'config', $object);
		$this->assertAttributeSame($mockClient, 'client', $object);

		$this->assertEquals('http://' . self::TEST_HTTP_HOST, $object->get('uri.base.host'));
	}

	/**
	 * @testdox  Tests that the application is executed successfully.
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::execute
	 * @uses    Joomla\Application\AbstractWebApplication::allowCache
	 * @uses    Joomla\Application\AbstractWebApplication::getBody
	 * @uses    Joomla\Application\AbstractWebApplication::getHeaders
	 */
	public function testExecute()
	{
		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication');
		$object->expects($this->once())
			->method('doExecute');

		// execute() has no return, with our mock nothing should happen but ensuring that the mock's doExecute() stub is triggered
		$this->assertNull($object->execute());

		$this->assertFalse($object->allowCache());

		$headers = $object->getHeaders();

		$this->assertSame(
			array(
				'name'  => 'Content-Type',
				'value' => 'text/html; charset=utf-8'
			),
			$headers[0]
		);

		$this->assertEmpty($object->getBody(true));
	}

	/**
	 * @testdox  Tests that the application with compression enabled is executed successfully.
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::execute
	 * @uses    Joomla\Application\AbstractWebApplication::allowCache
	 * @uses    Joomla\Application\AbstractWebApplication::getBody
	 * @uses    Joomla\Application\AbstractWebApplication::getHeaders
	 */
	public function testExecuteWithCompression()
	{
		// Verify compression is supported in this environment
		if (!(!ini_get('zlib.output_compression') && (ini_get('output_handler') != 'ob_gzhandler')))
		{
			$this->markTestSkipped('Output compression is unsupported in this environment.');
		}

		$mockConfig = $this->getMock('Joomla\Registry\Registry', array('get', 'set'), array(array('gzip' => true)), '', true, true, true, false, true);

		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication', array(null, $mockConfig));
		$object->expects($this->once())
			->method('doExecute');

		// execute() has no return, with our mock nothing should happen but ensuring that the mock's doExecute() stub is triggered
		$this->assertNull($object->execute());

		$this->assertFalse($object->allowCache());

		$headers = $object->getHeaders();

		$this->assertSame(
			array(
				'name'  => 'Content-Type',
				'value' => 'text/html; charset=utf-8'
			),
			$headers[0]
		);

		$this->assertEmpty($object->getBody(true));
	}

	/**
	 * @testdox  Tests the compress() method correctly compresses data with gzip encoding
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::compress
	 * @uses    Joomla\Application\AbstractWebApplication::getBody
	 * @uses    Joomla\Application\AbstractWebApplication::getHeaders
	 */
	public function testCompressWithGzipEncoding()
	{
		$mockClient = $this->getMock('Joomla\Application\Web\WebClient', array(), array(null, 'gzip, deflate'), '', true, true, true, false, true);

		// Mock the client internals to show encoding has been detected.
		TestHelper::setValue(
			$mockClient,
			'detection',
			array('acceptEncoding' => true)
		);
		TestHelper::setValue(
			$mockClient,
			'encodings',
			array('gzip', 'deflate')
		);

		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication', array(null, null, $mockClient), '', true, true, true, array('checkHeadersSent'));
		$object->expects($this->once())
			->method('checkHeadersSent')
			->willReturn(false);

		// Mock a response.
		$mockResponse = (object) array(
			'cachable' => null,
			'headers' => null,
			'body' => array('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
				eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
				veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
				consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
				dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
				sunt in culpa qui officia deserunt mollit anim id est laborum.'),
		);

		TestHelper::setValue(
			$object,
			'response',
			$mockResponse
		);

		TestHelper::invoke($object, 'compress');

		// Ensure that the compressed body is shorter than the raw body.
		$this->assertLessThan(
			strlen($mockResponse->body[0]),
			$object->getBody()
		);

		// Ensure that the compression headers were set.
		$this->assertSame(
			array(
				0 => array('name' => 'Content-Encoding', 'value' => 'gzip'),
				1 => array('name' => 'X-Content-Encoded-By', 'value' => 'Joomla')
			),
			$object->getHeaders()
		);
	}

	/**
	 * @testdox  Tests the compress() method correctly compresses data with deflate encoding
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::compress
	 * @uses    Joomla\Application\AbstractWebApplication::getBody
	 * @uses    Joomla\Application\AbstractWebApplication::getHeaders
	 */
	public function testCompressWithDeflateEncoding()
	{
		$mockClient = $this->getMock('Joomla\Application\Web\WebClient', array(), array(null, 'deflate'), '', true, true, true, false, true);

		// Mock the client internals to show encoding has been detected.
		TestHelper::setValue(
			$mockClient,
			'detection',
			array('acceptEncoding' => true)
		);
		TestHelper::setValue(
			$mockClient,
			'encodings',
			array('deflate', 'gzip')
		);

		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication', array(null, null, $mockClient), '', true, true, true, array('checkHeadersSent'));
		$object->expects($this->once())
			->method('checkHeadersSent')
			->willReturn(false);

		// Mock a response.
		$mockResponse = (object) array(
			'cachable' => null,
			'headers' => null,
			'body' => array('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
				eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
				veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
				consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
				dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
				sunt in culpa qui officia deserunt mollit anim id est laborum.'),
		);

		TestHelper::setValue(
			$object,
			'response',
			$mockResponse
		);

		TestHelper::invoke($object, 'compress');

		// Ensure that the compressed body is shorter than the raw body.
		$this->assertLessThan(
			strlen($mockResponse->body[0]),
			$object->getBody()
		);

		// Ensure that the compression headers were set.
		$this->assertSame(
			array(
				0 => array('name' => 'Content-Encoding', 'value' => 'deflate'),
				1 => array('name' => 'X-Content-Encoded-By', 'value' => 'Joomla')
			),
			$object->getHeaders()
		);
	}

	/**
	 * @testdox  Tests the compress() method does not compress data when no encoding methods are supported
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::compress
	 * @uses    Joomla\Application\AbstractWebApplication::getBody
	 * @uses    Joomla\Application\AbstractWebApplication::getHeaders
	 */
	public function testCompressWithNoAcceptEncodings()
	{
		$mockClient = $this->getMock('Joomla\Application\Web\WebClient', array(), array(), '', true, true, true, false, true);

		// Mock the client internals to show encoding has been detected.
		TestHelper::setValue(
			$mockClient,
			'detection',
			array('acceptEncoding' => true)
		);

		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication', array(null, null, $mockClient), '', true, true, true, array('checkHeadersSent'));

		// Mock a response.
		$mockResponse = (object) array(
			'cachable' => null,
			'headers' => null,
			'body' => array(str_replace("\r\n", "\n", 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
				eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
				veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
				consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
				dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
				sunt in culpa qui officia deserunt mollit anim id est laborum.')),
		);

		TestHelper::setValue(
			$object,
			'response',
			$mockResponse
		);

		TestHelper::invoke($object, 'compress');

		// Ensure that the compressed body is shorter than the raw body.
		$this->assertSame(
			strlen($mockResponse->body[0]),
			strlen($object->getBody())
		);

		// Ensure that no compression headers were set.
		$this->assertNull($object->getHeaders());
	}

	/**
	 * @testdox  Tests the compress() method does not compress data when the response headers have already been sent
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::compress
	 * @uses    Joomla\Application\AbstractWebApplication::getBody
	 * @uses    Joomla\Application\AbstractWebApplication::getHeaders
	 */
	public function testCompressWithHeadersSent()
	{
		$mockClient = $this->getMock('Joomla\Application\Web\WebClient', array(), array(null, 'deflate'), '', true, true, true, false, true);

		// Mock the client internals to show encoding has been detected.
		TestHelper::setValue(
			$mockClient,
			'detection',
			array('acceptEncoding' => true)
		);
		TestHelper::setValue(
			$mockClient,
			'encodings',
			array('deflate', 'gzip')
		);

		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication', array(null, null, $mockClient));

		// Mock a response.
		$mockResponse = (object) array(
			'cachable' => null,
			'headers' => null,
			'body' => array(str_replace("\r\n", "\n", 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
				eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
				veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
				consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
				dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
				sunt in culpa qui officia deserunt mollit anim id est laborum.')),
		);

		TestHelper::setValue(
			$object,
			'response',
			$mockResponse
		);

		TestHelper::invoke($object, 'compress');

		// Ensure that the compressed body is shorter than the raw body.
		$this->assertSame(
			strlen($mockResponse->body[0]),
			strlen($object->getBody())
		);

		// Ensure that no compression headers were set.
		$this->assertNull($object->getHeaders());
	}

	/**
	 * @testdox  Tests the compress() method does not compress data when the application does not support the client's encoding methods
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::compress
	 * @uses    Joomla\Application\AbstractWebApplication::getBody
	 * @uses    Joomla\Application\AbstractWebApplication::getHeaders
	 */
	public function testCompressWithUnsupportedEncodings()
	{
		$mockClient = $this->getMock('Joomla\Application\Web\WebClient', array(), array(), '', true, true, true, false, true);

		// Mock the client internals to show encoding has been detected.
		TestHelper::setValue(
			$mockClient,
			'detection',
			array('acceptEncoding' => true)
		);
		TestHelper::setValue(
			$mockClient,
			'encodings',
			array('foo', 'bar')
		);

		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication', array(null, null, $mockClient));

		// Mock a response.
		$mockResponse = (object) array(
			'cachable' => null,
			'headers' => null,
			'body' => array(str_replace("\r\n", "\n", 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
				eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
				veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
				consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
				dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
				sunt in culpa qui officia deserunt mollit anim id est laborum.')),
		);

		TestHelper::setValue(
			$object,
			'response',
			$mockResponse
		);

		TestHelper::invoke($object, 'compress');

		// Ensure that the compressed body is shorter than the raw body.
		$this->assertSame(
			strlen($mockResponse->body[0]),
			strlen($object->getBody())
		);

		// Ensure that no compression headers were set.
		$this->assertNull($object->getHeaders());
	}

	/**
	 * @testdox  Tests that the application sends the response successfully.
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::respond
	 * @uses    Joomla\Application\AbstractWebApplication::allowCache
	 * @uses    Joomla\Application\AbstractWebApplication::getBody
	 * @uses    Joomla\Application\AbstractWebApplication::getHeaders
	 */
	public function testRespond()
	{
		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication');

		TestHelper::invoke($object, 'respond');

		$this->assertFalse($object->allowCache());

		$headers = $object->getHeaders();

		$this->assertSame(
			array(
				'name'  => 'Content-Type',
				'value' => 'text/html; charset=utf-8'
			),
			$headers[0]
		);

		$this->assertEmpty($object->getBody(true));
	}

	/**
	 * @testdox  Tests that the application sends the response successfully with allowed caching.
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::respond
	 * @uses    Joomla\Application\AbstractWebApplication::allowCache
	 * @uses    Joomla\Application\AbstractWebApplication::getBody
	 * @uses    Joomla\Application\AbstractWebApplication::getHeaders
	 */
	public function testRespondWithAllowedCaching()
	{
		$modifiedDate = new \DateTime('now', new \DateTimeZone('UTC'));

		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication');
		$object->allowCache(true);
		$object->modifiedDate = $modifiedDate;

		TestHelper::invoke($object, 'respond');

		$this->assertTrue($object->allowCache());

		$headers = $object->getHeaders();

		$this->assertSame(
			array(
				'name'  => 'Last-Modified',
				'value' => $modifiedDate->format('D, d M Y H:i:s') . ' GMT'
			),
			$headers[2]
		);

		$this->assertEmpty($object->getBody(true));
	}

	/**
	 * @testdox  Tests that the application redirects successfully.
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::redirect
	 */
	public function testRedirect()
	{
		$mockInput  = $this->getMock('Joomla\Input\Input', array('get', 'getString'), array(), '', true, true, true, false, true);
		$mockConfig = $this->getMock('Joomla\Registry\Registry', array('get', 'set'), array(), '', true, true, true, false, true);
		$mockClient = $this->getMock('Joomla\Application\Web\WebClient', array(), array(), '', true, true, true, false, true);

		// Mock the Input object internals
		$mockServerInput = $this->getMock(
			'Joomla\Input\Input',
			array('get', 'set'),
			array(
				array(
					'HTTP_HOST'   => self::TEST_HTTP_HOST,
					'REQUEST_URI' => self::TEST_REQUEST_URI,
					'SCRIPT_NAME' => '/index.php'
				)
			),
			'',
			true,
			true,
			true,
			false,
			true
		);

		$inputInternals = array(
			'server' => $mockServerInput
		);

		TestHelper::setValue($mockInput, 'inputs', $inputInternals);

		// Mock the client internals to show engine has been detected.
		TestHelper::setValue(
			$mockClient,
			'detection',
			array('engine' => true)
		);
		TestHelper::setValue(
			$mockClient,
			'engine',
			WebClient::GECKO
		);

		$object = $this->getMockForAbstractClass(
			'Joomla\Application\AbstractWebApplication',
			array($mockInput, $mockConfig, $mockClient),
			'',
			true,
			true,
			true,
			array('checkHeadersSent', 'close', 'header')
		);

		$object->expects($this->once())
			->method('close');
		$object->expects($this->any())
			->method('checkHeadersSent')
			->willReturn(false);
		$object->expects($this->any())
			->method('header')
			->willReturnCallback(array($this, 'mockHeader'));

		$url = 'index.php';

		$object->redirect($url);

		$this->assertSame(
			self::$headers,
			array(
				array('HTTP/1.1 303 See other', true, null),
				array('Location: http://' . self::TEST_HTTP_HOST . "/$url", true, null),
				array('Content-Type: text/html; charset=utf-8', true, null),
			)
		);
	}

	/**
	 * @testdox  Tests that the application redirects and sends additional headers successfully.
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::redirect
	 */
	public function testRedirectWithAdditionalHeaders()
	{
		$mockInput  = $this->getMock('Joomla\Input\Input', array('get', 'getString'), array(), '', true, true, true, false, true);
		$mockConfig = $this->getMock('Joomla\Registry\Registry', array('get', 'set'), array(), '', true, true, true, false, true);
		$mockClient = $this->getMock('Joomla\Application\Web\WebClient', array(), array(), '', true, true, true, false, true);

		// Mock the Input object internals
		$mockServerInput = $this->getMock(
			'Joomla\Input\Input',
			array('get', 'set'),
			array(
				array(
					'HTTP_HOST'   => self::TEST_HTTP_HOST,
					'REQUEST_URI' => self::TEST_REQUEST_URI,
					'SCRIPT_NAME' => '/index.php'
				)
			),
			'',
			true,
			true,
			true,
			false,
			true
		);

		$inputInternals = array(
			'server' => $mockServerInput
		);

		TestHelper::setValue($mockInput, 'inputs', $inputInternals);

		// Mock the client internals to show engine has been detected.
		TestHelper::setValue(
			$mockClient,
			'detection',
			array('engine' => true)
		);
		TestHelper::setValue(
			$mockClient,
			'engine',
			WebClient::GECKO
		);

		$object = $this->getMockForAbstractClass(
			'Joomla\Application\AbstractWebApplication',
			array($mockInput, $mockConfig, $mockClient),
			'',
			true,
			true,
			true,
			array('checkHeadersSent', 'close', 'header')
		);

		$object->expects($this->once())
			->method('close');
		$object->expects($this->any())
			->method('checkHeadersSent')
			->willReturn(false);
		$object->expects($this->any())
			->method('header')
			->willReturnCallback(array($this, 'mockHeader'));

		$url = 'index.php';
		$expires = gmdate('D, d M Y H:i:s \G\M\T', time());

		$object->setHeader('Cache-Control', 'no-cache')
			->setHeader('Expires', $expires);

		$object->redirect($url);

		$this->assertSame(
			self::$headers,
			array(
				array('HTTP/1.1 303 See other', true, null),
				array('Location: http://' . self::TEST_HTTP_HOST . "/$url", true, null),
				array('Content-Type: text/html; charset=utf-8', true, null),
				array('Cache-Control: no-cache', true, null),
				array('Expires: ' . $expires, true, null),
			)
		);
	}

	/**
	 * @testdox  Tests that the application redirects successfully when the headers have already been sent.
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::redirect
	 */
	public function testRedirectWithHeadersSent()
	{
		$mockInput  = $this->getMock('Joomla\Input\Input', array('get', 'getString'), array(), '', true, true, true, false, true);
		$mockConfig = $this->getMock('Joomla\Registry\Registry', array('get', 'set'), array(), '', true, true, true, false, true);

		// Mock the Input object internals
		$mockServerInput = $this->getMock(
			'Joomla\Input\Input',
			array('get', 'set'),
			array(
				array(
					'HTTP_HOST'   => self::TEST_HTTP_HOST,
					'REQUEST_URI' => self::TEST_REQUEST_URI,
					'SCRIPT_NAME' => '/index.php'
				)
			),
			'',
			true,
			true,
			true,
			false,
			true
		);

		$inputInternals = array(
			'server' => $mockServerInput
		);

		TestHelper::setValue($mockInput, 'inputs', $inputInternals);

		$object = $this->getMockForAbstractClass(
			'Joomla\Application\AbstractWebApplication',
			array($mockInput, $mockConfig),
			'',
			true,
			true,
			true,
			array('checkHeadersSent', 'close')
		);

		$object->expects($this->once())
			->method('close');
		$object->expects($this->any())
			->method('checkHeadersSent')
			->willReturn(true);

		$url = 'index.php';

		// Capture the output for this test.
		ob_start();
		$object->redirect('index.php');
		$buffer = ob_get_clean();

		$this->assertSame(
			"<script>document.location.href='http://" . self::TEST_HTTP_HOST . "/$url';</script>\n",
			$buffer
		);
	}

	/**
	 * @testdox  Tests that the application redirects successfully with a JavaScript redirect.
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::redirect
	 */
	public function testRedirectWithJavascriptRedirect()
	{
		$mockInput  = $this->getMock('Joomla\Input\Input', array('get', 'getString'), array(), '', true, true, true, false, true);
		$mockConfig = $this->getMock('Joomla\Registry\Registry', array('get', 'set'), array(), '', true, true, true, false, true);
		$mockClient = $this->getMock('Joomla\Application\Web\WebClient', array(), array('MSIE'), '', true, true, true, false, true);

		// Mock the Input object internals
		$mockServerInput = $this->getMock(
			'Joomla\Input\Input',
			array('get', 'set'),
			array(
				array(
					'HTTP_HOST'   => self::TEST_HTTP_HOST,
					'REQUEST_URI' => self::TEST_REQUEST_URI
				)
			),
			'',
			true,
			true,
			true,
			false,
			true
		);

		$inputInternals = array(
			'server' => $mockServerInput
		);

		TestHelper::setValue($mockInput, 'inputs', $inputInternals);

		// Mock the client internals to show engine has been detected.
		TestHelper::setValue(
			$mockClient,
			'detection',
			array('engine' => true)
		);
		TestHelper::setValue(
			$mockClient,
			'engine',
			WebClient::TRIDENT
		);

		$object = $this->getMockForAbstractClass(
			'Joomla\Application\AbstractWebApplication',
			array($mockInput, $mockConfig, $mockClient),
			'',
			true,
			true,
			true,
			array('checkHeadersSent', 'close', 'header')
		);

		$object->expects($this->once())
			->method('close');
		$object->expects($this->any())
			->method('checkHeadersSent')
			->willReturn(false);

		$url = 'http://j.org/index.php?phi=Φ';

		// Capture the output for this test.
		ob_start();
		$object->redirect($url);
		$buffer = ob_get_clean();

		$this->assertSame(
			'<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" />'
			. "<script>document.location.href='{$url}';</script></head><body></body></html>",
			trim($buffer)
		);
	}

	/**
	 * @testdox  Tests that the application redirects successfully with the moved parameter set to true.
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::redirect
	 */
	public function testRedirectWithMoved()
	{
		$mockInput  = $this->getMock('Joomla\Input\Input', array('get', 'getString'), array(), '', true, true, true, false, true);
		$mockConfig = $this->getMock('Joomla\Registry\Registry', array('get', 'set'), array(), '', true, true, true, false, true);
		$mockClient = $this->getMock('Joomla\Application\Web\WebClient', array(), array(), '', true, true, true, false, true);

		// Mock the Input object internals
		$mockServerInput = $this->getMock(
			'Joomla\Input\Input',
			array('get', 'set'),
			array(
				array(
					'HTTP_HOST'   => self::TEST_HTTP_HOST,
					'REQUEST_URI' => self::TEST_REQUEST_URI
				)
			),
			'',
			true,
			true,
			true,
			false,
			true
		);

		$inputInternals = array(
			'server' => $mockServerInput
		);

		TestHelper::setValue($mockInput, 'inputs', $inputInternals);

		// Mock the client internals to show engine has been detected.
		TestHelper::setValue(
			$mockClient,
			'detection',
			array('engine' => true)
		);
		TestHelper::setValue(
			$mockClient,
			'engine',
			WebClient::GECKO
		);

		$object = $this->getMockForAbstractClass(
			'Joomla\Application\AbstractWebApplication',
			array($mockInput, $mockConfig, $mockClient),
			'',
			true,
			true,
			true,
			array('checkHeadersSent', 'close', 'header')
		);

		$object->expects($this->once())
			->method('close');
		$object->expects($this->any())
			->method('checkHeadersSent')
			->willReturn(false);
		$object->expects($this->any())
			->method('header')
			->willReturnCallback(array($this, 'mockHeader'));

		$url = 'http://j.org/index.php';

		$object->redirect($url, true);

		$this->assertSame(
			self::$headers,
			array(
				array('HTTP/1.1 301 Moved Permanently', true, null),
				array('Location: ' . $url, true, null),
				array('Content-Type: text/html; charset=utf-8', true, null),
			)
		);
	}

	/**
	 * @testdox  Tests that the application redirects successfully with the moved parameter set to true.
	 *
	 * @param   string  $url       The URL to redirect to
	 * @param   string  $expected  The expected redirect URL
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::redirect
	 * @dataProvider  getRedirectData
	 */
	public function testRedirectWithUrl($url, $expected)
	{
		$mockInput  = $this->getMock('Joomla\Input\Input', array('get', 'getString'), array(), '', true, true, true, false, true);
		$mockConfig = $this->getMock('Joomla\Registry\Registry', array('get', 'set'), array(), '', true, true, true, false, true);
		$mockClient = $this->getMock('Joomla\Application\Web\WebClient', array(), array(), '', true, true, true, false, true);

		// Mock the Input object internals
		$mockServerInput = $this->getMock(
			'Joomla\Input\Input',
			array('get', 'set'),
			array(
				array(
					'HTTP_HOST'   => self::TEST_HTTP_HOST,
					'REQUEST_URI' => self::TEST_REQUEST_URI
				)
			),
			'',
			true,
			true,
			true,
			false,
			true
		);

		$inputInternals = array(
			'server' => $mockServerInput
		);

		TestHelper::setValue($mockInput, 'inputs', $inputInternals);

		// Mock the client internals to show engine has been detected.
		TestHelper::setValue(
			$mockClient,
			'detection',
			array('engine' => true)
		);
		TestHelper::setValue(
			$mockClient,
			'engine',
			WebClient::GECKO
		);

		$object = $this->getMockForAbstractClass(
			'Joomla\Application\AbstractWebApplication',
			array($mockInput, $mockConfig, $mockClient),
			'',
			true,
			true,
			true,
			array('checkHeadersSent', 'close', 'header')
		);

		$object->expects($this->once())
			->method('close');
		$object->expects($this->any())
			->method('checkHeadersSent')
			->willReturn(false);
		$object->expects($this->any())
			->method('header')
			->willReturnCallback(array($this, 'mockHeader'));

		$object->redirect($url);

		$this->assertSame(
			'Location: ' . $expected,
			self::$headers[1][0]
		);
	}

	/**
	 * @testdox  Tests the allowCache() method returns the allowed cache state
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::allowCache
	 */
	public function testAllowCache()
	{
		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication');

		$this->assertFalse($object->allowCache());
		$this->assertTrue($object->allowCache(true));
	}

	/**
	 * @testdox  Tests the setHeader() method correctly sets and replaces a specified header
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::setHeader
	 * @uses    Joomla\Application\AbstractWebApplication::getHeaders
	 */
	public function testSetHeader()
	{
		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication');

		$object->setHeader('foo', 'bar');

		$this->assertSame(
			$object->getHeaders(),
			array(
				array('name' => 'foo', 'value' => 'bar')
			)
		);

		$object->setHeader('foo', 'car', true);

		$this->assertSame(
			$object->getHeaders(),
			array(
				array('name' => 'foo', 'value' => 'car')
			),
			'A header with the same name should be replaced.'
		);
	}

	/**
	 * @testdox  Tests the getHeaders() method return an array
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::getHeaders
	 */
	public function testGetHeaders()
	{
		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication');

		$this->assertEmpty($object->getHeaders());
	}

	/**
	 * @testdox  Tests the clearHeaders() method resets the internal headers array
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::clearHeaders
	 * @uses    Joomla\Application\AbstractWebApplication::getHeaders
	 * @uses    Joomla\Application\AbstractWebApplication::setHeader
	 */
	public function testClearHeaders()
	{
		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication');
		$object->setHeader('foo', 'bar');
		$oldHeaders = $object->getHeaders();

		$this->assertSame($object, $object->clearHeaders());
		$this->assertNotSame($oldHeaders, $object->getHeaders());
	}

	/**
	 * @testdox  Tests the sendHeaders() method correctly sends the response headers
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::sendHeaders
	 * @uses    Joomla\Application\AbstractWebApplication::setHeader
	 */
	public function testSendHeaders()
	{
		$object = $this->getMockForAbstractClass(
			'Joomla\Application\AbstractWebApplication',
			array(),
			'',
			true,
			true,
			true,
			array('checkHeadersSent', 'header')
		);

		$object->expects($this->any())
			->method('checkHeadersSent')
			->willReturn(false);
		$object->expects($this->any())
			->method('header')
			->willReturnCallback(array($this, 'mockHeader'));

		$object->setHeader('foo', 'bar');
		$object->setHeader('Status', 200);

		$this->assertSame($object, $object->sendHeaders());
		$this->assertSame(
			self::$headers,
			array(
				array('foo: bar', true, null),
				array('HTTP/1.1 200', null, 200)
			)
		);
	}

	/**
	 * @testdox  Tests the setBody() method correctly sets the response body
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::setBody
	 * @uses    Joomla\Application\AbstractWebApplication::getBody
	 */
	public function testSetBody()
	{
		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication');

		$this->assertSame($object, $object->setBody('Testing'));
		$this->assertSame('Testing', $object->getBody());
	}

	/**
	 * @testdox  Tests the prependBody() method correctly prepends content to the response body
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::prependBody
	 * @uses    Joomla\Application\AbstractWebApplication::getBody
	 * @uses    Joomla\Application\AbstractWebApplication::setBody
	 */
	public function testPrependBody()
	{
		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication');

		$object->setBody('Testing');
		$this->assertSame($object, $object->prependBody('Pre-'));
		$this->assertSame('Pre-Testing', $object->getBody());
	}

	/**
	 * @testdox  Tests the appendBody() method correctly appends content to the response body
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::appendBody
	 * @uses    Joomla\Application\AbstractWebApplication::getBody
	 * @uses    Joomla\Application\AbstractWebApplication::setBody
	 */
	public function testAppendBody()
	{
		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication');

		$object->setBody('Testing');
		$this->assertSame($object, $object->appendBody(' Later'));
		$this->assertSame('Testing Later', $object->getBody());
	}

	/**
	 * @testdox  Tests the getBody() method correctly retrieves the response body
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::getBody
	 */
	public function testGetBody()
	{
		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication');

		$this->assertSame('', $object->getBody(), 'Returns an empty string by default');
		$this->assertSame(array(), $object->getBody(true), 'Returns an empty array when requesting the body as an array');
	}

	/**
	 * @testdox  Tests that the application correcty detects the request URI based on the injected data
	 *
	 * @param   string  $https        Value for $_SERVER['HTTPS'] or null to not set it
	 * @param   string  $phpSelf      Value for $_SERVER['PHP_SELF']
	 * @param   string  $requestUri   Value for $_SERVER['REQUEST_URI']
	 * @param   string  $httpHost     Value for $_SERVER['HTTP_HOST']
	 * @param   string  $scriptName   Value for $_SERVER['SCRIPT_NAME']
	 * @param   string  $queryString  Value for $_SERVER['QUERY_STRING']
	 * @param   string  $expects      Expected full URI string
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::detectRequestUri
	 * @dataProvider  getDetectRequestUriData
	 */
	public function testDetectRequestUri($https, $phpSelf, $requestUri, $httpHost, $scriptName, $queryString, $expects)
	{
		$mockInput = $this->getMock('Joomla\Input\Input', array('get', 'getString'), array(), '', true, true, true, false, true);

		$serverInputData = array(
			'PHP_SELF'     => $phpSelf,
			'REQUEST_URI'  => $requestUri,
			'HTTP_HOST'    => $httpHost,
			'SCRIPT_NAME'  => $scriptName,
			'QUERY_STRING' => $queryString
		);

		if ($https !== null)
		{
			$serverInputData['HTTPS'] = $https;
		}

		// Mock the Input object internals
		$mockServerInput = $this->getMock('Joomla\Input\Input', array('get', 'set'), array($serverInputData), '', true, true, true, false, true);

		$inputInternals = array(
			'server' => $mockServerInput
		);

		TestHelper::setValue($mockInput, 'inputs', $inputInternals);

		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication', array($mockInput));

		$this->assertSame(
			$expects,
			TestHelper::invoke($object, 'detectRequestUri')
		);
	}

	/**
	 * @testdox  Tests the system URIs are correctly loaded when a URI is set in the application configuration
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::loadSystemUris
	 * @uses    Joomla\Application\AbstractApplication::get
	 */
	public function testLoadSystemUrisWithSiteUriSet()
	{
		$mockConfig = $this->getMock('Joomla\Registry\Registry', array('get', 'set'), array(array('site_uri' => 'http://test.joomla.org/path/')), '', true, true, true, false, true);

		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication', array(null, $mockConfig));

		TestHelper::invoke($object, 'loadSystemUris');

		$this->assertSame(
			'http://test.joomla.org/path/',
			$object->get('uri.base.full')
		);

		$this->assertSame(
			'http://test.joomla.org',
			$object->get('uri.base.host')
		);

		$this->assertSame(
			'/path/',
			$object->get('uri.base.path')
		);

		$this->assertSame(
			'http://test.joomla.org/path/media/',
			$object->get('uri.media.full')
		);

		$this->assertSame(
			'/path/media/',
			$object->get('uri.media.path')
		);
	}

	/**
	 * @testdox  Tests the system URIs are correctly loaded when a URI is passed into the method
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::loadSystemUris
	 * @uses    Joomla\Application\AbstractApplication::get
	 */
	public function testLoadSystemUrisWithoutSiteUriSet()
	{
		$mockInput = $this->getMock('Joomla\Input\Input', array('get', 'getString'), array(), '', true, true, true, false, true);

		// Mock the Input object internals
		$mockServerInput = $this->getMock(
			'Joomla\Input\Input',
			array('get', 'set'),
			array(
				array(
					'SCRIPT_NAME' => '/index.php'
				)
			),
			'',
			true,
			true,
			true,
			false,
			true
		);

		$inputInternals = array(
			'server' => $mockServerInput
		);

		TestHelper::setValue($mockInput, 'inputs', $inputInternals);

		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication', array($mockInput));

		TestHelper::invoke($object, 'loadSystemUris', 'http://joom.la/application');

		$this->assertSame(
			'http://joom.la/',
			$object->get('uri.base.full')
		);

		$this->assertSame(
			'http://joom.la',
			$object->get('uri.base.host')
		);

		$this->assertSame(
			'/',
			$object->get('uri.base.path')
		);

		$this->assertSame(
			'http://joom.la/media/',
			$object->get('uri.media.full')
		);

		$this->assertSame(
			'/media/',
			$object->get('uri.media.path')
		);
	}

	/**
	 * @testdox  Tests the system URIs are correctly loaded when a media URI is set in the application configuration
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::loadSystemUris
	 * @uses    Joomla\Application\AbstractApplication::get
	 */
	public function testLoadSystemUrisWithoutSiteUriWithMediaUriSet()
	{
		$mockInput  = $this->getMock('Joomla\Input\Input', array('get', 'getString'), array(), '', true, true, true, false, true);
		$mockConfig = $this->getMock('Joomla\Registry\Registry', array('get', 'set'), array(array('media_uri' => 'http://cdn.joomla.org/media/')), '', true, true, true, false, true);

		// Mock the Input object internals
		$mockServerInput = $this->getMock(
			'Joomla\Input\Input',
			array('get', 'set'),
			array(
				array(
					'SCRIPT_NAME' => '/index.php'
				)
			),
			'',
			true,
			true,
			true,
			false,
			true
		);

		$inputInternals = array(
			'server' => $mockServerInput
		);

		TestHelper::setValue($mockInput, 'inputs', $inputInternals);

		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication', array($mockInput, $mockConfig));

		TestHelper::invoke($object, 'loadSystemUris', 'http://joom.la/application');

		$this->assertSame(
			'http://joom.la/',
			$object->get('uri.base.full')
		);

		$this->assertSame(
			'http://joom.la',
			$object->get('uri.base.host')
		);

		$this->assertSame(
			'/',
			$object->get('uri.base.path')
		);

		$this->assertSame(
			'http://cdn.joomla.org/media/',
			$object->get('uri.media.full')
		);

		$this->assertSame(
			'http://cdn.joomla.org/media/',
			$object->get('uri.media.path')
		);
	}

	/**
	 * @testdox  Tests the system URIs are correctly loaded when a relative media URI is set in the application configuration
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::loadSystemUris
	 * @uses    Joomla\Application\AbstractApplication::get
	 */
	public function testLoadSystemUrisWithoutSiteUriWithRelativeMediaUriSet()
	{
		$mockInput  = $this->getMock('Joomla\Input\Input', array('get', 'getString'), array(), '', true, true, true, false, true);
		$mockConfig = $this->getMock('Joomla\Registry\Registry', array('get', 'set'), array(array('media_uri' => '/media/')), '', true, true, true, false, true);

		// Mock the Input object internals
		$mockServerInput = $this->getMock(
			'Joomla\Input\Input',
			array('get', 'set'),
			array(
				array(
					'SCRIPT_NAME' => '/index.php'
				)
			),
			'',
			true,
			true,
			true,
			false,
			true
		);

		$inputInternals = array(
			'server' => $mockServerInput
		);

		TestHelper::setValue($mockInput, 'inputs', $inputInternals);

		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication', array($mockInput, $mockConfig));

		TestHelper::invoke($object, 'loadSystemUris', 'http://joom.la/application');

		$this->assertSame(
			'http://joom.la/',
			$object->get('uri.base.full')
		);

		$this->assertSame(
			'http://joom.la',
			$object->get('uri.base.host')
		);

		$this->assertSame(
			'/',
			$object->get('uri.base.path')
		);

		$this->assertSame(
			'http://joom.la/media/',
			$object->get('uri.media.full')
		);

		$this->assertSame(
			'/media/',
			$object->get('uri.media.path')
		);
	}

	/**
	 * @testdox  Tests a session object is correctly injected into the application and retrieved
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::getSession
	 * @covers  Joomla\Application\AbstractWebApplication::setSession
	 */
	public function testSetSession()
	{
		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication');
		$mockSession = $this->getMock('Joomla\Session\Session', array(), array(), '', false);

		$this->assertSame($object, $object->setSession($mockSession));
		$this->assertSame($mockSession, $object->getSession());
	}

	/**
	 * @testdox  Tests a RuntimeException is thrown when a Session object is not set to the application
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::getSession
	 * @expectedException  \RuntimeException
	 */
	public function testGetSessionForAnException()
	{
		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication');
		$object->getSession();
	}

	/**
	 * @testdox  Tests the application correctly detects if a SSL connection is active
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::isSslConnection
	 */
	public function testisSslConnection()
	{
		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication');

		$this->assertFalse($object->isSslConnection());

		$object->input->server->set('HTTPS', 'on');

		$this->assertTrue($object->isSslConnection());
	}

	/**
	 * @testdox  Tests the application correctly retrieves a form token
	 *
	 * @covers  Joomla\Application\AbstractWebApplication::getFormToken
	 * @uses    Joomla\Application\AbstractApplication::set
	 * @uses    Joomla\Application\AbstractWebApplication::setSession
	 */
	public function testGetFormToken()
	{
		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractWebApplication');
		$mockSession = $this->getMock('Joomla\\Session\\Session');

		$object->setSession($mockSession);
		$object->set('secret', 'abc');
		$expected = md5('abc' . 0 . $object->getSession()->getToken());

		$this->assertSame(
			$expected,
			$object->getFormToken()
		);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function tearDown()
	{
		// Reset the $headers array
		self::$headers = array();

		parent::tearDown();
	}
}
