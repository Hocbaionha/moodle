<?php
require(__DIR__ . '/../../config.php');

global $PAGE;
$PAGE->requires->css(new moodle_url('/local/hbon_payment/styles.css'));
require_login();

$PAGE->set_url('/local/hbon_payment/payment_success.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Thanh toán thành công');
$PAGE->navbar->add('Thanh toán thành công');

echo $OUTPUT->header();
?>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12 info">
        <div class="alert alert-info">
            <h2>Bạn đã thanh toán thành công</h2>
        </div>
    </div>
</div>

<?php echo $OUTPUT->footer(); ?>