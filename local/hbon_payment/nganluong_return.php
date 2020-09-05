<?php
require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/cohort/locallib.php');

$merchant_id = '57833';
$merchant_pass = '3e439dc335fb7b512c6c736f724805e9';

$return_url = new moodle_url("/local/hbon_payment/nganluong_return.php");
$url_return = new moodle_url("/local/hbon_payment/payment_success.php");
$url_return_fail = new moodle_url("/local/hbon_payment/payment_unsuccess.php");
$url_nl_fail = new moodle_url("/local/hbon_payment/nganluong_unsuccess.php");

if (isset($_GET['product_code'])) {
    $product_code  = $_GET['product_code'];
}
if (isset($_GET['payment_type'])) {
	  $payment_type  = $_GET['payment_type'];
}
if (isset($_GET['error_text'])) {
    $error_text  = $_GET['error_text']; 
}
if (isset($_GET['transaction_info'])) {
	  $transaction_info  = $_GET['transaction_info']; 
}
if (isset($_GET['payment_id'])) {
  	$nl_transaction_info  = $_GET['payment_id']; 
}
if (isset($_GET['payment_type'])) {
  	$payment_type  = $_GET['payment_type']; 
}
if (isset($_GET['price'])) {
	  $price  = $_GET['price']; 
}
if (isset($_GET['order_code'])) {
  	$order_code  = $_GET['order_code']; 
}
if (isset($_GET['secure_code'])) {
  	$secure_code_get  = $_GET['secure_code']; 
}

global $USER;
global $DB;

$transaction = $DB->get_record('hbon_payment_nganluong', array('order_id'=>$order_code,'request_id'=>$transaction_info));

if ($error_text =="") {
    if (!(empty($transaction))) {
        $obj = new stdClass();
        $obj->id = $transaction->id;
        $obj->status = 1;
        $obj->nl_transaction_id = $transaction_info;
        $obj->time_done = time();
        $DB->update_record('hbon_payment_nganluong', $obj);
        
        $user_id = $transaction->user_id;
        $product = $DB->get_record('hbon_payment_product', array('code'=> $product_code));
        if(!empty($product)) {
            // add user to cohorts
            $product_cohort_codes = explode(",", $product->cohort_codes);
            for($i = 0, $size = count($product_cohort_codes); $i < $size; ++$i) {
                $cohort = $DB->get_record('cohort', array('idnumber'=>$product_cohort_codes[$i]));
                if (!(empty($cohort))){
                    cohort_add_member($cohort->id, $user_id) ;

                    $cohort_members_obj = new stdClass();
                    $cohort_members_obj->user_id = $user_id;
                    $cohort_members_obj->cohort_id = $cohort->id;
                    $cohort_members_obj->order_id = $transaction->order_id;
                    $cohort_members_obj->request_id = $transaction->request_id;
                    $cohort_members_obj->time_join = time();

                    $DB->insert_record('hbon_payment_cohort_members', $cohort_members_obj, false);
                }
            }
            // store history

        }
        header('Location: '.$url_return);
    }else {
        header('Location: '.$url_return_fail);
    }
} else {
    header('Location: '.$url_nl_fail);
}

// http://moodle.local/local/hbon_payment/nganluong_return.php?product_code=LTV10-TVA&transaction_info=5ea17d4f67397&order_code=HBON-1587641679&price=100000&payment_id=77445972&payment_type=1&error_text=&secure_code=e03815dac213e66ed190a24e49a72f9d&token_nl=42270440-3da3d82345e3c40519646296bd4c5194
// http://moodle.local/local/hbon_payment/nganluong_return.php?product_code=LTV10-TVA&transaction_info=5ea2a2959e17d&order_code=HBON-1587716757&price=100000&payment_id=77445972&payment_type=1&error_text=&secure_code=e03815dac213e66ed190a24e49a72f9d&token_nl=42270440-3da3d82345e3c40519646296bd4c5194
