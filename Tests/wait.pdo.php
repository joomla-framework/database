<?php

/**
 * @var array $dbCredentials The credentials to use to connect to the database, provided by caller
 * @var string $database The database type, provided by caller
 * @var string $dsn      The DSN for the database, provided by caller
 */
echo 'Waiting for ' . $database . ' to become available ...';
$maxTries  = 10;
$connected = false;
do {
    try {
        $db        = new PDO(
            $dsn,
            $dbCredentials['JOOMLA_TEST_DB_USER'] ?? null,
            $dbCredentials['JOOMLA_TEST_DB_PASSWORD'] ?? null
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
