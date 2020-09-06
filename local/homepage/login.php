<?php 
	// after requiring config.php which is really not needed.
  require(__DIR__ . '/../../config.php');
	// @codingStandardsIgnoreEnd

	// Include lib.php.
  require_once(__DIR__ . '/lib.php');
	// Globals.
	global $PAGE;
	$sessionKey=sesskey();
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
	<title>Đăng ký | Đăng nhập</title>
	<link href="local/homepage/css/bootstrap.min.css" rel="stylesheet" > 
	<link href="local/homepage/css/my-style.css" rel="stylesheet" /> 
	<script src="local/homepage/js/jquery-3.2.1.min.js"></script>
  <script src="local/homepage/js/popper.min.js"></script>
  <script src="local/homepage/js/bootstrap.min.js"></script>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v3.3&appId=2121450044640157&autoLogAppEvents=1"></script>

	<script>
      function statusChangeCallback(response) {
          console.log('statusChangeCallback');
          console.log(response);
          if (response.status === 'connected') {
                  
          testAPI();
        } else {
          document.getElementById('status').innerHTML = 'Please log ' +
            'into this app.';
        }
      }

    function checkLoginState() {
        var php_sessKey = "<?php echo $sessionKey; ?>";
        FB.getLoginStatus(function(response) {
          statusChangeCallback(response);
        });
        console.log(php_sessKey);
        window.location.href = "https://hocbaionha.com/index.php?php_sessKey=" + php_sessKey;

      }

      window.fbAsyncInit = function() {
        FB.init({
          appId      : '1677426979215816',
          cookie     : true,  
                              
          xfbml      : true,  
          version    : 'v2.8' 
        });


        FB.getLoginStatus(function(response) {
          statusChangeCallback(response);
        });

      };

      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "https://connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));

      function testAPI() {
        console.log('Welcome!  Fetching your information.... ');
        FB.api('/me', function(response) {
          console.log('Successful login for: ' + response.name);
          document.getElementById('status').innerHTML =
            'Thanks for logging in, ' + response.name + '!';
        });
      }
	</script>
  <style>
    html, body{
      height:100%;
      background: #A4A4A4;
      font-family: 'Roboto', sans-serif;
      padding-top:30px;
    }
    section{
      background: #fff;
      max-width: 500px;
      margin-left: 50%;
      transform: translateX(-50%);
      padding: 20px;
      /*margin-top:30px;*/
      /*transform:translateY(-50%);*/
    }
    input[type=checkbox] + label {
  display: block;
  margin: 0.2em;
  cursor: pointer;
  padding: 0.2em;
}

input[type=checkbox] {
  display: none;
}

input[type=checkbox] + label:before {
  content: "\2714";
  border: 0.1em solid gray;
  border-radius: 0.2em;
  display: inline-block;
  width: 21px;
  height: 23px;
  padding-left: 0.2em;
  padding-bottom: 0.3em;
  margin-right: 0.2em;
  vertical-align: bottom;
  color: transparent;
  transition: .2s;
}

input[type=checkbox] + label:active:before {
  transform: scale(0);
}

input[type=checkbox]:checked + label:before {
  background-color: #0DAEC8;
  border-color: #0DAEC8;
  color: #fff;
}

input[type=checkbox]:disabled + label:before {
  transform: scale(1);
  border-color: #aaa;
}

input[type=checkbox]:checked:disabled + label:before {
  transform: scale(1);
  background-color: #bfb;
  border-color: #bfb;
}
.mr-20{
  margin-bottom:20px;
}
p{
 font-size: 18px;
 /*font-weight: bold;*/
}

@media only screen and (max-width: 400px) {
  section {
    width: 100%;
    transform: translateX(0);
    margin-left: 0;
  }
  html, body{
    background: #fff;
  }
}



</style>


</head>
<body>
  <section>
    <div class="text-center mr-20">
      <img src="local/homepage/upload/img/logoblue4k-01-01.png" alt="" style="width: 100px; height:auto">
    </div>
    <p class="text-center mr-20">Đăng nhập nhanh với mạng xã hội của bạn!</p>
    <div class="text-center">
    <!-- <button class="btn mr-20" style="width: 100%; height:55px;background: #4267b2;color: #fff">Facebook</button> -->
      <fb:login-button scope="public_profile" class="btn mr-20" max-rows="1" size="large" style="background: #4267b2; border-radius: 5px; color: white; height: 55px; text-align: center;width: 100%;" 
            button-type="continue_with"  onlogin="checkLoginState();">
      </fb:login-button>
      <!-- <div class="fb-login-button btn mr-20" data-width="" data-size="large" data-button-type="login_with" data-auto-logout-link="true" data-use-continue-as="false" style="width: 100%; height:55px;background: #4267b2;color: #fff"></div> -->

      <p class="text-center mr-20">hoặc đăng nhập bằng tài khoản Hocbaionha</p> 
    </div>
    <form action="https://hocbaionha.com/login/index.php" role="form"
          data-toggle="validator" method="post"
          accept-charset="utf-8"
          enctype="multipart/form-data"
          class = "form-horizontal form-label-left input_mask">
      <div class="form-group">
        <input type="text"  id="username" name="username" required="required" 
              class="form-control " placeholder="Ký danh">
      </div>
      <div class="form-group">
        <input  type="password" data-minlength="8" id="password"  name="password"  p
          laceholder="Mật khẩu" class="form-control" >
      </div>
      <!-- <div class="form-group mr-20">
        <input type="checkbox" id="fruit1" name="fruit-1" value="Apple">
        <label for="fruit1">Duy trì đăng nhập</label>
      </div> -->
      <button type="submit" class="btn btn-primary" style="background: #0DAEC8; width: 100%; height:55px">ĐĂNG NHẬP</button>
    </form>
  </section>
</body>
</html>