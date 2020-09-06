<?php include 'header.php' ?>
<?php include 'menuheader.php' ?>
<style>
	.pay-option a:hover{
		color: #0DAEC8
	}
	.bg-bnn{
		height: 550px;
		width: 100%;
		/*margin-top: 20px;*/
		text-align: center;
		background: url(local/homepage/upload/img/PaymentBG.png);
	}
	.pay-option{
		display: inline-block;
		padding:10px;
		padding-top: 40px;
		padding-bottom: 20px;
		color:#fff;
		width: 282px;
		min-height: 354px;
		margin-top: 100px;
		margin-left: 10px;
		margin-right: 10px;
		border-radius: 10px;
	}
	.pay-option p{
		margin-bottom: 40px;
	}
	.price{
		display: block;
		font-size: 32px;
		font-weight: bold;
		color: #fff;
		text-align: center;
		margin-bottom:40px;
	}
	.btn-pay{
		border:2px solid #fff;
		color:#fff;
		text-align: center;
	}
	.b{
		text-align: center;
	}
	@media only screen and (max-width: 375px) {
		.bg-bn{
			
			height: auto !important;
		}
		.bg-bnn{
			height: auto;
			padding-bottom: 20px;
			background: url(local/homepage/upload/img/PaymentBG_mobile.png) auto !important;

		}
	}
	@media only screen and (max-width: 736px) {
		.bg-bnn{
			
			min-height:1000px !important;
		}
		
	}
</style>
<div  class="bg-bnn">
	<div class="container">
		<div class="pay-option" style="background: #EFA145;">
			<p>Trọn bộ 3 môn Luyện Thi vào lớp 10: Toán, Ngữ Văn, Tiếng Anh</p>
			<span class="price">2.500.000 đ</span>
			<div class="b">
				<a href="https://hocbaionha.com/payment/" class="btn btn-pay">THANH TOÁN</a>
			</div>
		</div>
		<div class="pay-option" style="background: #6B84C2;">
			<p>Trọn bộ chương trình phổ thông THCS trong 180 ngày sử dụng</p>
			<span class="price">395.000 đ</span>
			<div class="b">
				<a href="https://hocbaionha.com/payment/" class="btn btn-pay">THANH TOÁN</a>
			</div>
		</div>
	</div>
</div>
<?php include 'footer.php' ?>