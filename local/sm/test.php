<?PHP 
require  dirname(dirname(__DIR__)) . '/vendor/autoload.php';

use Google\Cloud\Core\Timestamp;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Firestore;
use Google\Cloud\Firestore\FieldValue;
require_once(dirname(dirname(__DIR__)) . '/config.php');
// global $USER;
// $url =  "https://localhost:5000/change_pass";

// $data=array("uid"=>$USER->uid,'password'=>"abcd1234");
// function httpPost($url, $data)
// {
//     $curl = curl_init($url);
//     curl_setopt($curl, CURLOPT_POST, true);
//     curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
//     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); //ssl stuff
//     curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
//     $response = curl_exec($curl);
//     curl_close($curl);
//     return $response;
// }


// $result = httpPost($url, $data);

// $key = $CFG->hbon_uid_admin;


// $factory = (new Factory)->withServiceAccount($CFG->dirroot . '/firebasekey.json');

// $auth = $factory->createAuth();
// $signInResult = $auth->signInAsUser($key);
//     $firestore = $factory->createFirestore();
//     $fdb = $firestore->database();

//     $docs = $fdb->collection('students')->where("school_id","==","qna-nguyenduyhieu")->documents();
//     var_dump($docs->size());

global $USER;
print_object($USER);