<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Mysqli;

use Joomla\Database\Mysqli\MysqliExporter;

/**
 * Tests the \Joomla\Database\Mysqli\MysqliExporter class.
 *
 * @since  1.0
 */
class MysqliExporterTest extends \PHPUnit_Framework_TestCase
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
		$instance = new MysqliExporter;

		try
		{
			$instance->check();
		}
		catch (\Exception $e)
		{
			// Exception expected.
			return;
		}

		$this->fail(
			'Check method should throw exception if DBO not set'
		);
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
		$instance = new MysqliExporter;
		$instance->setDbo($this->dbo);

		try
		{
			$instance->check();
		}
		catch (\Exception $e)
		{
			// Exception expected.
			return;
		}

		$this->fail(
			'Check method should throw exception if DBO not set'
		);
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
