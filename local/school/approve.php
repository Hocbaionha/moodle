<?php

require(__DIR__ . '/../../config.php');
require_once $CFG->libdir . '/hbonlib/string_util.php';
require_once('download.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/group/lib.php');
require_once($CFG->dirroot . '/cohort/lib.php');
require_once(__DIR__ . '/../../admin/tool/uploaduser/locallib.php');
require_once("$CFG->libdir/phpspreadsheet/vendor/autoload.php");
require_login();
set_time_limit(300);

$time_start = microtime(true);
$context = context_system::instance();
require_capability('local/school:write', $context);

$url = new moodle_url('/local/school/class.php');
$PAGE->set_context($context);
$PAGE->set_url($url);


if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow","local_school");
    echo $OUTPUT->footer();die;
}
$schoolid = optional_param('schoolid', 0, PARAM_INT);
if ($schoolid != 0) {
    $school = $DB->get_record('school', array('id' => $schoolid));
    if (empty($school)) {
        echo "School not found";
        die;
    }
} else {
    echo "School not found";
    die;
}


if (ob_get_level() == 0)
    ob_start();
if ($schoolid > 0) {
    $school = $DB->get_record("school", array("id" => $schoolid));

    $sql = "SELECT p.name as pname,d.name as dname from mdl_school s join mdl_district d on d.districtid=s.districtid join mdl_province p on p.provinceid=d.provinceid where s.id=$schoolid";
    $result = $DB->get_record_sql($sql);
    $country = "VN";
    $lang = "vi";

    $today = time();
    $today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);

    $sname = strtolower(non_unicode(preg_replace('/\s+/', '', $school->name)));
    $cohortbgh = array($school->cohort_code, "Paid-User", "Trial-User");
    $cohortgv = array("GV-" . $school->cohort_code, $school->cohort_code, "Paid-User", "Trial-User");
    $cohorths = array("HS-" . $school->cohort_code, $school->cohort_code, "Paid-User", "Trial-User");
    $ccache = array();
    $manualcache = array();
    $rolecache = uu_allowed_roles_cache();
    $manual = enrol_get_plugin('manual');
    $sid = $school->last_student;
    $allclasses = $DB->get_records("class", array("schoolid" => $schoolid));
    $total = count($allclasses);
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $fullpath = __DIR__ . '/upload/' . $school->cohort_code . ".xlsx";
    $studentSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Danh sách học sinh');

    $tid = insertTeacher($DB, $school);

    $i = 0;
    $bgh = $DB->get_record("temp_teacher", array("schoolid" => $schoolid, "department" => "Ban Giám Hiệu"));
    $userbgh = false;
    if ($bgh)
        $userbgh = $DB->get_record("user", array("username" => $bgh->username));
    foreach ($allclasses as $class) {
        $i++;
        $percent = intval($i / $total * 100) . "%";
        $tcs = $DB->get_records("temp_teacher_class", array("classid" => $class->id));

        $group_name = $class->code . "-" . $school->group_code;
        $grade = $class->code[0];
        foreach ($tcs as $tc) {
            $teacher = $DB->get_record("temp_teacher", array('id' => $tc->teacher_id, 'approve' => 1));
            if ($teacher) {
                //enrole only if had inserted teacher 
                $user = $DB->get_record("user", array("username" => $teacher->username));
                if ($user) {
                    if ($tc->type == 1) {
                        $user->profile_field_type = "gvcn";
                        $user->profile_field_classid = $group_name;
                        $rolename = "gvcn";
                        $user = uu_pre_process_custom_profile_data($user);
                        profile_save_data($user);
                    } else {
                        $rolename = "gvbm";
                    }
                    if (array_key_exists($rolename, $rolecache)) {
                        $roleid = $rolecache[$rolename]->id;
                    } else {
                        echo 'unknownrole' . $rolename;
                        die;
                    }
                    // insert course if existed
                    $gid = null;
                    if ($tc->type == 2 || $tc->type == 1) {//algebra
                        if ($grade == 6)
                            $gid = insertCourse("SH6", $group_name, $user->id);
                        else
                            $gid = insertCourse("DS" . $grade, $group_name, $user->id);
                    }
                    if ($tc->type == 3 || $tc->type == 1) { //geometry
                        $gid = insertCourse("HH" . $grade, $group_name, $user->id);
                    }
                    if ($tc->type == 4 || $tc->type == 1) {//english
                        $gid = insertCourse("TA" . $grade, $group_name, $user->id);
                    }
                    if ($tc->type == 5 || $tc->type == 1) {
                        $gid = insertCourse("NV" . $grade, $group_name, $user->id);
                    }

                    $tc_assignment = new stdClass();
                    $tc_assignment->classid = $class->id;
                    $tc_assignment->groupid = $gid;
                    $tc_assignment->userid = $user->id;
                    $tc_assignment->type = $tc->type;
                    $DB->insert_record("teacher_class", $tc_assignment);
                }
            }
        }
        $rolename = "bgh";
        if (array_key_exists($rolename, $rolecache)) {
            $roleid = $rolecache[$rolename]->id;
        } else {
            echo 'unknownrole' . $rolename;
            die;
        }
        // insert bgh if existed
        if ($userbgh) {
            if ($grade == 6)
                insertCourse("SH6", $group_name, $userbgh->id);
            else
                insertCourse("DS" . $grade, $group_name, $userbgh->id);
            insertCourse("HH" . $grade, $group_name, $userbgh->id);
            insertCourse("TA" . $grade, $group_name, $userbgh->id);
            insertCourse("NV" . $grade, $group_name, $userbgh->id);
        }
//-----------------------add student
        //student
        $students = $DB->get_records("temp_student", array('classid' => $class->id, 'approve' => 0));
        $rolename = "student";


        if (array_key_exists($rolename, $rolecache)) {
            $roleid = $rolecache[$rolename]->id;
        } else {
            echo 'unknownrole' . $rolename;
            die;
        }
        foreach ($students as $student) {
            $sid++;
            $stt = sprintf("%04d", $sid);
            $user = new stdClass();
            $student->username = $user->username = $school->code . "-hs" . $stt;
            $student->password = $user->password = rand_string(4);
            $arrName = split_name($student->name);
            $user->firstname = $arrName['first_name'];
            $user->lastname = $arrName['last_name'];
            $firstname = strtolower(non_unicode($user->firstname));
            $lastname = strtolower(preg_replace('/\s+/', '', non_unicode($user->lastname)));

            $s = explode("(", $firstname);
            $firstname = trim($s[0]);
            $user->email = "hs" . $stt . "-" . $lastname . "-" . $firstname . "@" . $school->code . ".edu.vn";

            $user->username = core_user::clean_field($user->username, 'username');
            $user->mnethostid = $CFG->mnet_localhost_id;
            if ($existinguser = $DB->get_record('user', array('username' => $user->username, 'mnethostid' => $user->mnethostid))) {
                echo $existinguser->id . " user existed";
                continue;
            }
            $user->confirmed = 1;
            $user->timemodified = time();
            $user->timecreated = time();
            $user->profile_field_classid = $student->classid;
            $user->profile_field_schoolid = $student->schoolid;
            $user->profile_field_type = "student";
            $user->profile_field_gender = $student->gender;
            $user->profile_field_birthdate = $student->birth_date;
            $user->profile_field_parent = $student->parent;
            $user->profile_field_parentphone = $student->parent_phone;
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
                continue;
            }

            $isinternalauth = $auth->is_internal();

            if (empty($user->email)) {
                echo 'invalidemail:' . $user->email;
                continue;
            } else if ($DB->record_exists('user', array('email' => $user->email))) {
                echo 'duplicateemail:' . $user->email;
                continue;
            }
            if (!validate_email($user->email)) {
                $user->email = non_unicode($user->email);
                if (!validate_email($user->email)) {
                    echo 'invalidemail:' . $user->email;
                    continue;
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


            $validation[$user->username] = core_user::validate($user);
            if (!empty($validation)) {
                foreach ($validation as $username => $result) {
                    if ($result !== true) {
                        \core\notification::warning(get_string('invaliduserdata', 'tool_uploaduser', s($username)));
                    }
                }
            }
            $student->approve = 1;
            $DB->update_record("temp_student", $student);
            echo $class->code . "&nbsp;&nbsp;" . $student->username . "&nbsp;&nbsp;" . $student->password . "&nbsp;&nbsp;" . $roleid . "<br/>";
        }
        echo '<script>
    parent.document.getElementById("progressbar").innerHTML="<div style=\"width:' . $percent . ';background:#1177d1; ;height:35px;\">&nbsp;</div>";
    parent.document.getElementById("information").innerHTML="<div style=\"text-align:center; font-weight:bold\">' . $percent . ' - Processing  class ' . $class->code . '</div>";</script>';

        doFlush();
    }

    $school->approve = 1;
    $school->last_student = $sid;
    $school->last_teacher = $tid;
    $DB->update_record("school", $school);
    echo '<script>parent.document.getElementById("information").innerHTML="<div style=\"text-align:center; font-weight:bold\">Process completed</div>"</script>';
}


