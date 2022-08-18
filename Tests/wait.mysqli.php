<?php

/**
 * @var array $dbCredentials The credentials to use to connect to the database, provided by following include:
 */
include __DIR__ . '/get_phpunit_env.php';

echo 'Waiting for MySQL to become available ...';
$maxTries = 10;
do {
    $mysql = new mysqli(
        $dbCredentials['JOOMLA_TEST_DB_HOST'] ?? 'localhost',
        $dbCredentials['JOOMLA_TEST_DB_USER'] ?? 'root',
        $dbCredentials['JOOMLA_TEST_DB_PASSWORD'] ?? null,
        $dbCredentials['JOOMLA_TEST_DB_DATABASE'] ?? null,
        $dbCredentials['JOOMLA_TEST_DB_PORT'] ?? '3306'
    );

    if ($mysql->connect_error) {
        echo '.';
        sleep(3);
    }
} while ($mysql->connect_error && 0 < $maxTries--);

if ($mysql->connect_error) {
    echo "\nFailed to connect to MySQL: (" . $mysql->connect_errno . ") " . $mysql->connect_error . "\n";
    exit(1);
}

echo " done.\n";
