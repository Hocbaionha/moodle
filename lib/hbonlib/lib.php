<?php

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../../admin/tool/uploaduser/locallib.php');
require_once($CFG->dirroot . '/group/lib.php');
require_once($CFG->dirroot . '/cohort/lib.php');
require_once($CFG->dirroot . '/user/lib.php');

function getProvinces() {
    global $DB;
    $provinces = $DB->get_records("province", null); //getProvince();
    return $provinces;
}

function getProvince($provinceid) {
    global $DB;
    return $DB->get_record("province", array('provinceid' => $provinceid));
}

function getDistrict($districtid) {
    global $DB;
    return $DB->get_record("district", array("districtid" => $districtid));
}

function getDistrictId($districtid) {
    global $DB;
    $district = $DB->get_record("district", array('districtid' => $districtid));

    return $district->provinceid;
}

function getDistricts($provinceid = null) {
    global $DB;
    if ($provinceid)
        $districts = $DB->get_records("district", array('provinceid' => $provinceid));
    else
        $districts = $DB->get_records("district");
    return $districts;
}

function getSchools($districtid = null, $approve = 1) {
    global $DB;
    if (empty($districtid)) {
        $sql = "select * from mdl_school where approve=? and code!='hbon'";
        $schools = $DB->get_records_sql($sql, array("approve" => $approve));
    } else {
        $sql = "select * from mdl_school where districtid=? approve=? and code!='hbon'";
        $schools = $DB->get_records_sql($sql, array("districtid" => $districtid, "approve" => $approve));
    }
    return $schools;
}

function getClasses($schoolid) {
    global $DB;
    $classes = $DB->get_records("class", array('schoolid' => $schoolid));
    return $classes;
}

function getDataSchools($schoolid) {
    global $DB;
    $school = $DB->get_record("school", array("id" => $schoolid));
    if (!$school) {
        return false;
    } else {
        $school->district = $DB->get_record("district", array("districtid" => $school->districtid));
        $school->province = $DB->get_record("province", array("provinceid" => $school->district->provinceid));
    }
    return $school;
}

function getDepartments() {
    global $DB;

    $departments = $DB->get_records("departments");
    $selectArray = array("" => "");
    foreach ($departments as $de) {
        $key = $de->code;
        $value = $de->name;
        $selectArray[$key] = $value;
    }
    return $selectArray;
}

