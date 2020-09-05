<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/authlib.php');
require_once($CFG->dirroot . '/group/lib.php');
require_once(__DIR__ . '/../../admin/tool/uploaduser/locallib.php');
require_once $CFG->libdir . '/hbonlib/string_util.php';

require_login();
$context = context_system::instance();
$url = new moodle_url('/local/school/assign_teacher.php');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_heading(get_string("assign_gvcn", "local_school"));

if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow","local_school");
    echo $OUTPUT->footer();die;
}
$userid = optional_param('teacher', null, PARAM_TEXT);

$groupname = optional_param('groupname', 0, PARAM_TEXT);
$type = optional_param('type', 0, PARAM_INT);
$schoolid = optional_param('schoolid', 0, PARAM_INT);
$classid = optional_param('classid', 0, PARAM_INT);

$back = new moodle_url('/local/school/teachers_assignment.php', array("school" => $schoolid));
$manualcache = array();
$rolecache = uu_allowed_roles_cache();
$manual = enrol_get_plugin('manual');

if (!empty($userid)) {
    $tx = $DB->start_delegated_transaction();
    if ($type == 1) {
        $roleid = $DB->get_record("role", array("shortname" => "gvcn"))->id;
        $user = $DB->get_record("user", array("id" => $userid));
        if ($user) {

            $groups = $DB->get_records("groups", array("name" => $groupname));
            foreach ($groups as $g) {
                enrolegroup($userid, $g->id, $roleid);
                $gid = $g->id;
            }
            insert_teacher_class($classid, $gid, $userid, $type);
        }
    } else {
        $roleid = $DB->get_record("role", array("shortname" => "gvbm"))->id;
        $sql = "select g.id,g.courseid,c.shortname from mdl_groups g join mdl_course c on c.id=g.courseid where g.name=?";
        $gcs = $DB->get_records_sql($sql, array("name" => $groupname));

        foreach ($gcs as $gc) {
            if ((startsWith($gc->shortname, "SH") || startsWith($gc->shortname, "DS")) && $type == 2) {
                enrolegroup($userid, $gc->id, $roleid);
                insert_teacher_class($classid, $gc->id, $userid, $type);
            } else if (startsWith($gc->shortname, "HH") && $type == 3) {
                enrolegroup($userid, $gc->id, $roleid);
                insert_teacher_class($classid, $gc->id, $userid, $type);
            } else if (startsWith($gc->shortname, "TA") && $type == 4) {
                enrolegroup($userid, $gc->id, $roleid);
                insert_teacher_class($classid, $gc->id, $userid, $type);
            } else if (startsWith($gc->shortname, "NV") && $type == 5) {
                enrolegroup($userid, $gc->id, $roleid);
                insert_teacher_class($classid, $gc->id, $userid, $type);
            }
        }
    }
    $tx->allow_commit();
    redirect($back, get_string("assign_success", "local_school"));
} else {
    $department = null;
    if (2 == $type || 3 == $type) {
        $department = "math";
    } else if (4 == $type) {
        $department = "english";
    } else if (5 == $type) {
        $department = "literature";
    }
    $sql = 'select u.id,u.firstname,u.lastname from mdl_user u join'
            . ' (SELECT userid FROM mdl_user_info_data where fieldid=(SELECT id FROM mdl_user_info_field where shortname="department") and data="' . $department . '") as de on u.id=de.userid join'
            . ' (SELECT userid,data FROM mdl_user_info_data where fieldid=(SELECT id FROM mdl_user_info_field where shortname="type") and (data="gvcn" or data="gvbm")) as d on u.id=d.userid join '
            . '(SELECT userid FROM mdl_user_info_data where fieldid=(SELECT id FROM mdl_user_info_field where shortname="schoolid") and data=?) as s on u.id=s.userid';
    if (1 == $type) {
        $sql = 'select u.id,u.firstname,u.lastname from mdl_user u join (SELECT userid,data FROM mdl_user_info_data where fieldid=(SELECT id FROM mdl_user_info_field where shortname="type") and (data="gvbm" or data="gvcn")) as d on u.id=d.userid join'
                . '(SELECT userid FROM mdl_user_info_data where fieldid=(SELECT id FROM mdl_user_info_field where shortname="schoolid") and data=?) as s on u.id=s.userid';
    }

    $selectArray = array();
    $teachers = $DB->get_records_sql($sql, array("data" => $schoolid));
    foreach ($teachers as $teacher) {
        $key = $teacher->id;
        $value = $teacher->lastname . " " . $teacher->firstname;
        $selectArray[$key] = $value;
    }
    $attributes['class'] = 'col-md-6';
    $attlabel['class'] = 'col-md-3';
    echo $OUTPUT->header();

    echo '<form action="' . $url . '" method="get">';
    $url = new moodle_url('/local/school/assign_teacher.php', array("type" => $type, "groupname" => $groupname, "classid" => $classid, "schoolid" => $schoolid));
    echo html_writer::input_hidden_params($url);
    echo '<div class="form-group row">';
    echo html_writer::label(get_string("choose_teacher", 'local_school') . " " . $department . " " . $groupname, "school", true, $attlabel);
    echo html_writer::select($selectArray, "teacher", '', null, $attributes);
    echo '<div class="col-md-3"><button id="approvebtn" class="btn btn-primary ">' . get_string("submit") . '</button> </div>';
    echo '</div>';
    echo '</form>';
    echo $OUTPUT->footer();
}

function enrolegroup($userid, $groupid, $roleid) {
    global $DB, $manual, $manualcache,$tx;
    $courseid = $DB->get_record("groups", array("id" => $groupid))->courseid;
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
    $timeend = 0;
    $today = time();
    $today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);
    $timestart = $today;
    $status = null;
    if ($manualcache[$courseid]->enrolperiod > 0) {
        $timeend = $timestart + $manualcache[$courseid]->enrolperiod;
    }


    $manual->enrol_user($manualcache[$courseid], $userid, $roleid, $timestart, $timeend, $status);

    if (!groups_add_member($groupid, $userid)) {
        echo "error gid:$groupid,teacher: $userid";
        $tx->rollback(new Exception('group add fail'));
        die;
    }
}

function insert_teacher_class($classid, $groupid, $userid, $type) {
    global $DB;
    $tc_assignment = new stdClass();
    $tc_assignment->classid = $classid;
    $tc_assignment->groupid = $groupid;
    $tc_assignment->userid = $userid;
    $tc_assignment->type = $type;
    $DB->insert_record("teacher_class", $tc_assignment);
}
