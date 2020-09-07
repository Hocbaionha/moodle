<?php

defined('MOODLE_INTERNAL') || die();

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
require_once($CFG->libdir . '/behat/lib.php');

require_once($CFG->dirroot . '/cohort/locallib.php');
global $USER;
global $CFG;
global $DB;


//-- /GET MENU AND RENDER
require_once($CFG->dirroot . '/theme/classon/layout/component/get_menu.php');
require_once($CFG->dirroot.'/theme/classon/layout/component/sso.php');

$url = new moodle_url('/');
$navdraweropen = false;
$extraclasses = [];
if ($navdraweropen) {
    $extraclasses[] = 'drawer-open-left';
}
$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$thcs=array("lop6","lop7","lop8","lop9");
$course_categorie = $DB->get_records("course_categories",array("visible"=>1),'sortorder');
$course_categories = array();
$i=0;
foreach($course_categorie as $cc){
    if (!in_array($cc->idnumber,$thcs)) {
        continue;
    }
    $i++;
    $title = $cc->name;
    $img=$url."/theme/classon/pix/course/sh6.png";
    $desc = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";
    $sql = "select c.shortname,cd.* from mdl_course c join mdl_course_desc cd on c.id=cd.courseid where c.visible=1 and c.category = $cc->id order by sortorder";

    $_courses = $DB->get_records_sql($sql);
    $courses = array();
    foreach($_courses as $course){
        $courses[] = (array)$course;
    }
//print_object($courses);die;
    $course_categories[] = array("title"=>$title,"img"=>$img,"desc"=>$desc,"id"=>$cc->idnumber,"courses"=>$courses,"css_class"=>"course_categories".$i);
    
}
//print_object($course_categories);die;
$regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();
$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes,
    'navdraweropen' => $navdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'main_menu' => $string00, // -> Send main menu string to front end
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'a' => $OUTPUT->main_content(),
    'course_categories'=>$course_categories,
    'coursename' => "Các khóa học",
    'isloggedin' =>isloggedin(),
    'loginurl'=>$loginurl,
    'signupurl'=>$signupurl
];

$PAGE->requires->js_call_amd('theme_classon/classon_homepage', 'classon_homepage');
$PAGE->requires->js('/theme/classon/amd/src/mmenu.js');
//$PAGE->requires->js_call_amd('theme_classon/classon_courses', 'classon_courses');
echo $OUTPUT->render_from_template('theme_classon/classon_courses', $templatecontext);


