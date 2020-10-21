<?PHP
require_once('../../../config.php');
require_once($CFG->dirroot . "/lib/externallib.php");
require_once($CFG->dirroot . "/vendor/autoload.php");

use Google\Cloud\Core\Timestamp;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Firestore;
defined('MOODLE_INTERNAL') || die();

require_login();


$context = context_system::instance();
$PAGE->set_context($context);

$PAGE->set_heading("Sync user to firebase");
$url = new moodle_url('/local/sm/remove_cohort.php');
$PAGE->set_url($url);
echo $OUTPUT->header();
$cohorts = array();

$sql = "SELECT concat(c.idnumber,u.id),c.idnumber,u.id, u.username, u.currentlogin FROM mdl_cohort c JOIN mdl_cohort_members cm ON c.id = cm.cohortid  JOIN mdl_user u ON cm.userid = u.id  WHERE (c.idnumber like '%hbon%' or c.idnumber like '%LTV10%')";
$cusers = $DB->get_records_sql($sql);
$factory = (new Factory)->withServiceAccount($CFG->dirroot . '/firebasekey.json');

$auth = $factory->createAuth();
if(!isset($SESSION->fb_token)){
    return;
}
$signInResult = $auth->signInWithCustomToken($SESSION->fb_token);
$firestore = $factory->createFirestore();
$fdb = $firestore->database();

$new_schools = array();
$schools = array();
$last_student = $last_teacher = 0;

foreach($cusers as $cuser){
    $productid=$cuser->idnumber;
    $userid=$cuser->id;
    $username=$cuser->username;
    $userArr = explode("-",$username);
    $uname = array_pop($userArr);  
    
    if($uname=="bgh"||$uname=="admin") {
            continue;
    }
    
    if(!empty($userArr)){
        $schoolid = implode("-",$userArr);
        //search school, create if not existed
        $docRef = $fdb->collection('schools')->document($schoolid);
        $snapshot = $docRef->snapshot();
        if (!$snapshot->exists()) {
        //     setUserClass()
            $fdb->collection('schools')->document($schoolid)->set(array("id"=>$schoolid,"name"=>$schoolid,"address"=>"","email"=>"","lms_url"=>"","total-student"=>0,"total_teacher"=>0));
        } else {
            continue;            
        }

        
    } else {
    }
}


