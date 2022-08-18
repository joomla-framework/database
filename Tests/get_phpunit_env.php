<?php

$phpunitConfiguration = simplexml_load_file(dirname(__DIR__) . '/phpunit.mysqli.xml.dist');
$env                  = $phpunitConfiguration->xpath('//phpunit/php/env');
foreach ($env as $envVar) {
    define((string) $envVar['name'], (string) $envVar['value']);
}
$consts = array_filter(
    get_defined_constants(),
    function ($const) {
        return strpos($const, 'JOOMLA_TEST_') === 0;
    },
    ARRAY_FILTER_USE_KEY
);
