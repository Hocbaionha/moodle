<?php

require(__DIR__ . '/../../config.php');
require("class_form.php");
require_login();

$id = optional_param('id', 0, PARAM_INT);
$schoolid = optional_param('schoolid', "", PARAM_TEXT);
$site = get_site();

$context = context_system::instance();
$PAGE->set_context($context);
$url = new moodle_url('/local/school/edit_class.php');
$PAGE->set_heading(get_string("class", "local_school"));
$PAGE->set_url($url);
if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow", "local_school");
    echo $OUTPUT->footer();
    die;
}
$mform = new class_form(null, array('classid' => $id, 'schoolid' => $schoolid));
//$url = new moodle_url('/course/editcategory.php');
if ($mform->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $schoolurl = new moodle_url('/local/school/classes.php', array('id' => $id));
    redirect($schoolurl);
} else if ($fromform = $mform->get_data()) {
    $fromform->schoolid = $fromform->school;
    $fromform->name = trim(preg_replace('/\s+/', ' ', $fromform->name));
    //check unique 

    $check = $DB->get_record("class", array("code" => $fromform->code, "schoolid" => $fromform->schoolid));

    $schoolurl = new moodle_url('/local/school/classes.php', array('id' => $id, "schoolid" => $fromform->schoolid));
    if ($fromform->id != 0) {
        $class = $DB->get_record("class", array("id" => $id));
        if ($check && $check->code != $class->code) {
            redirect($schoolurl, get_string("code_existed", "local_school"));
        }
        if (!$DB->update_record('class', $fromform)) {
            print_error('updateerror', 'school');
        }
    } else {
        if (!empty($check)) {
            redirect($schoolurl, get_string("code_existed", "local_school"));
        }
        if (!$DB->insert_record('class', $fromform)) {
            print_error('inserterror', 'school');
        }
    }
    redirect($schoolurl);
} else {

    if ($id) {
        //edit if have $id
        $mformpage = $DB->get_record('class', array('id' => $id));
        $mform = new class_form(null, array('classid' => $id, 'schoolid' => $schoolid));
        $mform->set_data($mformpage);
    }
    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
}