function add_teacher_to_core($data) {
    global $DB, $CFG;
    $tx = $DB->start_delegated_transaction();
    $schoolid = $data->school;
    $fullname = $data->name;

    $department = $data->department;
    if ($data->department == "Toán") {
        $department = "math";
    } else if ($data->department == "Ngữ Văn") {
        $department = "literature";
    } else if ($data->department == "Tiếng Anh") {
        $department = "english";
    }
    $phone = $data->phone;
    $sql = "SELECT p.name as pname,d.name as dname from mdl_school s join mdl_district d on d.districtid=s.districtid join mdl_province p on p.provinceid=d.provinceid where s.id=$schoolid";
    $result = $DB->get_record_sql($sql);
    if (!$result) {
        return false;
    }
    $user = new stdClass();

    $arrName = split_name($fullname);
    $user->firstname = $arrName['first_name'];
    $user->lastname = $arrName['last_name'];

    $tid = $DB->get_record("school", array("id" => $schoolid))->last_teacher;
    $firstname = strtolower(non_unicode($user->firstname));
    $lastname = strtolower(preg_replace('/\s+/', '', non_unicode($user->lastname)));
    $stt = sprintf("%02d", ++$tid);
    $school = $DB->get_record("school", array("id" => $schoolid));
    $sname = strtolower(non_unicode(preg_replace('/\s+/', '', $school->name)));
    $user->username = $school->code . "-gv" . $stt;
    $user->email = "gv" . $stt . "-" . $lastname . "-" . $firstname . "@" . $school->code . ".edu.vn";
    $user->username = core_user::clean_field($user->username, 'username');
    $user->mnethostid = $CFG->mnet_localhost_id;
    if ($existinguser = $DB->get_record('user', array('username' => $user->username, 'mnethostid' => $user->mnethostid))) {
        echo $existinguser->id . " user existed" . $user->username . "<br/>";
        return false;
    }

    $user->profile_field_schoolid = $schoolid;
    $user->profile_field_type = "gvbm";
    $user->profile_field_department = $department;
    $user->profile_field_phone = $phone;

    $user->password = $data->password;
    $user->confirmed = 1;
    $user->timemodified = time();
    $user->timecreated = time();
    $user->country = "VN";
    $user->lang = "vi";
    if (empty($user->auth)) {
        $user->auth = 'manual';
    }

    // do not insert record if new auth plugin does not exist!
    try {
        $auth = get_auth_plugin($user->auth);
    } catch (Exception $e) {
        echo "auth error " . $user->auth;
        return false;
    }

    $isinternalauth = $auth->is_internal();

    if (empty($user->email)) {
        echo get_string('invalidemail') . $user->email;
        return false;
    } else if ($DB->record_exists('user', array('email' => $user->email))) {
        echo get_string('emailduplicate:') . $stremailduplicate;
        return false;
    }
    if (!validate_email($user->email)) {
        $user->email = non_unicode($user->email);
        if (!validate_email($user->email)) {
            echo 'invalidemail:' . $user->email;
            return false;
        }
    }

    if (core_user::clean_field($user->lang, 'lang') === '') {
        echo 'cannotfindlang' . $user->lang;
        $user->lang = '';
    }


    if ($isinternalauth) {
        $user->password = hash_internal_user_password($user->password, true);
    } else {
        $user->password = AUTH_PASSWORD_NOT_CACHED;
    }
    $result = insertUser($user);
    $cohortgv = array("GV-" . $school->cohort_code, $school->cohort_code, "Paid-User", "Trial-User");
    insertCohort($cohortgv, $user->id);

    if ($school->code == "hbon") {
        global $ccache, $roleid;
        global $manualcache;
        global $rolecache;
        global $manual;
        $ccache = array();
        $manualcache = array();
        $rolecache = uu_allowed_roles_cache();
        $manual = enrol_get_plugin('manual');
        $rolename = "hbonteacher";
        if (array_key_exists($rolename, $rolecache)) {
            $roleid = $rolecache[$rolename]->id;
//        echo "found roleid:" . $roleid;
        } else {
            echo 'unknownrole:' . $rolename;
            $tx->rollback(new Exception('unknow role'));
            die;
        }
        $sql = "select c.code from mdl_class c join mdl_school s on s.id=c.schoolid where s.code='hbon'";
        $classes = $DB->get_records_sql($sql);
        foreach ($classes as $class) {
            $class_code = $class->code;
            $grade = $class_code;
            $group_name = $class_code . "-" . $school->group_code;
            if ($department == "math") {
                if ($grade == 6)
                    insertCourse("SH6", $group_name, $user->id);
                else
                    insertCourse("DS" . $grade, $group_name, $user->id);
                insertCourse("HH" . $grade, $group_name, $user->id);
            }
            if ($department == "english") {
                insertCourse("TA" . $grade, $group_name, $user->id);
            }
            if ($department == "literature") {
                insertCourse("NV" . $grade, $group_name, $user->id);
            }
        }
    }
    if ($result) {
        $school->last_teacher = $stt;
        $DB->update_record("school", $school);
    }
    $tx->allow_commit();
    return $result;
}

