<?php

/**
 * @var array $dbCredentials The credentials to use to connect to the database, provided by following include:
 */
include __DIR__ . '/get_phpunit_env.php';

$database = 'PostgreSQL';
$dsn      = sprintf(
    "pgsql:host=%s port=%s",
    $dbCredentials['JOOMLA_TEST_DB_HOST'] ?? 'localhost',
    $dbCredentials['JOOMLA_TEST_DB_PORT'] ?? '5432'
);

include __DIR__ . '/wait.pdo.php';
