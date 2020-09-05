<?php
$path = dirname(dirname(dirname(dirname(__FILE__))));
require($path. '/config.php');
//require_once(__DIR__ . '/lib.php');
/* add */
require_once($CFG->dirroot.'/cohort/locallib.php');

global $USER;
global $DB;

echo "Hi";
?>