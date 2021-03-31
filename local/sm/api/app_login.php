<?PHP
require_once('../../../config.php');
require_once($CFG->dirroot . "/lib/externallib.php");
require_once($CFG->dirroot . "/vendor/autoload.php");

use Google\Cloud\Core\Timestamp;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Firestore;
use Google\Cloud\Firestore\FieldValue;
defined('MOODLE_INTERNAL') || die();

//anhnn login from app
$idtokenfb = optional_param('idtokenfb', "", PARAM_TEXT);
$redirect_link = optional_param('callback', "", PARAM_TEXT);
if($idtokenfb!=""){
 
    login_from_app($idtokenfb);

}

function login_from_app($idtokenfb=""){
	global $PAGE;
    //anhnn login from app
    $issuerid=1;// sso oauth2
    if($idtokenfb!=""){
        if(empty($USER->id) || isguestuser()){
//var_dump($PAGE->url->get_query_string());die;
            $factory = (new Factory)->withServiceAccount(dirname(dirname(__DIR__)) . '/firebasekey.json');
            $auth = $factory->createAuth();
            if (!isset($idtokenfb)) {
                return;
            }

            $signInResult = $auth->verifyIdToken($idtokenfb);
            $uid = $signInResult->getClaim('sub');
            $firestore = $factory->createFirestore();
            $fdb = $firestore->database();
            $stuSnapshot = $fdb->collection("users")->document($uid)->snapshot();
            if ($stuSnapshot->exists()) {
                $fbinfo = $stuSnapshot->data();
            }
            //do login TODO anhnn\
            global $SESSION;
            $actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	    $query_string = parse_url($actual_link);
            $scheme=$query_string['scheme'];
            $host=$query_string['host'];
            $path=$query_string['path'];
            $query=$query_string['query'];
	    parse_str($query, $array_params);
	    
            $current = new moodle_url($actual_link);
            // var_dump($current);die;
            $issuer = new \core\oauth2\issuer($issuerid);
            $client = \core\oauth2\api::get_user_oauth_client($issuer, $current);
	    if ($client) {
//var_dump($client);die;
		    if($fbinfo["username"]==null){
			    $fbinfo["username"]=$fbinfo["email"];
		}
                $info =  ["email"=> $fbinfo['email'], "firstname"=> $fbinfo['firstname'], "lastname"=> $fbinfo["lastname"], "username"=> $fbinfo["username"], "uid"=>  $uid ];
                
		$auth = new \auth_oauth2\auth();
                $auth->complete_login($client, $redirect_link,$info); 
                redirect($redirect_link);
	    } else {
		    echo "error here;";die;
                throw new moodle_exception('Could not get an OAuth client.');
            }
        } 
    }
}
