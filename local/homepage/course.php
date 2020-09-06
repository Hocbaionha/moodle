<?php include 'header.php'; ?>
<?php include 'menuheader.php'; ?>
<style type="text/css" media="screen">
	.url, .url a{
		color: #515151;
	}
	.core{
		background: unset;
	}
  .owl-prev, .owl-next{
    display: none;
  }
  @media (max-width: 900px){
  .b-content .b-content__left {
    position: fixed;
    top: 62px !important;
  }
  .b-content .b-content__left ul li {
    width:20%;
  }
  .nav-link{
    padding:0px;
  }
  .b-content .b-content__left {
    top: 56px !important;
  }
  .mr70{
    margin-top:74px;
  }
}
.selected{
  display:none;
}
.nav-link:hover .normal{
  display:none;
}
.nav-link:hover .selected{
  display:block;
}

</style>
<div class="container">
    <div class="url"><a href="index.php">Trang chủ</a> / <a href="course.php">Khóa học </a> </div>
    <div class="b-content">
        <div class="container">
            <div class="row">
                <nav class="col-sm-3">
                    <div class="b-content__left">
                        <ul id="top-menu" class="nav nav-pills flex-column">
                            <li class="nav-item active">
                                <a class="nav-link" href="#ct_one">
                                    <span class="tx-pc"><i class="fa fa-caret-right" aria-hidden="true"></i> Chương
                                        trình lớp 6</span>
                                    <span class="tx-mb">
                                        <img src="local/homepage/upload/img1/icon_grade6.png" alt="" class="normal">
                                        <img src="local/homepage/upload/img1/icon_grade6_selected.png" alt=""
                                            class="selected">
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#ct_two">
                                    <span class="tx-pc"><i class="fa fa-caret-right" aria-hidden="true"></i> Chương
                                        trình lớp 7</span>
                                    <span class="tx-mb">
                                        <img src="local/homepage/upload/img1/icon_grade7.png" alt="" class="normal">
                                        <img src="local/homepage/upload/img1/icon_grade7_selected.png" alt=""
                                            class="selected">
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#ct_three">
                                    <span class="tx-pc"><i class="fa fa-caret-right" aria-hidden="true"></i> Chương
                                        trình lớp 8</span>
                                    <span class="tx-mb">
                                        <img src="local/homepage/upload/img1/icon_grade8.png" alt="" class="normal">
                                        <img src="local/homepage/upload/img1/icon_grade8_selected.png" alt=""
                                            class="selected">
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#ct_four">
                                    <span class="tx-pc"><i class="fas fa-caret-right"></i> Chương trình lớp 9</span>
                                    <span class="tx-mb">
                                        <img src="local/homepage/upload/img1/icon_grade9.png" alt="" class="normal">
                                        <img src="local/homepage/upload/img1/icon_grade9_selected.png" alt=""
                                            class="selected">
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#ct_five">
                                    <span class="tx-pc"><i class="fas fa-caret-right"></i>Phòng luyện tập Online</span>
                                    <span class="tx-mb">
                                        <img src="local/homepage/upload/img1/icon_gradeLT10.png" alt="" class="normal">
                                        <img src="local/homepage/upload/img1/icon_gradeLT10_selected.png" alt=""
                                            class="selected">
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#ct_six">
                                    <span class="tx-pc"><i class="fa fa-caret-right" aria-hidden="true"></i>Luyện thi
                                        vào lớp 10</span>
                                    <span class="tx-mb">
                                        <img src="local/homepage/upload/img1/icon_gradeLT10.png" alt="" class="normal">
                                        <img src="local/homepage/upload/img1/icon_gradeLT10_selected.png" alt=""
                                            class="selected">
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#ct_seven">
                                    <span class="tx-pc"><i class="fa fa-caret-right" aria-hidden="true"></i> Chương
                                        trình HBON Math</span>
                                    <span class="tx-mb">
                                        <img src="local/homepage/upload/img1/icon_gradeLT10.png" alt="" class="normal">
                                        <img src="local/homepage/upload/img1/icon_gradeLT10_selected.png" alt=""
                                            class="selected">
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="col-sm-9">
                    <div class="b-content__right">
                        <h3 style="text-align: center;">Chương trình miễn phí</h3>
                        <div id="ct_one" class="b-content__ls">
                            <h3>Chương trình lớp 6</h3>
                            <div id="bslider_one" class="owl-carousel b-content__slider">
                                <div class="b-content__items bg-dai6">
                                    <a href="/course/view.php?id=7" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Grade6_Dai.png" />
                                    </a>
                                </div>
                                <div class="b-content__items bg-van6">
                                    <a href="/course/view.php?id=6" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Grade6_Van.png" />
                                    </a>
                                </div>
                                <div class="b-content__items bg-hinh6">
                                    <a href="/course/view.php?id=14" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Grade6_Hinh.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="/course/view.php?id=44" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Grade6_Anh.png" />
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div id="ct_two" class="b-content__ls">
                            <h3>Chương trình lớp 7</h3>
                            <div id="bslider_two" class="owl-carousel b-content__slider">
                                <div class="b-content__items bg-dai7">
                                    <a href="/course/view.php?id=11" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Grade7_Dai.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="/course/view.php?id=5" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Grade7_Van.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="/course/view.php?id=13" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Grade7_Hinh.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="/course/view.php?id=45" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Grade7_Anh.png" />
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div id="ct_three" class="b-content__ls">
                            <h3>Chương trình lớp 8</h3>
                            <div id="bslider_three" class="owl-carousel b-content__slider">
                                <div class="b-content__items bg-dai8">
                                    <a href="/course/view.php?id=8" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Grade8_Dai.png" />
                                    </a>
                                </div>
                                <div class="b-content__items bg-van8">
                                    <a href="/course/view.php?id=10" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Grade8_Van.png" />
                                    </a>
                                </div>
                                <div class="b-content__items bg-hinh8">
                                    <a href="/course/view.php?id=12" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Grade8_Hinh.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="/course/view.php?id=46" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Grade8_Anh.png" />
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div id="ct_four" class="b-content__ls">
                            <h3>Chương trình lớp 9</h3>
                            <div id="bslider_four" class="owl-carousel b-content__slider">
                                <div class="b-content__items">
                                    <a href="/course/view.php?id=15" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Grade9_Dai.png" />
                                    </a>
                                </div>
                                <div class="b-content__items ">
                                    <a href="/course/view.php?id=9" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Grade9_Van.png" />
                                    </a>
                                </div>
                                <div class="b-content__items ">
                                    <a href="/course/view.php?id=16" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Grade9_Hinh.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="/course/view.php?id=47" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Grade9_Anh.png" />
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div id="ct_five" class="b-content__ls">
                            <h3>Phòng luyện tập Online thi vào 10</h3>
                            <div id="bslider_five" class="owl-carousel b-content__slider">
                                <div class="b-content__items ">
                                    <a href="/course/view.php?id=28" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Luyentap_Toan.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="/course/view.php?id=30" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Luyentap_Van.png" />
                                    </a>
                                </div>
                            </div>
                        </div>
                        <h3 style="text-align: center;">Chương trình trả phí</h3>
                        <div id="ct_six" class="b-content__ls">
                            <h3>Luyện thi vào 10</h3>
                            <div id="bslider_six" class="owl-carousel b-content__slider">
                                <div class="b-content__items">
                                    <a href="/course/view.php?id=49" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_LT10_Toan.png" />
                                    </a>
                                </div>
                                <div class="b-content__items ">
                                    <a href="/course/view.php?id=51" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_LT10_Anh.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="/course/view.php?id=50" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_LT10_Van.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="/course/view.php?id=42" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_LT10_Su.png" />
                                    </a>
                                </div>
                                <div class="b-content__items ">
                                    <a href="/course/view.php?id=28" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Luyentap_Toan.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="/course/view.php?id=30" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_Props_Luyentap_Van.png" />
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div id="ct_seven" class="b-content__ls">
                            <h3>Chương trình HBON Math (Sắp khai giảng)</h3>
                            <div id="bslider_seven" class="owl-carousel b-content__slider">
                                <div class="b-content__items">
                                    <a href="#" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_HBONMath_ToanCoBan.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="#" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_HBONMath_LyThuyetSo.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="#" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_HBONMath_TheGioiSo.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="#" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_HBONMath_DaiSo1.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="#" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_HBONMath_DaiSo2.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="#" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_HBONMath_TheGioiDaiSo.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="#" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_HBONMath_HinhHoc1.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="#" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_HBONMath_HinhHoc2.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="#" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_HBONMath_TheGioiHinhHoc.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="#" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_HBONMath_Logic.png" />
                                    </a>
                                </div>
                                <div class="b-content__items">
                                    <a href="#" class="title_slide">
                                        <img src="local/homepage/upload/subject/Subject_HBONMath_XSTK.png" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="local/homepage/css/c.js">
  
</script>


<?php include 'footer.php'; ?>
