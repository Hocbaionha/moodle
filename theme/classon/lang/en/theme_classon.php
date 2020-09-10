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
 * Language file.
 *
 * @package   theme_classon
 * @copyright 2020 ClassOn
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

// The name of the second tab in the theme settings.
$string['advancedsettings'] = 'Caì đặt nâng cao';
// The backgrounds tab name.
$string['backgrounds'] = 'Backgrounds';
// The brand colour setting.
$string['brandcolor'] = 'Brand colour';
// The brand colour setting description.
$string['brandcolor_desc'] = 'The accent colour.';
// A description shown in the admin theme selector.
$string['choosereadme'] = 'Theme classon is a child theme of Boost. It adds the ability to upload background photos.';
// Name of the settings pages.
$string['configtitle'] = 'Thiết lập cho classon';
// Background image for dashboard page.
$string['dashboardbackgroundimage'] = 'Dashboard page background image';
// Background image for dashboard page.
$string['dashboardbackgroundimage_desc'] = 'An image that will be stretched to fill the background of the dashboard page.';
// Background image for default page.
$string['defaultbackgroundimage'] = 'Default page background image';
// Background image for default page.
$string['defaultbackgroundimage_desc'] = 'An image that will be stretched to fill the background of all pages without a more specific background image.';
// Background image for front page.
$string['frontpagebackgroundimage'] = 'Front page background image';
// Background image for front page.
$string['frontpagebackgroundimage_desc'] = 'An image that will be stretched to fill the background of the front page.';
// Name of the first settings tab.
$string['generalsettings'] = 'General settings';
// Background image for incourse page.
$string['incoursebackgroundimage'] = 'Course page background image';
// Background image for incourse page.
$string['incoursebackgroundimage_desc'] = 'An image that will be stretched to fill the background of course pages.';
// Background image for login page.
$string['loginbackgroundimage'] = 'Login page background image';
// Background image for login page.
$string['loginbackgroundimage_desc'] = 'An image that will be stretched to fill the background of the login page.';
// The name of our plugin.
$string['pluginname'] = 'classon';
// Preset files setting.
$string['presetfiles'] = 'Additional theme preset files';
// Preset files help text.
$string['presetfiles_desc'] = 'Preset files can be used to dramatically alter the appearance of the theme. See <a href=https://docs.moodle.org/dev/Boost_Presets>Boost presets</a> for information on creating and sharing your own preset files, and see the <a href=http://moodle.net/boost>Presets repository</a> for presets that others have shared.';
// Preset setting.
$string['preset'] = 'Theme preset';
// Preset help text.
$string['preset_desc'] = 'Pick a preset to broadly change the look of the theme.';
// Raw SCSS setting.
$string['rawscss'] = 'Raw SCSS';
// Raw SCSS setting help text.
$string['rawscss_desc'] = 'Use this field to provide SCSS or CSS code which will be injected at the end of the style sheet.';
// Raw initial SCSS setting.
$string['rawscsspre'] = 'Raw initial SCSS';
// Raw initial SCSS setting help text.
$string['rawscsspre_desc'] = 'In this field you can provide initialising SCSS code, it will be injected before everything else. Most of the time you will use this setting to define variables.';
// We need to include a lang string for each block region.
$string['region-side-pre'] = 'Right';

//SLIDER
$string['frontpageslidersettings'] = 'Slider Trang chủ';
$string['slideshowsettingsheading'] = 'Customize the carousel on the front page. See the layout <a href="./../theme/classon/pix/layout.png" target="_blank"> here</a>';
$string['slideshowdesc'] = 'Upload the images, add the links and description for the carousel on the front page.';
$string['slideroption2snippet'] = '<p>Sample HTML for Slider Captions:</p>
<pre>
&#x3C;div class=&#x22;span6 col-sm-6&#x22;&#x3E;
&#x3C;h3&#x3E;Hand-crafted&#x3C;/h3&#x3E; &#x3C;h4&#x3E;pixels and code for the Moodle community&#x3C;/h4&#x3E;
&#x3C;a href=&#x22;#&#x22; class=&#x22;submit&#x22;&#x3E;Please favorite our theme!&#x3C;/a&#x3E;
</pre>';
$string['sliderenabled'] = 'Enable Slider';
$string['sliderenableddesc'] = 'Enable a slider at the top of your home page';
$string['sliderfullscreen'] = 'Slider full screen';
$string['sliderfullscreendesc'] = 'Check this box to make the slider full screen (100% width)';
$string['slidermargintop'] = 'Margin above slider';
$string['slidermargintopdesc'] = 'Set the size of the margin above the slider.';

