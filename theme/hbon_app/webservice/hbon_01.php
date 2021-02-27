<?php
/*
 * HBON VERIFY EMAIL OR PHONE
 * 01 - GEN and SEND OTP
 * CREATE RECORD
 *
 */



$duration_expir = 3600*6;
$path = dirname(dirname(dirname(dirname(__FILE__))));
$subject = 'Mã xác minh OTP của bạn tại hocbaionha.com';



function Send_otp_mail ($email_add,$subject,$mail_body) {
    $URL_SEND_MAIL = 'https://class.tmistones.com/hbon/api/v1/send-an-email/';
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $URL_SEND_MAIL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "email_add=".$email_add."&subject=".$subject."&mail_body=".$mail_body,
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded",
            "postman-token: 58f9437e-d35d-6b95-58dc-799d3ffbcda8"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return "cURL Error #:" . $err;
    } else {
        return $response;
    }
    /*
    $URL_SEND_MAIL = 'https://class.tmistones.com/hbon/api/v1/send-an-email/';
    $request = new HttpRequest();
    $request->setUrl($URL_SEND_MAIL);
    $request->setMethod(HTTP_METH_POST);
    $request->setHeaders(array(
        'postman-token' => 'c9f1802f-a2a1-9bff-8540-3166a6f380d7',
        'cache-control' => 'no-cache',
        'content-type' => 'application/x-www-form-urlencoded'
    ));
    $request->setContentType('application/x-www-form-urlencoded');
    $request->setPostFields(array(
        'email_add' => $email_add,
        'subject' => $subject,
        'mail_body' => $mail_body
    ));
    try {
        $response = $request->send();
        return $response->getBody();
        } catch (HttpException $ex) {
        return  $ex;
        }
    */
    }

function Send_otp_sms ($phone,$content){
    $sms_url = "http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_get?";
    $ApiKey = "EDDC4F5A2F7C0911FEA210163BA7CB";
    $SecretKey = "9A095E7C6138BB0B3FFFC8AB738042";
    $SmsType = "2";
    $Brandname = "Verify";

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $sms_url."Phone=".$phone."&Content=".$content."&ApiKey=".$ApiKey."&SecretKey=".$SecretKey."&SmsType=".$SmsType."&Brandname=".$Brandname,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "postman-token: f35c62f5-9459-0659-fa84-83adb1d7f581"
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        return "cURL Error #:" . $err;
    } else {
        return $response;
    }
}


require($path. '/config.php');
//require_once(__DIR__ . '/lib.php');
/* add */
require_once($CFG->dirroot.'/cohort/locallib.php');

$array_return = [
    'status'=>'OK',
    'content'=>''
];
global $DB;
global $USER;
$uid = $USER->id;


if (isset($_POST['emailorphone'])) {
    $emailorphone  = trim($_POST['emailorphone']);
}
else{
    return;
}

if (isset($_POST['signup_method'])) {
    $signup_method  = trim($_POST['signup_method']);
}
else{
    return;
}

//Get RANDOM OTP
$num = mt_rand(0,999999);
$str = strval($num);
$OTP = str_pad($str,6,"0",STR_PAD_LEFT);
$time_expiration = time() + $duration_expir;

$table = 'hbon_add_info_user';
$check_record = $DB->record_exists($table, array('user_id'=>$uid, 'signup_method'=>$signup_method));
if (!$check_record) {
    $new_record = new stdClass();
    $new_record->user_id = $uid;
    $new_record->has_confirm = 0;
    $new_record->signup_method = $signup_method;
    $new_record->signup_token = $OTP;
    $new_record->signup_expiration = $time_expiration;
    $new_record_id = $DB->insert_record($table, $new_record,true, false);
}
//Check xem nó đã xác thực chưa (thay đổi trong bản ghi tham số has_confirm)
else {
    $check_otp_record = $DB->get_record($table, array('user_id'=>$uid, 'signup_method'=>$signup_method));
    $new_record_id = $check_otp_record->id;
}

// Check email or phone here:
$domain = strstr ($emailorphone,'@');
$name = strstr ($emailorphone,'@', true);

