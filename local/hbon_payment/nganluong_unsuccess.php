<?php
require(__DIR__ . '/../../config.php');

global $PAGE;
$PAGE->requires->css(new moodle_url('/local/hbon_payment/styles.css'));

if (isset($_GET['errorcode'])) {
	$errorcode  = $_GET['errorcode'];
}
require_login();

$PAGE->set_url('/local/hbon_payment/nganluong_unsucess.php', array('errorcode'=> $errorcode));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Kich hoạt không thành công');
$PAGE->navbar->add('Kich hoạt không thành công');

echo $OUTPUT->header();
?>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12 info">
    <div class="alert alert-warning">
      <h3>Quá trình thanh toán của bạn trên trang Ngân lượng không thành công!</h3>
          <p>Mã lỗi phía Ngân lượng trả về: <?php echo $errorcode; ?></p>
    </div>
  </div>
</div>

<?php echo $OUTPUT->footer(); ?>