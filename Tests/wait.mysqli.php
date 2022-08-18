<?php

include __DIR__ . '/get_phpunit_env.php';

echo "Waiting for MySQL to become available ";
$maxTries = 10;
do {
	$mysql = new mysqli(
		JOOMLA_TEST_DB_HOST,
		JOOMLA_TEST_DB_USER,
		JOOMLA_TEST_DB_PASSWORD,
		JOOMLA_TEST_DB_DATABASE,
		JOOMLA_TEST_DB_PORT
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
