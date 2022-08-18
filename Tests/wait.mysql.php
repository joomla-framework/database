<?php

/**
 * @var array $dbCredentials The credentials to use to connect to the database, provided by following include:
 */
include __DIR__ . '/get_phpunit_env.php';

$database = 'MySQL';
$dsn      = sprintf(
    "mysql:host=%s;port=%s",
    $dbCredentials['JOOMLA_TEST_DB_HOST'] ?? 'localhost',
    $dbCredentials['JOOMLA_TEST_DB_PORT'] ?? '3306'
);

include __DIR__ . '/wait.pdo.php';
