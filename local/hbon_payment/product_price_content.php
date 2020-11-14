<!-- product price -->
<div class="container">
    <div class="block-heading">
        <h2 class="text-info" >
            Thanh toán đơn hàng
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="pricing">
                <table class="table table-condensed">
                    <tr>
                        <td width="20%">Khách hàng</td>
                        <td ><?php echo $USER->firstname . ' ' . $USER->lastname; ?></td>
                    </tr>
                    <hr>
                    <tr>
                        <td width="20%">Số điện thoại</td>
                        <td><input type="text" id="phone" name="phone"
                                                     required class="form-control rounded"
                                                     value="<?php if (isset($phone)) {
                                                         echo $phone;
                                                     }; ?>">
                            <div class="alert alert-danger" role="alert" style="display: none" id="error_phone">
                                Điền số điện thoại trước khi thanh toán
                            </div>
                        </td>

                    </tr>
                    <hr>
                    <tr>
                        <td width="20%">Tên sản phẩm</td>
                        <td class="text-info"> <?php echo $product->name ?></td>
                    </tr>
                    <tr>
                        <td width="20%">Mã sản phẩm</td>
                        <td class="text-info"> <?php echo $product->code ?></td>
                    </tr>
                    <tr>
                        <td idth="20%">Giá tiền</td>
                        <td class="text-info"> <?php echo number_format($product->price,0,",","."). ' đồng' ;  ?></td>
                    </tr>
                    <tr>
                        <td colspan="99" class="text-info">
                            <?php echo $product->description ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 30px;">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="pricing ui-ribbon-container">
                <div class="title">
                    <h3>Thanh toán qua Ngân Lượng </h3>
                </div>
                <div class="x_content">
                    <div class="">
                        <div class="pricing_features">
                            <ul class="list-unstyled text-left">
                                <li><i class="fa fa-check text-success"></i> Được đảm bảo bởi <a href="https://www.nganluong.vn" target="_blank">
                                        <img style = "width:120px;" src="<?php echo new moodle_url('/local/hbon_payment/pix/nganluong-logo-color.svg');?>"/></a></li>
                                <li><i class="fa fa-check text-success"></i> Hỗ trợ nhiều phương thức thanh toán (internet banking, VISA, MASTER, ...)</li>
                                <li><i class="fa fa-check text-success"></i> Quyền truy cập sẽ được kích hoạt ngay sau khi thanh toán thành công</li>
                            </ul>
                        </div>
                        <button type="button"
                                style="width: 50%; float: right;"
                                class="btn btn-primary"
                                id = "charge_money"

                                data-target="#payment_by_nganluong">
                            Thực hiện ngay
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="pricing ui-ribbon-container">
                <div class="title" style="margin-top: 30px;">
                    <h3>Thanh toán chuyển khoản qua ngân hàng </h3>
                </div>
                <div class="x_content" >
                    <div class="">
                        <div class="pricing_features">
                            <p>Thực hiện theo các bước sau:</p>
                            <ul class="list-unstyled text-left">
                                <li><i class="fa fa-check text-success"></i> Chuyển khoản theo thông tin:
                                    <ul >
                                        <li style="padding-left: 15px; margin-left: 18px;">Tên tài khoản: Công ty TNHH Công nghệ và Giáo dục Đông Phương</li>
                                        <li style="padding-left: 15px; margin-left: 18px; ">Số tài khoản: 1005299093</li>
                                        <li style="padding-left: 15px; margin-left: 18px; ">Ngân hàng: TMCP Sài gòn Hà Nội(SHB) - chi nhánh Hà Nội</li>
                                        <li style="padding-left: 15px; margin-left: 18px; ">Số tiền: <?php echo number_format($product->price,0,",","."). ' đồng' ;  ?></li>
                                        <li style="padding-left: 15px; margin-left: 18px; ">
                                            Nội dung chuyển khoản: <strong>Tên khóa học - Tên đăng nhập của tài khoản - Số điện thoại liên hệ</strong>
