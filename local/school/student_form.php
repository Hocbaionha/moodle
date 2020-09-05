<?php

defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir . '/formslib.php';

require_once $CFG->libdir .'/hbonlib/lib.php';
/**
 * Admin settings search form
 *
 * @package    admin
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class student_form extends moodleform {

    function definition() {
        global $PAGE;
        $PAGE->requires->js(new moodle_url('/local/school/js/student.js'));
        $mform = $this->_form;
        $id = $this->_customdata['studentid'];
        $schoolid = $classid=0;
        if(isset($this->_customdata['schoolid']))
        $schoolid = $this->_customdata['schoolid'];
        
        if(isset($this->_customdata['classid']))
        $classid = $this->_customdata['classid'];
        
        //$mform->addElement('header', 'settingsheader', get_string('search', 'admin'));
        $attributes = array('size' => '20');
        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);
        $mform->setConstant('id', $id);
        $attributes = array('size' => '20');
        $mform->addElement('hidden', 'school', $schoolid);
        $mform->setType('school', PARAM_INT);
//        $mform->setDefault( 'school', $schoolid);

        $attributes = array('size' => '20');
        $mform->addElement('hidden', 'class', $classid);
        $mform->setType('class', PARAM_INT);
//        $mform->setDefault('class', $classid);
        $mform->addElement('text', 'name', get_string('name', 'local_school')); // Add elements to your form
        $mform->setType('name', PARAM_TEXT);
        $arrayGender = array('1'=>'Nam','0'=>'Nữ');
        $mform->addElement('select', 'gender', get_string('gender', 'local_school'), $arrayGender); 
        $mform->setType('gender', PARAM_INT);
        
        $mform->addElement('date_selector', 'birthdate', get_string('birth_date','local_school'));
        $mform->setType('birthdate', PARAM_TEXT);
        $mform->addElement('text', 'parent', get_string('parent', 'local_school'));
        $mform->setType('parent', PARAM_TEXT);
        $mform->addElement('text', 'parentphone', get_string('parent_phone', 'local_school'));
        $mform->setType('parentphone', PARAM_TEXT);
        
        $mform->setType('query', PARAM_RAW);
        $mform->setDefault('query', optional_param('query', '', PARAM_RAW));
        $this->add_action_buttons();
    }

}
