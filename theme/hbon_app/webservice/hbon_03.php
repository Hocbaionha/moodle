<?php
/*
 * HBON VERIFY EMAIL OR PHONE
 * 02 - VERIFY OTP
 * UPDATE RECORD
 *
 */

$path = dirname(dirname(dirname(dirname(__FILE__))));

require $path . '/config.php';
//require_once(__DIR__ . '/lib.php');
/* add */
require_once $CFG->dirroot . '/cohort/locallib.php';

global $DB;
global $USER;
$uid = $USER->id;

$array_return = [
    'status' => 'OK',
    'content' => '',
];
$emailorphone="";
if (isset($_POST['emailorphone'])) {
    $emailorphone  = trim($_POST['emailorphone']);
}
else{
    return;
}

if (isset($_POST['signup_method'])) {
    $signup_method = trim($_POST['signup_method']);
} else {
    return;
}

$time_expiration = time() ;
$table = 'hbon_add_info_user';
$check_record = $DB->record_exists($table, array('user_id'=>$uid, 'signup_method'=>$signup_method));
if (!$check_record) {
    $new_record = new stdClass();
    $new_record->user_id = $uid;
    $new_record->has_confirm = 1;
    $new_record->signup_info = $emailorphone;
    $new_record->signup_type = 'verified';
    $new_record->signup_method = $signup_method;
    $new_record->signup_expiration = $time_expiration;
    $new_record_id = $DB->insert_record($table, $new_record,true, false);
    $array_return = [
        'status' => 'OK',
        'content' => 'Việc xác thực của bạn đã thành công'];
    echo json_encode($array_return);
    return;
}
//Check xem nó đã xác thực chưa (thay đổi trong bản ghi tham số has_confirm)
else {
    $array_return = [
        'status' => 'OK',
        'content' => 'Bạn đã xác thực từ trước'];
    echo json_encode($array_return);
    return;
}
