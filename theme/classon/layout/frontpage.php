<?php
	

defined('MOODLE_INTERNAL') || die();

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
require_once($CFG->libdir . '/behat/lib.php');

require_once($CFG->dirroot.'/cohort/locallib.php');
global $USER;

if ( isloggedin()&&(!isguestuser()) ) {
	$uid = $USER->id;

    if (isloggedin())
    {
	    if (!(cohort_is_member(1, $uid) || cohort_is_member(2, $uid) || cohort_is_member(3, $uid)))
	        {
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

	$PAGE->requires->jquery ();
	/*
    $PAGE->requires->js('/theme/classon/plugins/revolution/js/jquery.themepunch.tools.min.js');
    $PAGE->requires->js('/theme/classon/plugins/revolution/js/jquery.themepunch.revolution.min.js');

    $PAGE->requires->js('/theme/classon/plugins/revolution/js/extensions/revolution.extension.actions.min.js');
    $PAGE->requires->js('/theme/classon/plugins/revolution/js/extensions/revolution.extension.carousel.min.js');
    $PAGE->requires->js('/theme/classon/plugins/revolution/js/extensions/revolution.extension.kenburn.min.js');
    $PAGE->requires->js('/theme/classon/plugins/revolution/js/extensions/revolution.extension.layeranimation.min.js');
    $PAGE->requires->js('/theme/classon/plugins/revolution/js/extensions/revolution.extension.navigation.min.js');
    $PAGE->requires->js('/theme/classon/plugins/revolution/js/extensions/revolution.extension.parallax.min.js');
    $PAGE->requires->js('/theme/classon/plugins/revolution/js/extensions/revolution.extension.slideanims.min.js');
    $PAGE->requires->js('/theme/classon/plugins/revolution/js/extensions/revolution.extension.video.min.js');
    */
	
	//$PAGE->requires->js('/theme/maker/javascript/home.js');
    
	$templatecontext['flatnavigation'] = $PAGE->flatnav;
	echo $OUTPUT->render_from_template('theme_classon/frontpage', $templatecontext);
    
    
    
    
} else {
    $navdraweropen = false;
    
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
	
	$PAGE->requires->css('/theme/classon/plugins/revolution/css/settings.css');
    $PAGE->requires->css('/theme/classon/plugins/revolution/css/layers.css');
    $PAGE->requires->css('/theme/classon/plugins/revolution/css/navigation.css');
	$PAGE->requires->jquery ();
    $PAGE->requires->js('/theme/classon/plugins/revolution/js/jquery.themepunch.tools.min.js');
    $PAGE->requires->js('/theme/classon/plugins/revolution/js/jquery.themepunch.revolution.min.js');

    $PAGE->requires->js('/theme/classon/plugins/revolution/js/extensions/revolution.extension.actions.min.js');
    $PAGE->requires->js('/theme/classon/plugins/revolution/js/extensions/revolution.extension.carousel.min.js');
    $PAGE->requires->js('/theme/classon/plugins/revolution/js/extensions/revolution.extension.kenburn.min.js');
    $PAGE->requires->js('/theme/classon/plugins/revolution/js/extensions/revolution.extension.layeranimation.min.js');
    $PAGE->requires->js('/theme/classon/plugins/revolution/js/extensions/revolution.extension.navigation.min.js');
    $PAGE->requires->js('/theme/classon/plugins/revolution/js/extensions/revolution.extension.parallax.min.js');
    $PAGE->requires->js('/theme/classon/plugins/revolution/js/extensions/revolution.extension.slideanims.min.js');
    $PAGE->requires->js('/theme/classon/plugins/revolution/js/extensions/revolution.extension.video.min.js');
    $PAGE->requires->js('/theme/classon/main-slider-script.js');
	//$PAGE->requires->js('/theme/maker/javascript/home.js');
	
	$templatecontext['flatnavigation'] = $PAGE->flatnav;
	echo $OUTPUT->render_from_template('theme_classon/frontpage_guest', $templatecontext);
    
    
    
}


