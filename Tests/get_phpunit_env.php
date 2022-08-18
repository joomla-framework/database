<?php

$dbCredentials        = [];
$phpunitConfiguration = simplexml_load_file(dirname(__DIR__) . '/phpunit.mysqli.xml.dist');
$env                  = $phpunitConfiguration->xpath('//phpunit/php/env');

foreach ($env as $envVar) {
    $dbCredentials[(string) $envVar['name']] = (string) $envVar['value'];
}
