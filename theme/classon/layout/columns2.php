<?php
/**
 * A two column layout for the hbon theme.
 *
 * @package   theme_hbon
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
require_once($CFG->libdir . '/behat/lib.php');
//anhnn login from app
$idtokenfb = optional_param('idtokenfb', "", PARAM_TEXT);
if($idtokenfb!=""){
    if(empty($USER->id) || isguestuser()){
    login_from_app($idtokenfb);
}}
if (isloggedin()) {
    $navdraweropen = (get_user_preferences('drawer-open-nav', 'true') == 'true');
} else {
    $navdraweropen = false;
}

/*Begin- add cohort */
require_once($CFG->dirroot . '/cohort/locallib.php');
global $USER, $PAGE, $DB, $COURSE,$_REQUEST;
global $SESSION;
$SESSION->wantsurl = qualified_me();
$url = parse_url($PAGE->url);
$path = $url["path"];
$showpopup = false;
$popupimg = "";
$showsurvey =false;

if (isloggedin() && !isguestuser()) {
    $uid = $USER->id;
    if (!(cohort_is_member(1, $uid) || cohort_is_member(2, $uid) || cohort_is_member(3, $uid))) {
        cohort_add_member(1, $uid);
    }
    //check phone
    $sql = "select u.id,u.username,ud.data from mdl_user u join mdl_user_info_data ud on ud.userid=u.id
        join mdl_user_info_field uf on uf.id=ud.fieldid where u.id=? and uf.shortname='phone' and ud.data is not null and ud.data !=''";
    $phone = $DB->get_record_sql($sql, array("id" => $uid));

    if (!$phone) {
//    	$courseid = explode("=",$url["query"])[1];
//	    $coursedesc = $DB->get_record('course_desc', array('courseid' => $courseid));
//        if($coursedesc && isset($coursedesc->popup)){
        $showpopup = true;
//        }
    }
    else{
        $check_survey = $DB->get_record('hbon_collect_info', array('userid'=>$USER->id));
        if(!empty($check_survey)){
            if($check_survey->status_survey === NULL){
                $showsurvey = true;
            }
        }
    }

}
else {
    if ($path == "/course/view.php" && !isset($_SESSION["registed"])) {
        $courseid = explode("=", $url["query"])[1];
        $coursedesc = $DB->get_record('course_desc', array('courseid' => $courseid));
        if ($coursedesc && $coursedesc->popup) {
            $popupimg = $coursedesc->popupimg;
            $showpopup = true;
        }
    }
}
if (2 == $USER->id) {
    $showpopup = false;
}
/*End- add cohort*/

$extraclasses = [];
if ($navdraweropen) {
    $extraclasses[] = 'drawer-open-left';
}
$sql = 'select u.id,u.username,ud.data from mdl_user u join mdl_user_info_data ud on ud.userid=u.id
join mdl_user_info_field uf on uf.id=ud.fieldid where uf.shortname="phone" and ud.data is not null and ud.data !=""';
$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = strpos($blockshtml, 'data-block=') !== false;
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

//infor course for activities firebase
if(isset($_REQUEST['id'])){
    $context_activity_id = (int)$_REQUEST['id'];
}else{
    if(isset($_REQUEST['cmid'])){
        $context_activity_id =  (int)$_REQUEST['cmid'];
    }else{
        $context_activity_id=null;
    }
}
$fb_topic_name_in= '';
if($COURSE && $context_activity_id!==null){
    $section= $DB->get_records('course_sections', ["course" => $COURSE->id ], 'section ASC', 'id,name,section,visible');
    $activitys = get_array_of_activities($COURSE->id);
    if(array_key_exists($context_activity_id, $activitys)){
        $sectionid = $activitys[$context_activity_id]->sectionid;
        if($section[$sectionid]->section !== ''){
            $fb_topic_name_in = $section[$sectionid]->name;
        }else{
            $fb_topic_name_in = "Topic ". $section[$sectionid]->section;
        }
    }
}

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'logo' => $OUTPUT->image_url('LogoHBON', 'theme'),
    'logomobile' => $OUTPUT->image_url('LogoHBON-H', 'theme'),
    'isloggedin' => isloggedin(),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'navdraweropen' => $navdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'uid' => $USER->id,
    'showpopup' => $showpopup,
    'showsurvey'=>$showsurvey,
    'wanturl' => $PAGE->url,
    'popup_img' => $popupimg,
    'fb_course_id'=>!empty($COURSE)?$COURSE->shortname:'',
    'fb_course_name'=>!empty($COURSE)?$COURSE->fullname:'',
    'fb_topic_name_in'=>$fb_topic_name_in
//    'fb_id'=>$USER->uid
];

$nav = $PAGE->flatnav;
$templatecontext['flatnavigation'] = $nav;
$templatecontext['firstcollectionlabel'] = $nav->get_collectionlabel();

/* Add to accquire user data */

global $DB;
if (isloggedin() && !isguestuser()) {
    $uid = $USER->id;
    $table = 'hbon_add_info_user';

    $has_add_phone = $DB->record_exists($table, array('user_id' => $uid, 'signup_method' => 'phone'));


    if (!$has_add_phone) {
        $templatecontext['should_get_user_phone'] = '1';
    } else {
        $user_phone_info = $DB->get_record($table, array('user_id' => $uid, 'signup_method' => 'phone'));
        if ($user_phone_info->has_confirm == 0) {
            $templatecontext['should_get_user_phone'] = '1';

            if ($user_phone_info->signup_type == 'verifying') {
                $templatecontext['user_phone'] = $user_phone_info->signup_info;
                $templatecontext['verifying'] = '1';
            }
        } else {
            $has_add_email = $DB->record_exists($table, array('user_id' => $uid, 'signup_method' => 'email'));

            if (!$has_add_email) {
                $templatecontext['should_get_user_email'] = '1';
            } else {
                $user_email_info = $DB->get_record($table, array('user_id' => $uid, 'signup_method' => 'email'));

                if ($user_email_info->has_confirm == 0) {
                    $templatecontext['should_get_user_email'] = '1';

                    if ($user_email_info->signup_type == 'verifying') {
                        $templatecontext['user_email'] = $user_email_info->signup_info;
                        $templatecontext['verifying'] = '1';
                    }
                }
            }
        }
    }

    // $check_record = $DB->record_exists($table, array('user_id'=>$uid));
    // if (!$check_record) {
    //     $templatecontext['check_get_info_user'] = '1';
    // }
    // else {
    //     $user_info = $DB->get_record($table, array('user_id'=>$uid));
    //     if ($user_info->has_confirm == 0){
    //         $templatecontext['check_get_info_user'] = '1';
    //         if ($user_info->signup_type == 'verifying'){
    //             $templatecontext['emailandphone'] = $user_info->email.$user_info->phone;
    //             $templatecontext['verifying'] = '1';
    //         }
    //     }
    // }

}

/* \Add to accquire user data */
// $role = $DB->get_records("mdl_")
// $role = $DB->get_record("role_assignments",array("userid"=>$USER->id));
//print_object($COURSE);die();

if ($USER->id != 2) {

    $PAGE->requires->css('/theme/classon/style/killCopy.css');
    $PAGE->requires->js('/theme/classon/amd/src/killCopy.js');
}
echo $OUTPUT->render_from_template('theme_classon/columns2', $templatecontext);

