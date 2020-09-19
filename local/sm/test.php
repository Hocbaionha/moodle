<?PHP 

require_once(dirname(dirname(__DIR__)) . '/config.php');
// echo $fullpath = $CFG->dataroot . '/school/';
// echo "<script>alert('aaaaaaaaaaaaaaaaaa');</script>";
$firebaseConfig = '{
    "apiKey": "AIzaSyAb_jc2posJg2ZX4ZX3iWchsKjwZrMczAY",
    "authDomain": "hbon-dev.firebaseapp.com",
    "databaseURL": "https://hbon-dev.firebaseio.com",
    "projectId": "hbon-dev",
    "storageBucket": "hbon-dev.appspot.com",
    "messagingSenderId": "288259119229",
    "appId": "1:288259119229:web:e6eeccb58a8b9565c27914",
    "measurementId": "G-CQSKZV9B64"
  }';
// echo $firebaseConfig;
print_r( json_decode($firebaseConfig, TRUE));
 