if($signup_method == 'email') {
    $domain = strstr ($emailorphone,'@');
    $name = strstr ($emailorphone,'@', true);

    if ($name !=''){
        // Là emai -> Gửi email
        //Check xem email này đã sử dụng chưa?
        $check_record = $DB->record_exists($table, array( 'signup_method'=>$signup_method, 'signup_info'=>$emailorphone));
        if (!$check_record === false) {
            $check_user_have_email = $DB->get_record($table, array('signup_method'=>$signup_method, 'signup_info'=>$emailorphone));
            if ($check_user_have_email->user_id !== $uid){
                $array_return = [
                    'status'=>'Fault',
                    'content'=>'Địa chỉ email này đã được dùng để xác thực, hãy sử dụng một email khác'];
                echo json_encode($array_return);
                return;
            }
        }
    
        //Update DB
        $obj = new stdClass();
        $obj->id = $new_record_id;
        $obj->signup_type = 'verifying';
        $obj->signup_token = $OTP;
        $obj->signup_info = $emailorphone;
        $obj->signup_expiration = $time_expiration;
        //$obj->otp_number = $emailorphone;
        $DB->update_record($table, $obj);
        $mail_body = "Mã nhận dạng OPT của bạn là: <strong>".$OTP."</strong> <br/>
        Bạn lưu ý mã này chỉ có giá trị sử dụng trong 6 giờ. <br/>
        Hãy sử dụng mã này để xác thực tài khoản của bạn trên hocbaionha.com<br/>
        Chúc bạn có những giờ phút học tập bổ ích tại hocbaionha.com <br/>
        <br/><br/>
        Trân trọng!<br/>
        Đội ngũ phát triển <a href ='https://hocbaionha.com'>hocbaionha.com</a>
        ";
        $send_otp_mail = Send_otp_mail ($emailorphone,$subject,$mail_body);
        if (strpos($send_otp_mail,'OK')!== false){
            $out_content = 'Một email chứa mã nhận dạng OTP đã được gửi qua email mà bạn vừa cung cấp. Hãy sử dụng để xác thực!';
        }
        else {
            $out_content = 'Có lỗi xảy ra trong quá trình gửi mail  ['.$send_otp_mail.']';
        }
    
        $array_return = [
            'status'=>'OK',
            'content'=>$out_content];
        echo json_encode($array_return);
        return;
    } else {
        $array_return = [
            'status'=>'Fault',
            'content'=>'Email không hợp lệ'];
        echo json_encode($array_return);
        return;
    }
}else if($signup_method == 'phone') {
    if(preg_match("/^[0-9]{10}$/", $emailorphone)) {
        // Là số điện thoại -> Gửi tin nhắn
        //Check xem số điện thoại này đã sử dụng chưa?
        $check_record = $DB->record_exists($table, array('signup_method'=>$signup_method, 'signup_info'=>$emailorphone));
        if (!$check_record === false) {
            $check_user_have_phone = $DB->get_record($table, array('signup_method'=>$signup_method, 'signup_info'=>$emailorphone));
            if ($check_user_have_phone->user_id !== $uid){
                $array_return = [
                    'status'=>'Fault',
                    'content'=>'Số điện thoại này đã được dùng để xác thực, hãy sử dụng một số khác'];
                echo json_encode($array_return);
                return;

            }

        }
        //Update DB
        $obj = new stdClass();
        $obj->id = $new_record_id;
        $obj->signup_info = $emailorphone;
        $obj->signup_token = $OTP;
        $obj->signup_type = 'verifying';
        $obj->signup_expiration = $time_expiration;
        //$obj->otp_number = $emailorphone; Ghi lại số lần gửi tin
        $DB->update_record($table, $obj);
        $send_otp_sms = Send_otp_sms ($emailorphone,'Ma%20xac%20thuc%20cua%20ban%20la%20'.$OTP);

        if (strpos($send_otp_sms,'100')!== false){
            $out_content = 'Một tin nhắn chứa mã nhận dạng OTP đã được gửi qua số điện thoại mà bạn vừa cung cấp. Hãy sử dụng để xác thực!';
        }
        else {
            $out_content = 'Có lỗi xảy ra trong quá trình gửi tin nhắn  ['.$send_otp_sms.']';
        }

        $array_return = [
            'status'=>'OK',
            'content'=>$out_content];
        echo json_encode($array_return);
        return;

    }
    else {
        //Không phải mail hoặc số điện thoại
        $array_return = [
            'status'=>'ERROR',
            'content'=>'Bạn đã nhập vào email hoặc số điện thoại không hợp lệ'];

        echo json_encode($array_return);
        return;

    }
}

//Tạo bản ghi

/* has_confirm

'content'=>'Một email chứa mã nhận dạng OTP đã được gửi qua email mà bạn vừa cung cấp. Hãy sử dụng để xác thực!'
 * $new_nganluong_transaction = new stdClass();
$new_nganluong_transaction ->user_id = $USER ->id;
$new_nganluong_transaction ->status = 0;
$new_nganluong_transaction ->request_id = $transaction_info;
$new_nganluong_transaction ->order_id = $order_code;
$new_nganluong_transaction ->timecreate = $timecreate;
//echo $new_nganluong_transaction->user_id;
//echo $new_nganluong_transaction->request_id;
//echo $new_nganluong_transaction->order_id;
//echo $new_nganluong_transaction->timecreate;


$DB->insert_record('hbon_payment_nganluong', $new_nganluong_transaction, false);
 *
 *
 */


?>