<?php

/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseExporter;
use Joomla\Database\DatabaseFactory;
use Joomla\Database\DatabaseImporter;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\DatabaseIterator;
use Joomla\Database\Exception\UnsupportedAdapterException;
use Joomla\Database\Mysqli\MysqliDriver;
use Joomla\Database\QueryInterface;
use Joomla\Database\StatementInterface;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\DatabaseFactory
 */
class DatabaseFactoryTest extends TestCase
{
    /**
     * Object being tested
     *
     * @var  DatabaseFactory
     */
    private $factory;

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

        $this->factory = new DatabaseFactory();
    }

    /**
     * Data provider for driver test cases
     *
     * @return  array
     */
    public static function dataGetDriver(): array
    {
        return [
            'supported driver' => [
                'mysqli',
                false,
            ],

            'unsupported exporter' => [
                'mariadb',
                true,
            ],
        ];
    }

    /**
     * @testdox  The factory builds a database driver correctly
     *
     * @param   string   $adapter               The type of adapter to create
     * @param   boolean  $shouldRaiseException  Flag indicating the factory should raise an exception for an unsupported adapter
     */
    #[DataProvider('dataGetDriver')]
    public function testGetDriver(string $adapter, bool $shouldRaiseException)
    {
        if ($shouldRaiseException) {
            $this->expectException(UnsupportedAdapterException::class);
        }

        $this->assertInstanceOf(
            DatabaseInterface::class,
            $this->factory->getDriver($adapter)
        );
    }

    /**
     * Data provider for exporter test cases
     *
     * @return  array
     */
    public static function dataGetExporter(): array
    {
        return [
            'exporter without database driver' => [
                'mysqli',
                false,
                false,
            ],

            'exporter with database driver' => [
                'mysqli',
                false,
                true,
            ],

            'unsupported exporter' => [
                'mariadb',
                true,
                false,
            ],
        ];
    }

    /**
     * @testdox  The factory builds a database exporter correctly
     *
     * @param   string               $adapter               The type of adapter to create
     * @param   boolean              $shouldRaiseException  Flag indicating the factory should raise an exception for an unsupported adapter
     * @param   DatabaseDriver|null  $databaseDriver        The optional database driver to be injected into the exporter
     */
    #[DataProvider('dataGetExporter')]
    public function testGetExporter(string $adapter, bool $shouldRaiseException, bool $createDb)
    {
        if ($shouldRaiseException) {
            $this->expectException(UnsupportedAdapterException::class);
        }

        $databaseDriver = null;

        if ($createDb) {
            $databaseDriver = $this->createMock(MysqliDriver::class);
        }

        $exporter = $this->factory->getExporter($adapter, $databaseDriver);

        $this->assertInstanceOf(
            DatabaseExporter::class,
            $exporter
        );

        if ($databaseDriver) {
            $this->assertSame(
                TestHelper::getValue($exporter, 'db'),
                $databaseDriver
            );
        }
    }

    /**
     * Data provider for importer test cases
     *
     * @return  array
     */
    public static function dataGetImporter(): array
    {
        return [
            'importer without database driver' => [
                'mysqli',
                false,
                false,
            ],

            'importer with database driver' => [
                'mysqli',
                false,
                true,
            ],

            'unsupported importer' => [
                'mariadb',
                true,
                false,
            ],
        ];
    }

    /**
     * @testdox  The factory builds a database importer correctly
     *
     * @param   string   $adapter               The type of adapter to create
     * @param   boolean  $shouldRaiseException  Flag indicating the factory should raise an exception for an unsupported adapter
     * @param   boolean  $createDb              The optional database driver to be injected into the importer
     */
    #[DataProvider('dataGetImporter')]
    public function testGetImporter(string $adapter, bool $shouldRaiseException, bool $createDb)
    {
        if ($shouldRaiseException) {
            $this->expectException(UnsupportedAdapterException::class);
        }

        $databaseDriver = null;

        if ($createDb) {
            $databaseDriver = $this->createMock(MysqliDriver::class);
        }

        $importer = $this->factory->getImporter($adapter, $databaseDriver);

        $this->assertInstanceOf(
            DatabaseImporter::class,
            $importer
        );

        if ($databaseDriver) {
            $this->assertSame(
                TestHelper::getValue($importer, 'db'),
                $databaseDriver
            );
        }
    }

    /**
     * Data provider for iterator test cases
     *
     * @return  array
     */
    public static function dataGetIterator(): array
    {
        return [
            'driver without custom iterator' => [
                'mysqli',
                true,
            ],
        ];
    }

    /**
     * @testdox  The factory builds a database iterator correctly
     *
     * @param   string  $adapter    The type of adapter to create
     * @param   bool    $createStatement  Statement holding the result set to be iterated.
     */
    #[DataProvider('dataGetIterator')]
    public function testGetIterator(string $adapter, bool $createStatement)
    {
        $statement = null;

        if ($createStatement) {
            $statement = $this->createMock(StatementInterface::class);
        }

        $this->assertInstanceOf(
            DatabaseIterator::class,
            $this->factory->getIterator($adapter, $statement)
        );
    }

    /**
     * Data provider for query test cases
     *
     * @return  array
     */
    public static function dataGetQuery(): array
    {
        return [
            'supported query' => [
                'mysqli',
                false,
                true,
            ],

            'unsupported query' => [
                'mariadb',
                true,
                false,
            ],
        ];
    }

    /**
     * @testdox  The factory builds a database query object correctly
     *
     * @param   string   $adapter               The type of adapter to create
     * @param   boolean  $shouldRaiseException  Flag indicating the factory should raise an exception for an unsupported adapter
     * @param   boolean  $createDb              The optional database driver to be injected into the importer
     */
    #[DataProvider('dataGetQuery')]
    public function testGetQuery(string $adapter, bool $shouldRaiseException, bool $createDb)
    {
        if ($shouldRaiseException) {
            $this->expectException(UnsupportedAdapterException::class);
        }

        $databaseDriver = null;

        if ($createDb) {
            $databaseDriver = $this->createMock(MysqliDriver::class);
        }

        $this->assertInstanceOf(
            QueryInterface::class,
            $this->factory->getQuery($adapter, $databaseDriver)
        );
    }
}
