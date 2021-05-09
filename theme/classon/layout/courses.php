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
$PAGE->requires->jquery();
$PAGE->requires->css(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css'));
$PAGE->requires->css(new moodle_url('/local/class_regist/css/custom.css'));

$url = new moodle_url('/');
$navdraweropen = false;
$extraclasses = [];
if ($navdraweropen) {
    $extraclasses[] = 'drawer-open-left';
}
$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$thcs=array("lop6","lop7","lop8","lop9","ltv10","plt10","english","hbon_math");
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

$showpopup = false;
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
        $showpopup = true;
    }else{
        $check_survey = $DB->get_record('hbon_collect_info', array('userid'=>$USER->id));
        if(!empty($check_survey)){
            if($check_survey->status_survey === NULL){
                $showsurvey = true;
            }
        }
    }
}
$useragent=$_SERVER['HTTP_USER_AGENT'];
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
    $showpopup = false;
    $showsurvey = false;
}

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
    'signupurl'=>$signupurl,
    'showpopup'=>$showpopup,
    'showsurvey'=>$showsurvey
];

$PAGE->requires->js_call_amd('theme_classon/classon_homepage', 'classon_homepage');
$PAGE->requires->js('/theme/classon/amd/src/mmenu.js');
//$PAGE->requires->js_call_amd('theme_classon/classon_courses', 'classon_courses');
echo $OUTPUT->render_from_template('theme_classon/classon_courses', $templatecontext);


