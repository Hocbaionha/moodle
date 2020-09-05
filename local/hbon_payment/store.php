<?php
require_once('../../config.php');

$product_table = 'hbon_payment_product';
$ltv10_products = $DB->get_records($product_table , array('active' => 1, 'category_code' => 'LTV10'), $sort='sort_order,id');
$ttv_products = $DB->get_records($product_table , array('active' => 1, 'category_code' => 'TTV'), $sort='sort_order,id');

global $PAGE;
$PAGE->set_url('/local/hbon_payment/store.php');
$PAGE->requires->css(new moodle_url('/local/hbon_payment/styles.css'));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Cửa hàng');
// $PAGE->set_heading('Cửa hàng');
$PAGE->navbar->add('Cửa hàng');
echo $OUTPUT->header();
?>


<div class="container my-4">
    <h4><b>KHÓA HỌC LUYỆN THI VÀO 10</b></h4>
    <div class="row">
    <?php foreach ($ltv10_products as $product) { ?>
        <div class="col-sm-3">
            <div class="card product-card shadow-sm mb-3">
                <img src="<?php echo new moodle_url($product->image_url); ?>" class="card-img-top" alt="">
                <div class="p-2">
                    <span class="label label-success"><?php echo $product->category_code; ?></span>
                    <span class="label label-hot">HOT</span> 
                </div>
                <div class="card-body p-2 product-info ">
                    <h5 class="card-title product-name"><?php echo $product->name; ?></h5>
                    <p class="card-text"><?php echo $product->description ?></p>
                </div>
                <div class="card-body p-2 d-flex justify-content-between align-items-center">
                    <span class="product-price"><?php echo number_format($product->price, 0,",","."). ' đ'; ?> </span>
                    <a href="<?php echo new moodle_url('/local/hbon_payment/index.php', array('product_id'=> $product->id)); ?>" class="btn" role="button">MUA NGAY</a>
                </div>
            </div>
        </div>
    <?php } ?>
    </div>

    <h4 class="mt-2"><b>THẺ THÀNH VIÊN</b></h4>
    <div class="row">
    <?php foreach ($ttv_products as $product) { ?>
        <div class="col-sm-3">
            <div class="card product-card shadow-sm mb-3">
                <img src="<?php echo new moodle_url($product->image_url); ?>" class="card-img-top" alt="">
                <div class="p-2">
                    <span class="label label-success"><?php echo $product->category_code; ?></span>
                    <span class="label label-hot">HOT</span> 
                </div>
                <div class="card-body p-2 product-info ">
                    <h5 class="card-title product-name"><?php echo $product->name; ?></h5>
                    <p class="card-text"><?php echo $product->description ?></p>
                </div>
                <div class="card-body p-2 d-flex justify-content-between align-items-center">
                    <span class="product-price"><?php echo number_format($product->price, 0,",","."). ' đ'; ?> </span>
                    <a href="<?php echo new moodle_url('/local/hbon_payment/index.php', array('product_id'=> $product->id)); ?>" class="btn" role="button">MUA NGAY</a>
                </div>
            </div>
        </div>
    <?php } ?>
    </div>
    
</div>

<?php echo $OUTPUT->footer();