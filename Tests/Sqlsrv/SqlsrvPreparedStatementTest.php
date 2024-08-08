<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Sqlsrv;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Database\Sqlsrv\SqlsrvStatement;
use Joomla\Test\DatabaseTestCase;

/**
 * Test class for Joomla\Database\Sqlsrv\SqlsrvStatement
 */
class SqlsrvPreparedStatementTest extends DatabaseTestCase
{
    /**
     * This method is called before the first test of this test class is run.
     *
     * @return  void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (!static::$connection || static::$connection->getName() !== 'sqlsrv') {
            self::markTestSkipped('SQL Server database not configured.');
        }
    }

    /**
     * Sets up the fixture.
     *
     * This method is called before a test is executed.
     *
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        try {
            foreach (DatabaseDriver::splitSql(file_get_contents(dirname(__DIR__) . '/Stubs/Schema/sqlsrv.sql')) as $query) {
                static::$connection->setQuery($query)
                    ->execute();
            }
        } catch (ExecutionFailureException $exception) {
            $this->markTestSkipped(
                \sprintf(
                    'Could not load SQL Server database: %s',
                    $exception->getMessage()
                )
            );
        }
    }

    /**
     * Tears down the fixture.
     *
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        foreach (static::$connection->getTableList() as $table) {
            static::$connection->dropTable($table);
        }
    }

    /**
     * Make sure the mysqli driver correctly maps named query parameters appearing more than once.
     */
    public function testPrepareParameterKeyMappingWithDuplicateKey()
    {
        $statement = 'SELECT * FROM dbtest WHERE title LIKE :search OR description LIKE :search';
        $sqlsrvStatement = new SqlsrvStatement(static::$connection->getConnection(), $statement);
        $rawQuery = $sqlsrvStatement->prepareParameterKeyMapping($statement);

        $this->assertEquals(
            "SELECT * FROM dbtest WHERE title LIKE ? OR description LIKE ?",
            $rawQuery
        );

        $refObject = new \ReflectionObject($sqlsrvStatement);
        $refMapping = $refObject->getProperty('parameterKeyMapping');
        /** @noinspection PhpExpressionResultUnusedInspection */
        $refMapping->setAccessible(true);
        $parameterKeyMapping = $refMapping->getValue($sqlsrvStatement);

        $this->assertEquals(
            [
                ':search' => [0, 1],
            ],
            $parameterKeyMapping
        );
    }

    /**
     * Regression test to ensure mapping query parameters appearing once didn't break.
     */
    public function testPrepareParameterKeyMappingWithSingleKey()
    {
        $statement = 'SELECT * FROM dbtest WHERE title LIKE :search OR description LIKE :search2';
        $sqlsrvStatement = new SqlsrvStatement(static::$connection->getConnection(), $statement);
        $rawQuery = $sqlsrvStatement->prepareParameterKeyMapping($statement);

        $this->assertEquals(
            "SELECT * FROM dbtest WHERE title LIKE ? OR description LIKE ?",
            $rawQuery
        );

        $refObject = new \ReflectionObject($sqlsrvStatement);
        $refMapping = $refObject->getProperty('parameterKeyMapping');
        /** @noinspection PhpExpressionResultUnusedInspection */
        $refMapping->setAccessible(true);
        $parameterKeyMapping = $refMapping->getValue($sqlsrvStatement);

        $this->assertEquals(
            [
                ':search' => [0],
                ':search2' => [1],
            ],
            $parameterKeyMapping
        );
    }

    /**
     * Make sure the mysqli driver correctly runs queries with named parameters appearing more than once.
     *
     * @doesNotPerformAssertions
     */
    public function testPreparedStatementWithDuplicateKey()
    {
        $dummyValue = 'test';
        $query = static::$connection->getQuery(true);
        $query->select('*')
            ->from($query->quoteName('dbtest'))
            ->where([
                $query->quoteName('title') . ' LIKE :search',
                $query->quoteName('description') . ' LIKE :search',
            ], 'OR')
            ->bind(':search', $dummyValue);

        static::$connection->setQuery($query)->execute();
    }

    /**
     * Regression test to ensure running queries with named parameters appearing once didn't break.
     *
     * @doesNotPerformAssertions
     */
    public function testPreparedStatementWithSingleKey()
    {
        $dummyValue = 'test';
        $dummyValue2 = 'test';
        $query = static::$connection->getQuery(true);
        $query->select('*')
            ->from($query->quoteName('dbtest'))
            ->where([
                $query->quoteName('title') . ' LIKE :search',
                $query->quoteName('description') . ' LIKE :search2',
            ])
            ->bind(':search', $dummyValue)
            ->bind(':search2', $dummyValue2);

        static::$connection->setQuery($query)->execute();
    }
}
