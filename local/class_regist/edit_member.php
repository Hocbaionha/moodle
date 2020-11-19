<?php

require(__DIR__ . '/../../config.php');
require("member_form.php");
require_login();

global $USER,$DB;
$id = optional_param('id', 0, PARAM_INT);
$classid = optional_param('classid', 0, PARAM_INT);
$site = get_site();
$user = $USER->id;

$sql_permission = $DB->get_record('hbon_classes',array('id'=>$classid));
if(isset($sql_permission->is_accept)){
    $accept_user = explode(',', $sql_permission->is_accept);
}else{
    $accept_user=[];
}

$context = context_system::instance();
$PAGE->set_context($context);
$url = new moodle_url('/local/class_regist/edit_member.php');
$PAGE->set_heading(get_string("member", "local_class_regist"));
$PAGE->set_url($url);
if (!has_capability('local/school:write', $context) && !in_array($user, $accept_user)) {
    echo $OUTPUT->header();
    echo get_string("not_allow", "local_class_regist");
    echo $OUTPUT->footer();
    die;
}
$mform = new member_form(null, array('id' => $id));

//$url = new moodle_url('/course/editcategory.php');
if ($mform->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $schoolurl = new moodle_url('/local/class_regist/list_member.php', array('classid'=>$classid,'id' => $id));
    redirect($schoolurl);
} else if ($fromform = $mform->get_data()) {
    $fromform->name = trim(preg_replace('/\s+/', ' ', $fromform->name));
    $memberurl = new moodle_url('/local/class_regist/list_member.php', array('classid'=>$classid,'id' => $id));
    if ($fromform->id != 0) {
        $member= $DB->get_record("hbon_classes_register", array("id" => $id));
        if (!$DB->update_record('hbon_classes_register', $fromform)) {
            print_error('updateerror', 'school');
        }
    } else {
        if (!$DB->insert_record('hbon_classes_register', $fromform)) {
            print_error('inserterror', 'school');
        }
    }
    redirect($memberurl);
} else {
    if ($id) {
        //edit if have $id
        $mformpage = $DB->get_record('hbon_classes_register', array('id' => $id));
        $mform = new member_form(null, array('id' => $id));
        $mform->set_data($mformpage);
    }
    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
}
