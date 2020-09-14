<?php
require_once 'vendor/autoload.php';

// Turn off E_DEPRECATED caused by outdated PHPUnit
$errorReporting = error_reporting();
error_reporting($errorReporting & ~E_DEPRECATED);