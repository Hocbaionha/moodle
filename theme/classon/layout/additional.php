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

$lurl = optional_param('lurl', "", PARAM_TEXT);

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
    'signupurl'=>$signupurl,
    'lurl'=>$lurl
];
if ( isloggedin() && !isguestuser() ) {
    
    $uid = $USER->id;
    $table = 'hbon_add_info_user';
    
    $has_add_phone = $DB->record_exists($table, array('user_id' => $uid , 'signup_method' => 'phone'));


    if(!$has_add_phone) {
        $templatecontext['should_get_user_phone'] = '1';
    }else {
        $user_phone_info =  $DB->get_record($table, array('user_id'=>$uid, 'signup_method' => 'phone'));
        if($user_phone_info->has_confirm == 0) {
            $templatecontext['should_get_user_phone'] = '1';

            if($user_phone_info->signup_type == 'verifying') {
                $templatecontext['user_phone'] = $user_phone_info->signup_info;
                $templatecontext['verifying'] = '1';
            }
        }else {
            $has_add_email = $DB->record_exists($table, array('user_id' => $uid , 'signup_method' => 'email'));
            
            if(!$has_add_email) {
                $templatecontext['should_get_user_email'] = '1';
            }else {
                $user_email_info =  $DB->get_record($table, array('user_id'=>$uid, 'signup_method' => 'email'));
                
                if($user_email_info->has_confirm == 0) {
                    $templatecontext['should_get_user_email'] = '1';

                    if($user_email_info->signup_type == 'verifying') {
                        $templatecontext['user_email'] = $user_email_info->signup_info;
                        $templatecontext['verifying'] = '1';
                    }
                }
            }
        }        
    }
}
$PAGE->requires->js('/theme/classon/amd/src/mmenu.js');

//$PAGE->requires->js_call_amd('theme_classon/classon_courses', 'classon_courses');
echo $OUTPUT->render_from_template('theme_classon/classon_additional', $templatecontext);


