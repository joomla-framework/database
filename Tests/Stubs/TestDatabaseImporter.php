<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Stubs;

use Joomla\Database\DatabaseImporter;

class TestDatabaseImporter extends DatabaseImporter
{
    public function check()
    {
        // TODO: Implement check() method.
    }

    protected function getAlterTableSql(\SimpleXMLElement $structure)
    {
        // TODO: Implement getAlterTableSql() method.
    }

    protected function getColumnSql(\SimpleXMLElement $field)
    {
        // TODO: Implement getColumnSql() method.
    }

    protected function xmlToCreate(\SimpleXMLElement $table)
    {
        // TODO: Implement xmlToCreate() method.
    }
}
