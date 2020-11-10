<?PHP
require dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php';
require_once($CFG->dirroot . "/cohort/externallib.php");
require_once($CFG->dirroot . "/lib/externallib.php");

use Kreait\Firebase\Factory;
use Kreait\Firebase\Firestore;

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
        $factory = (new Factory)->withServiceAccount(dirname(dirname(__DIR__)) . '/firebasekey.json');
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
}