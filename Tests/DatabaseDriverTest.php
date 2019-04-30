<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\Tests\Cases\SqliteCase;
use Joomla\Test\TestHelper;

require_once __DIR__ . '/Stubs/nosqldriver.php';

/**
 * Test class for \Joomla\Database\DatabaseDriver.
 */
class DatabaseDriverTest extends SqliteCase
{
	/**
	 * @var  DatabaseDriver
	 */
	protected $instance;

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
	 * Data provider for splitSql test cases
	 *
	 * @return  array
	 */
	public function dataSplitSql()
	{
		// Order: SQL string to process; Expected result
		return array(
			'simple string' => array(
				'SELECT * FROM #__foo;SELECT * FROM #__bar;',
				array(
					'SELECT * FROM #__foo;',
					'SELECT * FROM #__bar;',
				),
			),
			'string with -- style comments' => array(
				<<<SQL
--
-- A test comment
--

ALTER TABLE `#__foo` MODIFY `text_column` varchar(150) NOT NULL;

ALTER TABLE `#__bar` MODIFY `text_column` varchar(150) NOT NULL;
SQL
				,
				array(
					'ALTER TABLE `#__foo` MODIFY `text_column` varchar(150) NOT NULL;',
					'ALTER TABLE `#__bar` MODIFY `text_column` varchar(150) NOT NULL;',
				)
			),
			'string with # style comments' => array(
				<<<SQL
# A test comment

INSERT INTO `#__foo` (`column_one`, `column_two`);
SQL
				,
				array(
					'INSERT INTO `#__foo` (`column_one`, `column_two`);',
				),
			),
			'string with C style comments' => array(
				<<<SQL
/*
 * A test comment
 */

ALTER TABLE `#__foo` MODIFY `text_column` varchar(150) NOT NULL;

ALTER TABLE `#__bar` MODIFY `text_column` varchar(150) NOT NULL;
SQL
				,
				array(
					'ALTER TABLE `#__foo` MODIFY `text_column` varchar(150) NOT NULL;',
					'ALTER TABLE `#__bar` MODIFY `text_column` varchar(150) NOT NULL;',
				),
			),
			'string with MySQL specific C style comments' => array(
				'CREATE /*!32302 TEMPORARY */ TABLE t (a INT);',
				array(
					'CREATE /*!32302 TEMPORARY */ TABLE t (a INT);',
				),
			),
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::splitSql method.
	 *
	 * @param   string  $sql       The SQL string to process
	 * @param   array   $expected  The expected result
	 *
	 * @return  void
	 *
	 * @dataProvider  dataSplitSql
	 */
	public function testSplitSql($sql, array $expected)
	{
		$this->assertEquals(
			$expected,
			$this->instance->splitSql($sql),
			'splitSql method should split a string of queries into an array.'
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::getName method.
	 */
	public function testGetName()
	{
		$this->assertThat(
			$this->instance->getName(),
			$this->equalTo('nosql')
		);
	}

	/**
	 * Tests the Joomla\Database\DatabaseDriver::getServerType method.
	 */
	public function testGetServerType()
	{
		$this->assertThat(
			$this->instance->getServerType(),
			$this->equalTo('nosql')
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
	 * Tests the Joomla\Database\DatabaseDriver::isMinimumVersion method.
	 */
	public function testIsMinimumVersion()
	{
		$this->assertTrue($this->instance->isMinimumVersion());
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
		// This call `escape()` method from nosqldriver, which is locale aware
		$this->assertSame(
			// Below line may generate "'-3.14-'" or "'-3,14-'"
			"'-" . 3.14 . "-'",
			$this->instance->quote(3.14)
		);
	}

    /**
     * Tests the Joomla\Database\DatabaseDriver::quoteBinary method.
     *
     * @return  void
     *
     * @since   1.7.0
     */
    public function testQuoteBinary()
    {
        $this->assertThat(
            $this->instance->quoteBinary('DATA'),
            $this->equalTo("X'" . bin2hex('DATA') . "'"),
            'Tests the binary data 1.'
        );

        $this->assertThat(
            $this->instance->quoteBinary("\x00\x01\x02\xff"),
            $this->equalTo("X'000102ff'"),
            'Tests the binary data 2.'
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

		$this->assertThat(
			$this->instance->quoteName('a.test', 'a.test'),
			$this->equalTo('[a].[test] AS [a.test]'),
			'Tests the left-right quotes on a dotted string for column alias.'
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
