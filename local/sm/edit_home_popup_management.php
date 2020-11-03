<?php

//echo "hehe";die();
require(__DIR__ . '/../../config.php');
require_once("home_popup_management_form.php");
require_once $CFG->libdir . '/hbonlib/string_util.php';
require_once $CFG->libdir . '/hbonlib/lib.php';
require_login();

$id = optional_param('id', 0, PARAM_INT);
$site = get_site();

$context = context_system::instance();
$PAGE->set_context($context);

$url = new moodle_url('/local/sm/edit_home_popup_management.php');
$PAGE->set_heading(get_string("popup"));
$PAGE->set_url($url);
if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow", "local_sm");
    echo $OUTPUT->footer();
    die;
}
$mform = new home_popup_management_form(null, array('id' => $id));

if ($mform->is_cancelled()) {
    $schoolurl = new moodle_url('/local/sm/schools.php', array('id' => $id));
    redirect($schoolurl);
} else if ($fromform = $mform->get_data()) {
    $fs = get_file_storage();
    $fileinfo = array(
        'component' => 'local_school', // usually = table name
        'filearea' => 'schoolplugin', // usually = table name
        'itemid' => 0, // usually = ID of row in table
        'contextid' => $context->id, // ID of context
        'filepath' => '/', // any path beginning and ending in /
        'filename' => ''); // any filename
    $fullpath = $CFG->dataroot . '/school/';
    if (!file_exists($fullpath)) {
        mkdir($fullpath, 0755, true);
    }

    $image = $mform->get_new_filename('image');
    if ($image) {
        $a=time()."_popup";
        $img_path = $fullpath . $a;

        $success = $mform->save_file('image', $img_path, true);
        if (!$success) {
            print_error('cant_upload', 'local_school');
        }
        $fileinfo['filename'] = $a;
        $fromform->image =  $a;
    } else {
        unset($fromform->image);
    }
    $fromform->public_at = date('Y-m-d H:i:s', $fromform->public_at);
    $fromform->expitime = date('Y-m-d H:i:s', $fromform->expitime);
    $schoolurl = new moodle_url('/local/sm/home_popup_management.php', array('id' => $id));
    $fromform->title = trim(preg_replace('/\s+/', ' ', $fromform->title));
    $fromform->created_at =date("Y-m-d H:i:s");

    if ($fromform->id != 0) {

        if (!$DB->update_record('hbon_popup_home', $fromform)) {
            print_error('updateerror', 'sm popup');
        }
    } else {

        if (!$DB->insert_record('hbon_popup_home', $fromform)) {
            print_error('inserterror', 'sm popup');
        }
    }
    redirect($schoolurl);
} else {

    if ($id) {
        //edit if have $id
        $mformpage = $DB->get_record('hbon_popup_home', array('id' => $id));
        $mformpage->public_at = (new \DateTime($mformpage->public_at))->getTimestamp();
        $mformpage->expitime = (new \DateTime($mformpage->expitime))->getTimestamp();
        $mform = new home_popup_management_form(null, array('id' => $id));
        $mform->set_data($mformpage);
    }
    echo $OUTPUT->header();

    $mform->display();
    echo $OUTPUT->footer();
}
