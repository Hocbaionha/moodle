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

$PAGE->set_heading("Sync cohort to firebase");
$url = new moodle_url('/local/sm/remove_cohort.php');
$PAGE->set_url($url);
echo $OUTPUT->header();
$cohorts = array();
//find all relation courses and cohorts
try{
$sql = 'select concat(course,availability),course,availability from mdl_course_sections where availability is not null and availability!=\'{"op":"&","c":[],"showc":[]}\' group by course,availability';
// die($sql);
$cas = $DB->get_records_sql($sql);
foreach ($cas as $ca) {
    $courseid = $ca->course;
    $avai = $ca->availability;
    if(isset($avai)){
        $availability = json_decode($avai);
        $op = $availability->op;
        $c = $availability->c;
        foreach($c as $co){
            if($co->type=="cohort"){
                if(!isset($cohorts[$co->id])){
                    $cohorts[$co->id] = array($courseid);
                } else {
                    if(!in_array($courseid, $cohorts[$co->id], true)){
                        array_push($cohorts[$co->id],$courseid);
                    }
                }
            }
            else if (isset($co->c)){
                $c2 = $co->c;
                foreach($c2 as $co2){
                    if($co2->type=="cohort"){
                        if(!isset($cohorts[$co2->id])){
                            $cohorts[$co2->id] = array($courseid);
                        } else {
                            if(!in_array($courseid, $cohorts[$co2->id], true)){
                                array_push($cohorts[$co2->id],$courseid);
                            }
                        }
                    }
                }
            }
        }
        
    }
}

$factory = (new Factory)->withServiceAccount($CFG->dirroot . '/firebasekey.json');

$auth = $factory->createAuth();
if(!isset($SESSION->fb_token)){
    return;
}
$signInResult = $auth->signInWithCustomToken($SESSION->fb_token);
$firestore = $factory->createFirestore();
$db = $firestore->database();


//build cohorts as a product

foreach($cohorts as $cohortid=>$courses){
    $product = array();
    $cohort = $DB->get_record("cohort",array("id"=>$cohortid));
    if(!$cohort){
        continue;
    }
    $product["name"] = $cohort->name;
    $product["idnumber"] = $cohort->idnumber;
    $product["courses"] = array();
    foreach($courses as $courseid){
        $course = $DB->get_record("course",array("id"=>$courseid));
        array_push($product["courses"] ,array("id"=>"hbon-".$course->shortname,"shortname"=>$course->shortname,"name"=>$course->fullname));
    }
//add to products on firebase
    $docRef = $db->collection('products')->document($product["idnumber"])->set($product);
    echo "sync cohort:".$product["name"]."<br/>";
}

echo "Done!!!";
}catch (Exception $e){
    serviceErrorLog("error:".json_encode($e->getTrace()));
}
echo $OUTPUT->footer();