function add_student_to_core($student) {

    global $DB, $CFG;
    global $ccache, $roleid;
    global $manualcache;
    global $rolecache;
    global $manual;
    $ccache = array();
    $manualcache = array();
    $rolecache = uu_allowed_roles_cache();
    $manual = enrol_get_plugin('manual');
    $schoolid = $student->school;
    $fullname = $student->name;

    $sql = "SELECT p.name as pname,d.name as dname from mdl_school s join mdl_district d on d.districtid=s.districtid join mdl_province p on p.provinceid=d.provinceid where s.id=$schoolid";
    $result = $DB->get_record_sql($sql);
    if (!$result) {
        return false;
    }
    $user = new stdClass();
    $arrName = split_name($fullname);
    $user->firstname = $arrName['first_name'];
    $user->lastname = $arrName['last_name'];


    $sid = $DB->get_record("school", array("id" => $schoolid))->last_student;
    $firstname = strtolower(non_unicode($user->firstname));
    $lastname = strtolower(preg_replace('/\s+/', '', non_unicode($user->lastname)));
    $stt = sprintf("%02d", ++$sid);

    $school = $DB->get_record("school", array("id" => $schoolid));
    $sname = strtolower(non_unicode(preg_replace('/\s+/', '', $school->name)));

    $user->username = $school->code . "-hs" . $stt;
    $user->password = rand_string(4);

    $user->email = "hs" . $stt . "-" . $lastname . "-" . $firstname . "@" . $school->code . ".edu.vn";

    $user->username = core_user::clean_field($user->username, 'username');
    $user->mnethostid = $CFG->mnet_localhost_id;
    if ($existinguser = $DB->get_record('user', array('username' => $user->username, 'mnethostid' => $user->mnethostid))) {
        echo $existinguser->id . " user existed";
        return false;
    }
    $user->confirmed = 1;
    $user->timemodified = time();
    $user->timecreated = time();
    $user->profile_field_classid = $student->classid;
    $user->profile_field_schoolid = $student->schoolid;
    $user->profile_field_type = "student";
    $user->profile_field_gender = $student->gender;
    $user->profile_field_birthdate = $student->birthdate;
    $user->profile_field_parent = $student->parent;
    $user->profile_field_parentphone = $student->parentphone;


    $user->country = "VN";
    $user->lang = "vi";
    if (empty($user->auth)) {
        $user->auth = 'manual';
    }

    // do not insert record if new auth plugin does not exist!
    try {
        $auth = get_auth_plugin($user->auth);
    } catch (Exception $e) {
        echo 'userautherror:' . $user->auth;
        return false;
    }

    $isinternalauth = $auth->is_internal();

    if (empty($user->email)) {
        echo 'invalidemail:' . $user->email;
        return false;
    } else if ($DB->record_exists('user', array('email' => $user->email))) {
        echo 'duplicateemail:' . $user->email;
        return false;
    }
    if (!validate_email($user->email)) {
        $user->email = non_unicode($user->email);
        if (!validate_email($user->email)) {
            echo 'invalidemail:' . $user->email;
            return false;
        }
    }
    if (core_user::clean_field($user->lang, 'lang') === '') {
        echo 'cannotfindlang:' . $user->lang;
        $user->lang = '';
    }

    if ($isinternalauth) {

        $user->password = hash_internal_user_password($user->password, true);
    } else {
        $user->password = AUTH_PASSWORD_NOT_CACHED;
    }
    $result = insertUser($user);

    if ($result) {
        $school->last_student = $stt;
        $DB->update_record("school", $school);
    }
    $cohorths = array("HS-" . $school->cohort_code, $school->cohort_code, "Paid-User", "Trial-User");
    insertCohort($cohorths, $user->id);
    $rolename = "student";
    if (array_key_exists($rolename, $rolecache)) {
        $roleid = $rolecache[$rolename]->id;
//        echo "found roleid:" . $roleid;
    } else {
        echo 'unknownrole' . $rolename;
        die;
    }
    $class_code = $DB->get_record("class", array("id" => $student->classid))->code;
    $group_name = $class_code . "-" . $school->group_code;
    $grade = $class_code[0];

    if ($grade == 6)
        insertCourse("SH6", $group_name, $user->id);
    else
        insertCourse("DS" . $grade, $group_name, $user->id);
    insertCourse("HH" . $grade, $group_name, $user->id);

    insertCourse("TA" . $grade, $group_name, $user->id);

    insertCourse("NV" . $grade, $group_name, $user->id);
    return $result;
}

function insertUser($user) {
    global $DB;

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
    return true;
}

function insertCourse($shortname, $group_name, $userid) {
    global $DB;
    global $ccache;
    global $manualcache;
    global $rolecache;
    global $manual;
    global $roleid;
    if (!array_key_exists($shortname, $ccache)) {
        if (!$course = $DB->get_record('course', array('shortname' => $shortname), 'id, shortname')) {
            echo 'unknowncourse' . $shortname;
        }
        $ccache[$shortname] = $course;
        $ccache[$shortname]->groups = null;
    }
    $courseid = $ccache[$shortname]->id;
    $coursecontext = context_course::instance($courseid);
    if (!isset($manualcache[$courseid])) {
        $manualcache[$courseid] = false;
        if ($manual) {
            if ($instances = enrol_get_instances($courseid, false)) {
                foreach ($instances as $instance) {
                    if ($instance->enrol === 'manual') {
                        $manualcache[$courseid] = $instance;
                        break;
                    }
                }
            }
        }
    }

    if ($roleid) {
        // Find duration and/or enrol status.
        $timeend = 0;

        $today = time();
        $timestart = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);

        $status = null;
        if ($manualcache[$courseid]->enrolperiod > 0) {
            $timeend = $timestart + $manualcache[$courseid]->enrolperiod;
        }
        $manual->enrol_user($manualcache[$courseid], $userid, $roleid, $timestart, $timeend, $status);

        $a = new stdClass();
        $a->course = $shortname;
        $a->role = $rolecache[$roleid]->name;
    }
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

