<!-- product price -->
<div class="container">
    <div class="block-heading">
        <h2 class="text-info text-center">
           Chọn phương thức thanh toán
        </h2>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="pricing">
                <div class="row">
                    <div class="col-md-3">Khách hàng</div>
                    <div class="col-md-6"><?php echo $USER->firstname . ' ' . $USER->lastname; ?></div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3">Số điện thoại</div>
                    <div class="col-md-6"><input type="text" id="phone" name="phone"
                                                 required class="form-control rounded"
                                                 value="<?php if (isset($phone)) {
                                                     echo $phone;
                                                 }; ?>">
                        <div class="alert alert-danger" role="alert" style="display: none" id="error_phone">
                            Điền số điện thoại trước khi thanh toán
                        </div>
                    </div>

                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3">Sản phẩm</div>
                    <div class="col-md-6">
                        <select name="product" class="form-control rounded" id="product">
                            <?php
                            if(!empty($list_product)){
                                foreach ($list_product as $object) {
                                    if ($object->id == $product_id) {
                                        echo '<option value="' . $object->id . '" selected>' . $object->name . '</option>';
                                    } else {
                                        echo '<option value="' . $object->id . '">' . $object->name . '</option>';
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3">Mã sản phẩm</div>
                    <div class="col-md-6" id="label_product_code"><?php echo $product->code ?></div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3">Giá tiền</div>
                    <div class="col-md-6"
                         id="label_product_price"><?php echo number_format($product->price, 0, ",", ".") . ' đồng'; ?></div>
                </div>
                <hr>
                <div class="row">
                    <div  id="label_product_desciption"><?php echo $product->description ?></div>
                </div>
                <input type="hidden" id="list_product" value='<?php echo json_encode($list_product); ?>'>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 30px;">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="pricing ui-ribbon-container">
                <div class="title" style="margin-top: 30px;">
                    <h3>Thanh toán chuyển khoản qua ngân hàng </h3>
                </div>
                <div class="x_content">
                    <div class="">
                        <div class="pricing_features">
                            <p>Thực hiện theo các bước sau:</p>
                            <ul class="list-unstyled text-left">
                                <li><i class="fa fa-check text-success"></i> Chuyển khoản theo thông tin:
                                    <ul>
                                        <li style="padding-left: 15px; margin-left: 18px;">Tên tài khoản: Công ty TNHH
                                            Công nghệ và Giáo dục Đông Phương
                                        </li>
                                        <li style="padding-left: 15px; margin-left: 18px; ">Số tài khoản: 1005299093
                                        </li>
                                        <li style="padding-left: 15px; margin-left: 18px; ">Ngân hàng: TMCP Sài gòn Hà
                                            Nội(SHB) - chi nhánh Hà Nội
                                        </li>
                                        <li style="padding-left: 15px; margin-left: 18px; " id="price">Số
                                            tiền: <?php echo number_format($product->price, 0, ",", ".") . ' đồng'; ?></li>
                                        <li style="padding-left: 15px; margin-left: 18px; ">
                                            Nội dung chuyển khoản: <strong>Tên khóa học - Tên đăng nhập của tài khoản -
                                                Số điện thoại liên hệ</strong>
                                            <!--                                            Nội dung: <em>Thanh toán khoá học: <strong>-->
                                            <?php //echo $product->name ?><!--</strong>,-->
                                            <!--                                              tên đăng nhập: <strong>-->
                                            <?php //echo $USER->username ?><!--</strong>, số điện thoại học sinh <strong>-->
                                            <?php //echo $USER->firstname. ' '.$USER->lastname;  ?><!--</strong>-->
                                            <!--                                            </em>-->
                                        </li>
                                        <li style="padding-left: 15px; margin-left: 18px;">
                                            <?php if($product->id == 8) echo 'Ví dụ: <span  class="pay_syntax">Toán Văn Anh 12 tháng – thutrang2020 – 0989.xxx.xxx</span>';?>
                                            <?php if($product->id == 10) echo 'Ví dụ: <span  class="pay_syntax">Toán Văn 12 tháng – thutrang2020 – 0989.xxx.xxx</span>';?>
                                            <?php if($product->id == 12) echo 'Ví dụ: <span  class="pay_syntax">Tiếng Anh 12 tháng  – thutrang2020 – 0989.xxx.xxx</span>';?>
                                            <?php if($product->id == 13) echo 'Ví dụ: <span  class="pay_syntax">Toán Văn Anh 6 tháng – thutrang2020 – 0989.xxx.xxx </span>';?>
                                            <?php if($product->id == 14) echo 'Ví dụ: <span  class="pay_syntax">Toán Văn 6 tháng – thutrang2020 – 0989.xxx.xxx</span>';?>
                                        </li>
                                    </ul>
                                </li>
                                <li><i class="fa fa-check text-success"></i> Chụp ảnh hóa đơn gửi đến email: <a href="mailto:info@hocbaionha.com">info@hocbaionha.com </a> hoặc chat trực tiếp trên trang web.
                                </li>
                                <li><i class="fa fa-check text-success"></i> Mọi thắc mắc liên hệ số hotline: 024 7100 5858 hoặc chat trực tiếp trên trang web.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>


            </div>
        </div>

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="pricing ui-ribbon-container">
                <div class="title">
                    <h3>Thanh toán qua Ngân Lượng </h3>
                </div>
                <div class="x_content">
                    <div class="">
                        <div class="pricing_features">
                            <ul class="list-unstyled text-left">
                                <li><i class="fa fa-check text-success"></i> Được đảm bảo bởi <a
                                            href="https://www.nganluong.vn" target="_blank">
                                        <img style="width:120px;"
                                             src="<?php echo new moodle_url('/local/hbon_payment/pix/nganluong-logo-color.svg'); ?>"/></a>
                                </li>
                                <li><i class="fa fa-check text-success"></i> Hỗ trợ nhiều phương thức thanh toán
                                    (internet banking, VISA, MASTER, ...)
                                </li>
                                <li><i class="fa fa-check text-success"></i> Quyền truy cập sẽ được kích hoạt ngay sau
                                    khi thanh toán thành công
                                </li>
                            </ul>
                        </div>
                        <button type="button"
                                style="width: 50%; float: right;"
                                class="btn btn-primary rounded"
                                id="charge_money"

                                data-target="#payment_by_nganluong">
                            Thực hiện ngay
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade in" id="payment_by_nganluong" role="dialog">
    <div class="modal-dialog">
        <form action="<?php echo new moodle_url('/local/hbon_payment/nganluong_checkout.php'); ?>" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Thông tin thanh toán qua Ngân lượng</h4>
                </div>
                <div class="modal-body" style="padding-left:40px; padding-right:40px;">
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                        <label>Thành viên</label>
                        <input type="text"
                               class="form-control has-feedback-left"
                               name="username"
                               value="<?php echo $USER->lastname . ' ' . $USER->firstname; ?>"
                               disabled="disabled"
                               id="username"/>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                        <label>Số tiền</label>
                        <input type="text"
                               class="form-control has-feedback-left"
                               name="money_trans_1"
                               value='<?php echo number_format($product->price, 0, ",", ".") . ' đồng'; ?>'
                               disabled="disabled"
                               id="money_trans_1"/>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                        <label>Mã sản phẩm: </label>
                        <span><strong id="modal_code"><?php echo $product->code; ?></strong></span>
                    </div>
                    <input type="hidden" id="product_code" name="product_code" value="<?php echo $product->code; ?>"/>
                    <input type="hidden" id="product_id" name="product_id" value="<?php echo $product->id; ?>"/>
                    <input type="hidden" id="product_name" name="product_name" value="<?php echo $product->name; ?>"/>
                    <input type="hidden" id="money_trans" name="money_trans" value="<?php echo $product->price; ?>"/>
                    <input type="hidden" name="user_id" value="<?php echo $USER->id; ?>"/>

                    <p>Khi bạn bấm "Thanh toán", hệ thống sẽ chuyển bạn tới trang thanh toán Ngân lượng!
                    </p>
                </div>
                <div class="modal-footer">
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">Thanh toán</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"> Hủy bỏ</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $('#product').on('change', function () {
        var list_product = $('#list_product').val();
        var obj = JSON.parse(list_product);
        var product = String(this.value);
        // obj[product].
        console.log(obj[product]);
        $('#label_product_code').html(obj[product].code);
        $('#label_product_price').html(parseInt(obj[product].price) + " đồng");
        $('#money_trans_1').val(parseInt(obj[product].price) + " đồng");
        $('#label_product_desciption').html(obj[product].description);
        $('#product_code').val(obj[product].code);
        $('#product_id').val(obj[product].id);
        $('#product_name').val(obj[product].name);
        $('#money_trans').val(obj[product].price);
        $('#modal_code').html(obj[product].code);
        $('#price').html('Số tiền: '+parseInt(obj[product].price)+' đồng');
        if(product == 8){
            $('.pay_syntax').html('Toán Văn Anh 12 tháng – thutrang2020 – 0989.xxx.xxx');
        }if(product == 10){
            $('.pay_syntax').html('Toán Văn 12 tháng – thutrang2020 – 0989.xxx.xxx');
        }if(product == 12){
            $('.pay_syntax').html('Tiếng Anh 12 tháng  – thutrang2020 – 0989.xxx.xxx');
        }if(product == 13){
            $('.pay_syntax').html('Toán Văn Anh 6 tháng – thutrang2020 – 0989.xxx.xxx');
        }if(product == 14){
            $('.pay_syntax').html('Toán Văn 6 tháng – thutrang2020 – 0989.xxx.xxx');
        }
       console.log(product);
    });
    $('#charge_money').on('click', function (event) {
        event.preventDefault();
        var check = $('#phone').val();
        console.log(check);
        if (typeof check == "undefined" || check == null || check == '') {
            $('#phone').addClass('is-invalid ');
            document.getElementById("error_phone").style.display = "block";
        } else {
           phonenumber(String(check),"submit");
        }
    });
    $("#phone").keypress(function () {
        $(this).removeClass('is-invalid');
        document.getElementById("error_phone").style.display = "none";
    });
    $('#phone').on('input', function() {
        phonenumber(String($(this).val()));
    });

    function phonenumber(inputtxt,type=null) {
        var phoneno = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
        if(inputtxt.match(phoneno)) {
            if(type === "submit"){
                $.ajax({
                    type: "post",
                    url: "/local/hbon_payment/update_phone.php",
                    dataType:"json",
                    data: {phone:inputtxt},
                    success: function (response) {
                        if(response.status === "success") {
                            $('#payment_by_nganluong').modal('show');
                        } else if(response.status === "error") {
                            $('#phone').addClass('is-invalid');
                            document.getElementById("error_phone").style.display = "block";
                            document.getElementById("error_phone").innerHTML = "Định dạng không khớp";
                        }
                    }
                });
            }else{
                $('#phone').addClass('is-valid');
            }

        }
        else {
            // if(type === "submit"){
                $('#phone').addClass('is-invalid');
                document.getElementById("error_phone").style.display = "block";
                document.getElementById("error_phone").innerHTML = "Định dạng không khớp";
            // }
    }
    }
</script>
