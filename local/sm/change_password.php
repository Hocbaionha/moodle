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
 * Change password page.
 *
 * @package    core
 * @subpackage auth
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(__DIR__)) . '/config.php');
global $USER,$CFG;

if($USER->auth!=="oauth2"){
    redirect(new moodle_url("/login/change_password.php"));
}
require_once($CFG->dirroot.'/user/lib.php');
require_once('change_password_form.php');
require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/webservice/lib.php');

require_once($CFG->dirroot.'/login/lib.php');

$id     = optional_param('id', SITEID, PARAM_INT); // current course
$return = optional_param('return', 0, PARAM_BOOL); // redirect after password change

$systemcontext = context_system::instance();

$PAGE->set_url('/login/change_password.php', array('id'=>$id));

$PAGE->set_context($systemcontext);

//anhnn disable change password form
if ($return) {
// if(true){
    // this redirect prevents security warning because https can not POST to http pages
    if (empty($SESSION->wantsurl)
            or stripos(str_replace('https://', 'http://', $SESSION->wantsurl), str_replace('https://', 'http://', $CFG->wwwroot.'/login/change_password.php')) === 0) {
        $returnto = "$CFG->wwwroot/user/preferences.php?userid=$USER->id&course=$id";
    } else {
        $returnto = $SESSION->wantsurl;
    }
    unset($SESSION->wantsurl);

    redirect($returnto);
}

$strparticipants = get_string('participants');


// require proper login; guest user can not change password
if (!isloggedin() or isguestuser()) {
    if (empty($SESSION->wantsurl)) {
        $SESSION->wantsurl = $CFG->wwwroot.'/local/sm/change_password.php';
    }
    redirect(get_login_url());
}
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('admin');

// do not require change own password cap if change forced
if (!get_user_preferences('auth_forcepasswordchange', false)) {
    require_capability('moodle/user:changeownpassword', $systemcontext);
}

// do not allow "Logged in as" users to change any passwords
if (\core\session\manager::is_loggedinas()) {
    print_error('cannotcallscript');
}

$userauth = get_auth_plugin($USER->auth);
$mform = new login_change_password_form();


$navlinks = array();

if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot.'/my');

} else if ($data = $mform->get_data()) {
    
    if (!$userauth->user_update_password($USER, $data->newpassword1)) {
        print_error('errorpasswordupdate', 'auth');
    }
    //AnhNN call firebase 
    firebasechangepass($USER->uid,$data->newpassword1);
    user_add_password_history($USER->id, $data->newpassword1);

    if (!empty($CFG->passwordchangelogout)) {
        \core\session\manager::kill_user_sessions($USER->id, session_id());
    }

    if (!empty($data->signoutofotherservices)) {
        webservice::delete_user_ws_tokens($USER->id);
    }
    
    
    // Reset login lockout - we want to prevent any accidental confusion here.
    login_unlock_account($USER);

    // register success changing password
    unset_user_preference('auth_forcepasswordchange', $USER);
    unset_user_preference('create_password', $USER);

    $strpasswordchanged = get_string('passwordchanged');

    // Plugins can perform post password change actions once data has been validated.
    core_login_post_change_password_requests($data);

    $fullname = fullname($USER, true);

    $PAGE->set_title($strpasswordchanged);
    $PAGE->set_heading(fullname($USER));
    echo $OUTPUT->header();

    notice($strpasswordchanged, new moodle_url($PAGE->url, array('return'=>1)));

    echo $OUTPUT->footer();
    exit;
}

$strchangepassword = get_string('changepassword');

$fullname = fullname($USER, true);

$PAGE->set_title($strchangepassword);
$PAGE->set_heading($fullname);
echo $OUTPUT->header();

if (get_user_preferences('auth_forcepasswordchange')) {
    echo $OUTPUT->notification(get_string('forcepasswordchangenotice'));
}
$mform->display();
echo $OUTPUT->footer();

function httpPost($url, $data) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); //ssl stuff
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function firebasechangepass($uid,$password){
    global $USER,$CFG;
    $url =  $CFG->changepass;
    if(!isset($url)){
        $url="https://localhost:5000/change_pass";
    }
    $data=array("uid"=>$uid,'password'=>$password);
    $result = httpPost($url, $data);
}