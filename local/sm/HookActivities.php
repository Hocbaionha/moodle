<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
require_once(dirname(dirname(__DIR__)) . '/config.php');
require_once($CFG->dirroot . "/lib/externallib.php");

use Google\Cloud\Core\Timestamp;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Firestore;
set_time_limit(1800);
$param = optional_param('param', 0, PARAM_TEXT);
global $CFG, $USER, $SESSION, $DB;
$key = $CFG->hbon_uid_admin;
if (isset($param) && $param === $key) {

    $ressult = $DB->get_records('hbon_activity_one_hourse');
    try{
        $factory = (new Factory)->withServiceAccount(dirname(dirname(__DIR__)) . '/firebasekey.json');
        $auth = $factory->createAuth();
        $signInResult = $auth->signInAsUser($key);
        $firestore = $factory->createFirestore();
        $fb_db = $firestore->database();
        $date = new DateTime();

        foreach ($ressult as $key=>$item) {
            if ($item->uid !== 0 && $item->uid !== null) {
                $setdata = [];
                $setdata['activity'] = $item->activity;
                $setdata['activity_id'] = (int)$item->activity_id;
                $setdata['course_id'] = 'hbon-'.$item->course_name;
                $setdata['course_name'] = $item->course_name;
                $setdata['created_at'] = new Timestamp($date->setTimestamp(time()));
                $setdata['time_spent'] = (float)$item->timespent;
                $setdata['topic'] = $item->topic;
                $fb_db->collection('students')->document($item->uid)->collection('activities')->newDocument()->set($setdata);
            }
        }
        $DB->delete_records('hbon_activity_one_hourse');
    }catch (Exception $exception){
        $DB->delete_records('hbon_activity_one_hourse');
    }



//    $factory = (new Factory)->withServiceAccount(dirname(dirname(__DIR__)) . '/firebasekey.json');
//    $auth = $factory->createAuth();
//    $signInResult = $auth->signInAsUser($key);
//    $firestore = $factory->createFirestore();
//    $fb_db = $firestore->database();
//    $citiesRef = $fb_db->collection('users');
//    $query = $citiesRef->where('moodleUserId', '<=', 4000);
//    $documents = $query->documents();
//    $result = [];
//    foreach ($documents as $key=>$document) {
//        if ($document->exists()) {
//            $setdata = [];
//            $setdata['user_id'] = $key+1;
//            $setdata['activity'] = 'assign';
//            $setdata['activity_id'] = 66;
//            $setdata['course_id'] = 18;
//            $setdata['course_name'] = 'LTT10N';
//            $setdata['created_at'] = time();
//            $setdata['timespent'] = 1900.502;
//            $setdata['topic'] = 'qưe';
//            $setdata['uid'] =   $document->data()['userId'];
//            $result[] = (object)$setdata;
//        } else {
//            printf('Document %s does not exist!' . PHP_EOL, $document->id());
//        }
//    }
////    print_r(json_encode($result));die();
//    $DB->insert_records('hbon_activity_one_hourse', $result);
}
