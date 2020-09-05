<?php
require(__DIR__ . '/../../config.php');

if (isset($_GET['request_id'])) {
	$request_id  = $_GET['request_id'];
}

require_login();
global $USER;
global $DB;

$url_cancel = new moodle_url("/local/hbon_payment/payment_cancel.php");

$transaction = $DB->get_record('hbon_payment_nganluong', array('request_id'=>$request_id));
$obj = new stdClass();
$obj->id = $transaction->id;
$obj->status = 2;
$obj->time_cancel = time();
$DB->update_record('hbon_payment_nganluong', $obj);

header('Location: '.$url_cancel);