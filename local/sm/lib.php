<?PHP
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

use Google\Cloud\Core\Timestamp;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Firestore;

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(__DIR__)) . '/config.php');
require_once($CFG->dirroot . "/mod/book/lib.php");
require_once($CFG->dirroot . "/cohort/lib.php");
require_once($CFG->dirroot . "/lib/externallib.php");
require_once($CFG->dirroot . '/group/lib.php');
$ccache = array();
function local_sm_enrole($uid)
{
    global $CFG, $DB;
    global $USER;
    global $SESSION;
    $fb_token = $CFG->hbon_uid_admin;
    if (isset($USER->auth)) {
        if ($USER->auth != "oauth2") {
            return;
        }
    }
    // $db = new FirestoreClient($CFG->firebase_config);
    //check student
    $factory = (new Factory)->withServiceAccount(dirname(dirname(__DIR__)) . '/firebasekey.json');
    $auth = $factory->createAuth();
    if (!isset($fb_token)) {
        return;
    }
    $signInResult = $auth->signInAsUser($fb_token);
    $firestore = $factory->createFirestore();
    $db = $firestore->database();


    $student_role = $DB->get_record("role", array("shortname" => "student"))->id;

    $docRef = $db->collection('students')->document($uid);
    $snapshot = $docRef->snapshot();
    try {
        if ($snapshot->exists()) {

            $student = $snapshot->data();
            $enddate = time();
            $group_name = $student["class"]["name"];
            foreach ($student["products"] as $productref) {
                $snapshot = $productref->snapshot();
                if ($snapshot->exists()) {
                    $product = $snapshot->data();
                    $endtime = $product["endtime"];
                    switch ($endtime) {
                        case "0":
                            $enddate = $product['enddate']->get()->getTimestamp();
                            break;
                        case "00":
                            $enddate = strtotime("01/01/2100");
                            break;
                        default:
                            $enddate = strtotime("+$endtime month", time());
                    }
                    $courses = $product['courses'];
                    foreach ($courses as $course) {
                        $shortname = $course["shortname"];
                        insertGroup($shortname, $group_name, $USER->id);
                    }

                    //dont need to enrol
                    //add to cohort only

                    $cohort = $DB->get_record('cohort', array('idnumber' => "Trial-User"), '*', MUST_EXIST);
                    cohort_add_member($cohort->id, $USER->id);
                    $cohort = $DB->get_record('cohort', array('idnumber' => $product["idnumber"]), '*', MUST_EXIST);
                    cohort_add_member($cohort->id, $USER->id);
                }
            };

        } else {
            echo "not found" . $uid;
        }
    } catch (Exception $e) {
        serviceErrorLog("error:" . json_encode($e->getTrace()));
    }
}

function insertGroup($shortname, $group_name, $userid)
{
    global $DB;
    global $ccache;
    if (!isset($ccache)) {
        $ccache = array();
    }
    if (!array_key_exists($shortname, $ccache)) {
        if (!$course = $DB->get_record('course', array('shortname' => $shortname), 'id, shortname')) {
            echo 'unknowncourse' . $shortname;
        }
        $ccache[$shortname] = $course;
        $ccache[$shortname]->groups = null;
    }

    $courseid = $ccache[$shortname]->id;
    $coursecontext = context_course::instance($courseid);
    if (is_null($ccache[$shortname]->groups)) {
        $ccache[$shortname]->groups = array();
        if ($groups = groups_get_all_groups($courseid)) {
            foreach ($groups as $gid => $group) {
                $ccache[$shortname]->groups[$gid] = new stdClass();
                $ccache[$shortname]->groups[$gid]->id = $gid;
                $ccache[$shortname]->groups[$gid]->name = $group->name;
                if (!is_numeric($group->name)) { // only non-numeric names are supported!!!
                    $ccache[$shortname]->groups[$group->name] = new stdClass();
                    $ccache[$shortname]->groups[$group->name]->id = $gid;
                    $ccache[$shortname]->groups[$group->name]->name = $group->name;
                }
            }
        }
    }
    // group exists?
    if (!array_key_exists($group_name, $ccache[$shortname]->groups)) {
        // if group doesn't exist,  create it
        $newgroupdata = new stdClass();
        $newgroupdata->name = $group_name;
        $newgroupdata->courseid = $ccache[$shortname]->id;
        $newgroupdata->description = '';
        $gid = groups_create_group($newgroupdata);

        if ($gid) {
            $ccache[$shortname]->groups[$group_name] = new stdClass();
            $ccache[$shortname]->groups[$group_name]->id = $gid;
            $ccache[$shortname]->groups[$group_name]->name = $newgroupdata->name;
        } else {
            echo 'unknowngroup:' . $group_name;
            return;
        }
    }
    $gid = $ccache[$shortname]->groups[$group_name]->id;
    $gname = $ccache[$shortname]->groups[$group_name]->name;
    try {
        if (groups_add_member($gid, $userid)) {
            // add to group success
        } else {
            echo $userid . ' addedtogroup ' . $gname . "<br/>";
        }
    } catch (moodle_exception $e) {
        echo 'addedtogroupnot error:' . $gname;
    }
    return $gid;
}

