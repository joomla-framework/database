<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Mysqli;

use Joomla\Database\Mysqli\MysqliExporter;
use PHPUnit\Framework\TestCase;

/**
 * Tests the \Joomla\Database\Mysqli\MysqliExporter class.
 *
 * @since  1.0
 */
class MysqliExporterTest extends TestCase
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
	public function setup()
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
		$instance = new MysqliExporter;
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
		$instance = new MysqliExporter;
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
		$instance = new MysqliExporter;
		$instance->setDbo($this->dbo);
		$instance->from('foobar');

		try
		{
			$result = $instance->check();

			$this->assertThat(
				$result,
				$this->identicalTo($instance),
				'check must return an object to support chaining.'
			);
		}
		catch (\Exception $e)
		{
			$this->fail(
				'Check method should not throw exception with good setup: ' . $e->getMessage()
			);
		}
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
		$instance = new MysqliExporter;

		try
		{
			$result = $instance->setDbo($this->dbo);

			$this->assertThat(
				$result,
				$this->identicalTo($instance),
				'setDbo must return an object to support chaining.'
			);
		}
		catch (PHPUnit_Framework_Error $e)
		{
			// Unknown error has occurred.
			$this->fail(
				$e->getMessage()
			);
		}
	}
}