$string['slidermarginbottom'] = 'Margin below slider';
$string['slidermarginbottomdesc'] = 'Set the size of the margin below the slider.';

$string['slideroption2'] = 'Choose Slider Type';
$string['slideroption2desc'] = 'Choose Slider Type <strong>and then click SAVE</strong> to see colour settings for your chosen slider';

$string['sliderh3color'] = 'Slider 1 H3 Colour';
$string['sliderh3colordesc'] = 'Choose the colour you want for the slider 1 H3 tag';

$string['sliderh4color'] = 'Slider 1 H4 Colour';
$string['sliderh4colordesc'] = 'Choose the colour you want for the slider 1 H4 tag';

$string['slidersubmitcolor'] = 'Slider 1 Submit Text';
$string['slidersubmitcolordesc'] = 'Choose the text colour of the Slider 1 submit button';

$string['slidersubmitbgcolor'] = 'Slider 1 Submit bg';
$string['slidersubmitbgcolordesc'] = 'Choose the background colour of the Slider 1 submit button';

$string['slider2h3color'] = 'Slider 2 H3 Text Colour';
$string['slider2h3colordesc'] = 'Choose the text colour you want for the slider 2 H3 tag';

$string['slider2h4color'] = 'Slider 2 H4 Text Colour';
$string['slider2h4colordesc'] = 'Choose the text colour you want for the slider 2 H4 tag';

$string['slider2h3bgcolor'] = 'Slider 2 H3 bg Colour';
$string['slider2h3bgcolordesc'] = 'Choose the background colour you want for the slider 2 H3 tag';

$string['slider2h4bgcolor'] = 'Slider 2 H4 bg Colour';
$string['slider2h4bgcolordesc'] = 'Choose the background colour you want for the slider 2 H4 tag';

$string['slideroption2submitcolor'] = 'Slider 2 Submit Text';
$string['slideroption2submitcolordesc'] = 'Set a background colour for the submit text in slider style option 2 colour';

$string['slideroption2color'] = 'Slider 2 Submit bg';
$string['slideroption2colordesc'] = 'Set a background colour for the submit text in slider style option';

$string['slideroption2a'] = 'Slider style option 2 arrow background colour';
$string['slideroption2adesc'] = 'Set the slider style option 2 arrow background colour';

$string['sliderstyle1'] = 'Slider style 1';
$string['sliderstyle2'] = 'Slider style 2';

$string['configcustommenuitems'] = 'Cấu hình menu tuỳ biến tại đây. Enter each menu item on a new line with format: menu text, a link URL (optional, not for a top menu item with sub-items), a tooltip title (optional) and a language code or comma-separated list of codes (optional, for displaying the line to users of the specified language only), separated by pipe characters. Lines starting with a hyphen will appear as menu items in the previous top level menu and ### makes a divider. For example:
<pre>
Courses
-All courses|/course/
-Course search|/course/search.php
-###
-FAQ|https://someurl.xyz/faq
-Preguntas más frecuentes|https://someurl.xyz/pmf||es
Mobile app|https://someurl.xyz/app|Download our app
</pre>';

$string['slidercount'] = 'slidercount'; 
$string['slidercountdesc'] = 'slidercountdesc';
$string['navbardarkdesc'] = 'Swaps text and background colours for the navbar at the top of the page between dark and light.';
$string['navbardark'] = 'Use a dark style navbar';