function local_sm_attempt_submitted(mod_quiz\event\attempt_submitted $event)
{
    global $CFG, $USER, $SESSION,$DB;
    $fb_token = $CFG->hbon_uid_admin;
    try {
        //get quiz_attempt data
        $quiz_attempt = $event->get_record_snapshot('quiz_attempts', $event->objectid);
        //get quiz data
        $quiz = $event->get_record_snapshot('quiz', $quiz_attempt->quiz);
        //get course data
        $course = $event->get_record_snapshot('course', $event->courseid);
        // firebase.firestore.Timestamp.fromDate(data.birthdate.toDate())
        $section = $DB->get_records('course_sections', array('course' => $event->courseid));

        $send_data = [];
        $send_data['uid'] = $USER->uid;
        $send_data['course'] = 'hbon-' . $course->shortname;
        $send_data['course_shortname'] = $course->shortname;
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
        $send_data['grade'] = (int)$quiz_attempt->sumgrades / (int)$quiz->sumgrades * (int)$quiz->grade;
        $send_data['url'] = ((string)$event->get_url()) . "&cmid=" . $quiz->cmid;

        $factory = (new Factory)->withServiceAccount(dirname(dirname(__DIR__)) . '/firebasekey.json');
        $auth = $factory->createAuth();
        if (!isset($fb_token)) {
            return;
        }
        $signInResult = $auth->signInAsUser($fb_token);
        $firestore = $factory->createFirestore();
        $db = $firestore->database();

        $db->collection('students')->document($USER->uid)->collection('grades')->newDocument()->set($send_data);
        $complete_activities = [];
        $complete_activities ['activity_id']= $quiz->cmid;
        $complete_activities ['activity_mod']= "quiz";
        $complete_activities ['activity_name']=$quiz->name;
        $complete_activities ['course_id']=$quiz->course;
        $complete_activities ['course_name']=$course->shortname;
        if(isset($section) && count($section)>0){
            foreach ($section as $key=>$object){
                if(in_array($quiz->cmid, explode(",",$object->sequence))){
                    $complete_activities ['topic_id']= $key;
                    $complete_activities ['topic_name']=$object->name;
                }
            }
        }
        $db->collection('students')->document($USER->uid)->collection('complete_activities')->document( 'hbon-'.$course->shortname.'-'.$complete_activities ['topic_id']."-".$quiz->cmid)->set($complete_activities);
        return true;
    } catch (Exception $exception) {
        if($CFG->wwwroot === 'https://moodledev.classon.vn'){
            print_object($exception);die();
        }else{
            serviceErrorLog("error:".json_encode($exception->getTrace()));
        }
    }
}


