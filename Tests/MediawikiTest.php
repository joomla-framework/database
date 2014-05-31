<?php
/**
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Mediawiki\Tests;

use Joomla\Registry\Registry;
use Joomla\Mediawiki\Mediawiki;

/**
 * Test class for JMediawiki.
 *
 * @since  1.0
 */
class MediawikiTest extends \PHPUnit_Framework_TestCase
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
	 * @var    Mediawiki Object under test.
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
		$this->client = $this->getMock('\\Joomla\\Mediawiki\\Http', array('get', 'post', 'delete', 'patch', 'put'));

		$this->object = new Mediawiki($this->options, $this->client);
	}

	/**
	 * Tests the magic __get method - pages
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function test__GetPages()
	{
		$this->assertThat(
			$this->object->pages,
			$this->isInstanceOf('Joomla\Mediawiki\Pages')
		);
	}

	/**
	 * Tests the magic __get method - users
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function test__GetUsers()
	{
		$this->assertThat(
			$this->object->users,
			$this->isInstanceOf('Joomla\Mediawiki\Users')
		);
	}

	/**
	 * Tests the magic __get method - links
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function test__GetLinks()
	{
		$this->assertThat(
			$this->object->links,
			$this->isInstanceOf('Joomla\Mediawiki\Links')
		);
	}

	/**
	 * Tests the magic __get method - categories
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function test__GetCategories()
	{
		$this->assertThat(
			$this->object->categories,
			$this->isInstanceOf('Joomla\Mediawiki\Categories')
		);
	}

	/**
	 * Tests the magic __get method - images
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function test__GetImages()
	{
		$this->assertThat(
			$this->object->images,
			$this->isInstanceOf('Joomla\Mediawiki\Images')
		);
	}

	/**
	 * Tests the magic __get method - search
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function test__GetSearch()
	{
		$this->assertThat(
			$this->object->search,
			$this->isInstanceOf('Joomla\Mediawiki\Search')
		);
	}

	/**
	 * Tests the setOption method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testSetOption()
	{
		$this->object->setOption('api.url', 'https://example.com/settest');

		$this->assertThat(
			$this->options->get('api.url'),
			$this->equalTo('https://example.com/settest')
		);
	}

	/**
	 * Tests the getOption method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testGetOption()
	{
		$this->options->set('api.url', 'https://example.com/gettest');

		$this->assertThat(
			$this->object->getOption('api.url', 'https://example.com/gettest'),
			$this->equalTo('https://example.com/gettest')
		);
	}
}
