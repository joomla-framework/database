<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Gitignore;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for Gitignore.
 *
 * @since  1.0
 */
class GitignoreTest extends GitHubTestCase
{
	/**
	 * @var Gitignore
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @since   1.0
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new Gitignore($this->options, $this->client);
	}

	/**
	 * Tests the getList method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetList()
	{
		$this->response->code = 200;
		$this->response->body = '[
    "Actionscript",
    "Android",
    "AppceleratorTitanium",
    "Autotools",
    "Bancha",
    "C",
    "C++"
    ]';

		$this->client->expects($this->once())
			->method('get')
			->with('/gitignore/templates', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getList(),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the get method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGet()
	{
		$this->response->code = 200;
		$this->response->body = '{
    "name": "C",
    "source": "# Object files\n*.o\n\n# Libraries\n*.lib\n*.a\n\n# Shared objects (inc. Windows DLLs)\n*.dll\n*.so\n*.so.*\n*.dylib\n\n# Executables\n*.exe\n*.out\n*.app\n"
    }';

		$this->client->expects($this->once())
			->method('get')
			->with('/gitignore/templates/C', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->get('C'),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the get method with raw return data.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetRaw()
	{
		$this->response->code = 200;
		$this->response->body = '# Object files
     *.o

    # Libraries
     *.lib
     *.a

    # Shared objects (inc. Windows DLLs)
     *.dll
     *.so
     *.so.*
     *.dylib

    # Executables
     *.exe
     *.out
     *.app
';

		$this->client->expects($this->once())
			->method('get')
			->with('/gitignore/templates/C', array('Accept' => 'application/vnd.github.raw+json'), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->get('C', true),
			$this->equalTo($this->response->body)
		);
	}

	/**
	 * Tests the get method with failure.
	 *
	 * @expectedException \DomainException
	 *
	 * @since   1.0
	 * @return  void
	 */
	public function testGetFailure()
	{
		$this->response->code = 404;
		$this->response->body = '{"message":"Not found"}';

		$this->client->expects($this->once())
			->method('get')
			->with('/gitignore/templates/X', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->get('X'),
			$this->equalTo(json_decode($this->response->body))
		);
	}
}
