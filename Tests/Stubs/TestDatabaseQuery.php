<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Stubs;

use Joomla\Database\DatabaseQuery;

class TestDatabaseQuery extends DatabaseQuery
{
    public function processLimit($query, $limit, $offset = 0)
    {
        // TODO: Implement processLimit() method.
    }

    public function groupConcat($expression, $separator = ',')
    {
        // TODO: Implement groupConcat() method.
    }
}
