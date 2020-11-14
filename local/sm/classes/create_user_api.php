<?PHP
require dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php';
require_once($CFG->dirroot . "/cohort/lib.php");
require_once($CFG->dirroot . "/lib/externallib.php");
require_once $CFG->libdir . '/hbonlib/string_util.php';
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/group/lib.php');
require_once(__DIR__ . '/../../../admin/tool/uploaduser/locallib.php');

use Kreait\Firebase\Factory;
use Kreait\Firebase\Firestore;

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
                        serviceErrorLog("cohort:" . $cohort."userid:".$userid);
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
                    ))
            )
        );
    }
    public static function upload_school($params){
        serviceErrorLog("start...");
        global $DB;
        $hsnum=0;
        $gvnum=0;
         $school_id  = $params["school_id"];
         echo $school_id;
         serviceErrorLog("start upload_school:".$school_id);
         die;
         $hs = json_decode($params["hs"]);
         $gv = json_decode($params["gv"]);
         $assignments = json_decode($params["assignment"]);
         //check school_id
         serviceErrorLog("start upload_school:");
         try{
         $sql = "select count(*) from mdl_user where email like '?%'";
         serviceErrorLog("step a:");
         $school_member = $DB->count_records_sql($sql,array("email"=>$school_id ));
         serviceErrorLog("step b:");
         if($school_member>0){
            $sql = "select max(email) from mdl_user where email like '?%'";
            $maxhs = $DB->count_records_sql($sql,array("start"=>$school_id."-hs" ));
            if($maxhs){
                $first = explode("@",$maxhs)[0];
                $number = str_replace($maxhs,"",$first);
                $hsnum = (int) $number;
            }
            $sql = "select max(email) from mdl_user where email like '?%'";
            $maxgv = $DB->count_records_sql($sql,array("start"=>$school_id."-gv" ));
            if($maxgv){
                $first = explode("@",$maxhs)[0];
                $number = str_replace($maxhs,"",$first);
                $gvnum = (int) $number;
            }
         }
         serviceErrorLog("step 1:");
        // foreach($hs as $hs_row){
        //     $hsnum++;
        //     self:create_student($hs_row,$hsnum,$school_id);

        // }
        $arrgv = array();
        foreach($gv as $gv_row){
            $arrgv[$gv_row[1]] = array("phone"=>$gv_row[2]);
        }
        serviceErrorLog("step 2:");
        serviceErrorLog("1111111:".json_encode($arrgv));
        foreach($assignments as $assignment){
            $class_code = $assignment[1];
            $group_name = $class->code . "-" . $school_id;
            $grade = $class->code[0];
            for($i=2;$i<6;$i++){
                $teachername = $assignment[$i];
                $arrgv[$teachername]["groupname"] = $group_name;
                $arrgv[$teachername]["grade"] = $grade;
                if($i==2){
                    $arrgv[$teachername]["rolename"]="gvcn";
                } else {
                    $arrgv[$teachername]["rolename"]="gvbm";
                }
                insertTeacher($arrgv);
            }
        }
    } catch (Exception $e) {
        serviceErrorLog("error:" . json_encode($e->getTrace()));
    }
         serviceErrorLog("created user:".gettype($assignment));
         return ["status"=>"success:".$firebase_uid];
    }
    public static function upload_school_returns() {
        return new external_single_structure(
            array(
            'status' => new external_value(PARAM_TEXT, 'return')
            )
        );
    }
    function create_student($hs_row,$hsnum,$school_id){
        $class_code = $hs_row[1];
        $fullname = $hs_row[2];
        $birthdate = $hs_row[3];
        $phone = $hs_row[4];


    $cohortbgh = array($school_id,"HBON-TVA" ,"Trial-User");
    $cohortgv = array("GV-" . $school_id, $school_id,"HBON-TVA" , "Trial-User");
    $cohorths = array("HS-" . $school_id, $school_id,"HBON-TVA" , "Trial-User");

        $user = new stdClass();
         $user->username = $school_id . "-hs" . $hsnum;
         $user->password = rand_string(4);
        $arrName = split_name($fullname);
        $user->firstname = $arrName['first_name'];
        $user->lastname = $arrName['last_name'];
        $firstname = strtolower(non_unicode($user->firstname));
        $lastname = strtolower(preg_replace('/\s+/', '', non_unicode($user->lastname)));

        $s = explode("(", $firstname);
        $firstname = trim($s[0]);
        $user->email = "hs" . $hsnum . "-" . $lastname . "-" . $firstname . "@" . $school_id . ".edu.vn";

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
        $user->profile_field_phone = $phone;

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
        insertUser($user);
        insertCohort($cohorths, $user->id);
        $group_name = $class_code . "-" . $school_id;
        $grade = $class_code[0];
        if ($grade == 6)
            insertCourse("SH6", $group_name, $user->id);
        else
            insertCourse("DS" . $grade, $group_name, $user->id);
        insertCourse("HH" . $grade, $group_name, $user->id);

        insertCourse("TA" . $grade, $group_name, $user->id);

        insertCourse("NV" . $grade, $group_name, $user->id);
        
        $validation[$user->username] = core_user::validate($user);
        if (!empty($validation)) {
            foreach ($validation as $username => $result) {
                if ($result !== true) {
                    \core\notification::warning(get_string('invaliduserdata', 'tool_uploaduser', s($username)));
                }
            }
        }
        serviceErrorLog("create_student error:" . json_encode($user));
    }

    function insertUser($user) {

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

        // Trigger event.
        \core\event\user_created::create_from_userid($user->id)->trigger();

        // make sure user context exists
        context_user::instance($user->id);
    }

    function insertCohort($cohorts, $userid) {
        global $DB;
        foreach ($cohorts as $addcohort) {
            if (is_number($addcohort)) {
                // only non-numeric idnumbers!
                $cohort = $DB->get_record('cohort', array('id' => $addcohort));
            } else {
                $cohort = $DB->get_record('cohort', array('idnumber' => $addcohort));
                if (empty($cohort) && has_capability('moodle/cohort:manage', context_system::instance())) {
                    // Cohort was not found. Create a new one.
                    $cohortid = cohort_add_cohort((object) array(
                                'idnumber' => $addcohort,
                                'name' => $addcohort,
                                'contextid' => context_system::instance()->id
                    ));
                    $cohort = $DB->get_record('cohort', array('id' => $cohortid));
                }
            }

            if (empty($cohort)) {
                $cohort = get_string('unknowncohort', 'core_cohort', s($addcohort));
            } else if (!empty($cohort->component)) {
                // cohorts synchronised with external sources must not be modified!
                $cohort = get_string('external', 'core_cohort');
            }
            if (is_object($cohort)) {
                if (!$DB->record_exists('cohort_members', array('cohortid' => $cohort->id, 'userid' => $userid))) {
                    cohort_add_member($cohort->id, $userid);
                }
            } else {
                // error message
                echo 'enrolments error' . $cohorts[$addcohort];
            }
        }
    }
    function insertTeacher($teacher){
        serviceErrorLog("created user:".json_encode($teacher));
        print_object($teacher);die;
        $user = new stdClass();
        $isBgh = false;

        $arrName = split_name($teacher->name);
        $user->firstname = $arrName['first_name'];
        $user->lastname = $arrName['last_name'];

        $firstname = strtolower(non_unicode($user->firstname));
        $lastname = strtolower(preg_replace('/\s+/', '', non_unicode($user->lastname)));

        if (strcasecmp($teacher->department, "Ban Giám Hiệu") == 0) {
            $user->username = $school->code . "-bgh";
            $user->email = "bgh@" . $school->code . ".edu.vn";
            $user->firstname = "BGH";
            $user->lastname = $school->name;
            $isBgh = true;
        } else {
            $tid++;
            $stt = sprintf("%02d", $tid);
            $user->username = $school->code . "-gv" . $stt;
            $s = explode("(", $firstname);
            $firstname = trim($s[0]);
            $user->email = "gv" . $stt . "-" . $lastname . "-" . $firstname . "@" . $school->code . ".edu.vn";
        }
        $teacher->username = $user->username;
        $user->username = core_user::clean_field($user->username, 'username');
        $user->mnethostid = $CFG->mnet_localhost_id;
        if ($existinguser = $DB->get_record('user', array('username' => $user->username, 'mnethostid' => $user->mnethostid))) {
            echo $existinguser->id . " user existed" . $user->username . "<br/>";
            return;
        }
        $user->password = rand_string(4);
        $teacher->password = $user->password;

        $user->profile_field_schoolid = $teacher->schoolid;
        $user->profile_field_type = "gvbm";
        if ($isBgh) {
            $user->profile_field_type = "bgh";
        }
        $user->profile_field_department = $teacher->department;
        $user->profile_field_phone = $teacher->phone;
        $user->confirmed = 1;
        $user->timemodified = time();
        $user->timecreated = time();
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
            echo "auth error " . $user->auth;
            return;
        }

        $isinternalauth = $auth->is_internal();

        if (empty($user->email)) {
            echo get_string('invalidemail') . $user->email;
            return;
        } else if ($DB->record_exists('user', array('email' => $user->email))) {
            echo get_string('emailduplicate:') . $stremailduplicate;
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
            echo 'cannotfindlang' . $user->lang;
            $user->lang = '';
        }


        if ($isinternalauth) {
            $user->password = hash_internal_user_password($user->password, true);
        } else {
            $user->password = AUTH_PASSWORD_NOT_CACHED;
        }
        insertUser($user);
        if ($isBgh) {
            insertCohort($cohortbgh, $user->id);
        } else {
            insertCohort($cohortgv, $user->id);
        }
    }
}