function update_user($user) {
    global $DB;

    // Set the timecreate field to the current time.
    if (!is_object($user)) {
        $user = (object) $user;
    }

    // Check username.
    if (isset($user->username)) {
        if ($user->username !== core_text::strtolower($user->username)) {
            throw new moodle_exception('usernamelowercase');
        } else {
            if ($user->username !== core_user::clean_field($user->username, 'username')) {
                throw new moodle_exception('invalidusername');
            }
        }
    }

    // Make sure calendartype, if set, is valid.
    if (empty($user->calendartype)) {
        // Unset this variable, must be an empty string, which we do not want to update the calendartype to.
        unset($user->calendartype);
    }

    $user->timemodified = time();

    // Validate user data object.
    $uservalidation = core_user::validate($user);
    if ($uservalidation !== true) {
        foreach ($uservalidation as $field => $message) {
            debugging("The property '$field' has invalid data and has been cleaned.", DEBUG_DEVELOPER);
            $user->$field = core_user::clean_field($user->$field, $field);
        }
    }

    $DB->update_record('user', $user);
}

function update_teacher_group($data) {

    global $DB, $CFG;
    global $ccache, $roleid;
    global $manualcache;
    global $rolecache;
    global $manual;
    $ccache = array();
    $manualcache = array();
    $rolecache = uu_allowed_roles_cache();
    $manual = enrol_get_plugin('manual');
    $rolename = "hbonteacher";
    if (array_key_exists($rolename, $rolecache)) {
        $roleid = $rolecache[$rolename]->id;
//        echo "found roleid:" . $roleid;
    } else {
        echo 'unknownrole:' . $rolename;
        $tx->rollback(new Exception('unknow role'));
        die;
    }
    $DB->delete_records('groups_members', array('userid' => $data->id));
    $DB->delete_records('user_enrolments', array('userid' => $data->id));

    $school = $DB->get_record("school", array("id" => $data->schoolid));
    $sql = "select c.code from mdl_class c join mdl_school s on s.id=c.schoolid where s.code='hbon'";
    $classes = $DB->get_records_sql($sql);
    $department = $data->department;
    
    foreach ($classes as $class) {
        $class_code = $class->code;
        $grade = $class_code;
        $group_name = $class_code . "-" . $school->group_code;
        if ($department == "math") {
            if ($grade == 6)
                insertCourse("SH6", $group_name, $data->id);
            else
                insertCourse("DS" . $grade, $group_name, $data->id);
            insertCourse("HH" . $grade, $group_name, $data->id);
        }
        if ($department == "english") {
            insertCourse("TA" . $grade, $group_name, $data->id);
        }
        if ($department == "literature") {
            insertCourse("NV" . $grade, $group_name, $data->id);
        }
    }
}
function getFileFromStorage( $filearea, $args, $forcedownload, array $options=array()) {
 
    // Make sure the filearea is one of those used by the plugin.
//    if ($filearea !== 'expectedfilearea' && $filearea !== 'anotherexpectedfilearea') {
//        return false;
//    }
 
    // Make sure the user is logged in and has access to the module (plugins that are not course modules should leave out the 'cm' part).
//    require_login($course, true, $cm);
 
    // Check the relevant capabilities - these may vary depending on the filearea being accessed.
//    if (!has_capability('mod/MYPLUGIN:view', $context)) {
//        return false;
//    }
 
    // Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
//    $itemid = array_shift($args); // The first item in the $args array.
 
    // Use the itemid to retrieve any relevant data records and perform any security checks to see if the
    // user really does have access to the file in question.
 
    // Extract the filename / filepath from the $args array.
//    $filename = array_pop($args); // The last item in the $args array.
    if (!$args) {
        $filepath = '/'; // $args is empty => the path is '/'
    } else {
        $filepath = '/'.implode('/', $args).'/'; // $args contains elements of the filepath
    }
//print_object($args);die; 
    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($args['contextid'], $args['component'], $args['filearea'], $args['itemid'], $args['filepath'], $args['filename']);
//var_dump($file);die;
    if (!$file) {
        return false; // The file does not exist.
    }
 
    // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering. 

    send_stored_file($file, 86400, 0, $forcedownload, $options);
}