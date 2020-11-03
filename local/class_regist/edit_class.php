<?php

require(__DIR__ . '/../../config.php');
require("class_form.php");
require_login();

$id = optional_param('id', 0, PARAM_INT);
$site = get_site();

$context = context_system::instance();
$PAGE->set_context($context);
$url = new moodle_url('/local/class_regist/edit_class.php');
$PAGE->set_heading(get_string("class", "local_class_regist"));
$PAGE->set_url($url);
if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow", "local_class_regist");
    echo $OUTPUT->footer();
    die;
}
$mform = new class_form(null, array('id' => $id));
//$url = new moodle_url('/course/editcategory.php');
if ($mform->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $schoolurl = new moodle_url('/local/class_regist/list_class.php', array('id' => $id));
    redirect($schoolurl);
} else if ($fromform = $mform->get_data()) {
    $fromform->name = trim(preg_replace('/\s+/', ' ', $fromform->name));
    //check unique 

    $check = $DB->get_record("hbon_classes", array("code" => $fromform->code));

    $schoolurl = new moodle_url('/local/class_regist/list_class.php', array('id' => $id));
    if ($fromform->id != 0) {
        $class = $DB->get_record("hbon_classes", array("id" => $id));
        if ($check && $check->code != $class->code) {
            redirect($schoolurl, get_string("code_existed", "local_class_regist"));
        }
        if (!$DB->update_record('hbon_classes', $fromform)) {
            print_error('updateerror', 'school');
        }
    } else {
        if (!empty($check)) {
            redirect($schoolurl, get_string("code_existed", "local_class_regist"));
        }
        if (!$DB->insert_record('hbon_classes', $fromform)) {
            print_error('inserterror', 'school');
        }
    }
    redirect($schoolurl);
} else {

    if ($id) {
        //edit if have $id
        $mformpage = $DB->get_record('hbon_classes', array('id' => $id));
        $mform = new class_form(null, array('id' => $id));
        $mform->set_data($mformpage);
    }
    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
}