<!--                                            Nội dung: <em>Thanh toán khoá học: <strong>--><?php //echo $product->name ?><!--</strong>,-->
<!--                                              tên đăng nhập: <strong>--><?php //echo $USER->username ?><!--</strong>, số điện thoại học sinh <strong>--><?php //echo $USER->firstname. ' '.$USER->lastname;  ?><!--</strong>-->
<!--                                            </em>-->
                                        </li>
                                    </ul>
                                </li>
                                <li><i class="fa fa-check text-success"></i> Chụp ảnh phiếu uỷ nhiệm chi gửi email tới info@hocbaionha.com</li>
                                <li><i class="fa fa-check text-success"></i> Mọi thắc mắc liên hệ số hot line: 024.7100.5858</li>
                            </ul>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
<div class="modal fade in" id="payment_by_nganluong" role = "dialog">
<div class="modal-dialog">
    <form action = "<?php echo new moodle_url('/local/hbon_payment/nganluong_checkout.php'); ?>" method ="POST">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Thông tin thanh toán qua Ngân lượng</h4>
            </div>
            <div class="modal-body" style = "padding-left:40px; padding-right:40px;">
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                        <label>Thành viên</label>
                        <input type="text"
                              class="form-control has-feedback-left"
                              name="username"
                              value="<?php echo $USER->lastname. ' '.$USER->firstname;  ?>"
                              disabled = "disabled"
                              id = "username"/>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                        <label>Số tiền</label>
                        <input type="text"
                              class="form-control has-feedback-left"
                              name="money_trans_1"
                              value='<?php echo number_format($product->price,0,",","."). ' đồng' ;  ?>'
                              disabled = "disabled"
                              id = "money_trans_1"/>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                        <label>Mã sản phẩm: </label>
                        <span><strong><?php echo $product->code;?></strong></span>
                    </div>
                    <input type="hidden" id = "product_code" name="product_code" value="<?php echo $product->code;?>"/>
                    <input type="hidden" id = "product_id" name="product_id" value="<?php echo $product->id;?>"/>
                    <input type="hidden" id = "product_name" name="product_name" value="<?php echo $product->name;?>"/>
                    <input type="hidden" id = "money_trans" name="money_trans" value="<?php echo $product->price;?>"/>
                    <input type="hidden" name="user_id" value="<?php echo $USER->id;?>"/>

                <p>Khi bạn bấm "Thanh toán", hệ thống sẽ chuyển bạn tới trang thanh toán Ngân lượng!
                </p>
            </div>
            <div class="modal-footer">
                <div class="btn-group">
                    <button type = "submit" class="btn btn-primary">Thanh toán</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"> Hủy bỏ </button>
                </div>
            </div>
        </div>
    </form>
</div>
</div>
<script>
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
<!-- <div class="modal fade in" id="payment_by_banktranfer" role = "dialog">
    <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h4>Chuyển khoản qua ngân hàng</h4>
              </div>
              <div class="modal-body" style = "padding-left:40px; padding-right:40px;">
                  <div class="pricing_features">
                      <p>Thực hiện theo các bước sau:</p>
                      <ul class="list-unstyled text-left">
                          <li><i class="fa fa-check text-success"></i> Chuyển khoản theo thông tin:
                              <ul>
                                  <li>Tên tài khoản: </li>
                                  <li>Số tài khoản: </li>
                                  <li>Ngân hàng: </li>
                                  <li>Số tiền: <?php echo number_format($product->price,0,",","."). ' đồng' ;  ?></li>
                                  <li>Nội dung: Chuyển khoản thanh toán khoá học: <?php echo $product->name ?>, mã số học sinh: <?php echo $USER->id ?></li>
                              </ul>
                          </li>
                          <li><i class="fa fa-check text-success"></i> Chụp ảnh phiếu uỷ nhiệm chi gửi email tới info@hocbaionha.com</li>
                          <li><i class="fa fa-check text-success"></i> Mọi thắc mặc liên hệ số hot line: 024.7100.5858</li>
                      </ul>
                  </div>
              </div>
              <div class="modal-footer">
                  <div class="btn-group">

                      <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"> Đóng lại </button>
                  </div>
              </div>
          </div>
    </div>
</div> -->
<!-- product price -->
