<?php

//echo "hehe";die();
require(__DIR__ . '/../../config.php');
require("temp_student_form.php");
require_login();

$id = optional_param('id', 0, PARAM_INT);
$schoolid = optional_param('schoolid', 0, PARAM_INT);
$classid = optional_param('classid', 0, PARAM_INT);
$site = get_site();

$context = context_system::instance();
$PAGE->set_context($context);
if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow","local_school");
    echo $OUTPUT->footer();die;
}
$mform = new temp_student_form(null, array('studentid' => $id,'schoolid' => $schoolid,'classid' => $classid));

$schoolurl = new moodle_url('/local/school/temp_students.php', array('id' => $id,"schoolid"=>$schoolid,"classid"=>$classid));
if ($mform->is_cancelled()) {
    redirect($schoolurl);
} else if ($fromform = $mform->get_data()) {
    $fromform->birth_date = date('Y-m-d', $fromform->birth_date);
    $fromform->name  = trim(preg_replace('/\s+/', ' ',$fromform->name));
    $fromform->parent  = trim(preg_replace('/\s+/', ' ',$fromform->parent));
    $fromform->parent_phone = trim($fromform->parent_phone) ;
    $fromform->schoolid = $fromform->school;
    $fromform->classid = $_REQUEST['class'];
    if ($fromform->id != 0) {
        if (!$DB->update_record('temp_student', $fromform)) {
            print_error('updateerror', 'school');
        }
    } else {
        $school = $DB->get_record("school", array("id" => $fromform->schoolid));
        $sql = "select SUBSTRING_INDEX(max(username),'hs',-1) as sid from mdl_temp_student where schoolid=?";
        $sid = $DB->get_record_sql($sql,array("schoolid"=>$fromform->schoolid))->sid;
        if ($sid == false) {
            $sid = 0;
        }
        $stt = sprintf("%04d", ++$sid);

        $arrName = split_name($fromform->name);
        $fromform->firstname = $arrName['first_name'];
        $fromform->lastname = $arrName['last_name'];
        $firstname = strtolower(non_unicode($fromform->firstname));
        $lastname = strtolower(preg_replace('/\s+/', '', non_unicode($fromform->lastname)));
        $fromform->username = $school->code . "-hs" . $stt;
        $fromform->email = "hs" . $stt . "-" . $lastname . "-" . $firstname . "@" . $school->code . ".edu.vn";
        if (!$DB->insert_record('temp_student', $fromform)) {
            print_error('inserterror', 'school');
        }
    }


    redirect($schoolurl);
} else {

    if ($id) {
        //edit if have $id
        $mformpage = $DB->get_record('temp_student', array('id' => $id));
        $mformpage->birth_date  = strtotime($mformpage->birth_date);
        
        $mform = new temp_student_form(null, array('studentid' => $id,'schoolid' => $schoolid,'classid' => $classid));
        $mform->set_data($mformpage);
    } else {
        //create new
    }
    $url = new moodle_url('/local/school/edit_temp_student.php');
    $PAGE->set_url($url);
    echo $OUTPUT->header();

    echo $OUTPUT->heading(get_string('student', 'local_school'));

    $mform->display();
    echo $OUTPUT->footer();
}