<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Mysqli;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Database\Mysqli\MysqliStatement;
use Joomla\Test\DatabaseTestCase;

class MysqliPreparedStatementTest extends DatabaseTestCase
{
    /**
     * This method is called before the first test of this test class is run.
     *
     * @return  void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (!static::$connection || static::$connection->getName() !== 'mysqli') {
            self::markTestSkipped('MySQL database not configured.');
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
            foreach (DatabaseDriver::splitSql(file_get_contents(dirname(__DIR__) . '/Stubs/Schema/mysql.sql')) as $query) {
                static::$connection->setQuery($query)
                    ->execute();
            }
        } catch (ExecutionFailureException $exception) {
            $this->markTestSkipped(
                \sprintf(
                    'Could not load MySQL database: %s',
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
        $statement = 'SELECT * FROM dbtest WHERE `title` LIKE :search OR `description` LIKE :search';
        $mysqliStatementObject = new MysqliStatement(static::$connection->getConnection(), $statement);
        $rawQuery = $mysqliStatementObject->prepareParameterKeyMapping($statement);

        $this->assertEquals(
            "SELECT * FROM dbtest WHERE `title` LIKE ? OR `description` LIKE ?",
            $rawQuery
        );

        $refObject = new \ReflectionObject($mysqliStatementObject);
        $refMapping = $refObject->getProperty('parameterKeyMapping');
        /** @noinspection PhpExpressionResultUnusedInspection */
        $refMapping->setAccessible(true);
        $parameterKeyMapping = $refMapping->getValue($mysqliStatementObject);

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
        $statement = 'SELECT * FROM dbtest WHERE `title` LIKE :search OR `description` LIKE :search2';
        $mysqliStatementObject = new MysqliStatement(static::$connection->getConnection(), $statement);
        $rawQuery = $mysqliStatementObject->prepareParameterKeyMapping($statement);

        $this->assertEquals(
            "SELECT * FROM dbtest WHERE `title` LIKE ? OR `description` LIKE ?",
            $rawQuery
        );

        $refObject = new \ReflectionObject($mysqliStatementObject);
        $refMapping = $refObject->getProperty('parameterKeyMapping');
        /** @noinspection PhpExpressionResultUnusedInspection */
        $refMapping->setAccessible(true);
        $parameterKeyMapping = $refMapping->getValue($mysqliStatementObject);

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
        $statement = 'SELECT * FROM dbtest WHERE `title` LIKE :search OR `description` LIKE :search';
        $mysqliStatementObject = new MysqliStatement(static::$connection->getConnection(), $statement);
        $dummyValue = 'test';
        $mysqliStatementObject->bindParam(':search', $dummyValue);

        $mysqliStatementObject->execute();
    }

    /**
     * Regression test to ensure running queries with named parameters appearing once didn't break.
     *
     * @doesNotPerformAssertions
     */
    public function testPreparedStatementWithSingleKey()
    {
        $statement = 'SELECT * FROM dbtest WHERE `title` LIKE :search OR `description` LIKE :search2';
        $mysqliStatementObject = new MysqliStatement(static::$connection->getConnection(), $statement);
        $dummyValue = 'test';
        $dummyValue2 = 'test';
        $mysqliStatementObject->bindParam(':search', $dummyValue);
        $mysqliStatementObject->bindParam(':search2', $dummyValue);

        $mysqliStatementObject->execute();
    }
}
