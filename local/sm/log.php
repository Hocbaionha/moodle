<?PHP

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
require_once(dirname(dirname(__DIR__)) . '/config.php');
require_once($CFG->dirroot . "/lib/externallib.php");
global $CFG, $USER, $SESSION, $DB;

$timeSpent = optional_param('timeSpent', 0, PARAM_INT);
$timeSpent = $timeSpent / 1000;
$bodyattributes = optional_param('bodyattributes', 0, PARAM_TEXT);
$action = optional_param('action', 0, PARAM_TEXT);
$course = optional_param('course', 0, PARAM_TEXT);
$course_link = optional_param('course_link', 0, PARAM_TEXT);
$topic = optional_param('topic', 0, PARAM_TEXT);

$aurl = new Sup();

$activity = $aurl->get_name($action);
$activity_id = $aurl->get_param($action);
$course_name = $aurl->get_name($course_link);
$course_id = $aurl->get_param($course_link);

$listActivity = ["assign", "book", "feedback", "quiz", "wiki", "resource", "geogebra", "url", "page", "hp5activity", "forum"];
if (isset($topic) && $activity !== false && $activity_id !== false && in_array($activity, $listActivity)) {
    $checkexist = $DB->count_records('hbon_activity_one_hourse', array('user_id' => $USER->id, 'activity' => $activity, 'activity_id' => $activity_id));
    if ($checkexist === 0) {
        $newData = array('user_id' => (int)$USER->id,
            'activity' => $activity,
            'activity_id' => (int)$activity_id,
            'timespent' => $timeSpent,
            'link' => $action,
            'course_id' => (int)$course_id,
            'course_name' => $course,
            'uid' => $USER->uid,
            'topic' => $topic
        );
        $DB->insert_record('hbon_activity_one_hourse', (object)$newData);
    } else {
        $old = $DB->get_record('hbon_activity_one_hourse', array('user_id' => $USER->id, 'activity' => $activity, 'activity_id' => $activity_id));
        $newTime = $timeSpent + $old->timespent;
        $newData = array(
            'id' => (int)$old->id,
            'user_id' => (int)$USER->id,
            'activity' => $activity,
            'activity_id' => (int)$activity_id,
            'timespent' => $newTime,
            'link' => $action,
            'course_id' => (int)$course_id,
            'course_name' => $course,
            'uid' => $USER->uid,
            'topic' => $topic);
        $DB->update_record('hbon_activity_one_hourse', (object)$newData);
    }
}
//            $date = new DateTime();
//            $setdata = [];
//            $setdata['activity'] = $aSingleRow[NAME];
//            $setdata['activity_id'] = $aSingleRow[INPUT_ID];
//            $setdata['course_id'] = $aSingleRow[COURSE_ID];
//            $setdata['course_name'] = $aSingleRow[COURSE_NAME];
//            $setdata['created_at'] = new Timestamp($date->setTimestamp($aSingleRow[TIME_ADD]));
//            $setdata['time_spent'] = $aSingleRow[TIME_SPENT];
//            $setdata['topic'] = $aSingleRow[TOPIC];
//            $factory = (new Factory)->withServiceAccount(dirname(dirname(__DIR__)) . '/firebasekey.json');
//            $auth = $factory->createAuth();
//            if(!isset($SESSION->fb_token)){
//                return;
//            }
//            $signInResult = $auth->signInWithCustomToken($SESSION->fb_token);
//            $firestore = $factory->createFirestore();
//            $fb_db = $firestore->database();
//            $fb_db->collection('students')->document($USER->uid)->collection('activities')->newDocument()->set($setdata);

class Sup
{
    function get_name($url)
    {
        $name = parse_url($url, PHP_URL_PATH);
        $t = explode("/", $name);
        if (array_key_exists(2, $t)) {
            return $t[2];
        } else {
            return false;
        }
    }

    function get_param($url)
    {
        $parts = parse_url($url);
        $query = array();
        if (array_key_exists('query', $parts)) {
            parse_str($parts['query'], $query);
        }
        if (!array_key_exists('id', $query)) {
            return false;
        }
        return $query['id'];
    }
}


