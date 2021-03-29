<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

use Google\Cloud\Core\Timestamp;
use Google\Cloud\Firestore\FieldValue;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Firestore;
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
 * Photo backgrounds callbacks.
 *
 * @package    theme_hbon_app
 * @copyright
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_hbon_app_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    if ($filename == 'default.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');

    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_photo', 'preset', 0, '/', $filename))) {
        // This preset file was fetched from the file area for theme_photo and not theme_boost (see the line above).
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.
    // $pre = file_get_contents($CFG->dirroot . '/theme/hbon_app/scss/pre.scss');
    // // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.
    // $post = file_get_contents($CFG->dirroot . '/theme/hbon_app/scss/post.scss');

    // Combine them together.
    return $pre . "\n" . $scss . "\n" . $post;
}

/**
 * Copy the updated theme image to the correct location in dataroot for the image to be served
 * by /theme/image.php. Also clear theme caches.
 *
 * @param $settingname
 */
function theme_hbon_app_update_settings_images($settingname) {
    global $CFG;

    // The setting name that was updated comes as a string like 's_theme_photo_loginbackgroundimage'.
    // We split it on '_' characters.
    $parts = explode('_', $settingname);
    // And get the last one to get the setting name..
    $settingname = end($parts);

    // Admin settings are stored in system context.
    $syscontext = context_system::instance();
    // This is the component name the setting is stored in.
    $component = 'theme_hbon_app';


    // This is the value of the admin setting which is the filename of the uploaded file.
    $filename = get_config($component, $settingname);
    // We extract the file extension because we want to preserve it.
    $extension = substr($filename, strrpos($filename, '.') + 1);

    // This is the path in the moodle internal file system.
    $fullpath = "/{$syscontext->id}/{$component}/{$settingname}/0{$filename}";

    // This location matches the searched for location in theme_config::resolve_image_location.
    $pathname = $CFG->dataroot . '/pix_plugins/theme/hbon_app/' . $settingname . '.' . $extension;

    // This pattern matches any previous files with maybe different file extensions.
    $pathpattern = $CFG->dataroot . '/pix_plugins/theme/photo/' . $settingname . '.*';

    // Make sure this dir exists.
    @mkdir($CFG->dataroot . '/pix_plugins/theme/hbon_app/', $CFG->directorypermissions, true);

    // Delete any existing files for this setting.
    foreach (glob($pathpattern) as $filename) {
        @unlink($filename);
    }

    // Get an instance of the moodle file storage.
    $fs = get_file_storage();
    // This is an efficient way to get a file if we know the exact path.
    if ($file = $fs->get_file_by_hash(sha1($fullpath))) {
        // We got the stored file - copy it to dataroot.
        $file->copy_content_to($pathname);
    }

    // Reset theme caches.
    theme_reset_all_caches();
}


/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return array
 */
function theme_hbon_app_get_pre_scss($theme) {
    $scss = '';
    $configurable = [
        // Config key => [variableName, ...].
        'brandcolor' => ['primary'],
    ];

    // Prepend variables first.
    foreach ($configurable as $configkey => $targets) {
        $value = isset($theme->settings->{$configkey}) ? $theme->settings->{$configkey} : null;
        if (empty($value)) {
            continue;
        }
        array_map(function($target) use (&$scss, $value) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }, (array) $targets);
    }

    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    return $scss;
}

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_hbon_app_get_extra_scss($theme) {
    global $CFG;
    $content = '';

    // Set the page background image.
    $imageurl = $theme->setting_file_url('backgroundimage', 'backgroundimage');
    if (!empty($imageurl)) {
        $content .= '$imageurl: "' . $imageurl . '";';
        $content .= file_get_contents($CFG->dirroot .
            '/theme/hbon_app/scss/classic/body-background.scss');
    }

    if (!empty($theme->settings->navbardark)) {
        $content .= file_get_contents($CFG->dirroot .
            '/theme/hbon_app/scss/classic/navbar-dark.scss');
    } else {
        $content .= file_get_contents($CFG->dirroot .
            '/theme/hbon_app/scss/classic/navbar-light.scss');
    }
    if (!empty($theme->settings->scss)) {
        $content .= $theme->settings->scss;
    }
    return $content;
}

/**
 * Get compiled css.
 *
 * @return string compiled css
 */
function theme_hbon_app_get_precompiled_css() {
    global $CFG;
    return file_get_contents($CFG->dirroot . '/theme/hbon_app/style/moodle.css');
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_hbon_app_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM && ($filearea === 'backgroundimage')) {
        $theme = theme_config::load('classic');
        // By default, theme files must be cache-able by both browsers and proxies.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}

function login_from_app($idtokenfb=""){
	global $PAGE;
    //anhnn login from app
    $issuerid=1;// sso oauth2
    if($idtokenfb!=""){
        if(empty($USER->id) || isguestuser()){
//var_dump($PAGE->url->get_query_string());die;
            $factory = (new Factory)->withServiceAccount(dirname(dirname(__DIR__)) . '/firebasekey.json');
            $auth = $factory->createAuth();
            if (!isset($idtokenfb)) {
                return;
            }

            $signInResult = $auth->verifyIdToken($idtokenfb);
            $uid = $signInResult->getClaim('sub');
            $firestore = $factory->createFirestore();
            $fdb = $firestore->database();
            $stuSnapshot = $fdb->collection("users")->document($uid)->snapshot();
            if ($stuSnapshot->exists()) {
                $fbinfo = $stuSnapshot->data();
            }
            //do login TODO anhnn\
            global $SESSION;
            $SESSION->theme = "hbon_app";
            $actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	    $query_string = parse_url($actual_link);
            $scheme=$query_string['scheme'];
            $host=$query_string['host'];
            $path=$query_string['path'];
            $query=$query_string['query'];
	    parse_str($query, $array_params);

            unset($array_params['idtokenfb']);
            $redirect_link = $scheme."://".$host.$path."?".http_build_query($array_params);
            
	    
            $current = new moodle_url($actual_link);
            // var_dump($current);die;
            $issuer = new \core\oauth2\issuer($issuerid);
            $client = \core\oauth2\api::get_user_oauth_client($issuer, $current);
	    if ($client) {
//var_dump($client);die;
		    if($fbinfo["username"]==null){
			    $fbinfo["username"]=$fbinfo["email"];
		}
                $info =  ["email"=> $fbinfo['email'], "firstname"=> $fbinfo['firstname'], "lastname"=> $fbinfo["lastname"], "username"=> $fbinfo["username"], "uid"=>  $uid ];
                
		$auth = new \auth_oauth2\auth();
                $auth->complete_login($client, $redirect_link,$info); 
                redirect($redirect_link);
	    } else {
		    echo "error here;";die;
                throw new moodle_exception('Could not get an OAuth client.');
            }
        } 
    }
}
