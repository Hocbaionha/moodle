<?PHP
require  dirname(dirname(__DIR__)) . '/vendor/autoload.php';

use Google\Cloud\Core\Timestamp;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Firestore;
use function foo\func;

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(__DIR__)) . '/config.php');
require_once($CFG->dirroot . "/mod/book/lib.php");

function local_sm_enrole($uid){
    global $CFG,$DB;
    global $USER;
    global $SESSION;
    if(isset($USER->auth)){
        if($USER->auth!="oauth2"){
            return;
        }
    }
    // $db = new FirestoreClient($CFG->firebase_config);
    //check student
    $factory = (new Factory)->withServiceAccount(dirname(dirname(__DIR__)) . '/firebasekey.json');
    $auth = $factory->createAuth();
    if(!isset($SESSION->fb_token)){
        return;
    }
    $signInResult = $auth->signInWithCustomToken($SESSION->fb_token);
    $firestore = $factory->createFirestore();
    $db = $firestore->database();


    $student_role = $DB->get_record("role",array("shortname"=>"student"))->id;

    $docRef = $db->collection('students')->document($uid);
    $snapshot = $docRef->snapshot();

    if ($snapshot->exists()) {

        $student = $snapshot->data();
        $enddate = time();
        foreach($student["products"] as $productref){
            $snapshot = $productref->snapshot();
            if ($snapshot->exists()) {
                $product = $snapshot->data();
                $endtime = $product["endtime"];
                switch($endtime){
                    case "0":
                        $enddate=$product['enddate']->get()->getTimestamp();
                        break;
                    case "00":
                        $enddate = strtotime("01/01/2100");
                        break;
                    default:
                        $enddate =  strtotime("+$endtime month", time());
                }
                $courses = $product['courses'];
                foreach($courses as $course) {
                    check_enrol($course["shortname"],$USER->id,$student_role,$enddate);
                }
            }
        };
    } else {
        echo "not found".$uid;
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
        $enrol->enrol_user($instance, $userid, $roleid, $timestart, $endtime);
    }
    return true;
}

function local_sm_check_session(){
    global $USER;
    \core\session\manager::apply_concurrent_login_limit($USER->id, session_id());
}

function local_sm_attempt_submitted(mod_quiz\event\attempt_submitted $event) {
    global $CFG, $USER,$SESSION;
    try{
    //get quiz_attempt data
    $quiz_attempt = $event->get_record_snapshot('quiz_attempts', $event->objectid);
    //get quiz data
    $quiz = $event->get_record_snapshot('quiz', $quiz_attempt->quiz);
    //get course data
    $course = $event->get_record_snapshot('course', $event->courseid);
    // firebase.firestore.Timestamp.fromDate(data.birthdate.toDate())
    $send_data = [];
    $send_data['uid'] = $USER->uid;
    $send_data['course'] = 'hbon-'.$course->shortname ;
    $send_data['course_shortname'] = $course->shortname ;
    $send_data['course_id'] = $course->id;
    $send_data['course_name'] = $course->fullname;
    $send_data['quiz_id'] = $quiz->id;
    $send_data['quiz_name'] = $quiz->name;
    $send_data['cmid'] = $quiz->cmid;
    $send_data['quiz_attempt_id'] = $quiz_attempt->id;
    $date = new DateTime();
    $send_data['timestart'] = new Timestamp($date->setTimestamp($quiz_attempt->timestart));
    $send_data['timefinish'] = new Timestamp($date->setTimestamp($quiz_attempt->timefinish));
    $send_data['timemodified'] = new Timestamp($date->setTimestamp($quiz_attempt->timemodified));
    $send_data['sumgrades'] = (int)$quiz_attempt->sumgrades;
    $send_data['grade'] = (int)$quiz_attempt->sumgrades/(int)$quiz->sumgrades*(int)$quiz->grade;
    $send_data['url'] = ((string)$event->get_url())."&cmid=".$quiz->cmid;

        $factory = (new Factory)->withServiceAccount(dirname(dirname(__DIR__)) . '/firebasekey.json');
        $auth = $factory->createAuth();
        if(!isset($SESSION->fb_token)){
            return;
        }
        $signInResult = $auth->signInWithCustomToken($SESSION->fb_token);
        $firestore = $factory->createFirestore();
        $db = $firestore->database();

    $db->collection('students')->document($USER->uid)->collection('grades')->newDocument()->set($send_data);
    return true;
    }catch (Exception $exception){
    throwException($exception);
    }
    }

// event update section
function local_sm_course_section_update(core\event\course_section_updated $event){
    global $CFG, $USER, $DB,$SESSION;
    try{
        $course_info = $event->get_record_snapshot('course',$event->contextinstanceid);
        $all_sections_of_course = $DB->get_records('course_sections',array('course'=>$course_info->id),'id ASC','id,name');
        $activities = get_array_of_activities($course_info->id);
//        print_object($all_sections_of_course);die();
        $result = [];
        foreach ($all_sections_of_course as $item){
            foreach ($activities as $activitie) {
                if ($item->id == $activitie->sectionid) {
                  $item->activities = (array)$activitie;
                }
            }
            $result[]=(array)$item;
        }
        $factory = (new Factory)->withServiceAccount(dirname(dirname(__DIR__)) . '/firebasekey.json');
        $auth = $factory->createAuth();
        if(!isset($SESSION->fb_token)){
            return;
        }
        $signInResult = $auth->signInWithCustomToken($SESSION->fb_token);
        $firestore = $factory->createFirestore();
        $db = $firestore->database();
//        $result = $db->collection('courses')->document('hbon-'.$course_info->shortname);
        $db->collection('courses')->document('hbon-'.$course_info->shortname)->update([
            ['path' => 'topics', 'value' => $result]
        ]);

        return true;
    }catch (Exception $exception){
        print_r($exception);die();
    }
}

