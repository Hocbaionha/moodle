<?PHP
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
if($startwith==""){
    echo "missing school param";
    die;    
}
switch ($startwith){
    case "as":
        $old_school="as";
        $schoolid = "na-anhson";
        break;
    case "quangminh":
        $old_school = "hn-quangminh";
        $schoolid = "hn-quangminh";
        break;
    case "qna-ndh":
        $old_school = "qna-ndh";
        $schoolid = "qna-nguyenduyhieu";
        break;
    case "kd":
        $old_school="kd";
        $schoolid = "hn-khuongdinh";
        break;
    case "kd":
        $old_school="kd";
        $schoolid = "hn-khuongdinh";
        break;
}
$context = context_system::instance();
$PAGE->set_context($context);

$PAGE->set_heading("Sync user to firebase");
$url = new moodle_url('/local/sm/remove_cohort.php');
$PAGE->set_url($url);
echo $OUTPUT->header();
$cohorts = array();

//get users from each school
$sql = "SELECT id,username,email,firstname,lastname from mdl_user where username like '$old_school%'";
$mdlusers = $DB->get_records_sql($sql);


$factory = (new Factory)->withServiceAccount($CFG->dirroot . '/firebasekey.json');

$auth = $factory->createAuth();
if(!isset($SESSION->fb_token)){
    echo "not found fb token";
    return;
}
try {
    $signInResult = $auth->signInWithCustomToken($SESSION->fb_token);
    $firestore = $factory->createFirestore();
    $fdb = $firestore->database();


    $new_schools = array();
    $schools = array();
    $last_student = $last_teacher = 0;
    if(count($mdlusers)==0) {
        echo "not found any"; 
        return;
    }
    $codeField = $DB->get_record("user_info_field",array("shortname"=>"student_code"))->id;
    $uidField = $DB->get_record("user_info_field",array("shortname"=>"uid"))->id;
    foreach($mdlusers as $mdluser){
        
        $userid=$mdluser->id;
        $username=$mdluser->username;
        $userArr = explode("-",$username);
        $uname = array_pop($userArr);  
        if($uname=="bgh" || $uname=="admin") {
            continue;
        }
        
        $user = array("moodleUserId"=>$userid,"email"=>$mdluser->email,"firstname"=>$mdluser->firstname,"lastname"=>$mdluser->lastname,"username"=>$mdluser->username,"status"=>0,"school_id"=>$schoolid);
        $sql = "SELECT concat(c.idnumber,u.id),c.idnumber,u.id, u.username,u.firstname,u.lastname, u.currentlogin FROM mdl_cohort c JOIN mdl_cohort_members cm ON c.id = cm.cohortid JOIN mdl_user u ON cm.userid = u.id  WHERE u.id=? and c.idnumber like '%hbon%'";
        
        $cusers = $DB->get_records_sql($sql,array("id"=>$mdluser->id));

        $products = [];
        $cuser = false;
        foreach ($cusers as $cuser){
            $productid=$cuser->idnumber;
            $prodRef = $fdb->collection('products')->document($productid);
            $products[] = $prodRef;
        }
        if($cuser){
            $batch = $fdb->batch();
            // da mua the thanh vien
                $sql = "select g.name from mdl_groups_members gm  join mdl_groups g on gm.groupid=g.id join mdl_user u on u.id=gm.userid where u.id=? group by name limit 1";
                $group = $DB->get_record_sql($sql,array("id"=>$mdluser->id));
                if($group){
                    // Mua theo trường
                    $classid = "";
                    $classname = $group->name;
                    
                    $docRef = $fdb->collection('classes');
                    $query = $docRef->where('school_id', '==', $schoolid)->where('name', '==', $classname)->where("years","==","2020_2021");
                    $documents = $query->documents();
                    echo $classname.";";
                    foreach ($documents as $document) {
                        $classid=$document->id();
                        if ($document->exists()) {
                            $data = $document->data();
                            if(isset($data->name)&&$data->name!=""){
                                $classname = $data->name;
                                echo $classname." found! ";
                            }
                            
                        } else {
                            echo $classname." created! ";
                            $sclass = array("name"=>$classname,"school_id"=>$schoolid,"years"=>"2020_2021");
                            $classRef = $fdb->collection('classes')->document($classid);
                            $batch->set($classRef,$sclass);
                            $schoolRef = $fdb->collection('schools')->document($schoolid);
                            $batch->update($schoolRef,[["path"=>"classes","value"=>FieldValue::arrayUnion([$classRef])]]);
                        }
                    }
                    if($classid==""){
                        $classid= substr(md5(microtime()),rand(0,26),6);
                        echo $classname." created! ";
                        $sclass = array("name"=>$classname,"school_id"=>$schoolid,"years"=>"2020_2021");
                        $classRef = $fdb->collection('classes')->document($classid);
                        $batch->set($classRef,$sclass);
                        $schoolRef = $fdb->collection('schools')->document($schoolid);
                        $batch->update($schoolRef,[["path"=>"classes","value"=>FieldValue::arrayUnion([$classRef])]]);
                    }

                    
                    $docRefUser = $fdb->collection('users');
                    $query = $docRefUser->where('username', '==', $username);
                    $documents = $query->documents();
                    echo $classid." find user:".$username;
                    foreach ($documents as $document) {
                        if ($document->exists()) {
                            $uid=$document->id();
                            echo " found user:".$username."-".$uid;
                            if(startsWith($uname,"hs")){
                                $student = $user;
                                $student["class"]=array("id"=>$classid,"name"=>$classname);
                                $student["products"] = $products;
                                $student["code"]=generateStudentCode();
                                $stuRef = $fdb->collection('students')->document($uid);
                                $stuSnapshot = $stuRef->snapshot();
                                if (!$stuSnapshot->exists()) {
                                    $batch->set($fdb->collection('students')->document($uid),$student);
                                    $batch->update($fdb->collection('classes')->document($classid),[["path"=>"students","value"=>FieldValue::arrayUnion([$stuRef])]]);
                                    $batch->set($fdb->collection('student_code')->document($student["code"]["code"]),array("expired_time"=>$student["code"]["expired_time"],"student_id"=>$uid));
                                    updateStudentData($student["moodleUserId"],$uid,$student["code"]["code"]);
                                } else {
                                    //do nothing if found student
                                    // $datas = array();
                                    // foreach($student as $key=>$value){
                                    //     $datas[] = ['path'=>$key,'value'=>$value];
                                    // }
                                    // $batch->update($fdb->collection('students')->document($uid),$datas);
                                }
        
                            } else if(startsWith($uname,"gv")){
                                $teaRef = $fdb->collection('teachers')->document($uid);
                                $batch->set($fdb->collection('teachers')->document($uid),$user);
                                $batch->update($fdb->collection('schools')->document($schoolid),[["path"=>"teachers","value"=>FieldValue::arrayUnion([$teaRef])]]);                        }
                        }
                    }
                } else {
                    // không có lớp
                    $student = $user;
                    $docRefUser = $fdb->collection('users');
                    $query = $docRefUser->where('username', '==', $username);
                    $documents = $query->documents();
                    foreach ($documents as $document) {
                        if ($document->exists()) {
                            $uid=$document->id();
                            if(startsWith($uname,"hs")){
                                $student["products"] = $products;
                                $student["code"]=generateStudentCode();
                                $stuRef = $fdb->collection('students')->document($uid);
                                $stuSnapshot = $stuRef->snapshot();
                                if (!$stuSnapshot->exists()) {
                                    $batch->set($fdb->collection('students')->document($uid),$student);
                                    $batch->set($fdb->collection('student_code')->document($student["code"]["code"]),array("expired_time"=>$student["code"]["expired_time"],"student_id"=>$uid));
                                    updateStudentData($student["moodleUserId"],$uid,$student["code"]["code"]);
                                }
                            } else if(startsWith($uname,"gv")){
                                $teaRef = $fdb->collection('teachers')->document($uid);
                                $batch->set($fdb->collection('teachers')->document($uid),$user);
                                $batch->update($fdb->collection('schools')->document($schoolid),[["path"=>"teachers","value"=>FieldValue::arrayUnion([$teaRef])]]);
                            }
                        }
                    }
                }
            if (!$batch->isEmpty()) {
                $batch->commit();
            }
        } else {
            // chua mua the thanh vien
            $batch = $fdb->batch();
            $docRef = $fdb->collection('users');
            $query = $docRef->where('username', '==', $username);
            $documents = $query->documents();
            foreach ($documents as $document) {
                $uid=$document->id();
                if(startsWith($uname,"hs")){
                    $stuRef = $fdb->collection('students')->document($uid);
                    $stuSnapshot = $stuRef->snapshot();
                    if (!$stuSnapshot->exists()) {
                        $student = $user;
                        $student["code"]=generateStudentCode();
                        $batch->set($fdb->collection('students')->document($uid),$student);
                        $batch->set($fdb->collection('student_code')->document($student["code"]["code"]),array("expired_time"=>$student["code"]["expired_time"],"student_id"=>$uid));
                        updateStudentData($student["moodleUserId"],$uid,$student["code"]["code"]);
                    } else {
                        //not update
                    }
                }  else if(startsWith($uname,"gv")){
                    $teaRef = $fdb->collection('teachers')->document($uid);
                    $batch->set($teaRef,$user);
                    $batch->update($fdb->collection('schools')->document($schoolid),[["path"=>"teachers","value"=>FieldValue::arrayUnion([$teaRef])]]);
                }
            }
            if (!$batch->isEmpty()) {
                $batch->commit();
            }
        }
        print_object($mdluser);
        
    }
    echo " done<br/>";
} catch (Exception $exception) {
    serviceErrorLog("error:".json_encode($exception->getTrace()));

}    

