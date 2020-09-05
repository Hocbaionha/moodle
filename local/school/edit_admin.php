<?php

//echo "hehe";die();
require(__DIR__ . '/../../config.php');
require("admin_form.php");
require_once $CFG->libdir .'/hbonlib/string_util.php';

require_login();

$id = optional_param('id', 0, PARAM_INT);
$schoolid = optional_param('schoolid', 0, PARAM_INT);
$site = get_site();

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_heading(get_string("school_admins","local_school"));
if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow","local_school");
    echo $OUTPUT->footer();die;
}
$mform = new admin_form(null, array('userid' => $id,'schoolid'=>$schoolid));
//$url = new moodle_url('/course/editcategory.php');
if ($mform->is_cancelled()) {
    $schoolurl = new moodle_url('/local/school/school_admins.php', array('id' => $id));
    redirect($schoolurl);
} else if ($fromform = $mform->get_data()) {

    $schoolurl = new moodle_url('/local/school/school_admins.php', array('id' => $id));
    $fromform->schoolid = $fromform->school;
    $fromform->name  = trim(preg_replace('/\s+/', ' ',$fromform->name));
    print_object($fromform);die;
    if ($fromform->id != 0) {
        //update admin
//        if (!$DB->update_record('temp_teacher', $fromform)) {
//            print_error('updateerror', 'local_school');
//        }
    } else {
        $user = new stdClass();
        $arrName = split_name($teacher->name);
        $user->firstname = $arrName['first_name'];
        $user->lastname = $arrName['last_name'];
        $user->email = "gv" . $stt . "-" . non_unicode(preg_replace('/\s+/', '', $arrName['last_name'])) . "-" . non_unicode($arrName['first_name']) . "@" . $provinceAcronym . "-" . $districtAcronym . ".edu.vn";


        $user->username = core_user::clean_field($user->username, 'username');
        $user->mnethostid = $CFG->mnet_localhost_id;
//insert
//        if (!$DB->insert_record('temp_teacher', $fromform)) {
//            print_error('inserterror', 'local_school');
//        }
    }


    redirect($schoolurl);
} else {

    if ($id) {
        //edit if have $id
        $mformpage = $DB->get_record('user', array('id' => $id));
        $mform = new admin_form(null, array('teacherid' => $id,'schoolid'=>$schoolid));
        $mform->set_data($mformpage);
    } else {
        //create new
    }
    $url = new moodle_url('/local/school/edit_temp_teacher.php');
    $PAGE->set_url($url);
    echo $OUTPUT->header();

    $mform->display();
    echo $OUTPUT->footer();
}