function local_sm_course_update(core\event\course_updated $event){
    global $CFG, $USER, $DB,$SESSION;
    try{
        $course_info = $event->get_record_snapshot('course',$event->contextinstanceid);
        $image = "";
        if(isset($course_info->summaryfiles[0])){
            $image = $course_info->summaryfiles[0]->fileurl;
        }
        $all_sections_of_course = $DB->get_records('course_sections',array('course'=>$course_info->id),'id ASC','id,name');
        $newdata = [];
        $newdata["category"] = $course_info->categoryname;
        $newdata["categoryid"] = $course_info->categoryid;
        $newdata["image"] = $image;
        $newdata["name"] = $course_info->fullname;
        $newdata["school"] = [
            "id"=>$CFG->school_firebase_id?$CFG->school_firebase_id:'vFPBJ0wkJoxBY3s8RmVI',
            "lms_url"=>$CFG->wwwroot
        ];
        $newdata["shortname"] = $course_info->shortname;
        $newdata["summary"] = $course_info->summary;
        $newdata["topic"] = $all_sections_of_course;
        $school_deputy_id = $CFG->school_deputy_id?$CFG->school_deputy_id:'hbon';
        $factory = (new Factory)->withServiceAccount(dirname(dirname(__DIR__)) . '/firebasekey.json');
        $auth = $factory->createAuth();
        if(!isset($SESSION->fb_token)){
            return;
        }
        $signInResult = $auth->signInWithCustomToken($SESSION->fb_token);
        $firestore = $factory->createFirestore();
        $db = $firestore->database();
        $db->collection('courses')->document($school_deputy_id.'-'.$course_info->shortname)->set($newdata);
        return true;
    }catch (Exception $exception){
        print_r($exception);die();
    }
}

function local_sm_check_session(){
    //TODO bổ sung popup confirm 5' trước khi destroy session 
    global $USER;
    \core\session\manager::apply_concurrent_login_limit($USER->id, session_id());
}

function local_sm_mod_book_chapter_viewed(mod_book\event\chapter_viewed $event){
    global $CFG, $USER, $DB,$SESSION;
    try{

    }catch (Exception $exception){
        print_r($exception);die();
    }
}

function local_sm_mod_book_module_viewed(mod_book\event\course_module_viewed $event){
    global $CFG, $USER, $DB,$SESSION;
    try{

    }catch (Exception $exception){
        print_r($exception);die();
    }
}

function local_sm_mod_assign_submission_created(mod_assign\event\submission_created $event){
    global $CFG, $USER, $DB,$SESSION;
    try{

    }catch (Exception $exception){
        print_r($exception);die();
    }
}

 function local_sm_mod_feedback_view_feedback(mod_feedback\event\course_module_viewed $event){
     global $CFG, $USER, $DB,$SESSION;
     try{

     }catch (Exception $exception){
         print_r($exception);die();
     }
}

function local_sm_mod_view_forum(mod_forum\event\forum_viewed $event){
    global $CFG, $USER, $DB,$SESSION;
    try{

    }catch (Exception $exception){
        print_r($exception);die();
    }
}

function local_sm_mod_view_forum_discussion(mod_forum\event\discussion_viewed $event){
    global $CFG, $USER, $DB,$SESSION;
    try{

    }catch (Exception $exception){
        print_r($exception);die();
    }
}

function local_sm_mod_wiki_page_viewed(mod_wiki\event\page_viewed $event){
    global $CFG, $USER, $DB,$SESSION;
    try{

    }catch (Exception $exception){
        print_r($exception);die();
    }
}

function local_sm_mod_resource_course_module_viewed(mod_resource\event\course_module_viewed $event){
    global $CFG, $USER, $DB,$SESSION;
    try{

    }catch (Exception $exception){
        print_r($exception);die();
    }
}

function local_sm_mod_resource_course_module_instance_list_viewed(mod_resource\event\course_module_instance_list_viewed $event){
    global $CFG, $USER, $DB,$SESSION;
    try{

    }catch (Exception $exception){
        print_r($exception);die();
    }
}

function local_sm_mod_page_course_module_viewed(mod_page\event\course_module_viewed $event){
    global $CFG, $USER, $DB,$SESSION;
    try{

    }catch (Exception $exception){
        print_r($exception);die();
    }
}

function local_sm_mod_url_course_module_viewed(mod_url\event\course_module_viewed $event){
    global $CFG, $USER, $DB,$SESSION;
    try{

    }catch (Exception $exception){
        print_r($exception);die();
    }
}