// event update section
function local_sm_course_section_update(core\event\course_section_updated $event)
{
    global $CFG, $USER, $DB, $SESSION;
    $fb_token = $CFG->hbon_uid_admin;
    try {
        $course_info = $event->get_record_snapshot('course', $event->contextinstanceid);
        if($course_info->visible == 1){
            $section = $DB->get_records('course_sections', array('id' => $event->objectid), 'section ASC', 'id,name,section,visible');
            $activities = get_array_of_activities($course_info->id);
            $result = [];
            foreach ($activities as $activity){
                if((int)$activity->sectionid == $event->objectid && !isset($activity->deletioninprogress) ){
                    $result[] = array('id'=>(int)$activity->cm,'mod'=>$activity->mod,'name'=>$activity->name,'visible'=>$activity->visible);
                }
            }
            $newdata = [];
            $newdata['id'] = (int)$section[$event->objectid]->id;
            $newdata['name'] = $section[$event->objectid]->name;
            $newdata['section'] = (int)$section[$event->objectid]->section;
            $newdata['visible'] = (int)$section[$event->objectid]->visible;
            $newdata['activities'] = $result;
            $factory = (new Factory)->withServiceAccount(dirname(dirname(__DIR__)) . '/firebasekey.json');
            $auth = $factory->createAuth();
            if (!isset($fb_token)) {
                return;
            }
            $signInResult = $auth->signInAsUser($fb_token);
            $firestore = $factory->createFirestore();
            $db = $firestore->database();
            $db->collection('courses')->document('hbon-' . $course_info->shortname)->collection('topics')->document($event->objectid)->set($newdata);
            return true;
        }
    } catch (Exception $exception) {
        if($CFG->wwwroot === 'https://moodledev.classon.vn'){
            print_object($exception);die();
        }else{
            serviceErrorLog("error:".json_encode($exception->getTrace()));
        }
    }
}

function local_sm_course_update(core\event\course_updated $event)
{
    global $CFG, $USER, $DB, $SESSION;
    $fb_token = $CFG->hbon_uid_admin;
    try {
        $course_info = $event->get_record_snapshot('course', $event->contextinstanceid);
        if($course_info->visible == 1) {
            $image = "";
            if (isset($course_info->summaryfiles[0])) {
                $image = $course_info->summaryfiles[0]->fileurl;
            }
//            $all_sections_of_course = $DB->get_records('course_sections', array('course' => $course_info->id), 'id ASC', 'id,name');
            $newdata = [];
            $newdata["category"] = !empty($event->get_record_snapshot('course_categories', $course_info->category)->name)?$event->get_record_snapshot('course_categories', $course_info->category)->name:'';
            $newdata["categoryid"] = (int)$course_info->category;
            $newdata["image"] = $image;
            $newdata["name"] = $course_info->fullname;
            $newdata["school"] = [
                "id" => $CFG->school_firebase_id ? $CFG->school_firebase_id : 'vFPBJ0wkJoxBY3s8RmVI',
                "lms_url" => $CFG->wwwroot
            ];
            $newdata["shortname"] = $course_info->shortname;
            $newdata["summary"] = $course_info->summary;
//            $newdata["topic"] = $all_sections_of_course;
            $school_deputy_id = $CFG->school_deputy_id ? $CFG->school_deputy_id : 'hbon';
            $factory = (new Factory)->withServiceAccount(dirname(dirname(__DIR__)) . '/firebasekey.json');
            $auth = $factory->createAuth();
            if (!isset($fb_token)) {
                return;
            }
            $signInResult = $auth->signInAsUser($fb_token);
            $firestore = $factory->createFirestore();
            $db = $firestore->database();
            $db->collection('courses')->document($school_deputy_id . '-' . $course_info->shortname)->set($newdata);
            return true;
        }
    } catch (Exception $exception) {
        if($CFG->wwwroot === 'https://moodledev.classon.vn'){
            print_object($exception);die();
        }else{
            serviceErrorLog("error:".json_encode($exception->getTrace()));
        }
    }
}

function local_sm_check_session()
{
//    TODO bổ sung popup confirm 5' trước khi destroy session
    global $USER;
    \core\session\manager::apply_concurrent_login_limit($USER->id, session_id());
}