$time_end = microtime(true);
$execution_time = ($time_end - $time_start);
export($schoolid, $school->code . ".xlsx");
echo "<br/> Execution time:" . $execution_time;

function insertTeacher($DB, $school) {
    global $CFG, $sname, $cohortgv, $cohortbgh;
    $stt = 0;
    $teachers = $DB->get_records("temp_teacher", array("schoolid" => $school->id, "approve" => 0));

    $tid = $school->last_teacher;
    $percent = 1;
    echo '<script>
    parent.document.getElementById("progressbar").innerHTML="<div style=\"width:' . $percent . ';background:#1177d1; ;height:35px;\">&nbsp;</div>";
    parent.document.getElementById("information").innerHTML="<div style=\"text-align:center; font-weight:bold\">' . $percent . ' % - Start insert teacher </div>";'
    . '</script>';

    doFlush();

    foreach ($teachers as $teacher) {
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
            continue;
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
            continue;
        }

        $isinternalauth = $auth->is_internal();

        if (empty($user->email)) {
            echo get_string('invalidemail') . $user->email;
            continue;
        } else if ($DB->record_exists('user', array('email' => $user->email))) {
            echo get_string('emailduplicate:') . $stremailduplicate;
            continue;
        }
        if (!validate_email($user->email)) {
            $user->email = non_unicode($user->email);
            if (!validate_email($user->email)) {
                echo 'invalidemail:' . $user->email;
                continue;
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
        $validation[$user->username] = core_user::validate($user);
        if (!empty($validation)) {
            foreach ($validation as $username => $result) {
                if ($result !== true) {
                    \core\notification::warning(get_string('invaliduserdata', 'tool_uploaduser', s($username)));
                }
            }
        }
        $teacher->approve = 1;
        $DB->update_record("temp_teacher", $teacher);
        echo $teacher->name . " - " . $teacher->username . "&nbsp;-&nbsp;" . $teacher->password . "<br/>";
    }
    return $tid;
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

function insertCourse($shortname, $group_name, $userid) {
    global $DB;
    global $ccache;
    global $manualcache;
    global $rolecache;
    global $manual;
    global $roleid;
    global $today;
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
        $timestart = $today;
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