function generateStudentCode(){
    global $fdb;
    $code = substr(md5(microtime()),rand(0,26),6);
    $exist=true;
    while ($exist) {
        $docRef = $fdb->collection('student_code')->document($code);
        $snapshot = $docRef->snapshot();
        if (!$snapshot->exists()) {
            $exist=false;
        } else {
            $code = substr(md5(microtime()),rand(0,26),6);
        }
    }
    
    $date = new DateTime();
    $time = $date->getTimestamp();
    $expired_time = $time + (365*24*60*60); 
    return array("code"=>$code,"expired_time"=>$expired_time*1000);
}

function updateStudentData($moodleUserId,$uid,$code){
    global $codeField,$uidField,$DB;
    $check = $DB->get_record("user_info_data",array("userid"=>$moodleUserId,"fieldid"=>$uidField));
    $sql = "update mdl_user_info_data set data=? where userid=? and fieldid=?";
    if(!$check){
        $DB->insert_record('user_info_data', array('userid' => $moodleUserId,
                    'fieldid' => $uidField, 'data' => $uid));
    } else {
        $DB->execute($sql,array("data"=>$uid,"userid"=>$moodleUserId,"fieldid"=>$uidField));
    }

    $check = $DB->get_record("user_info_data",array("userid"=>$moodleUserId,"fieldid"=>$codeField));
    if(!$check){
        $DB->insert_record('user_info_data', array('userid' => $moodleUserId,
                    'fieldid' => $codeField, 'data' => $code));
    } else {
        $DB->execute($sql,array("data"=>$code,"userid"=>$moodleUserId,"fieldid"=>$codeField));
    }
}