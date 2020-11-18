<?php

use block_xp\di;

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
global $USER,$CFG,$DB,$PAGE;

$sitecontext = context_system::instance();
$url = new moodle_url('/local/class_regist/class.php');
$PAGE->set_context($sitecontext);
$PAGE->set_url($url);

$PAGE->set_title("English Everyday Club");
$PAGE->set_heading('Đánh giá học thử khóa English EveryDay Club');

echo $OUTPUT->header();
$phone = optional_param('phone', 0, PARAM_TEXT);
try{
    $check = $DB->count_records('hbon_classes_register',array('phone'=>$phone));
    if($check>0){
        $member = $DB->get_records('hbon_classes_register', array('phone'=>$phone));
//        $class= $DB->get_record('hbon_classes', array('id'=>$member->classid));
//        $templatecontext = [
//            'hbon_classes_register'=>$member,
////            'class'=> $class->code
//        ];
        require_once(__DIR__ . '/share_comments.php');
//        echo $OUTPUT->render_from_template('theme_classon/share_comments', $templatecontext);
    }else{

    }
}catch (Exception $exception){

}
echo $OUTPUT->footer();




