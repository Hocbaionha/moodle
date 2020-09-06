<?php include 'header.php'; ?>
<?php include 'menuheader.php'; ?>

<style type="text/css" media="screen">
   .url, .url a{
   color: #515151;
   }
   .core{
   background: unset;
   }
   .min{
     min-height:120px;
   }
   .owl-prev, .owl-next{
   display: none;
   }
   a.nav-link{
     text-align:left;
   }
   @media (max-width: 575px){
     .b-content .b-content__left {
       position: fixed;
       top: 57px !important;
     }
   }
   .b-content .b-content__items{
    box-shadow: none !important;
   }

   #top-menu ul a.nav-link{
    text-align:left !important;
   }
   #bslider_video div[class=""]{
    /*padding-left: 50%;*/
    margin-left:50%;
    transform: translateX(-50%);
    display: inline-block;
   }

   /* #bslider_team5 div[class=""]{
    bottom:-25px;
  } */

  #bslider_team2 div[class=""], #bslider_team4 div[class=""], #bslider_team5 div[class=""], #bslider_team7 div[class=""]{
    bottom:-25px;
   }
   .selected{
     display:none;
   }
   .nav-item:hover .normal{
     display:none
   }
   .nav-item:hover .selected{
     display:block;
   }
   @media (min-width: 576px){
      .b-content .b-content__left-mb {
        top: 56px;
      }
      .b-content .b-content__left-mb ul li:hover a{
        padding-left: 30px;
      }
      .mr70{
        margin-top:75px;
      }
  }

