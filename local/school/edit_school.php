<?php

//echo "hehe";die();
require(__DIR__ . '/../../config.php');
require_once("school_form.php");
require_once $CFG->libdir . '/hbonlib/string_util.php';
require_once $CFG->libdir . '/hbonlib/lib.php';
require_login();

$id = optional_param('id', 0, PARAM_INT);
$site = get_site();

$context = context_system::instance();
$PAGE->set_context($context);

$url = new moodle_url('/local/school/edit_school.php');
$PAGE->set_heading(get_string("school", "local_school"));
$PAGE->set_url($url);
if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow", "local_school");
    echo $OUTPUT->footer();
    die;
}
$mform = new school_form(null, array('schoolid' => $id, 'districtid' => null));

if ($mform->is_cancelled()) {
    $schoolurl = new moodle_url('/local/school/schools.php', array('id' => $id));
    redirect($schoolurl);
} else if ($fromform = $mform->get_data()) {
    if (empty($fromform->province)) {
        redirect(new moodle_url('/local/school/edit_school.php'), get_string("province_require", "local_school"));
    }
    $districtid = $_REQUEST['district'];
    $schoolurl = new moodle_url('/local/school/schools.php', array('id' => $id));
    $fromform->name = trim(preg_replace('/\s+/', ' ', $fromform->name));
    $fromform->districtid = $districtid;

    if ($fromform->id != 0) {
        if ($fromform->approve == 2) {
            $fromform->approve = 1;
        }
        if (!$DB->update_record('school', $fromform)) {
            print_error('updateerror', 'school');
        }
    } else {

        if (!$DB->insert_record('school', $fromform)) {
            print_error('inserterror', 'school');
        }
    }
    redirect($schoolurl);
} else {

    if ($id) {
        //edit if have $id
        $mformpage = $DB->get_record('school', array('id' => $id));
        $districtid = $mformpage->districtid;
        $mform = new school_form(null, array('schoolid' => $id, 'districtid' => $districtid));
        $mform->set_data($mformpage);
    }
    echo $OUTPUT->header();

    $mform->display();
    echo $OUTPUT->footer();
}