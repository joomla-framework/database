<?php

/**
 * @var array $dbCredentials The credentials to use to connect to the database, provided by following include:
 */
include __DIR__ . '/get_phpunit_env.php';

$database = 'MS SQL Server';
$dsn      = sprintf(
    "sqlsrv:Server==%s,%s",
    $dbCredentials['JOOMLA_TEST_DB_HOST'] ?? 'localhost',
    $dbCredentials['JOOMLA_TEST_DB_PORT'] ?? '1433'
);

include __DIR__ . '/wait.pdo.php';
