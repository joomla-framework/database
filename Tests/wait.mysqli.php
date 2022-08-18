<?php

include __DIR__ . '/get_phpunit_env.php';

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
		sleep(3);
	}
} while ($mysql->connect_error && 0 < $maxTries--);

exit($mysql->connect_error ? 0 : 1);
