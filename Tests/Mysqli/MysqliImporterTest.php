<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Mysqli;

use Joomla\Database\Mysqli\MysqliImporter;
use PHPUnit\Framework\TestCase;

/**
 * Tests the \Joomla\Database\Mysqli\MysqliImporter class.
 *
 * @since  1.0
 */
class MysqliImporterTest extends TestCase
{
	/**
	 * @var    object  The mocked database object for use by test methods.
	 * @since  1.0
	 */
	protected $dbo = null;

	/**
	 * Sets up the testing conditions
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setup()
	{
		parent::setUp();

		// Set up the database object mock.
		$this->dbo = $this->getMockBuilder('Joomla\\Database\\Mysqli\MysqliDriver')
			->disableOriginalConstructor()
			->getMock();
	}

	/**
	 * Tests the check method.
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testCheckWithNoDbo()
	{
		$this->expectException(\RuntimeException::class);
		$instance = new MysqliImporter;
		$instance->check();
	}

	/**
	 * Tests the check method.
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testCheckWithNoTables()
	{
		$this->expectException(\RuntimeException::class);
		$instance = new MysqliImporter;
		$instance->setDbo($this->dbo);
		$instance->check();
	}

	/**
	 * Tests the check method.
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testCheckWithGoodInput()
	{
		$instance = new MysqliImporter;
		$instance->setDbo($this->dbo);
		$instance->from('foobar');

		$result = $instance->check();

		$this->assertThat(
			$result,
			$this->identicalTo($instance),
			'check must return an object to support chaining.'
		);
	}

	/**
	 * Tests the setDbo method with the wrong type of class.
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testSetDboWithGoodInput()
	{
		$instance = new MysqliImporter;
		$result   = $instance->setDbo($this->dbo);

		$this->assertThat(
			$result,
			$this->identicalTo($instance),
			'setDbo must return an object to support chaining.'
		);
	}
}
