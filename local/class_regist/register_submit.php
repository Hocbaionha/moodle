<?php

require(__DIR__ . '/../../config.php');


global $DB;
// $sitecontext = context_system::instance();


// $url = new moodle_url('/local/class_regist/register_submit.php');
// $PAGE->set_context($sitecontext);
// $PAGE->set_url($url);
$name = optional_param('name', '', PARAM_TEXT);
$school = optional_param('school', '', PARAM_TEXT);
$class = optional_param('class', '', PARAM_TEXT);
$province = optional_param('province', '', PARAM_TEXT);
$phone = optional_param('phone', '', PARAM_TEXT);
$choose  = optional_param('choose', '', PARAM_TEXT);

$check = $DB->get_record("hbon_classes_register",array("phone"=>$phone,"name"=>$name));
if($check){
    echo "phone";die;
}
$record = new stdClass();
$record->classid=$choose;
$record->name=$name;
$record->school=$school;
$record->class=$class;
$record->province=$province;
$record->phone=$phone;
$DB->insert_record("hbon_classes_register",$record);
echo "success";