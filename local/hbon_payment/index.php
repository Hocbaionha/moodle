<?php

use availability_date\condition;

require_once('../../config.php');

global $USER;

// params
$product_id = optional_param('product_id', 0, PARAM_INT);
$getlist =  optional_param('getlist', 0, PARAM_TEXT);
$condition  = [8,10,12,13,14];
$product_table = 'hbon_payment_product';
$product_is_exists = $DB->record_exists($product_table, array('id'=>$product_id));
if ($product_is_exists) {
    $product = $DB->get_record($product_table, array('id' => $product_id), '*', MUST_EXIST);
}

$sql = "SELECT * FROM mdl_hbon_payment_product WHERE id IN (8,10,12,13,14)";
$list_product = $DB->get_records_sql($sql);

$login_url = new moodle_url('/login/index.php');

require_login();

if($getlist === 'getlist'){

}
if(!isguestuser()) {
    $PAGE->set_url('/local/hbon_payment/index.php', array('product_id' => $product_id));
    $PAGE->requires->css(new moodle_url('/local/hbon_payment/styles.css'));
    $PAGE->requires->jquery();
    $PAGE->set_context(context_system::instance());
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title('Thanh toán');
    // $PAGE->set_heading('Thanh toán khóa học');
    $PAGE->navbar->add('Thanh toán');
    echo $OUTPUT->header();

    if ($product_is_exists) {
        $sql = "SELECT * FROM mdl_user_info_data d JOIN  mdl_user_info_field f ON d.fieldid=f.id JOIN mdl_user u ON u.id=d.userid WHERE u.id=? AND f.`name`='phone';";
        $data = $DB->get_record_sql($sql, array('id'=>$USER->id));
        if(isset($data->data)){
            $phone = $data->data;
        }else{
            $phone = '';
        }
        if(in_array($product_id, $condition)){
            require_once(__DIR__ . '/product_price_content_for_ttv.php');
        }else{
            require_once(__DIR__ . '/product_price_content.php');
        }
    } else {
        echo 'Chúng tôi không thể tìm thấy thông tin sản phẩm';
    }
    echo $OUTPUT->footer();
} else {
    header('Location: '. $login_url);
}
