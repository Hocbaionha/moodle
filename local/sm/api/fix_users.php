<?PHP
//Đồng bộ user theo từng trường lên firebase 
require_once('../../../config.php');
require_once($CFG->dirroot . "/lib/externallib.php");
require_once($CFG->dirroot . "/vendor/autoload.php");

use Google\Cloud\Core\Timestamp;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Firestore;
use Google\Cloud\Firestore\FieldValue;
defined('MOODLE_INTERNAL') || die();

require_login();


//set timeout: 5 minute
set_time_limit(300);
$startwith = optional_param('startwith', "", PARAM_TEXT);//as
$schoolid=$startwith;
if($startwith!="start"){
    echo "missing access";
    die;    
}

ob_end_clean();
header("Connection: close");
ignore_user_abort(); // optional
ob_start();
echo ('Starting sync user moodle to firebase');
$size = ob_get_length();
header("Content-Length: $size");
ob_end_flush(); // Strange behaviour, will not work
flush();            // Unless both are called !
session_write_close(); // Added a line suggested in the comment
// Do processing here
sleep(30);


$context = context_system::instance();
$PAGE->set_context($context);

$PAGE->set_heading("Sync user to firebase");
$url = new moodle_url('/local/sm/api/fix_users.php');
$PAGE->set_url($url);
echo $OUTPUT->header();
$cohorts = array();

$factory = (new Factory)->withServiceAccount($CFG->dirroot . '/firebasekey.json');

$auth = $factory->createAuth();
$key = $CFG->hbon_uid_admin;
$signInResult = $auth->signInAsUser($key);
$firestore = $factory->createFirestore();
$fdb = $firestore->database();


    $mdlusers = $DB->get_records("user",array("deleted"=>0));

    try {
        $codeField = $DB->get_record("user_info_field",array("shortname"=>"student_code"))->id;
        $uidField = $DB->get_record("user_info_field",array("shortname"=>"uid"))->id;
        foreach($mdlusers as $mdluser){
            
            $userid=$mdluser->id;
            $username=$mdluser->username;
            $userArr = explode("-",$username);
            $uname = array_pop($userArr);  
            if($uname=="admin"||$uname=="guest") {
                continue;
            }
            
            
            $docRefUser = $fdb->collection('users');
            $query = $docRefUser->where('email', '==', $mdluser->email);
            $role="student";
            if (strpos("-gv", $mdluser->email) !== false || str_starts_with($mdluser->email,"gv")) {
                $role="teacher";
            }
            $documents = $query->documents();
            if($documents->size()>0){
                foreach ($documents as $document) {
                    if ($document->exists()) {
                        $uid=$document->id();
                        serviceErrorLog($mdluser->email."-checking:".$uid);

                        try {
                             $user = $auth->getUser($uid);
                            serviceErrorLog($mdluser->id."-verfied:".$uid);
                        } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
                            serviceErrorLog($mdluser->id."not-verfied:".$uid);
                            $fdb->collection('users')->document($uid)->delete();        
                            serviceErrorLog("===".$mdluser->id."-deleted:".$uid);
                            
                        }
                    }
                }
            } else {
                //not found old user
                $user = array("moodleUserId"=>$userid,"email"=>$mdluser->email,"firstname"=>$mdluser->firstname,"lastname"=>$mdluser->lastname,"username"=>$mdluser->username,"status"=>0,"role"=>$role,"roles"=>array($role));
                try {
                    if(isValidEmail($mdluser->email)){
                        serviceErrorLog($mdluser->email."-valid");
                        $providers = $auth->getUserByEmail($user['email']);
                        $uid=$providers->uid;
                        $fdb->collection('users')->document($uid)->set($user);
                        updateStudentData($user['moodleUserId'],$uid);
                        serviceErrorLog("====>set ".$mdluser->id."-done:".$uid);
                    }
                } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
                    //do nothing if not found auth
                }
            }
        }
        echo " done<br/>";
    } catch (Exception $exception) {
        serviceErrorLog("error:".json_encode($exception->getTrace()));

    }    

function isValidEmail($email){ 
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function updateStudentData($moodleUserId,$uid){
    global $uidField,$DB;
    $check = $DB->get_record("user_info_data",array("userid"=>$moodleUserId,"fieldid"=>$uidField));
    $sql = "update mdl_user_info_data set data=? where userid=? and fieldid=?";
    if(!$check){
        $DB->insert_record('user_info_data', array('userid' => $moodleUserId,
                    'fieldid' => $uidField, 'data' => $uid));
    } else {
        $DB->execute($sql,array("data"=>$uid,"userid"=>$moodleUserId,"fieldid"=>$uidField));
    }

}


