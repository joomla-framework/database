<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Sqlite;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Test\DatabaseTestCase;

class SqlitePreparedStatementTest extends DatabaseTestCase
{
    /**
     * This method is called before the first test of this test class is run.
     *
     * @return  void
     */
    public static function setUpBeforeClass(): void
    {
        $manager = static::getDatabaseManager();

        $connection = $manager->getConnection();
        $manager->dropDatabase();
        $manager->createDatabase();
        $connection->select($manager->getDbName());

        static::$connection = $connection;
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
            foreach (DatabaseDriver::splitSql(file_get_contents(dirname(__DIR__) . '/Stubs/Schema/sqlite.sql')) as $query) {
                static::$connection->setQuery($query)
                    ->execute();
            }
        } catch (ExecutionFailureException $exception) {
            $this->markTestSkipped(
                \sprintf(
                    'Could not load SQLite database: %s',
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
        $tables = array_filter(
            static::$connection->getTableList(),
            function (string $table): bool {
                return $table !== 'sqlite_sequence';
            }
        );

        foreach ($tables as $table) {
            static::$connection->dropTable($table);
        }
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
