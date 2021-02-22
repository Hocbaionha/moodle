<?PHP
require_once($CFG->dirroot .'/config.php');
require_once($CFG->dirroot . "/lib/externallib.php");
require_once($CFG->dirroot . "/vendor/autoload.php");


require_once($CFG->dirroot . "/cohort/lib.php");
require_once $CFG->libdir . '/hbonlib/string_util.php';
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/group/lib.php');
require_once(__DIR__ . '/../../../admin/tool/uploaduser/locallib.php');
require_once($CFG->dirroot . '/local/sm/lib.php');

use Kreait\Firebase\Factory;
use Kreait\Firebase\Firestore;
use Google\Cloud\Firestore\FieldValue;

$ccache = array();
$manualcache = array();
$rolecache = uu_allowed_roles_cache();
$manual = enrol_get_plugin('manual');
class local_sm_user_external extends external_api{


    public static function create_user_parameters() {
        return new external_function_parameters(
            array(
                'data' => 
                    new external_single_structure(
                        array(
                    'uid' => new external_value(PARAM_TEXT, 'uid'),
                    'firstname' => new external_value(PARAM_TEXT, 'firstname'),
                    'lastname' => new external_value(PARAM_TEXT, 'lastname'),
                    'email' => new external_value(PARAM_TEXT, 'email'),
                    'username' => new external_value(PARAM_TEXT, 'username'),
                    'phone' => new external_value(PARAM_TEXT, 'phone')
                    ))
            )
        );
    }

    public static function create_user($params){
        $userinfo = $params;
        $userinfo['username'] = $userinfo['email'];
        // $name = json_encode($params['data']);
        $firebase_uid = $params["uid"];
    try{
        global $DB;
        $iss = $DB->get_record("oauth2_issuer",array("name"=>"sso-server"));
        $issuerid=$iss->id;//oauth2
        $issuer = new \core\oauth2\issuer($issuerid);

        $newuser = \auth_oauth2\api::create_new_confirmed_account($userinfo, $issuer);
        serviceErrorLog("created user:".$newuser->id);
        $userinfo = get_complete_user_data('id', $newuser->id);
        $DB->set_field("user", "confirmed", 1, array("id" => $newuser->id));

        self::local_sm_enrole($newuser->id,$firebase_uid);
        serviceErrorLog("local_sm_enrole done");
    } catch (Exception $e){
        serviceErrorLog("error:".json_encode($e->getTrace()));
        return ["status"=>"error:".$cohort->id];
    }
        return ["status"=>"success:".$firebase_uid];
    }

    public static function local_sm_enrole($userid,$firebase_uid)
    {
        global $CFG, $DB;
        $fb_token = $CFG->hbon_uid_admin;

        // $db = new FirestoreClient($CFG->firebase_config);
        //check student
        $factory = (new Factory)->withServiceAccount(dirname(dirname(dirname(__DIR__))) . '/firebasekey.json');
        $auth = $factory->createAuth();
        if (!isset($fb_token)) {
            return;
        }
        $signInResult = $auth->signInAsUser($fb_token);
        $firestore = $factory->createFirestore();
        $db = $firestore->database();


        $student_role = $DB->get_record("role", array("shortname" => "student"))->id;

        $docRef = $db->collection('students')->document($firebase_uid);
        $snapshot = $docRef->snapshot();
        try {
            if ($snapshot->exists()) {

                $student = $snapshot->data();
                $enddate = time();
                foreach ($student["products"] as $productref) {
                    $snapshot = $productref->snapshot();
                    if ($snapshot->exists()) {
                        $product = $snapshot->data();
                        $cohort = $DB->get_record('cohort', array('idnumber' => "Trial-User"), '*', MUST_EXIST);
                        cohort_add_member($cohort->id, $userid);
                        $cohort = $DB->get_record('cohort', array('idnumber' => $product["idnumber"]), '*', MUST_EXIST);
                        serviceErrorLog("cohort:" . $cohort->idnumber."userid:".$userid);
                        cohort_add_member($cohort->id, $userid);
                    }
                }
            } else {
                echo "not found" . $firebase_uid;
            }
        } catch (Exception $e) {
            serviceErrorLog("error:" . json_encode($e->getTrace()));
        }
    }
    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.9
     */
    public static function create_user_returns() {
        return new external_single_structure(
            array(
            'status' => new external_value(PARAM_TEXT, 'return')
            )
        );
    }

