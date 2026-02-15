<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Mysql;

use Joomla\Database\DatabaseInterface;
use Joomla\Database\Mysql\MysqlDriver;
use Joomla\Database\Mysql\MysqlExporter;
use Joomla\Database\Mysql\MysqlQuery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\Mysql\MysqlExporter.
 */
class MysqlExporterTest extends TestCase
{
    /**
     * Mock database driver
     *
     * @var  MockObject|MysqlDriver
     */
    private $db;

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

        $this->db = $this->createMock(MysqlDriver::class);

        $this->db->expects($this->any())
            ->method('getPrefix')
            ->willReturn('jos_');

        $this->db->expects($this->any())
            ->method('createQuery')
            ->willReturnCallback(function () {
                return new MysqlQuery($this->db);
            });

        $this->db->expects($this->any())
            ->method('getTableColumns')
            ->willReturn(
                [
                    'id' => (object) [
                        'Field'      => 'id',
                        'Type'       => 'int(11) unsigned',
                        'Collation'  => null,
                        'Null'       => 'NO',
                        'Key'        => 'PRI',
                        'Default'    => '',
                        'Extra'      => 'auto_increment',
                        'Privileges' => 'select,insert,update,references',
                        'Comment'    => '',
                    ],
                    'title' => (object) [
                        'Field'      => 'title',
                        'Type'       => 'varchar(255)',
                        'Collation'  => 'utf8_general_ci',
                        'Null'       => 'NO',
                        'Key'        => '',
                        'Default'    => '',
                        'Extra'      => '',
                        'Privileges' => 'select,insert,update,references',
                        'Comment'    => '',
                    ],
                ]
            );

        $this->db->expects($this->any())
            ->method('getTableKeys')
            ->willReturn(
                [
                    (object) [
                        'Table'        => 'jos_test',
                        'Non_unique'   => '0',
                        'Key_name'     => 'PRIMARY',
                        'Seq_in_index' => '1',
                        'Column_name'  => 'id',
                        'Collation'    => 'A',
                        'Cardinality'  => '2695',
                        'Sub_part'     => '',
                        'Packed'       => '',
                        'Null'         => '',
                        'Index_type'   => 'BTREE',
                        'Comment'      => '',
                    ],
                ]
            );

        $this->db->expects($this->any())
            ->method('quoteName')
            ->willReturnCallback(
                function ($name, $as = null) {
                    if (is_string($name)) {
                        return "`$name`";
                    }

                    $fields = [];

                    foreach ($name as $value) {
                        $fields[] = "`$value`";
                    }

                    return $fields;
                }
            );
    }

    /**
     * Data provider for string casting test cases
     *
     * @return  array
     */
    public static function dataCastingToString(): array
    {
        return [
        'without structure or data' => [
            false,
            false,
            <<<XML
<mysqldump xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
 <database name="">
 </database>
</mysqldump>
XML
            ,
        ],

        'with only structure' => [
            true,
            false,
            <<<XML
<mysqldump xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <database name="">
    <table_structure name="#__test">
     <field Field="id" Type="int(11) unsigned" Null="NO" Key="PRI" Default="" Extra="auto_increment" />
     <field Field="title" Type="varchar(255)" Null="NO" Key="" Default="" Extra="" />
     <key Table="#__test" Non_unique="0" Key_name="PRIMARY" Seq_in_index="1" Column_name="id" Collation="A" Null="" Index_type="BTREE" Sub_part="" Comment="" />
    </table_structure>
  </database>
</mysqldump>
XML
            ,
        ],

        'with only data' => [
            false,
            true,
            <<<XML
<mysqldump xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <database name="">
    <table_data name="#__test">
      <row>
        <field name="id">1</field>
        <field name="title">Row 1</field>
      </row>
      <row>
        <field name="id">2</field>
        <field name="title">Row 2</field>
      </row>
    </table_data>
  </database>
</mysqldump>
XML
            ,
        ],

        'with structure and data' => [
            true,
            true,
            <<<XML
<mysqldump xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <database name="">
    <table_structure name="#__test">
      <field Field="id" Type="int(11) unsigned" Null="NO" Key="PRI" Default="" Extra="auto_increment" />
      <field Field="title" Type="varchar(255)" Null="NO" Key="" Default="" Extra="" />
      <key Table="#__test" Non_unique="0" Key_name="PRIMARY" Seq_in_index="1" Column_name="id" Collation="A" Null="" Index_type="BTREE" Sub_part="" Comment="" />
    </table_structure>
    <table_data name="#__test">
      <row>
        <field name="id">1</field>
        <field name="title">Row 1</field>
      </row>
      <row>
        <field name="id">2</field>
        <field name="title">Row 2</field>
      </row>
    </table_data>
  </database>
</mysqldump>
XML
            ,
        ],
        ];
    }

    /**
     * @testdox  The exporter can be cast to a string
     *
     * @param   boolean  $withStructure  True to export the structure, false to not.
     * @param   boolean  $withData       True to export the data, false to not.
     * @param   string   $expectedXml    Expected XML string.
     */
    #[DataProvider('dataCastingToString')]
    public function testCastingToString(bool $withStructure, bool $withData, string $expectedXml)
    {
        $exporter = new MysqlExporter();

        $exporter->setDbo($this->db)
            ->from('jos_test')
            ->withStructure($withStructure)
            ->withData($withData);

        if ($withData) {
            $this->db->expects($this->once())
                ->method('loadObjectList')
                ->willReturn(
                    [
                        (object) [
                            'id'    => 1,
                            'title' => 'Row 1',
                        ],
                        (object) [
                            'id'    => 2,
                            'title' => 'Row 2',
                        ],
                    ]
                );
        }

        $this->assertXmlStringEqualsXmlString($expectedXml, (string) $exporter);
    }

    /**
     * Data provider for check test cases
     *
     * @return  array
     */
    public static function dataCheck(): array
    {
        return [
            'passes checks' => [
                MysqlDriver::class,
                '#__dbtest',
                null,
            ],

            'fails checks with incorrect database driver subclass' => [
                DatabaseInterface::class,
                '#__dbtest',
                'Database connection wrong type.',
            ],

            'fails checks with no database driver' => [
                null,
                '#__dbtest',
                'Database connection wrong type.',
            ],

            'fails checks with no tables' => [
                MysqlDriver::class,
                null,
                'ERROR: No Tables Specified',
            ],
        ];
    }

    /**
     * @testdox  The exporter checks for errors
     *
     * @param   string|null           $db                Database driver to set in the exporter.
     * @param   string[]|string|null  $from              Database tables to export from.
     * @param   string|null           $exceptionMessage  If an Exception should be thrown, the expected message
     */
    #[DataProvider('dataCheck')]
    public function testCheck(?string $db, $from, ?string $exceptionMessage)
    {
        if ($exceptionMessage) {
            $this->expectException(\RuntimeException::class);
            $this->expectExceptionMessage($exceptionMessage);
        }

        $exporter = new MysqlExporter();

        if ($db) {
            $exporter->setDbo($this->createMock($db));
        }

        if ($from) {

            $exporter->from($from);
        }

        $this->assertSame($exporter, $exporter->check(), 'The exporter supports method chaining');
    }
}
