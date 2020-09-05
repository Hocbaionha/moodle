<?php
require(__DIR__ . '/../../config.php');

global $PAGE;
$PAGE->requires->css(new moodle_url('/local/hbon_payment/styles.css'));
require_login();

$PAGE->set_url('/local/hbon_payment/payment_unsuccess.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Kich hoạt không thành công');
$PAGE->navbar->add('Kich hoạt không thành công');

echo $OUTPUT->header();
?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12 info">
        <div class="alert alert-warning">
            <h3>Có lỗi không xác định, quá trình kích hoạt không thành công</h3>
        </div>
    </div>
</div>

<?php echo $OUTPUT->footer(); ?>