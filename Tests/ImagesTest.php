<?php
/**
 * Part of the Joomla Framework Mediawiki Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Mediawiki\Tests;

use Joomla\Registry\Registry;
use Joomla\Mediawiki\Images;

/**
 * Test class for Images.
 *
 * @since  1.0
 */
class ImagesTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var    Registry  Options for the Mediawiki object.
	 * @since  1.0
	 */
	protected $options;

	/**
	 * @var    \PHPUnit_Framework_MockObject_MockObject  Mock client object.
	 * @since  1.0
	 */
	protected $client;

	/**
	 * @var    Images  Object under test.
	 * @since  1.0
	 */
	protected $object;

	/**
	 * @var    \Joomla\Http\Response  Mock response object.
	 * @since  1.0
	 */
	protected $response;

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
		$this->client = $this->getMock('\\Joomla\\Mediawiki\\Http', array('get', 'post', 'delete', 'patch', 'put'));
		$this->response = $this->getMock('\\Joomla\\Http\\Response');

		$this->object = new Images($this->options, $this->client);
	}

	/**
	 * Tests the getImages method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testGetImages()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&prop=images&titles=Main Page&format=xml')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getImages(array('Main Page')),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
	 * Tests the getImagesUsed method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testGetImagesUsed()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&generator=images&prop=info&titles=Main Page&format=xml')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getImagesUsed(array('Main Page')),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
	 * Tests the getImageInfo method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testGetImageInfo()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&prop=imageinfo&format=xml')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getImageInfo(),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
	 * Tests the enumerateImages method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testEnumerateImages()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&list=allimages&format=xml')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->enumerateImages(),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}
}
