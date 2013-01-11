<?php
/**
 * Instagram Tagger
 *
 * See README.md for the purpose of this application.
 */

// By default, show all errors
error_reporting(-1);
ini_set('display_errors', 'on');

define('ABS', dirname(__FILE__).'/');
define('IS_CLI', (php_sapi_name() === 'cli' OR defined('STDIN')) ? TRUE : FALSE);

// Dependencies
file_exists(ABS.'/vendor/autoload.php') ? require(ABS.'/vendor/autoload.php') : die('Composer not initiated.');

// Internal functions
require(ABS.'functions.php');