<?php
    require(__DIR__ . '/../../config.php');
    global $OUTPUT;
?>
<style>
    .action-menu-trigger{
        margin: 0 auto;
        font-size: 17px;

    }
    .userpicture.defaultuserpic{
        border-radius: 50%;
    }
    @media (max-width: 900px){
        .b-content {
            margin-top:65px;
        }
    }
</style>
<div class="b-header">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand _desktop" href="index.php"> <img src="local/homepage/images/logo_hbon.jpg" /></a>
        <!-- mobile -->
            <a class="navbar-brand _mobile" href="index.php"> <img src="local/homepage/upload/img/logo_mobile.png" /></a>
            <!-- endmobile -->
        <!--  <span style="background-image: url(local/homepage/upload/img/icon_user.png);" class="mobile_menu_user _mobile">
                <ul class="menu-user">
                <li><a href="" class="a"><i class="fas fa-user-circle"></i> Hồ sơ</a></li>
                <li><a href="" class="a"><i class="fa fa-trophy"></i> Thành tích</a></li>
                <li><a href="" class="a"><i class="fa fa-bell"></i> Thông báo</a></li>
                <li><a href="" class="a"><i class="fa fa-cog"></i> Tùy chọn</a></li>
                <li><a href="" class="a"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
            </ul>
            </span> -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto auto">
            <li class="nav-item">
                <a class="nav-link" href="course.php">Khóa học</a>
            </li>
                <li class="nav-item">
                <a class="nav-link" href="video.php">Video</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="about.php">Giới thiệu</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/cua-hang">Cửa hàng</a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" href="team.php">Đội ngũ</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="payInAdvance.php">Thanh toán</a>
            </li> -->
            <!-- <li class="nav-item">
                <a class="nav-link" href="report.php">Kết quả học tập</a>
            </li> -->
            <li class="nav-item">
                <a class="nav-link" href="/luyen-thi-vao-10">Luyện thi vào 10</a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" href="https://hocbaionha.com/local/staticpage/view.php?page=landing_page-hbon-math">HBON Math</a>
            </li> -->
            <li class="nav-item">
                <a class="nav-link" href="/covid-19">Covid 19</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/huong-dan">Hướng dẫn</a>
            </li>
            
            <!-- login -->
            <li class="nav-item login-mobile">
                <!-- <a class="nav-link" href="login.php">Đăng nhập</a> -->
                <?php echo $OUTPUT->user_menu(); ?>
            </li>
            <!-- endlogin -->
            </ul>
            <ul class="navbar-nav menu-desktop float-right"> 
            <li class="nav-item">
                <?php echo $OUTPUT->user_menu(); ?>
                <!-- <a class="nav-link" href="login.php">Đăng nhập</a> -->
            </li> 
            <!-- login -->
            <!--  <span class="name-user" style="">User Name</span>
            <span class="avt-user" style="background-image: url('local/homepage/upload/img/icon_user.png');"></span>
            <ul class="menu-user">
                <li><a href="" class="a"><i class="fas fa-user-circle"></i> Hồ sơ</a></li>
                <li><a href="" class="a"><i class="fa fa-trophy"></i> Thành tích</a></li>
                <li><a href="" class="a"><i class="fa fa-bell"></i> Thông báo</a></li>
                <li><a href="" class="a"><i class="fa fa-cog"></i> Tùy chọn</a></li>
                <li><a href="" class="a"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
            </ul> -->
            <!-- endlogin -->
            </ul> 
        </div>
        </nav>
    </div>
</div><!-- l-header -->  