</style>
<div class="container">
   <div class="url"><a href="index.php">Trang chủ</a> / <a href="video.php">Video </a> </div>
   <div class="b-content">
      <div class="container">
         <div class="row">
         <nav class="col-sm-3">
               <div class="b-content__left">
                  <ul id="top-menu" class="nav nav-pills flex-column">
                     <li class="nav-item active">
                        <a class="nav-link" href="#bslider_team2">
                        <span class="tx-pc"><i class="fa fa-caret-right" aria-hidden="true"></i>Video nổi bật</span> 
                        <span class="tx-mb">
                          <img src="local/homepage/upload/img1/icon_video_hot.png" alt="" class="normal">
                          <img src="local/homepage/upload/img1/icon_video_hot_selected.png" alt="" class="selected">
                        </span> 
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" href="#bslider_team7">
                        <span class="tx-pc"><i class="fas fa-caret-right"></i>Video mới nhất</span> 
                        <span class="tx-mb">
                          <img src="local/homepage/upload/img1/icon_video_new.png" alt="" class="normal">
                          <img src="local/homepage/upload/img1/icon_video_new_selected.png" alt="" class="selected">
                        </span>  
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" href="#bslider_team5">
                        <span class="tx-pc"><i class="fa fa-caret-right" aria-hidden="true"></i>Toán</span>
                        <span class="tx-mb">
                          <img src="local/homepage/upload/img1/icon_video_math.png" alt="" class="normal">
                          <img src="local/homepage/upload/img1/icon_video_math_selected.png" alt="" class="selected">
                        </span> 
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" href="#bslider_team4">
                        <span class="tx-pc"><i class="fa fa-caret-right" aria-hidden="true"></i> Ngữ văn</span> 
                        <span class="tx-mb">
                          <img src="local/homepage/upload/img1/icon_video_literary.png" alt="" class="normal">
                          <img src="local/homepage/upload/img1/icon_video_literary_selected.png" alt="" class="selected">
                        </span>  
                        </a>
                     </li>
                    
                  </ul>
               </div>
            </nav>
            <div class="col-sm-9">
            <div class="b-content__right _video" style="margin-top:0px">
                <h3 style="margin-bottom:0px">Video nổi bật</h3>
                   <div id="bslider_team2" class="owl-carousel b-content__slider slide-multi"> 
                    <div class="item"  >
                        <div class="b-thumbs__img ">
                          <iframe src="https://player.vimeo.com/video/336293846" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Tìm điều kiện tham số để phương trình bậc 2 có nghiệm thỏa mãn điều kiện cho trước</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 14/05/2019</span>
                          </div>
                        </div>
                    </div> 
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/336294949" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Tính diện tích của hình bình hành và hình thang</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 21/03/2019</span>
                          </div>
                        </div>
                    </div> 
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/336292961" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Giải và biện luận số nghiệm của phương trình bậc 2</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 11/05/2019</span>
                          </div>
                        </div>
                    </div> 
                    <div class="item">
                        <div class="b-thumbs__img">
                         <iframe src="https://player.vimeo.com/video/336290857" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Giải bài toán bằng cách lập phương trình (Bài toán có nội dung hình học)</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i>***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 30/04/2019</span>
                          </div>
                        </div>
                    </div> 
                  </div>
               </div>

               <div class="b-content__right _video" style="margin-top:50px">
                  <h3 style="padding-top:25px">Video mới nhất</h3>
                   <div id="bslider_team7" class="owl-carousel b-content__slider slide-multi"> 
                     <div class="item"  >
                        <div class="b-thumbs__img ">
                           <iframe src="https://player.vimeo.com/video/336288461" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                           <p  class="text-left min mrtop-10" >Cảm nhận nhân vật qua chi tiết truyện</p>
                           <div class="_date">
                              <span><i class="fa fa-folder-open"></i> ***</span>
                              <span class="float-right"><i class="fa fa-calendar"></i> 04/05/2019</span>
                           </div>
                        </div>
                     </div> 
                     <div class="item">
                        <div class="b-thumbs__img">
                           <iframe src="https://player.vimeo.com/video/336287061" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                           <p  class="text-left min mrtop-10" >Kỹ năng đọc - hiểu nhân vật</p>
                           <div class="_date">
                              <span><i class="fa fa-folder-open"></i> L***</span>
                              <span class="float-right"><i class="fa fa-calendar"></i> 30/04/2019</span>
                           </div>
                        </div>
                     </div> 
                     <div class="item">
                        <div class="b-thumbs__img">
                           <iframe src="https://player.vimeo.com/video/336293846" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                           <p  class="text-left min mrtop-10" >Tìm điều kiện tham số để phương trình bậc 2 có nghiệm thỏa mãn điều kiện cho trước</p>
                           <div class="_date">
                              <span><i class="fa fa-folder-open"></i> ***</span>
                              <span class="float-right"><i class="fa fa-calendar"></i> 14/05/2019</span>
                           </div>
                        </div>
                     </div> 
                     <div class="item">
                        <div class="b-thumbs__img">
                           <iframe src="https://player.vimeo.com/video/336292961" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                           <p  class="text-left min mrtop-10" >Giải và biện luận số nghiệm của phương trình bậc 2</p>
                           <div class="_date">
                              <span><i class="fa fa-folder-open"></i> ***</span>
                              <span class="float-right"><i class="fa fa-calendar"></i> 11/05/2019</span>
                           </div>
                        </div>
                     </div> 
                  </div>
               </div>

               <div class="b-content__right _video">
                <h3 style="padding-top:25px">Toán</h3>
                   <div id="bslider_team5" class="owl-carousel b-content__slider slide-multi"> 
                    <div class="item"  >
                        <div class="b-thumbs__img ">
                          <iframe src="https://player.vimeo.com/video/330917798" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Ứng dụng của số pi để tính diện tích hình tròn</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> Lớp 9</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 13/02/2019</span>
                          </div>
                        </div>
                    </div> 
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/330918149" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Tìm giá trị biến để PT có giá trị nguyên</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 21/03/2019</span>
                          </div>
                        </div>
                    </div> 
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/330917928" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Rút gọn biểu thức chứa căn</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> Lớp 9</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 02/03/2019</span>
                          </div>
                        </div>
                    </div> 
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/330917798" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >So sánh giá trị biểu thức với một số hoặc một biểu thức khác</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 14/03/2019</span>
                          </div>
                        </div>
                    </div> 
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/330918149" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Tìm các giá trị của biến để biểu thức có giá trị nguyên (phần 1)</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 20/03/2019</span>
                          </div>
                        </div>
                    </div> 
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/330918211" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Tìm các giá trị của biến để biểu thức có giá trị nguyên (phần 2)</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 02/04/2019</span>
                          </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/336286275" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Giải bài toán bằng cách lập phương trình (Bài toán chuyển động)</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 25/04/2019</span>
                          </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/336290857" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Giải bài toán bằng cách lập phương trình (Bài toán có nội dung hình học)</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 30/04/2019</span>
                          </div>
                        </div>
                    </div> 
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/336291808" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Giải bài toán bằng cách lập phương trình hoặc hệ phương trình (Bài toán năng suất)</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 04/05/2019</span>
                          </div>
                        </div>
                    </div> 
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/336292961" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Giải và biện luận số nghiệm của phương trình bậc 2</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 11/05/2019</span>
                          </div>
                        </div>
                    </div> 
                  <!-- </div> -->
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/336293846" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Tìm điều kiện tham số để phương trình bậc 2 có nghiệm thỏa mãn điều kiện cho trước</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 14/05/2019</span>
                          </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/336294384" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Tính chất phân phối của phép nhân đối với phép cộng</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 14/05/2019</span>
                          </div>
                        </div>
                    </div> 
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/336294949" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Tính diện tích của hình bình hành và hình thang</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 14/05/2019</span>
                          </div>
                        </div>
                    </div> 
                  </div>
               </div>
               
                <div class="b-content__right _video" style="margin-top:50px">
                <h3 style="padding-top:25px">Ngữ văn</h3>
                   <div id="bslider_team4" class="owl-carousel b-content__slider slide-multi"> 
                    <div class="item"  >
                        <div class="b-thumbs__img ">
                          <iframe src="https://player.vimeo.com/video/330917126" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Cách làm đề "Nghị luận xã hội"</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 03/02/2019</span>
                          </div>
                        </div>
                    </div> 
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/330917269" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Nghị luận xã hội về một sự việc, hiện tượng đời sống</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 14/02/2019</span>
                          </div>
                        </div>
                    </div> 
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/330917380" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Nghị luận xã hội về một vấn đề xã hội đặt ra trong tác phẩm văn học</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 28/02/2019</span>
                          </div>
                        </div>
                    </div> 
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/336266617" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Kỹ năng viết đoạn văn nghị luận xã hội</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 11/03/2019</span>
                          </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/330917535" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Kỹ năng viết đoạn văn nghị luận văn học</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 01/04/2019</span>
                          </div>
                        </div>
                    </div> 
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/336268045" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Ý nghĩa nhan đề tác phẩm</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 02/04/2019</span>
                          </div>
                        </div>
                    </div> 
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/336285336" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Tình huống truyện</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 21/04/2019</span>
                          </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/336287061" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Kỹ năng đọc - hiểu nhân vật</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 30/04/2019</span>
                          </div>
                        </div>
                    </div> 
                    <div class="item">
                        <div class="b-thumbs__img">
                          <iframe src="https://player.vimeo.com/video/336288461" width="200" height="auto" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                          <p  class="text-left min mrtop-10" >Cảm nhận nhân vật qua chi tiết truyện</p>
                          <div class="_date">
                            <span><i class="fa fa-folder-open"></i> ***</span>
                            <span class="float-right"><i class="fa fa-calendar"></i> 04/05/2019</span>
                          </div>
                        </div>
                    </div> 
                  </div>
                  <div style="padding-bottom:40px;"></div>
               </div>
               
            </div>
         </div>
      </div>
   </div>
</div>
<script src="css/c.js"></script>
<script src="js/video.js"></script>
<?php include 'footer.php'; ?>