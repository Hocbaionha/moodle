<?php
/**
 * A two column layout for the hbon theme.
 *
 * @package   theme_hbon
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/theme/hbon_app/lib.php');
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

$now =date("Y-m-d H:i:s");
$sql = "select * from mdl_hbon_popup_home where status=1 and public_at <= ? order by public_at DESC limit 1";
$popup_event = $DB->get_records_sql($sql,array("public_at"=>$now));
if (count($popup_event)>0){
    $popup = null;
    foreach ($popup_event as $object){
        if($object->to_course!= null){
            $in_course = json_decode($object->to_course);
            if(!empty((array)$in_course)){
                if( in_array($COURSE->shortname, (array)$in_course)){
                    if($object->is_countdown != 0){
                        $object->expitime = strtotime($object->expitime);
                        $popup = $object;
                    }else{
                        if(strtotime($object->expitime) > strtotime($now)){
                            $object->expitime = null;
                            $popup = $object;
                        }
                    }
                }
            }
        }
    }
}else{
    $popup = null;
}
$useragent=$_SERVER['HTTP_USER_AGENT'];
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
    $showpopup = false;
    $showsurvey = false;
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
    'fb_topic_name_in'=>$fb_topic_name_in,
    'popup_event' => $popup
//    'fb_id'=>$USER->uid
];

//die();
$nav = $PAGE->flatnav;
$templatecontext['flatnavigation'] = $nav;
$templatecontext['firstcollectionlabel'] = $nav->get_collectionlabel();

/* Add to accquire user data */
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

