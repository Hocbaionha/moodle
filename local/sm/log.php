<?PHP
global $CFG,$USER,$SESSION;
require  dirname(dirname(__DIR__)) . '/vendor/autoload.php';
require_once(dirname(dirname(__DIR__)) . '/config.php');
require_once('flatfile.php');
require_once($CFG->dirroot . "/lib/externallib.php");
use Google\Cloud\Core\Timestamp;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Firestore;

if(!isset($USER->id)){
    die;
}
//only log activity with SSO user
$timeSpent = optional_param('timeSpent', 0, PARAM_INT);
$timeSpent = $timeSpent/1000;
$bodyattributes=optional_param('bodyattributes', 0, PARAM_TEXT);
$action=optional_param('action', 0, PARAM_TEXT);
$course=optional_param('course', 0, PARAM_TEXT);
$course_link=optional_param('course_link', 0, PARAM_TEXT);
$topic=optional_param('topic', 0, PARAM_TEXT);
// $locate = $CFG->dataroot."/school/";
$aurl= new Sup();
$db = new Flatfile();
$db->datadir = $CFG->dataroot."/activity/";
// $activity = array("uid"=>$USER->id,"name"=>"quiz","time_add"=>time(),"time_spent"=>$timeSpent);
$name = $USER->id.'activity.txt';

define('ID',   0);
define('NAME', 1);
define('INPUT_ID', 2);
define('TIME_ADD',  3);
define('TIME_SPENT',  4);
define('URL',   5);
define('COURSE_ID',   6);
define('COURSE_NAME',   7);
define('TOPIC',   8);
define('UID',   9);


$activity[ID] = $USER->id;
$activity[NAME] = $aurl->get_name($action);
$activity[INPUT_ID] = $aurl->get_param($action);
if(!$activity[INPUT_ID]){
    die;
}
$activity[TIME_ADD] = time();
$activity[TIME_SPENT] = $timeSpent;
$activity[URL] = $action;
$activity[COURSE_ID] = $aurl->get_param($course_link);
$activity[COURSE_NAME] = $course;
$activity[TOPIC] = $topic;
$activity[UID] = $USER->uid;

$listActivity = ["assign", "book", "feedback", "quiz", "wiki", "resource", "geogebra", "url", "page", "hp5activity"];

if($topic && (in_array($activity[NAME], $listActivity) == true)){
    $aSingleRow = $db->selectUnique($name, NAME,  $aurl->get_name($action));
    if(empty($aSingleRow)){
        $db->insert($name, $activity);
    } else {
        $db->updateSetWhere($name, array(TIME_SPENT => $aSingleRow[TIME_SPENT]+$timeSpent),
            new SimpleWhereClause(NAME, '=', $aurl->get_name($action)));
//        if((time() - $aSingleRow[TIME_ADD])>3600000){
            //push to firebase every 1h activity
            $date = new DateTime();
            $setdata = [];
            $setdata['activity'] = $aSingleRow[NAME];
            $setdata['activity_id'] = $aSingleRow[INPUT_ID];
            $setdata['course_id'] = $aSingleRow[COURSE_ID];
            $setdata['course_name'] = $aSingleRow[COURSE_NAME];
            $setdata['created_at'] = new Timestamp($date->setTimestamp($aSingleRow[TIME_ADD]));
            $setdata['time_spent'] = $aSingleRow[TIME_SPENT];
            $setdata['topic'] = $aSingleRow[TOPIC];
            $factory = (new Factory)->withServiceAccount(dirname(dirname(__DIR__)) . '/firebasekey.json');
            $auth = $factory->createAuth();
            if(!isset($SESSION->fb_token)){
                return;
            }
            $signInResult = $auth->signInWithCustomToken($SESSION->fb_token);
            $firestore = $factory->createFirestore();
            $fb_db = $firestore->database();
            $fb_db->collection('students')->document($USER->uid)->collection('activities')->newDocument()->set($setdata);
        }
//    }
}


class Sup
{
    function get_name($url)
    {
        $name =  parse_url($url,PHP_URL_PATH);
        $t = explode("/",$name);
        return $t[2];
    }

    function get_param($url){
        $parts = parse_url($url);
        $query=array();
        if(array_key_exists('query',$parts)){
            parse_str($parts['query'], $query);
        }
        if(!array_key_exists('id',$query)){
            return false;
        }
        return $query['id'];
    }
}

