<?PHP
require  dirname(dirname(__DIR__)) . '/vendor/autoload.php';
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Core\Timestamp;

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(__DIR__)) . '/config.php');

function local_sm_enrole(core\event\user_loggedin $event){
    global $CFG,$DB;
    global $USER;

    if($USER->auth!="oauth2"){
        return;
    }
    $uid = $USER->uid;
    $db = new FirestoreClient($CFG->firebase_config);
    //check student

    $student_role = $DB->get_record("role",array("shortname"=>"student"))->id;
    $documents = $db->collection('students')->document($uid)->collection('products')->documents();
    foreach ($documents as $document) {
        if ($document->exists()) {

            $product = $document->data();
            $courses = $product['courses'];
            $endtime=$product['end_time']->get()->getTimestamp();
            foreach($courses as $course) {
                check_enrol($course["shortname"],$USER->id,$student_role,$endtime);
            }
        } else {
            printf('Document %s does not exist!');
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
        $enrol->enrol_user($instance, $userid, $roleid, $timestart, $endtime);
    }
    return true;
}

function local_sm_attempt_submitted(mod_quiz\event\attempt_submitted $event) {
    global $CFG, $USER;
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

    $db = new FirestoreClient($CFG->firebase_config);

    $db->collection('students')->document($USER->uid)->collection('activities')->newDocument()->set($send_data);
    return true;
    }catch (Exception $exception){
    throwException($exception);
    }
    }
