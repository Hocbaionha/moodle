<?php

require(__DIR__ . '/../../config.php');
require("mapping_test_management_form.php");
require_login();

global $USER,$DB;
$id = optional_param('id', 0, PARAM_INT);
$site = get_site();

$context = context_system::instance();
$PAGE->set_context($context);
$url = new moodle_url('/local/sm/edit_mapping_test_management.php');
$PAGE->set_heading(get_string("mapping_test", "local_sm"));
$PAGE->set_url($url);
if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow", "local_sm");
    echo $OUTPUT->footer();
    die;
}
$mform = new mapping_test_management_form(null, array('id' => $id));

//$url = new moodle_url('/course/editcategory.php');
if ($mform->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $mapping_test_url = new moodle_url('/local/sm/edit_mapping_test_management.php', array('id' => $id));
    redirect($mapping_test_url);
} else if ($fromform = $mform->get_data()) {
    $fromform->name = trim(preg_replace('/\s+/', ' ', $fromform->name));
    $mapping_test_url = new moodle_url('/local/sm/mapping_test_management.php', array('id' => $id));
    if ($fromform->id != 0) {
        $member= $DB->get_record("hbon_mapping_test", array("id" => $id));
        if (!$DB->update_record('hbon_mapping_test', $fromform)) {
            print_error('updateerror', 'sm');
        }
    } else {
        if (!$DB->insert_record('hbon_mapping_test', $fromform)) {
            print_error('inserterror', 'sm');
        }
    }
    redirect($mapping_test_url);
} else {
    if ($id) {
        //edit if have $id
        $mformpage = $DB->get_record('hbon_mapping_test', array('id' => $id));
        $mform = new mapping_test_management_form(null, array('id' => $id));
        $mform->set_data($mformpage);
    }
    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
}
