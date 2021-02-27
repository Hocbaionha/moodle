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

if (isset($_POST['otp_input'])) {
    $otp_input = trim($_POST['otp_input']);
} else {
    return;
}

if (isset($_POST['signup_method'])) {
    $signup_method = trim($_POST['signup_method']);
} else {
    return;
}

$table = 'hbon_add_info_user';
$check_record = $DB->record_exists($table, array('user_id' => $uid, 'signup_method' => $signup_method));
if (!$check_record) {
    $array_return = [
        'status' => 'Fault',
        'content' => 'Có lỗi, bản ghi xác định OTP không tồn tại'];
    echo json_encode($array_return);
    return;
} else {
    $check_otp_record = $DB->get_record($table, array('user_id' => $uid, 'signup_method' => $signup_method));
    if ($check_otp_record->signup_expiration < time()) {
        $array_return = [
            'status' => 'Fault',
            'content' => 'Thời gian hiệu lực của mã OTP đã hết, hãy thử lại bằng cách chọn gửi lại OTP'];
        echo json_encode($array_return);
        return;
    } else {
        if ($check_otp_record->signup_token == $otp_input) {
            //Xác thực thành công
            //1- Ghi vào DB
            $obj = new stdClass();
            $obj->has_confirm = 1;
            $obj->signup_type = 'verified';
            $obj->id = $check_otp_record->id;
            $DB->update_record($table, $obj);
            //2- Thông báo
            $array_return = [
                'status' => 'OK',
                'content' => 'Việc xác thực của bạn đã thành công'];
            echo json_encode($array_return);
            return;
        } else {
            //Xác thực thất bại
            $array_return = [
                'status' => 'Fault',
                'content' => 'Việc xác thực thất bại, hãy thử lại bằng cách chọn gửi lại OTP'];
            echo json_encode($array_return);
            return;
        }
    }
}
/*
 *
 * $timedone = time();
$obj = new stdClass();
$obj->id = $transaction->id;
$obj->status = 1;
$obj->trans_nl_id = $transaction_info;
$timecancel = time();
$obj->timedone = $timedone;
$DB->update_record('hbon_payment_nganluong', $obj);
 *
 *
 */
