<?php

$phpunitConfiguration = simplexml_load_file(dirname(__DIR__) . '/phpunit.mysqli.xml.dist');
$env = $phpunitConfiguration->xpath('//phpunit/php/env');
foreach ($env as $envVar) {
	define((string) $envVar['name'], (string) $envVar['value']);
}
$consts = array_filter(
	get_defined_constants(),
	function ($const) {
		return str_starts_with($const, 'JOOMLA_TEST_');
	},
	ARRAY_FILTER_USE_KEY
);

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
