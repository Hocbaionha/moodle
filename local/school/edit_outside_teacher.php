<?php

require_once(__DIR__ . '/../../config.php');
require_once("outside_teacher_form.php");
require_once $CFG->libdir . '/hbonlib/string_util.php';
require_once $CFG->libdir . '/hbonlib/lib.php';
require_login();
$id = optional_param('id', 0, PARAM_INT);
$schoolid = optional_param('schoolid', 0, PARAM_INT);
$site = get_site();

$context = context_system::instance();
$PAGE->set_context($context);
$url = new moodle_url('/local/school/edit_outside_teacher.php');
$PAGE->set_url($url);
$PAGE->set_heading(get_string("teacher", "local_school"));
if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow","local_school");
    echo $OUTPUT->footer();die;
}
$mform = new outside_teacher_form(null, array('teacherid' => $id, 'schoolid' => $schoolid));

$back = new moodle_url('/local/school/outside_teachers.php?schoolid=' . $schoolid, array('id' => $id));

if ($mform->is_cancelled()) {
    redirect($back);
} else if ($fromform = $mform->get_data()) {
    $fromform->school = $_REQUEST["school"];
    $fromform->name = trim(preg_replace('/\s+/', ' ', $fromform->name));
    $fromform->department = trim(preg_replace('/\s+/', ' ', $fromform->department));
    $fromform->phone = trim($fromform->phone);
    $fromform->schoolid = $fromform->school;

    if ($fromform->id != 0) {
        $arrName = split_name($fromform->name);
        $user = $DB->get_record("user", array("id" => $fromform->id));
        $user->firstname = $arrName['first_name'];
        $user->lastname = $arrName['last_name'];
        $user->profile_field_department = $fromform->department;
        $user->profile_field_phone = $fromform->phone;
        $user = uu_pre_process_custom_profile_data($user);
        profile_save_data($user);
        update_teacher_group($fromform);
        if (!$DB->update_record('user', $user)) {
            print_error('updateerror', 'school');
        }
        redirect($back, get_string("update_teacher_success", "local_school"));
    } else {
        $fromform->schoolid = $fromform->school;
        $fromform->name = trim(preg_replace('/\s+/', ' ', $fromform->name));
        $result = add_teacher_to_core($fromform);
        if (!$result) {
            throw new Exception("update_teacher_fail");
            redirect($back, get_string("update_teacher_fail", "local_school"));
        }
    }
    redirect($back, get_string("update_teacher_success", "local_school"));
} else {

    if ($id) {
        $user = $DB->get_record("user", array("id" => $id));
        $sql = "select f.shortname,d.data,u.id from mdl_user_info_field f 
                join mdl_user_info_data d on f.id=d.fieldid 
                join mdl_user u on d.userid=u.id 
                where u.id=?";
        $datas = $DB->get_records_sql($sql, array("id" => $id));
        $mformpage = array();
//        dd($datas);
        foreach ($datas as $data) {
            $mformpage["$data->shortname"] = $data->data;
        }
        $mformpage["name"] = $user->lastname . " " . $user->firstname;
        $mform = new outside_teacher_form(null, array('teacherid' => $id, 'schoolid' => $schoolid, 'department' => $mformpage["department"]));
        $mform->set_data($mformpage);
    } else {
        //create new
    }
    echo $OUTPUT->header();


    $mform->display();
    echo $OUTPUT->footer();
}
