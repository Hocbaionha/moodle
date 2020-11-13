<?php

use availability_date\condition;

require_once('../../config.php');

global $USER;

// params
$product_id = optional_param('product_id', 0, PARAM_INT);

$product_table = 'hbon_payment_product';
$product_is_exists = $DB->record_exists($product_table, array('id'=>$product_id));
if ($product_is_exists) {
    $product = $DB->get_record($product_table, array('id' => $product_id), '*', MUST_EXIST);
}

$list_product = $DB->get_records($product_table);

$login_url = new moodle_url('/login/index.php');

require_login();

if(!isguestuser()) {
    $PAGE->set_url('/local/hbon_payment/index.php', array('product_id' => $product_id));
    $PAGE->requires->css(new moodle_url('/local/hbon_payment/styles.css'));
    $PAGE->requires->jquery();
    $PAGE->set_context(context_system::instance());
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title('Thanh toán');
    // $PAGE->set_heading('Thanh toán khóa học');
    $PAGE->navbar->add('Thanh toán');
    $condition  = [8,10,12,13,14];
    echo $OUTPUT->header();

    if ($product_is_exists) {
//        echo $OUTPUT->render_from_template('theme_classon/payment_product', $data);
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
