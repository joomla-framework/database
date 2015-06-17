<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests;

use Joomla\Database\DatabaseDriver;
use Joomla\Test\TestHelper;
use Joomla\Test\TestDatabase;
use Psr\Log;

require_once __DIR__ . '/Stubs/nosqldriver.php';

/**
 * Test class for \Joomla\Database\DatabaseDriver.
 */
class DatabaseDriverTest extends TestDatabase
{
	/**
	 * @var  DatabaseDriver
	 */
	protected $instance;

	/**
	 * A store to track if logging is working.
	 *
	 * @var  array
	 */
	protected $logs;

	/**
	 * Mocks the log method to track if logging is working.
	 *
	 * @param   Log\LogLevel  $level    The level.
	 * @param   string        $message  The message.
	 * @param   array         $context  The context.
	 *
	 * @return  void
	 */
	public function mockLog($level, $message, $context)
	{
		$this->logs[] = array(
			'level' => $level,
			'message' => $message,
			'context' => $context,
		);
	}

	/**
	 * Test for the Joomla\Database\DatabaseDriver::__call method.
	 */
	public function test__callQuote()
	{
		$this->assertEquals(
			$this->instance->quote('foo'),
			$this->instance->q('foo')
		);
	}

	/**
	 * Test for the Joomla\Database\DatabaseDriver::__call method.
	 */
	public function test__callQuoteName()
	{
		$this->assertEquals(
			$this->instance->quoteName('foo'),
			$this->instance->qn('foo')
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::getConnection method.
	 */
	public function testGetConnection()
	{
		$this->assertNull($this->instance->getConnection());
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::getConnectors method.
	 */
	public function testGetConnectors()
	{
		$this->assertContains(
			'Sqlite',
			$this->instance->getConnectors(),
			'The getConnectors method should return an array with Sqlite as an available option.'
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::getCount method.
	 */
	public function testGetCount()
	{
		$this->assertEquals(0, $this->instance->getCount());
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::getDatabase method.
	 */
	public function testGetDatabase()
	{
		$this->assertEquals('europa', TestHelper::invoke($this->instance, 'getDatabase'));
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::getDateFormat method.
	 */
	public function testGetDateFormat()
	{
		$this->assertSame(
			'Y-m-d H:i:s',
			$this->instance->getDateFormat()
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::splitSql method.
	 */
	public function testSplitSql()
	{
		$this->assertSame(
			array(
				'SELECT * FROM #__foo;',
				'SELECT * FROM #__bar;'
			),
			$this->instance->splitSql('SELECT * FROM #__foo;SELECT * FROM #__bar;')
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::getPrefix method.
	 */
	public function testGetPrefix()
	{
		$this->assertSame(
			'&',
			$this->instance->getPrefix()
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::getNullDate method.
	 */
	public function testGetNullDate()
	{
		$this->assertSame(
			'1BC',
			$this->instance->getNullDate()
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::getMinimum method.
	 */
	public function testGetMinimum()
	{
		$this->assertSame(
			'12.1',
			$this->instance->getMinimum()
		);
	}

	/**
	 * Tests the Driver::log method.
	 *
	 * @covers  Joomla\Database\DatabaseDriver::log
	 * @covers  Joomla\Database\DatabaseDriver::setLogger
	 */
	public function testLog()
	{
		$this->logs = array();

		$mockLogger = $this->getMock('Psr\Log\AbstractLogger', array('log'), array(), '', false);
		$mockLogger->expects($this->any())
			->method('log')
			->willReturnCallback(array($this, 'mockLog'));

		$this->instance->log(Log\LogLevel::DEBUG, 'Debug', array('sql' => true));

		$this->assertEmpty($this->logs);

		// Set the logger and try again.
		$this->instance->setLogger($mockLogger);
		$this->instance->log(Log\LogLevel::DEBUG, 'Debug', array('sql' => true));

		$this->assertSame(Log\LogLevel::DEBUG, $this->logs[0]['level']);
		$this->assertSame('Debug', $this->logs[0]['message']);
		$this->assertSame(array('sql' => true), $this->logs[0]['context']);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::isMinimumVersion method.
	 */
	public function testIsMinimumVersion()
	{
		$this->assertTrue($this->instance->isMinimumVersion());
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::setDebug method.
	 */
	public function testSetDebug()
	{
		$this->assertInternalType(
			'boolean',
			$this->instance->setDebug(true)
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::setQuery method.
	 */
	public function testSetQuery()
	{
		$this->assertSame(
			$this->instance,
			$this->instance->setQuery('SELECT * FROM #__dbtest')
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::replacePrefix method.
	 */
	public function testReplacePrefix()
	{
		$this->assertSame(
			'SELECT * FROM &dbtest',
			$this->instance->replacePrefix('SELECT * FROM #__dbtest')
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::replacePrefix method with an empty prefix.
	 */
	public function testReplacePrefixWithAnEmptyPrefix()
	{
		$instance = \Joomla\Database\DatabaseDriver::getInstance(
			array(
				'driver' => 'nosql',
				'database' => 'europa',
				'prefix' => '',
			)
		);

		$this->assertSame(
			'SELECT * FROM dbtest',
			$instance->replacePrefix('SELECT * FROM #__dbtest')
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::quote method.
	 *
	 * @covers  Joomla\Database\DatabaseDriver::quote
	 */
	public function testQuote()
	{
		$this->assertSame(
			"'test'",
			$this->instance->quote('test', false)
		);

		$this->assertSame(
			"'-test-'",
			$this->instance->quote('test')
		);

		$this->assertSame(
			array("'-test1-'", "'-test2-'"),
			$this->instance->quote(array('test1', 'test2'))
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::quote method.
	 */
	public function testQuoteBooleanTrue()
	{
		$this->assertSame(
			"'-1-'",
			$this->instance->quote(true)
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::quote method.
	 */
	public function testQuoteBooleanFalse()
	{
		$this->assertSame(
			"'--'",
			$this->instance->quote(false)
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::quote method.
	 */
	public function testQuoteNull()
	{
		$this->assertSame(
			"'--'",
			$this->instance->quote(null)
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::quote method.
	 */
	public function testQuoteInteger()
	{
		$this->assertSame(
			"'-42-'",
			$this->instance->quote(42)
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::quote method.
	 */
	public function testQuoteFloat()
	{
		$this->assertSame(
			"'-3.14-'",
			$this->instance->quote(3.14)
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::quoteName method.
	 */
	public function testQuoteName()
	{
		$this->assertSame(
			'[test]',
			$this->instance->quoteName('test')
		);

		$this->assertSame(
			'[a].[test]',
			$this->instance->quoteName('a.test')
		);

		$this->assertSame(
			array('[a]', '[test]'),
			$this->instance->quoteName(array('a', 'test'))
		);

		$this->assertSame(
			array('[a].[b]', '[test].[quote]'),
			$this->instance->quoteName(array('a.b', 'test.quote'))
		);

		$this->assertSame(
			array('[a].[b]', '[test].[quote] AS [alias]'),
			$this->instance->quoteName(array('a.b', 'test.quote'), array(null, 'alias'))
		);

		$this->assertSame(
			array('[a].[b] AS [alias1]', '[test].[quote] AS [alias2]'),
			$this->instance->quoteName(array('a.b', 'test.quote'), array('alias1', 'alias2'))
		);

		$this->assertSame(
			array('[a]', '[test]'),
			$this->instance->quoteName((object) array('a', 'test'))
		);

 		TestHelper::setValue($this->instance, 'nameQuote', '/');

		$this->assertSame(
			'/test/',
			$this->instance->quoteName('test')
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::truncateTable method.
	 */
	public function testTruncateTable()
	{
		$this->assertNull($this->instance->truncateTable('#__dbtest'));
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->instance = DatabaseDriver::getInstance(
			array(
				'driver' => 'nosql',
				'database' => 'europa',
				'prefix' => '&',
			)
		);
	}

	/**
	 * Tears down the fixture.
	 *
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		// We need this to be empty.
	}
}