function complete_view($event)
{
    global $CFG, $USER, $DB, $SESSION;
    $fb_token = $CFG->hbon_uid_admin;
    try {
        $course = $event->get_record_snapshot('course', $event->courseid);
        $course_module = $event->get_record_snapshot('course_modules', $event->contextinstanceid);
        $section = $event->get_record_snapshot('course_sections', $course_module->section);
        $activity = get_array_of_activities($event->courseid)[$event->contextinstanceid];
        $send_data = [];
        $input_check =   array(
            'courseid' => $event->courseid,
            'contextinstanceid' => $event->contextinstanceid,
            'userid' => $USER->id,
            'component' => $event->component,
            'action' => $event->action
        );
        $check = $DB->count_records('hbon_complete_activity',$input_check);
        if ($check === 0) {
            $send_data['course_id'] = $CFG->school_deputy_id . '-' . $course->shortname;
            $send_data['course_name'] = $course->fullname;
            $send_data['topic_id'] = (int)$section->id;
            $send_data['topic_name'] = $section->name;
            $send_data['activity_id'] = (int)$activity->cm;
            $send_data['activity_name'] = $activity->name;
            $send_data['activity_mod'] = $activity->mod;
            $send_data['created_at'] = time();
            $factory = (new Factory)->withServiceAccount(dirname(dirname(__DIR__)) . '/firebasekey.json');
            $auth = $factory->createAuth();
            if (!isset($fb_token)) {
                return;
            }
            $signInResult = $auth->signInAsUser($fb_token);
            $firestore = $factory->createFirestore();
            $db = $firestore->database();
            $db->collection('students')->document($USER->uid)->collection('complete_activities')->document($send_data['course_id'] . '-' . $send_data['topic_id'] . '-' . $send_data['activity_id'])->set($send_data);
            $complete = new stdClass();
            $complete->courseid = $event->courseid;
            $complete->contextinstanceid = $event->contextinstanceid;
            $complete->userid = $USER->id;
            $complete->component = $event->component;
            $complete->action = $event->action;
            $DB->insert_record('hbon_complete_activity',  $complete);
        }

    } catch (Exception $exception) {
        if($CFG->wwwroot === 'https://moodledev.classon.vn'){
            print_object($exception);die();
        }else{
            serviceErrorLog("error:".json_encode($exception->getTrace()));
        }
    }
}

function local_sm_mod_book_chapter_viewed(mod_book\event\chapter_viewed $event)
{
    complete_view($event);
}

function local_sm_mod_book_module_viewed(mod_book\event\course_module_viewed $event)
{
    complete_view($event);
}

function local_sm_mod_assign_submission_created(mod_assign\event\submission_created $event)
{
   complete_view($event);
}

function local_sm_mod_feedback_view_feedback(mod_feedback\event\course_module_viewed $event)
{
    complete_view($event);
}

function local_sm_mod_view_forum(mod_forum\event\forum_viewed $event)
{
    complete_view($event);
}

function local_sm_mod_view_forum_discussion(mod_forum\event\discussion_viewed $event)
{
    complete_view($event);
}

function local_sm_mod_wiki_page_viewed(mod_wiki\event\page_viewed $event)
{
    complete_view($event);
}

function local_sm_mod_wiki_course_module_viewed(mod_wiki\event\course_module_viewed $event){
    complete_view($event);
}

function local_sm_mod_resource_course_module_viewed(mod_resource\event\course_module_viewed $event)
{
    complete_view($event);
}

function local_sm_mod_resource_course_module_instance_list_viewed(mod_resource\event\course_module_instance_list_viewed $event)
{
    global $CFG, $USER, $DB, $SESSION;
    try {

    } catch (Exception $exception) {
        print_r($exception);
        die();
    }
}

function local_sm_mod_page_course_module_viewed(mod_page\event\course_module_viewed $event)
{
    complete_view($event);
}

function local_sm_mod_url_course_module_viewed(mod_url\event\course_module_viewed $event)
{
    complete_view($event);
}

//function local_sm_
