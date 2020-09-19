<?PHP 
require  dirname(dirname(__DIR__)) . '/vendor/autoload.php';
use Google\Cloud\Firestore\FirestoreClient;

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(__DIR__)) . '/config.php');

function local_sm_enrole(core\event\user_loggedin $event){
    global $CFG,$DB;
    global $USER;

    if($USER->auth!="oauth2"){
        return;
    }
    $config =  array("apiKey" => "AIzaSyAb_jc2posJg2ZX4ZX3iWchsKjwZrMczAY",
        "authDomain" => "hbon-dev.firebaseapp.com",
        "databaseURL" => "https://hbon-dev.firebaseio.com",
        "projectId" => "hbon-dev",
        "storageBucket" => "hbon-dev.appspot.com",
        "messagingSenderId" => "288259119229",
        "appId" => "1:288259119229:web:e6eeccb58a8b9565c27914",
        "measurementId" => "G-CQSKZV9B64");
    $uid = $USER->uid;
    $db = new FirestoreClient($config);
    //check student

    $student_role = $DB->get_record("role",array("shortname"=>"student"))->id;
    $docRef = $db->collection('students')->document($uid);
    $snapshot = $docRef->snapshot();
    if ($snapshot->exists()) {
        $data = $snapshot->data();
        $products =  $data['products'];
        //each product (thẻ thành viên)
        foreach($products as $product){
            $courses = $product['courses'];
            $endtime=$product['end_time']->get()->getTimestamp();
            
            foreach($courses as $course) {
                check_enrol($course,$USER->id,$student_role,$endtime);
            }
        }
    }
}

function check_enrol($shortname, $userid, $roleid,$endtime, $enrolmethod = 'manual') {
    global $DB;
    $timestart=time();
    $user = $DB->get_record('user', array('id' => $userid, 'deleted' => 0), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('shortname' => $shortname), '*', MUST_EXIST);
    $context = context_course::instance($course->id);
    if (!is_enrolled($context, $user)) {
        $enrol = enrol_get_plugin($enrolmethod);
        if ($enrol === null) {
            return false;
        }
        $instances = enrol_get_instances($course->id, true);
        $manualinstance = null;
        foreach ($instances as $instance) {
            if ($instance->name == $enrolmethod) {
                $manualinstance = $instance;
                break;
            }
        }
        if ($manualinstance !== null) {
            $instanceid = $enrol->add_default_instance($course);
            if ($instanceid === null) {
                $instanceid = $enrol->add_instance($course);
            }
            $instance = $DB->get_record('enrol', array('id' => $instanceid));
        }
        $enrol->enrol_user($instance, $userid, $roleid, $timestart, $timeend);
    }
    return true;
}