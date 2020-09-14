<?php
/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\AbstractGithubObject;
use Joomla\Github\Tests\Stub\GitHubTestCase;
use Joomla\Github\Tests\Stub\ObjectMock;
use Joomla\Http\Http;
use Joomla\Http\Transport\Curl;

/**
 * Test class for Joomla\Github\Object.
 *
 * @since  1.0
 */
class GithubObjectTest extends GitHubTestCase
{
	/**
	 * @var    AbstractGithubObject  Object under test.
	 * @since  1.0
	 */
	protected $object;

	/**
	 * @var    Http  The HTTP client
	 * @since  __DEPLOY_VERSION__
	 */
	protected $client;

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

		$transport    = new Curl(array());
		$this->client = new Http(array(), $transport);
		$this->object = new ObjectMock($this->options, $this->client);
	}

	/**
	 * Data provider method for the fetchUrl method tests.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function fetchUrlData()
	{
		return array(
			'Standard github - no pagination data'    => array(
				'https://api.github.com',
				'/gists',
				0,
				0,
				'https://api.github.com/gists'
			),
			'Enterprise github - no pagination data'  => array(
				'https://mygithub.com',
				'/gists',
				0,
				0,
				'https://mygithub.com/gists'
			),
			'Standard github - page 3'                => array(
				'https://api.github.com',
				'/gists',
				3,
				0,
				'https://api.github.com/gists?page=3'
			),
			'Enterprise github - page 3, 50 per page' => array(
				'https://mygithub.com',
				'/gists',
				3,
				50,
				'https://mygithub.com/gists?page=3&per_page=50'
			),
		);
	}

	/**
	 * Tests the fetchUrl method
	 *
	 * @param   string   $apiUrl    @todo
	 * @param   string   $path      @todo
	 * @param   integer  $page      @todo
	 * @param   integer  $limit     @todo
	 * @param   string   $expected  @todo
	 *
	 * @return  void
	 *
	 * @since        1.0
	 * @dataProvider fetchUrlData
	 */
	public function testFetchUrl($apiUrl, $path, $page, $limit, $expected)
	{
		$this->options->set('api.url', $apiUrl);

		self::assertEquals(
			$expected,
			$this->object->fetchUrl($path, $page, $limit),
			'URL is not as expected.'
		);
	}

	/**
	 * Tests the fetchUrl method with basic authentication data
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testFetchUrlBasicAuth()
	{
		$this->options->set('api.url', 'https://api.github.com');

		$this->options->set('api.username', 'MyTestUser');
		$this->options->set('api.password', 'MyTestPass');

		self::assertEquals(
			'https://MyTestUser:MyTestPass@api.github.com/gists',
			$this->object->fetchUrl('/gists', 0, 0),
			'URL is not as expected.'
		);
	}

	/**
	 * Tests the fetchUrl method using an oAuth token.
	 *
	 * @return void
	 */
	public function testFetchUrlToken()
	{
		$this->options->set('api.url', 'https://api.github.com');

		$this->options->set('gh.token', 'MyTestToken');

		self::assertEquals(
			'https://api.github.com/gists',
			$this->object->fetchUrl('/gists', 0, 0),
			'URL is not as expected.'
		);

		self::assertEquals(
			array('Authorization' => 'token MyTestToken'),
			$this->client->getOption('headers'),
			'Token should bhe propagated as a header.'
		);
	}
}
