<?php

/**
 * @var string $database The database type, provided by caller
 * @var string $dsn      The DSN for the database, provided by caller
 */
include __DIR__ . '/get_phpunit_env.php';

echo 'Waiting for ' . $database . ' to become available ...';
$maxTries  = 10;
$connected = false;
do {
    try {
        $db        = new PDO(
            $dsn,
            JOOMLA_TEST_DB_USER,
            JOOMLA_TEST_DB_PASSWORD
        );
        $connected = true;
    } catch (PDOException $e) {
        echo '.';
        sleep(3);
    }
} while (!$connected && 0 < $maxTries--);

if (!$connected) {
    echo "\nFailed to connect to " . $database . ": " . $e->getMessage() . "\n";
    exit(1);
}

echo " done.\n";
