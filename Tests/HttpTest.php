<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Github\Http;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for \Joomla\Github\Http.
 *
 * @since  1.0
 */
class HttpTest extends GitHubTestCase
{
	/**
	 * @var    \Joomla\Http\TransportInterface  Mock client object.
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

		$this->transport = $this->getMockBuilder('Joomla\\Http\\TransportInterface')
			->setConstructorArgs(array($this->options))
			->getMock();

		$this->object = new Http($this->options, $this->transport);
	}

	/**
	 * Tests the __construct method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function test__Construct()
	{
		// Verify the options are set in the object
		$this->assertThat(
			$this->object->getOption('userAgent'),
			$this->equalTo('JGitHub/2.0')
		);

		$this->assertThat(
			$this->object->getOption('timeout'),
			$this->equalTo(120)
		);
	}
}
