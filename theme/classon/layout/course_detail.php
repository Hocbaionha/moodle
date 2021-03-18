<?php

defined('MOODLE_INTERNAL') || die();
user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
require_once $CFG->libdir . '/behat/lib.php';

require_once $CFG->dirroot . '/cohort/locallib.php';
require_once $CFG->libdir . '/hbonlib/string_util.php';
global $USER;
global $CFG;
global $DB;

//-- /GET MENU AND RENDER
require_once $CFG->dirroot . '/theme/classon/layout/component/get_menu.php';
require_once($CFG->dirroot.'/theme/classon/layout/component/sso.php');

$url = new moodle_url('/');
$navdraweropen = false;
$extraclasses = [];
if ($navdraweropen) {
    $extraclasses[] = 'drawer-open-left';
}
$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = strpos($blockshtml, 'data-block=') !== false;
$name = optional_param('course_detail', 0, PARAM_TEXT);
$course = $DB->get_record("course", array("shortname" => $name));
if (!$course) {
    echo "not found course";
    die;
}

$productid = $DB->get_record("course_desc",array("courseid"=>$course->id))->productid;
$coursehref1=null;
$coursehrefbutton1=null;
$coursehref = "/local/hbon_payment/index.php?product_id=$productid";
$coursehrefbutton = "Đăng ký";
$sql = "select count(*) from mdl_enrol e join mdl_cohort c on c.id=e.customint1
	join mdl_user_enrolments ue on ue.enrolid=e.id
	where e.courseid=?
	and ue.userid=?
	and c.idnumber like 'HBON%'";
        $coursehref = "/course/view.php?id=$course->id";
        $coursehrefbutton = "Vào học";
$cohortids = $DB->count_records_sql($sql,array("courseid"=>$course->id,"userid"=>$USER->id));
if($cohortids > 0 || $productid==8){
    $coursehref = "/course/view.php?id=$course->id";
    $coursehrefbutton = "Vào học";
} else {
    $coursehref = "/course/view.php?id=$course->id";
    $coursehrefbutton = "Vào học";   //"Học thử ";
    $coursehref1 = "/local/hbon_payment/index.php?product_id=$productid";
    $coursehrefbutton1 = "Đăng ký";
}

$parents = $DB->get_records("course_hierarchy", array("courseid" => $course->id, "parentid" => null));
$topic = array();
$i = 0;
foreach ($parents as $p) {
    $i++;
    $p->index = $i % 2;
    if (strlen($p->name) > 60) {
        $p->sname = truncate($p->name, 60);
    } else {
        $p->sname = $p->name;
    }

    if($p->isfree==1){
        $p->free=true;
    }
    $children = $DB->get_records("course_hierarchy", array("parentid" => $p->id));
    if (count($children) > 0) {
        $arrayChild = array();
        $idx=0;
        foreach ($children as $child) {
            if (strlen($child->name) > 50) {
                $child->sname = truncate($child->name, 70);
            } else {
                $child->sname = $child->name;
            }
            $child->free=false;
            if($child->isfree==1){
                $child->free=true;
            }

            $arrayChild[] = $child;
            $idx++;
        }
        $t = (array) $p;
        $a = array();
        $t['children'] = $arrayChild;
        $topic[] = $t;
    } else {
        $topic[] = (array) $p;
    }

}
$coursedesc = (array) $DB->get_record("course_desc", array("courseid" => $course->id));
$regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();
$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'navdraweropen' => $navdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'main_menu' => $string00, // -> Send main menu string to front end
    'coursename' => $course->fullname,
    'summary' => $course->summary,
    'shortname' => $course->shortname,
    "course_desc" => $coursedesc,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'a' => $OUTPUT->main_content(),
    'isloggedin' => isloggedin(),
    'topic' => $topic,
    'coursehref' => $coursehref,
    'coursehrefbutton' => $coursehrefbutton,
    'coursehref1' => $coursehref1,
    'coursehrefbutton1' => $coursehrefbutton1,
    'loginurl'=>$loginurl,
    'signupurl'=>$signupurl
];


$PAGE->requires->js('/theme/classon/amd/src/mmenu.js');
// $PAGE->requires->js('https://unpkg.com/@popperjs/core@2.4.4/dist/umd/popper.min.js');
// $PAGE->requires->js('https://unpkg.com/tippy.js@6');
$templatecontext['flatnavigation'] = $PAGE->flatnav;
echo $OUTPUT->render_from_template('theme_classon/classon_course_detail', $templatecontext);
