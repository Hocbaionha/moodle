<?php

require_once(__DIR__ . '/../../config.php');
require_once ("temp_teacher_form.php");
require_login();

$id = optional_param('id', 0, PARAM_INT);
$schoolid = optional_param('schoolid', 0, PARAM_INT);
$site = get_site();

$context = context_system::instance();
$PAGE->set_context($context);

$url = new moodle_url('/local/school/edit_temp_teacher.php');
$PAGE->set_url($url);
if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow", "local_school");
    echo $OUTPUT->footer();
    die;
}
$mform = new temp_teacher_form(null, array('teacherid' => $id, 'schoolid' => $schoolid));
//$url = new moodle_url('/course/editcategory.php');
if ($mform->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    echo $schoolid;
    $schoolurl = new moodle_url('/local/school/temp_teachers.php', array('id' => $id, 'schoolid' => $schoolid));
    redirect($schoolurl);
} else if ($fromform = $mform->get_data()) {

    $schoolurl = new moodle_url('/local/school/temp_teachers.php', array('id' => $id, 'schoolid' => $fromform->school));
    $fromform->schoolid = $fromform->school;
    $fromform->name = trim(preg_replace('/\s+/', ' ', $fromform->name));

    if ($fromform->id != 0) {
        if (!$DB->update_record('temp_teacher', $fromform)) {
            print_error('updateerror', 'local_school');
        }
    } else {
        $school = $DB->get_record("school", array("id" => $fromform->schoolid));
        $sql = "select SUBSTRING_INDEX(max(username),'gv',-1) as tid from mdl_temp_teacher where schoolid=?";
        $tid = $DB->get_record_sql($sql, array("schoolid" => $fromform->schoolid))->tid;
        if ($tid == false) {
            $tid = 0;
        }
        $stt = sprintf("%02d", ++$tid);

        $arrName = split_name($fromform->name);
        $fromform->firstname = $arrName['first_name'];
        $fromform->lastname = $arrName['last_name'];
        $firstname = strtolower(non_unicode($fromform->firstname));
        $lastname = strtolower(preg_replace('/\s+/', '', non_unicode($fromform->lastname)));
        $fromform->username = $school->code . "-gv" . $stt;
        $fromform->email = "gv" . $stt . "-" . $lastname . "-" . $firstname . "@" . $school->code . ".edu.vn";
        if (!$DB->insert_record('temp_teacher', $fromform)) {
            print_error('inserterror', 'local_school');
        }
    }

    redirect($schoolurl);
} else {

    if ($id) {
        //edit if have $id
        $mformpage = $DB->get_record('temp_teacher', array('id' => $id));
        $mform = new temp_teacher_form(null, array('teacherid' => $id, 'schoolid' => $schoolid));
        $mform->set_data($mformpage);
    } else {
        //create new
    }
    echo $OUTPUT->header();

    echo $OUTPUT->heading(get_string('title', 'local_school'));

    $mform->display();
    echo $OUTPUT->footer();
}