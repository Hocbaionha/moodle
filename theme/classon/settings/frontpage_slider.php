<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version details
 *
 * @package    theme_classon
 * @copyright 2020 ClassOn
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

// Frontpage Slider.
$temp = new admin_settingpage('theme_classon_frontpage_slider', get_string('frontpageslidersettings', 'theme_classon'));

$temp->add(new admin_setting_heading('theme_classon_slideshow', get_string('slideshowsettingsheading', 'theme_classon'),
    format_text(get_string('slideshowdesc', 'theme_classon') .
        get_string('slideroption2snippet', 'theme_classon'), FORMAT_MARKDOWN)));

$name = 'theme_classon/sliderenabled';
$title = get_string('sliderenabled', 'theme_classon');
$description = get_string('sliderenableddesc', 'theme_classon');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$temp->add($setting);

$name = 'theme_classon/sliderfullscreen';
$title = get_string('sliderfullscreen', 'theme_classon');
$description = get_string('sliderfullscreendesc', 'theme_classon');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$temp->add($setting);
$from0to20px = array();
$name = 'theme_classon/slidermargintop';
$title = get_string('slidermargintop', 'theme_classon');
$description = get_string('slidermargintopdesc', 'theme_classon');
$radchoices = $from0to20px;
$setting = new admin_setting_configselect($name, $title, $description, '20px', $radchoices);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_classon/slidermarginbottom';
$title = get_string('slidermarginbottom', 'theme_classon');
$description = get_string('slidermarginbottomdesc', 'theme_classon');
$radchoices = $from0to20px;
$setting = new admin_setting_configselect($name, $title, $description, '20px', $radchoices);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

$name = 'theme_classon/slideroption2';
$title = get_string('slideroption2', 'theme_classon');
$description = get_string('slideroption2desc', 'theme_classon');
$sliderstyles = array();
$radchoices = $sliderstyles;
$setting = new admin_setting_configselect($name, $title, $description, 'nocaptions', $radchoices);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

if (!isset($PAGE->theme->settings->slideroption2)) {
    $PAGE->theme->settings->slideroption2 = 'slider1';
}

if ($PAGE->theme->settings->slideroption2 == 'slider1') {
    $name = 'theme_classon/sliderh3color';
    $title = get_string('sliderh3color', 'theme_classon');
    $description = get_string('sliderh3colordesc', 'theme_classon');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_classon/sliderh4color';
    $title = get_string('sliderh4color', 'theme_classon');
    $description = get_string('sliderh4colordesc', 'theme_classon');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_classon/slidersubmitcolor';
    $title = get_string('slidersubmitcolor', 'theme_classon');
    $description = get_string('slidersubmitcolordesc', 'theme_classon');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_classon/slidersubmitbgcolor';
    $title = get_string('slidersubmitbgcolor', 'theme_classon');
    $description = get_string('slidersubmitbgcolordesc', 'theme_classon');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#51666C', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
}

if ($PAGE->theme->settings->slideroption2 == 'slider2') {
    $name = 'theme_classon/slider2h3color';
    $title = get_string('slider2h3color', 'theme_classon');
    $description = get_string('slider2h3colordesc', 'theme_classon');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_classon/slider2h3bgcolor';
    $title = get_string('slider2h3bgcolor', 'theme_classon');
    $description = get_string('slider2h3bgcolordesc', 'theme_classon');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#000000', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_classon/slider2h4color';
    $title = get_string('slider2h4color', 'theme_classon');
    $description = get_string('slider2h4colordesc', 'theme_classon');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#000000', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_classon/slider2h4bgcolor';
    $title = get_string('slider2h4bgcolor', 'theme_classon');
    $description = get_string('slider2h4bgcolordesc', 'theme_classon');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_classon/slideroption2submitcolor';
    $title = get_string('slideroption2submitcolor', 'theme_classon');
    $description = get_string('slideroption2submitcolordesc', 'theme_classon');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_classon/slideroption2color';
    $title = get_string('slideroption2color', 'theme_classon');
    $description = get_string('slideroption2colordesc', 'theme_classon');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#51666C', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_classon/slideroption2a';
    $title = get_string('slideroption2a', 'theme_classon');
    $description = get_string('slideroption2adesc', 'theme_classon');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#51666C', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
}

// Number of Sliders.
$name = 'theme_classon/slidercount';
$title = get_string('slidercount', 'theme_classon');
$description = get_string('slidercountdesc', 'theme_classon');

//$default = theme_classon_DEFAULT_SLIDERCOUNT;
//$choices0to12 = array();
//$setting = new admin_setting_configselect($name, $title, $description, $default, $choices0to12);
//$setting->set_updatedcallback('theme_reset_all_caches');
//$temp->add($setting);

// If we don't have an slide yet, default to the preset.
$slidercount = get_config('theme_classon', 'slidercount');

//if (!$slidercount) {
//    $slidercount = theme_classon_DEFAULT_SLIDERCOUNT;
//}

for ($sliderindex = 1; $sliderindex <= $slidercount; $sliderindex++) {
    $fileid = 'p' . $sliderindex;
    $name = 'theme_classon/p' . $sliderindex;
    $title = get_string('sliderimage', 'theme_classon');
    $description = get_string('sliderimagedesc', 'theme_classon');
    $setting = new admin_setting_configstoredfile($name, $title, $description, $fileid);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_classon/p' . $sliderindex . 'url';
    $title = get_string('sliderurl', 'theme_classon');
    $description = get_string('sliderurldesc', 'theme_classon');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $temp->add($setting);

    $name = 'theme_classon/p' . $sliderindex . 'cap';
    $title = get_string('slidercaption', 'theme_classon');
    $description = get_string('slidercaptiondesc', 'theme_classon');
    $default = '';
    $setting = new classon_setting_confightmleditor($name, $title, $description, $default);
    $temp->add($setting);
}

$ADMIN->add('theme_classon', $temp);

$temp->add(new admin_setting_configtextarea('custommenuitems', new lang_string('custommenuitems', 'admin'),
    new lang_string('configcustommenuitems', 'admin'),
    'Khoá học
    -All courses|/course/
    -Course search|/course/search.php
    -###
    -FAQ|https://someurl.xyz/faq
    -Preguntas más frecuentes|https://someurl.xyz/pmf||es
    Mobile app|https://someurl.xyz/app|Download our app
    ', PARAM_RAW, '50', '10'));

//$ADMIN->add('themes', $temp);