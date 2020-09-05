<?php

defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir . '/formslib.php';
require_once $CFG->libdir .'/hbonlib/string_util.php';
require_once $CFG->libdir .'/hbonlib/lib.php';
/**
 * Admin settings search form
 *
 * @package    admin
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_form extends moodleform {

    function definition() {
        global $PAGE;
        $PAGE->requires->js(new moodle_url('/local/school/js/school.js'));
        $mform = $this->_form;
        $id = $this->_customdata['userid'];
        if(isset($this->_customdata['schoolid']))
        $schoolid = $this->_customdata['schoolid'];
        else $schoolid="";
        //$mform->addElement('header', 'settingsheader', get_string('search', 'admin'));
        $attributes = array('size' => '20');
        $schools = getSchools();
        foreach($schools as $school){
            $key = $school->id;
            $value = $school->name;
            $selectArray[$key] = $value;
        }
        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);
        $mform->setConstant('id', $id);
        $mform->addElement('select', 'school', get_string('schoolid', 'local_school'), $selectArray); 
        $mform->setDefault('school', $schoolid);
        $mform->addElement('text', 'name', get_string('name', 'local_school')); // Add elements to your form
        $mform->setType('name', PARAM_TEXT);
        $mform->addElement('text', 'email', get_string('email'));
        $mform->setType('email', PARAM_TEXT);
        $mform->addElement('text', 'phone', get_string('phone', 'local_school'));
        $mform->setType('phone', PARAM_TEXT);
        
        $mform->setType('query', PARAM_RAW);
        $mform->setDefault('query', optional_param('query', '', PARAM_RAW));
        $this->add_action_buttons();
    }

}
