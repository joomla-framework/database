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
