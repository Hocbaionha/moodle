<?php

defined('MOODLE_INTERNAL') || die();

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
require_once($CFG->libdir . '/behat/lib.php');

require_once($CFG->dirroot . '/cohort/locallib.php');
global $USER;
global $CFG;
global $DB;


//-- /GET MENU AND RENDER
require_once($CFG->dirroot . '/theme/hbon_app/layout/component/get_menu.php');
require_once($CFG->dirroot.'/theme/hbon_app/layout/component/sso.php');

$url = new moodle_url('/');
$navdraweropen = false;
$extraclasses = [];
if ($navdraweropen) {
    $extraclasses[] = 'drawer-open-left';
}
$bodyattributes = $OUTPUT->body_attributes($extraclasses);


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
    'isloggedin' =>isloggedin(),
    'loginurl'=>$loginurl,
    'signupurl'=>$signupurl
];

$PAGE->requires->js_call_amd('theme_hbon_app/hbon_app_homepage', 'hbon_app_homepage');
$PAGE->requires->js('/theme/hbon_app/amd/src/mmenu.js');
//$PAGE->requires->js_call_amd('theme_hbon_app/hbon_app_courses', 'hbon_app_courses');
echo $OUTPUT->render_from_template('theme_hbon_app/hbon_app_en_speak', $templatecontext);


