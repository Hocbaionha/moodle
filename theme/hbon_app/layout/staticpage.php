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

/*Begin- add cohort */
require_once($CFG->dirroot.'/cohort/locallib.php');
global $USER;
if ( isloggedin() && !isguestuser() ) {
    $uid = $USER->id;
    if (!(cohort_is_member(1, $uid) || cohort_is_member(2, $uid) || cohort_is_member(3, $uid))){
        cohort_add_member(1, $uid);
    }
}
/*End- add cohort*/

$navdraweropen = false;

$extraclasses = [];
if ($navdraweropen) {
    $extraclasses[] = 'drawer-open-left';
}
$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = strpos($blockshtml, 'data-block=') !== false;
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;
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
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu)
];

$nav = $PAGE->flatnav;
$templatecontext['flatnavigation'] = $nav;
$templatecontext['firstcollectionlabel'] = $nav->get_collectionlabel();
echo $OUTPUT->render_from_template('theme_hbon_app/staticpage', $templatecontext);

