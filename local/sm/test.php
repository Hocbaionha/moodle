<?PHP 
require  dirname(dirname(__DIR__)) . '/vendor/autoload.php';
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Core\Timestamp;

require_once(dirname(dirname(__DIR__)) . '/config.php');
global $USER;
$url =  "https://localhost:5000/change_pass";

$data=array("uid"=>$USER->uid,'password'=>"abcd1234");
function httpPost($url, $data)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); //ssl stuff
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}


$result = httpPost($url, $data);
