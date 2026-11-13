<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Sqlsrv;

use Joomla\Test\DatabaseManager;

/**
 * Lets the SQL Server test cases trust a self-signed server certificate
 *
 * The SQL Server test containers use a self-signed certificate, which the ODBC driver rejects by default. Set the
 * environment variable JOOMLA_TEST_DB_TRUST_CERTIFICATE to "yes" to pass the trust_certificate option to the driver.
 */
trait TrustServerCertificateTrait
{
    /**
     * Create the database manager for this test case.
     *
     * @return  DatabaseManager
     */
    protected static function createDatabaseManager(): DatabaseManager
    {
        return new class () extends DatabaseManager {
            protected function initialiseParams(): void
            {
                parent::initialiseParams();

                if (\is_array($this->params) && getenv('JOOMLA_TEST_DB_TRUST_CERTIFICATE') === 'yes') {
                    $this->params['trust_certificate'] = true;
                }
            }
        };
    }
}
