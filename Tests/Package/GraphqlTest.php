<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Graphql;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for Graphql.
 *
 * @since  1.0
 */
class GraphqlTest extends GitHubTestCase
{
	/**
	 * @var Graphql
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new Graphql($this->options, $this->client);
	}

	/**
	 * Tests the create method
	 *
	 * @return void
	 */
	public function testCreate()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		// Build the query.
		$query = 'foo';

		// Build the request data.
		$data = array(
			'query' => $query,
		);

		// Build the headers.
		$headers = array(
			'Accept'       => 'application/vnd.github.v4+json',
			'Content-Type' => 'application/json',
		);

		$this->client->expects($this->once())
			->method('post')
			->with('/graphql', $data, $headers)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->execute($query),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the create method
	 *
	 * @return void
	 */
	public function testCreateFailure()
	{
		$exception = false;

		$this->response->code = 500;
		$this->response->body = $this->errorString;

		// Build the query.
		$query = 'foo';

		// Build the request data.
		$data = array(
			'query' => $query,
		);

		// Build the headers.
		$headers = array(
			'Accept'       => 'application/vnd.github.v4+json',
			'Content-Type' => 'application/json',
		);

		$this->client->expects($this->once())
			->method('post')
			->with('/graphql', $data, $headers)
			->will($this->returnValue($this->response));

		try
		{
			$this->object->execute($query);
			$this->fail('Exception not thrown');
		}
		catch (\DomainException $e)
		{
			$this->assertThat(
				$e->getMessage(),
				$this->equalTo(json_decode($this->errorString)->message)
			);
		}
	}
}
