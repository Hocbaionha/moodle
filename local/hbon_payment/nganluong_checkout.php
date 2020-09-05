<?php 
require(__DIR__ . '/../../config.php');
global $USER;
global $DB;

$product_id = optional_param('product_id', 0, PARAM_INT);
$product_code = optional_param('product_code', '', PARAM_RAW);
$product_name = optional_param('product_name', 0, PARAM_INT);
$price = optional_param('money_trans', 0, PARAM_INT);

// thong tin ben Ngan Luong
$nganluong_url = "https://www.nganluong.vn/checkout.php";
$merchant_id = '57833';
$merchant_pass = '3e439dc335fb7b512c6c736f724805e9';
$return_url = new moodle_url("/local/hbon_payment/nganluong_return.php", array("product_code"=> $product_code) );
$receiver ='info@hocbaionha.com'; //Địa chỉ email của tài khoản đăng ký trên ngân lượng

// create transaction
$transaction_info = uniqid();
$order_code = 'HBON-'.time();
$currency = 'vnd';
$quantity = 1;
$tax = 0;
$discount = 0;
$fee_cal = 0;
$fee_shipping = 0;
$order_description = 'Ma SP: '. $product_code;
$buyer_info = $USER->firstname. ' '.$USER->lastname . "*|*" .	$USER->email . "*|*" . $USER->phone1 . "*|*" . $USER->address;
$affiliate_code = "";
$lang = "vi";
$secure_code_before_md5 = $merchant_id . ' ' . $return_url .' ' . $receiver . ' ' . $transaction_info . ' '. $order_code . ' '. $price . ' ' . $currency . ' ' . $quantity . ' ' . $tax . ' '. $discount . ' ' . $fee_cal . ' '. $fee_shipping . ' ' . $order_description . ' ' .$buyer_info . ' ' . $affiliate_code . ' ' . $merchant_pass;
$secure_code = md5($secure_code_before_md5);
$cancel_url =  new moodle_url("/local/hbon_payment/nganluong_payment_cancel.php", array("request_id"=> $transaction_info) );
$time_created = time();

$new_nganluong_transaction = new stdClass();
$new_nganluong_transaction ->order_id = $order_code;
$new_nganluong_transaction ->request_id = $transaction_info; // note
$new_nganluong_transaction ->status = 0;
$new_nganluong_transaction ->user_id = $USER ->id;
$new_nganluong_transaction ->product_id = $product_id;
$new_nganluong_transaction ->unit_price = $price;
$new_nganluong_transaction ->quantity = $quantity;
$new_nganluong_transaction ->sales_amount = $price * $quantity;

$new_nganluong_transaction ->time_created = $time_created;

$DB->insert_record('hbon_payment_nganluong', $new_nganluong_transaction, false);

$url_string = $nganluong_url.'?merchant_site_code='.$merchant_id.
							'&return_url='.$return_url.'&receiver='.$receiver.'&transaction_info='.$transaction_info.
							'&order_code='.$order_code.
							'&price='.$price.
							'&currency='.$currency.
							'&quantity='.$quantity.
							'&tax='.$tax.
							'&discount='.$discount.
							'&fee_cal='.$fee_cal.
							'&fee_shipping='.$fee_shipping.
							'&order_description='.$order_description.
							'&buyer_info='.$buyer_info.
							'&affiliate_code='.$affiliate_code.
							'&lang='.$lang.
							'&secure_code='.$secure_code.
							'&cancel_url='.$cancel_url;
header('Location: '.$url_string);

echo "Có lỗi";
?>