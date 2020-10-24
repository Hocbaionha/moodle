<?php


defined('MOODLE_INTERNAL') || die();

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
require_once($CFG->libdir . '/behat/lib.php');

require_once($CFG->dirroot . '/cohort/locallib.php');
global $USER;
global $CFG;
global $DB;
if (false) {// alway redirect to homepage
//if ( isloggedin()&&(!isguestuser()) ) {
    $uid = $USER->id;

    if (isloggedin()) {
        if (!(cohort_is_member(1, $uid) || cohort_is_member(2, $uid) || cohort_is_member(3, $uid))) {
            cohort_add_member(1, $uid);
        }
    }

    //$navdraweropen = (get_user_preferences('drawer-open-nav', 'true') == 'true');
    $navdraweropen = true;

    $extraclasses = [];
    if ($navdraweropen) {
        $extraclasses[] = 'drawer-open-left';
    }
    $bodyattributes = $OUTPUT->body_attributes($extraclasses);
    $blockshtml = $OUTPUT->blocks('side-pre');
    $hasblocks = strpos($blockshtml, 'data-block=') !== false;
    $regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();

    $templatecontext = [
        'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
        'output' => $OUTPUT,
        'sidepreblocks' => $blockshtml,
        'hasblocks' => $hasblocks,
        'bodyattributes' => $bodyattributes,
        'navdraweropen' => $navdraweropen,
        'regionmainsettingsmenu' => $regionmainsettingsmenu,
        'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu)

    ];
    $nav = $PAGE->flatnav;

    //$PAGE->requires->js('/theme/maker/javascript/home.js');
    //$PAGE->requires->js_call_amd('theme_classon/countdowntimer', 'initialise');
    //$PAGE->requires->js_call_amd('theme_classon/tippy_call', 'init_tippy');
    $PAGE->requires->js_call_amd('theme_classon/main_slider_script', 'slider');
    $templatecontext['flatnavigation'] = $PAGE->flatnav;
    $templatecontext['firstcollectionlabel'] = $nav->get_collectionlabel();
    echo $OUTPUT->render_from_template('theme_classon/frontpage_guest', $templatecontext);


} else {


    //$setting = $PAGE->theme->settings->custommenuitems;
    //$setting = 'Hi test';

    //GET MENU AND RENDER
    require_once($CFG->dirroot . '/theme/classon/layout/component/get_menu.php');
    require_once($CFG->dirroot . '/theme/classon/layout/component/sso.php');
    //-- /GET MENU AND RENDER

    //GET TESMONIAL AND RENDER
    // will be call here
    require_once($CFG->dirroot . '/theme/classon/layout/front-page/tesmonial.php');

    //-- /GET TESMONIAL AND RENDER

    require_once($CFG->libdir . '/behat/lib.php');

    $navdraweropen = false;
    $extraclasses = [];
    if ($navdraweropen) {
        $extraclasses[] = 'drawer-open-left';
    }
    $bodyattributes = $OUTPUT->body_attributes($extraclasses);
    $blockshtml = $OUTPUT->blocks('side-pre');
    $hasblocks = strpos($blockshtml, 'data-block=') !== false;
    $regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();
    $sql = "select * from mdl_course_categories where idnumber='lop6' or idnumber='lop7' or idnumber='lop8' or idnumber='lop9' and visible=1 order by sortorder";
    $course_categorie = $DB->get_records_sql($sql);
    $course_categories = array();
    foreach ($course_categorie as $cc) {
        $title = $cc->name;
        // $img=$url."/theme/classon/pix/course/sh6.png";
        $desc = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";
        $sql = "select c.shortname,cd.* from mdl_course c join mdl_course_desc cd on c.id=cd.courseid where c.visible=1 and c.category = $cc->id";

        $_courses = $DB->get_records_sql($sql);
        $courses = array();
        foreach ($_courses as $course) {
            $courses[] = (array)$course;
        }
        //print_object($courses);die;
        $course_categories[] = array("title" => $title, "desc" => $desc, "id" => $cc->id, "courses" => $courses);

    }
    $now =date("Y-m-d H:i:s");
    $popup_event = $DB->get_records('hbon_popup_home', array('status'=>1),'created_at ASC','*',0,1);
    $popup = new stdClass();
    foreach ($popup_event as $object){
        $popup = $object;
    }
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
        'tesmonial' => $string01_tesmonial,
        'a' => $OUTPUT->main_content(),
        'isloggedin' => isloggedin(),
        'course_categories' => $course_categories,
        'stringmobilemenu' => $stringmobilemenu,
        'loginurl' => $loginurl,
        'signupurl' => $signupurl,
        'popup_event' => $popup
    ];
//var_dump($popup);die();
    $PAGE->requires->js_call_amd('theme_classon/classon_homepage', 'classon_homepage');
    $PAGE->requires->js('/theme/classon/amd/src/mmenu.js');


    // <script src="assets/js/jquery-ui.min.js"></script>
    // <script src="assets/js/mobilemenu.js"></script>

    $templatecontext['flatnavigation'] = $PAGE->flatnav;
    echo $OUTPUT->render_from_template('theme_classon/classon_frontpage', $templatecontext);


}


