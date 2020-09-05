<?php
require_once('../../config.php');

// params
$product_id = optional_param('product_id', 0, PARAM_INT);

$product_table = 'hbon_payment_product';
$product_is_exists = $DB->record_exists($product_table, array('id'=>$product_id));
if ($product_is_exists) {
    $product = $DB->get_record($product_table, array('id' => $product_id), '*', MUST_EXIST);
}

$login_url = new moodle_url('/login/index.php');

require_login();

if(!isguestuser()) {
    $PAGE->set_url('/local/hbon_payment/index.php', array('product_id' => $product_id));
    $PAGE->requires->css(new moodle_url('/local/hbon_payment/styles.css'));
    $PAGE->set_context(context_system::instance());
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title('Thanh toán');
    // $PAGE->set_heading('Thanh toán khóa học');
    $PAGE->navbar->add('Thanh toán');
    echo $OUTPUT->header();

    if ($product_is_exists) {
        require_once(__DIR__ . '/product_price_content.php');
    } else {
        echo 'Chúng tôi không thể tìm thấy thông tin sản phẩm';
    }
    echo $OUTPUT->footer();
} else {
    header('Location: '. $login_url);
}
