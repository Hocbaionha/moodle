<?php

require_once(__DIR__ . '/../../config.php');
require_once("student_form.php");
require_once $CFG->libdir . '/hbonlib/string_util.php';
require_once $CFG->libdir . '/hbonlib/lib.php';
require_once(__DIR__ . '/../../admin/tool/uploaduser/locallib.php');
require_login();

$id = optional_param('id', 0, PARAM_INT);
$schoolid = optional_param('schoolid', 0, PARAM_INT);
$classid = optional_param('classid', 0, PARAM_INT);
$site = get_site();

$context = context_system::instance();
$PAGE->set_context($context);

$url = new moodle_url('/local/school/edit_temp_student.php');
$PAGE->set_url($url);
if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow","local_school");
    echo $OUTPUT->footer();die;
}
$mform = new student_form(null, array('studentid' => $id, 'schoolid' => $schoolid, 'classid' => $classid));
$back = new moodle_url('/local/school/change_student.php', array('id' => $id, "schoolid" => $schoolid, "classid" => $classid));


if ($mform->is_cancelled()) {
    redirect($back);
} else if ($fromform = $mform->get_data()) {
    $fromform->birthdate = date('Y-m-d', $fromform->birthdate);
    $fromform->name = trim(preg_replace('/\s+/', ' ', $fromform->name));
    $fromform->parent = trim(preg_replace('/\s+/', ' ', $fromform->parent));
    $fromform->parentphone = trim($fromform->parentphone);


    if ($fromform->id != 0) {

        $arrName = split_name($fromform->name);
        $user = $DB->get_record("user", array("id" => $fromform->id));
        $user->firstname = $arrName['first_name'];
        $user->lastname = $arrName['last_name'];
        $user->profile_field_gender = $fromform->gender;
        $user->profile_field_birthdate = $fromform->birthdate;
        $user->profile_field_parent = $fromform->parent;
        $user->profile_field_parentphone = $fromform->parentphone;
        if (empty($fromform->schoolid)) {
            $fromform->schoolid = $fromform->school;
        }
        if (empty($fromform->classid)) {
            $fromform->classid = $fromform->class;
        }
        $user = uu_pre_process_custom_profile_data($user);
        profile_save_data($user);

        if (!$DB->update_record('user', $user)) {
            print_error('updateerror', 'school');
        }
        $back = new moodle_url('/local/school/change_student.php', array('id' => $id, "schoolid" => $fromform->schoolid, "classid" => $fromform->classid));
        redirect($back, get_string("update_teacher_success", "local_school"));
    } else {
        if (empty($fromform->schoolid)) {
            $fromform->schoolid = $fromform->school;
        }
        if (empty($fromform->classid)) {
            $fromform->classid = $fromform->class;
        }
        $result = add_student_to_core($fromform);
        $back = new moodle_url('/local/school/change_student.php', array('id' => $id, "schoolid" => $fromform->schoolid, "classid" => $fromform->classid));
        if (!$result) {
            redirect($back, get_string("update_teacher_fail", "local_school"), null, \core\output\notification::NOTIFY_ERROR);
        }
        redirect($back, get_string("update_teacher_success", "local_school"));
    }
} else {

    if ($id) {
        //edit if have $id
        $user = $DB->get_record("user", array("id" => $id));
        $sql = "select f.shortname,d.data,u.id from mdl_user_info_field f 
                join mdl_user_info_data d on f.id=d.fieldid 
                join mdl_user u on d.userid=u.id 
                where u.id=?";
        $datas = $DB->get_records_sql($sql, array("id" => $id));
        $mformpage = array();
        foreach ($datas as $data) {
            $mformpage[$data->shortname] = $data->data;
        }
        $mformpage["name"] = $user->lastname . " " . $user->firstname;

        $mform = new student_form(null, array('studentid' => $id, 'schoolid' => $schoolid, 'classid' => $classid));
        $mform->set_data($mformpage);
    } else {
        //create new
    }

//    echo $OUTPUT->heading(get_string('student', 'local_school'));
    echo $OUTPUT->header(get_string('student', 'local_school'));
    $mform->display();
    echo $OUTPUT->footer();
}
