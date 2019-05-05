<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/*
 * Ensure that required path constants are defined.  These can be overridden within the phpunit.xml file
 * if you chose to create a custom version of that file.
 */
if (!\defined('JPATH_ROOT'))
{
	define('JPATH_ROOT', realpath(dirname(__DIR__)));
}

// Search for the Composer autoload file
$composerAutoload = dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists($composerAutoload))
{
	include_once $composerAutoload;
}