    public static function upload_school_parameters() {
        return new external_function_parameters(
            array(
                'data' => 
                    new external_single_structure(
                        array(
                    'school_id' => new external_value(PARAM_TEXT, 'school_id'),
                    'hs' => new external_value(PARAM_TEXT, 'hs'),
                    'gv' => new external_value(PARAM_TEXT, 'gv'),
                    'assignment' => new external_value(PARAM_TEXT, 'assignment'),
                    'command_id' => new external_value(PARAM_TEXT, 'command_id'),
                    'year' => new external_value(PARAM_TEXT, 'year')
                        )
                    )
            )
        );
    }
    public static function upload_school($params){
        serviceErrorLog("start...");
        global $DB;
         $school_id  = $params["school_id"];
         ob_end_clean();
        header("Connection: close");
         ob_start();
         echo json_encode(["status"=>"doing:".$params['command_id']."-".$params['year']]);
         $size = ob_get_length();
         header("Content-Length: $size");
         ob_end_flush(); // Strange behaviour, will not work
         flush();            // Unless both are called !
         session_write_close();

         serviceErrorLog("start upload_school:".$school_id);
         $hs = json_decode($params["hs"]);
         $gv = json_decode($params["gv"]);
         $assignments = json_decode($params["assignment"]);
         
         try{

         serviceErrorLog("step 1: Kiểm tra có dấu ");
         serviceErrorLog(json_encode($hs[0]));
         serviceErrorLog(json_encode($hs[1]));
         $r=0;
        $classname="";
        $classid="";
        $factory = (new Factory)->withServiceAccount(dirname(dirname(dirname(__DIR__))) . '/firebasekey.json');
        $auth = $factory->createAuth();
        $firestore = $factory->createFirestore();
        $fdb = $firestore->database();
        $prodRef = $fdb->collection('products')->document("HBON-TVA");
        $products = array($prodRef);

        foreach($hs as $hs_row){

            if($r==0) {
                $r++;
                continue;
            };
            if(""==$classname || $classname!=$hs_row[1]){
                $classname=$hs_row[1];
                
                $docRef = $fdb->collection('classes');
                $query = $docRef->where('school_id', '==', $school_id)->where('name', '==', $classname)->where("years","==",$params['year']);
                $documents = $query->documents();
                
                if($documents->size()>0){
                    foreach ($documents as $document) {
                        serviceErrorLog("1");
                        if ($document->exists()) {
                            $classid=$document->id();
                            serviceErrorLog("found class:".$classid);
                        } 
                    }
                } else {
                    $batch = $fdb->batch();
                    //create class if not found
                    $classid= substr(md5(microtime()),rand(0,26),6);
                    serviceErrorLog($school_id." msg: ".$classname." creating! ".$classid);
                    $sclass = array("name"=>$classname,"school_id"=>$school_id,"years"=>"2020_2021","status"=>0);
                    $classRef = $fdb->collection('classes')->document($classid);
                    $batch->set($classRef,$sclass);
                    $schoolRef = $fdb->collection('schools')->document($school_id);
                    $batch->update($schoolRef,[["path"=>"classes","value"=>FieldValue::arrayUnion([$classRef])]]);
                    if (!$batch->isEmpty()) {
                        $batch->commit();
                    }
                    serviceErrorLog("class:".$classname." created! ");
                }
            } 
            if ($classid!="" && $classname!=""){
                $username=$hs_row[5];
                serviceErrorLog("search user:".$username."-".$classid.$classname);
                $mdluser =  $DB->get_record("user",array("username"=>$username));
                serviceErrorLog(json_encode($mdluser));
                if($mdluser){
                    serviceErrorLog("found:".$mdluser->username);
                } else {
                    //create moodle user if not exits
                    $mdluser = self::create_moodle_user($hs_row,$school_id);
                    
                    serviceErrorLog("moodle created:".$mdluser->username);
                    $cohort = $DB->get_record('cohort', array('idnumber' => "Trial-User"), '*', MUST_EXIST);
                    cohort_add_member($cohort->id, $mdluser->id);
                    $cohort = $DB->get_record('cohort', array('idnumber' => "HBON-TVA"), '*', MUST_EXIST);
                    serviceErrorLog("cohort:" . $cohort->idnumber."userid:".$mdluser->id);
                    cohort_add_member($cohort->id, $mdluser->id);
                }
                $docRefUser = $fdb->collection('users');
                $query = $docRefUser->where('email', '==', $mdluser->email);
                $documents = $query->documents();
                serviceErrorLog("msg: ".$classid." find user:".$username);
                $user = array("moodleUserId"=>$mdluser->id,"email"=>$mdluser->email,"firstname"=>$mdluser->firstname,"lastname"=>$mdluser->lastname,"username"=>$mdluser->username,"status"=>0,"school_id"=>$school_id,"displayname"=>$mdluser->firstname." ".$mdluser->lastname);
                if($documents->size()>0){
                    foreach ($documents as $document) {
                        if ($document->exists()) {
                            //found firebase user
                            $uid=$document->id();
                            serviceErrorLog("found user:".$username."-".$uid);
                            $data = $document->data();
                            serviceErrorLog("found data:".json_encode($data));
                            $stuRef = $fdb->collection('students')->document($uid);
                            $stuSnapshot = $stuRef->snapshot();
                            $student = $user;
                            if (!$stuSnapshot->exists()) {
                                //if student not exist
                                serviceErrorLog(" student not exist".$uid);
                                
                                $student["class"]=array("id"=>$classid,"name"=>$classname);
                                $student["products"] = $products;
                                serviceErrorLog(" student:".json_encode($student));
                                $student["code"]=generateStudentCode($fdb);
                                serviceErrorLog(" student:".json_encode($student));
                                $fdb->collection('students')->document($uid)->set($student);
                                //add to classclassId
                                $fdb->collection('classes')->document($classid)->update([["path"=>"students","value"=>FieldValue::arrayUnion([$stuRef])]]);
                                //add to classmember
                                $fdb->collection('classes')->document($classid)->collection("class_members")->document($uid)->set(array("email"=>$data['email'],"displayname"=>$data['displayname'],"username"=>$data['username']));
                                $fdb->collection('student_code')->document($student["code"]["student_code"])->set(array("expired_time"=>$student["code"]["expired_time"],"student_id"=>$uid));

                            } else {
                                serviceErrorLog(" found student ".$uid);
                                $student["class"]=array("id"=>$classid,"name"=>$classname);
                                $student["products"] = $products;
                                $student["code"]=generateStudentCode($fdb);
                                $fdb->collection('students')->document($uid)->update([["path"=>"class","value"=>$student['class']]]);
                                $fdb->collection('students')->document($uid)->update([["path"=>"products","value"=>$student['products']]]);
                                $fdb->collection('students')->document($uid)->update([["path"=>"code","value"=>$student['code']]]);
                                $fdb->collection('classes')->document($classid)->update([["path"=>"students","value"=>FieldValue::arrayUnion([$stuRef])]]);
                                //add to classmember
                                serviceErrorLog(" set classmember:".json_encode($data));
                                $fdb->collection('classes')->document($classid)->collection("class_members")->document($uid)->set(array("email"=>$data['email'],"displayname"=>$data['displayname'],"username"=>$data['username']));
                                $fdb->collection('student_code')->document($student["code"]["student_code"])->set(array("expired_time"=>$student["code"]["expired_time"],"student_id"=>$uid));
                            }
                            updateStudentData($student["moodleUserId"],$uid,$student["code"]["student_code"]);

                            serviceErrorLog("created11:".$mdluser->username);
                            //update role student
                            $fdb->collection('users')->document($uid)->update([["path"=>"role","value"=>"student"]]);
                            $fdb->collection('users')->document($uid)->update([["path"=>"roles","value"=>FieldValue::arrayUnion(["student"])]]);
                        }
                    }
                } else {
                    // create firebase user if not found
                    serviceErrorLog("check email auth:".json_encode($user['email']));

                    try {
                        $providers = $auth->getUserByEmail($user['email']);
                        $uid=$providers->uid;
                        serviceErrorLog("provider:".json_encode($providers));
                    } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
                        $user['password']=$hs_row[6];
                        $fbuser = $auth->createUser($user);
                        serviceErrorLog("fbuser:".json_encode($fbuser));
                        $uid= $fbuser->uid;
                        serviceErrorLog("verify student fbuser:".$uid);
                        $auth->updateUser($uid,array("emailVerified"=>true));
                        unset($user['password']);
                        serviceErrorLog("created firebase auth:".$uid."".$user['email']);
                        
                    } finally {
                        serviceErrorLog("uid firebase auth:".$uid);
                    }

                    

                    $student=$user;
                    //set role student
                    $user["role"] = "student";
                    $user["roles"] = array("student");
                    $docRefUser = $fdb->collection('users')->document($uid)->set($user);
                    
                    $student["class"]=array("id"=>$classid,"name"=>$classname);
                    $student["products"] = $products;
                    $student["code"]=generateStudentCode($fdb);
                    $fdb->collection('students')->document($uid)->set($student);
                    $stuRef = $fdb->collection('students')->document($uid);
                     //add to class
                     $fdb->collection('classes')->document($classid)->update([["path"=>"students","value"=>FieldValue::arrayUnion([$stuRef])]]);
                     //add to classmember
                     $fdb->collection('classes')->document($classid)->collection("class_members")->document($uid)->set(array("email"=>$user['email'],"displayname"=>$user['displayname'],"username"=>$user['email']));
                     $fdb->collection('student_code')->document($student["code"]["student_code"])->set(array("expired_time"=>$student["code"]["expired_time"],"student_id"=>$uid));

                     updateStudentData($student["moodleUserId"],$uid,$student["code"]["student_code"]);
                     serviceErrorLog("created 22:".$mdluser->username);
                }
            }
        }
        //add gv
        $r=0;
        foreach($gv as $gv_row){

            if($r==0) {
                $r++;
                continue;
            };
            $fullname=$gv_row[1];
            $phone=$gv_row[2];
            $subject_raw=$gv_row[3];
            $username=$gv_row[4];
            $password=$gv_row[5];
            serviceErrorLog("search user:".$username."-".$school_id);
            $mdluser =  $DB->get_record("user",array("username"=>$username));
            serviceErrorLog(json_encode($mdluser));
            if($mdluser){
                serviceErrorLog("found:".$mdluser->username);
            } else {
                //create moodle user if not exits
                $user_row[1]="";
                $user_row[2]=$fullname;
                $user_row[3]=null;
                $user_row[4]=null;
                $user_row[5]=$username;
                $user_row[6]=$password;
                serviceErrorLog("moodle user row:".json_encode($user_row));
                $mdluser = self::create_moodle_user($user_row,$school_id);
                
                serviceErrorLog("moodle created:".$mdluser->username);
            }
            $docRefUser = $fdb->collection('users');
            $query = $docRefUser->where('email', '==', $mdluser->email);
            $documents = $query->documents();
            serviceErrorLog("msg: ".$classid." find user:".$username);
            $user = array("moodleUserId"=>$mdluser->id,"email"=>$mdluser->email,"firstname"=>$mdluser->firstname,"lastname"=>$mdluser->lastname,"username"=>$mdluser->username,"status"=>0,"school_id"=>$school_id,"displayname"=>$mdluser->firstname." ".$mdluser->lastname);
            if($documents->size()>0){
                foreach ($documents as $document) {
                    if ($document->exists()) {
                        //found firebase user
                        $uid=$document->id();
                        serviceErrorLog("found teacher user:".$username."-".$uid);
                        $data = $document->data();
                        serviceErrorLog("found teacher data:".json_encode($data));
                        $teracherRef = $fdb->collection('teachers')->document($uid);
                        $teacher = $user;
                        $teacher["subject"]=$subject_raw;
                        if (!$teracherRef->snapshot()->exists()) {
                            //create teacher if not exist
                            serviceErrorLog(" teacher not exist".$uid);
                            $fdb->collection('teachers')->document($uid)->set($teacher);
                            //add to school
                            $fdb->collection('schools')->document($school_id)->update([["path"=>"teachers","value"=>FieldValue::arrayUnion([$teracherRef])]]);
                        } else {
                            serviceErrorLog(" found teacher:".$uid);

                            
                        }

                        $fdb->collection('users')->document($uid)->update([["path"=>"role","value"=>"teacher"]]);
                        $fdb->collection('users')->document($uid)->update([["path"=>"roles","value"=>FieldValue::arrayUnion(["teacher"])]]);

                    }
                }
            } else {
                //create user,teacher
                // create firebase user if not found
                serviceErrorLog("check email auth:".json_encode($user['email']));

                try {
                    $providers = $auth->getUserByEmail($user['email']);
                    $uid=$providers->uid;
                    serviceErrorLog("provider:".json_encode($providers));
                } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
                    $user['password']=$password;
                    $fbuser = $auth->createUser($user);
                    serviceErrorLog("teacher fbuser:".json_encode($fbuser));
                    $uid= $fbuser->uid;
                    serviceErrorLog("verify teacherr fbuser:".$uid);
                    $auth->updateUser($uid,array("emailVerified"=>true));
                    unset($user['password']);
                    serviceErrorLog("created teacher firebase auth:".$uid."".$user['email']);

                    
                } finally {
                    serviceErrorLog("uid firebase auth:".$uid);
                }
                $teacher = $user;
                $user["role"] = "teacher";
                $user["roles"] = array("teacher");
                $docRefUser = $fdb->collection('users')->document($uid)->set($user);
                updateStudentData($mdluser->id,$uid,null);
                $teracherRef = $fdb->collection('teachers')->document($uid);
                
                if (!$teracherRef->snapshot()->exists()) {
                    //create teacher if not exist
                    serviceErrorLog(" teacher not exist".$uid);

                    $teacher["subject"]=$subject_raw;
                    $fdb->collection('teachers')->document($uid)->set($teacher);
                    //add to school
                    $fdb->collection('schools')->document($school_id)->update([["path"=>"teachers","value"=>FieldValue::arrayUnion([$teracherRef])]]);
                } else {
                    serviceErrorLog(" found teacher:".$uid);
                }
                serviceErrorLog("created 22:".$mdluser->username);
            }

        }
        //assiign TODO
        $r=0;
        foreach($assignments as $assignment){
            if($r==0) {
                $r++;
                continue;
            };
            serviceErrorLog("$r assign: ".$assignment[0]." ".json_encode($assignment));
            $classname=$assignment[1];
            $gvcn=$assignment[2];
            $gvtoan1=$assignment[3];
            $gvtoan2=$assignment[4];
            $gvta=$assignment[5];
            $gvnv=$assignment[6];
            $docRef = $fdb->collection('classes');
            $query = $docRef->where('school_id', '==', $school_id)->where('name', '==', $classname)->where("years","==",$params['year']);
            $documents = $query->documents();
            serviceErrorLog("search class:$school_id ".$classname."-".$params['year']);
            if($documents->size()>0){
                
                foreach ($documents as $document) {
                    if ($document->exists()) {
                        $classid=$document->id();
                        $data=$document->data();
                        serviceErrorLog("found class:$classid ".json_encode($data));
                        //assign teacher for each class
                        serviceErrorLog("assign 1: $gvcn for $classid in $school_id".$data['name']);
                        self::insert_assignment($fdb,$gvcn,$classid,$data['name'],"gvcn",$school_id);
                        

                        serviceErrorLog("assign 2: $gvtoan1 for $classid in $school_id".$data['name']);
                        self::insert_assignment($fdb,$gvtoan1,$classid,$data['name'],"toan",$school_id);

                        serviceErrorLog("assign 3: $gvtoan2 for $classid in $school_id".$data['name']);
                        self::insert_assignment($fdb,$gvtoan2,$classid,$data['name'],"toan",$school_id);

                        serviceErrorLog("assign 4: $gvta for $classid in $school_id".$data['name']);
                        self::insert_assignment($fdb,$gvta,$classid,$data['name'],"anh",$school_id);

                        serviceErrorLog("assign 5: $gvnv for $classid in $school_id".$data['name']);
                        self::insert_assignment($fdb,$gvnv,$classid,$data['name'],"van",$school_id);

                    }
                }
            }
            $r++;
        }

    } catch (Exception $e) {
        serviceErrorLog("error:" . json_encode($e->getTrace()));
    }
        //  serviceErrorLog("created user:".gettype($assignment));
         return ["status"=>"success:".$school_id];
    }
    public static function upload_school_returns() {
        return new external_single_structure(
            array(
            'status' => new external_value(PARAM_TEXT, 'return')
            )
        );
    }
    function create_moodle_user($user_row,$school_id){
        global $DB,$CFG;
        
        $class_name=  $user_row[1];
        $fullname=$user_row[2];
        $birthdate=$user_row[3];
        $gender=$user_row[4];
        $username=$user_row[5];
        $user = new stdClass();
         $user->username = $user_row[5];
         $user->password = $user_row[6];
        $arrName = split_name($fullname);
        $user->firstname = $arrName['first_name'];
        $user->lastname = $arrName['last_name'];
        $firstname = strtolower(non_unicode($user->firstname));
        $lastname = strtolower(preg_replace('/\s+/', '', non_unicode($user->lastname)));

        $s = explode("(", $firstname);
        $firstname = trim($s[0]);
        $user->email = $username.'@hocbaionha.com';

        $user->username = core_user::clean_field($user->username, 'username');
        $user->mnethostid = $CFG->mnet_localhost_id;

        if ($existinguser = $DB->get_record('user', array('username' => $user->username, 'mnethostid' => $user->mnethostid))) {
            echo $existinguser->id . " user existed";
            return;
        }
        $user->confirmed = 1;
        $user->timemodified = time();
        $user->timecreated = time();
        $user->profile_field_type = "student";
        $user->profile_field_birthdate = $birthdate;
        $user->profile_field_gender = $gender;
        $user->profile_field_schoolid=$school_id;
        $user->profile_field_classid=$class_name;

        if (!isset($user->suspended) or $user->suspended === '') {
            $user->suspended = 0;
        } else {
            $user->suspended = $user->suspended ? 1 : 0;
        }

        if (empty($user->auth)) {
            $user->auth = 'manual';
        }
        // do not insert record if new auth plugin does not exist!
        try {
            $auth = get_auth_plugin($user->auth);
        } catch (Exception $e) {
            echo 'userautherror:' . $user->auth;
            return;
        }

        $isinternalauth = $auth->is_internal();

        if (empty($user->email)) {
            echo 'invalidemail:' . $user->email;
            return;
        } else if ($DB->record_exists('user', array('email' => $user->email))) {
            echo 'duplicateemail:' . $user->email;
            return;
        }
        if (!validate_email($user->email)) {
            $user->email = non_unicode($user->email);
            if (!validate_email($user->email)) {
                echo 'invalidemail:' . $user->email;
                return;
            }
        }

        if (empty($user->lang)) {
            $user->lang = '';
        } else if (core_user::clean_field($user->lang, 'lang') === '') {
            echo 'cannotfindlang:' . $user->lang;
            $user->lang = '';
        }

        if ($isinternalauth) {

            $user->password = hash_internal_user_password($user->password, true);
        } else {
            $user->password = AUTH_PASSWORD_NOT_CACHED;
        }
        serviceErrorLog("check:".json_encode($user));
        self::insertUser($user);
        serviceErrorLog("inserted");
        
        $validation[$user->username] = core_user::validate($user);
        if (!empty($validation)) {
            foreach ($validation as $username => $result) {
                if ($result !== true) {
                    \core\notification::warning(get_string('invaliduserdata', 'tool_uploaduser', s($username)));
                }
            }
        }
        return $user;
    }

    function insertUser($user) {
        serviceErrorLog("start insert:".json_encode($user));
        $user->id = user_create_user($user, false, false);

        // pre-process custom profile menu fields data from csv file
        $user = uu_pre_process_custom_profile_data($user);
        // save custom profile fields data
        profile_save_data($user);
        //force change password
        set_user_preference('auth_forcepasswordchange', 1, $user);

        if ($user->password === 'to be generated') {
            set_user_preference('create_password', 1, $user);
        }
        serviceErrorLog("create:".json_encode($user));
        // Trigger event.
        \core\event\user_created::create_from_userid($user->id)->trigger();
        serviceErrorLog("make sure done:".json_encode($user));
        // make sure user context exists
        context_user::instance($user->id);
    }

    private function insert_assignment($fdb,$fullname,$classid,$classname,$subject,$school_id){
        serviceErrorLog("=>>>>start insert assignment $classid,$classname,$subject,$school_id: ".$fullname);
        $arrName = split_name($fullname);
        $query = $fdb->collection('teachers')->where("firstname","==",$arrName['first_name'])->where("lastname","==",$arrName['last_name'])->where("school_id","==",$school_id);
        $documents = $query->documents();
        serviceErrorLog("search teacher by name: ".$fullname);
        if($documents->size()>0){
            serviceErrorLog("found teachername");
            foreach ($documents as $document) {
                if ($document->exists()) {
                    
                    $data = $document->data();
                    $teacherid=$document->id();
                    $teracherRef = $fdb->collection('teachers')->document($teacherid);
                    serviceErrorLog("assigning teacher $teacherid: ".json_encode($data));
                    // $teracherRef->update([
                    //     ['path' => 'classes', 'value' => FieldValue::deleteField()]
                    // ]);
                    if(array_key_exists("classes",$data)){
                        $classes = $data['classes'];
                        $rolefound=0;
                        foreach($classes as $key=>$class){
                            serviceErrorLog("found class: ".json_encode($class));
                            serviceErrorLog("=01");
                            if($class['id']==$classid){
                                if(array_key_exists("roles",$class)){
                                    serviceErrorLog("=02");
                                    $roles = $class['roles'];
                                    $roles = array_unique(array_merge($roles,array($subject)));
                                    $classes[$key]['roles'] = $roles;
                                    $rolefound=1;
                                    serviceErrorLog("=1");
                                }
                            }
                        }
                        serviceErrorLog("=2");
                        if($rolefound==0){
                            serviceErrorLog("=3");
                            $newclass = array("id"=>$classid,"name"=>$classname,"roles"=>array($subject));
                            $classes = array_unique(array_merge($classes,$newclass));
                        }
                    } else {
                        serviceErrorLog("=6 ".$teacherid);
                        $classes = array("id"=>$classid,"name"=>$classname,"roles"=>array($subject));
                        $fdb->collection('teachers')->document($teacherid)->update([["path"=>"classes","value"=>array($classes)]]);
                        serviceErrorLog("=7");
                    }
                    serviceErrorLog("=8");
                    $fdb->collection('classes')->document($classid)->update([["path"=>"teachers","value"=>FieldValue::arrayUnion([$teracherRef])]]);
                    serviceErrorLog("=9");
                    if($subject=="gvcn"){
                        serviceErrorLog("=10");
                        $fdb->collection('classes')->document($classid)->update([["path"=>"gvcn","value"=>$teacherid]]);
                    }
                   
                }
            }
        } else {
            serviceErrorLog(" not found teacher:".$arrName['last_name']." ".$arrName['first_name']);
        }
        serviceErrorLog("======== done:".$fullname);
    }
}
