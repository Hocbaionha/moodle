<?php

defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir .'/hbonlib/string_util.php';
require_once $CFG->libdir .'/hbonlib/lib.php';
require_once $CFG->libdir . '/formslib.php';

/**
 * Admin settings search form
 *
 * @package    admin
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class temp_student_form extends moodleform {

    function definition() {
        global $PAGE;
        $PAGE->requires->js(new moodle_url('/local/school/js/student.js'));
        $mform = $this->_form;
        $id = $this->_customdata['studentid'];
        $schoolid = $classid = 0;
        if (isset($this->_customdata['schoolid']))
            $schoolid = $this->_customdata['schoolid'];

        if (isset($this->_customdata['classid']))
            $classid = $this->_customdata['classid'];


        //$mform->addElement('header', 'settingsheader', get_string('search', 'admin'));
        $attributes = array('size' => '20');
        $schools = getSchools(null, 0);
        foreach ($schools as $school) {
            $key = $school->id;
            $value = $school->name;
            $selectSchool[$key] = $value;
        }
        if ($schoolid !== 0) {
            $classes = getClasses($schoolid);
        }
        $selectClass = array();
        foreach ($classes as $class) {
            $key = $class->id;
            $value = $class->name;
            $selectClass[$key] = $value;
        }
        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);
        $mform->setConstant('id', $id);
        $mform->addElement('hidden', 'schoolid', null);
        $mform->setType('schoolid', PARAM_INT);
        $mform->setConstant('schoolid', $schoolid);
        $mform->addElement('hidden', 'classid', null);
        $mform->setType('classid', PARAM_INT);
        $mform->setConstant('classid', $classid);
        $mform->addElement('select', 'school', get_string('school', 'local_school'), $selectSchool);
        $mform->setDefault('school', $schoolid);
        $mform->addElement('select', 'class', get_string('class', 'local_school'), $selectClass);
        if ($classid !== 0) {
            $mform->setDefault('class', $classid);
        }
        $mform->addElement('text', 'name', get_string('name', 'local_school')); // Add elements to your form
        $mform->setType('name', PARAM_TEXT);
        $arrayGender = array('1' => 'Nam', '0' => 'Nữ');
        $mform->addElement('select', 'gender', get_string('gender', 'local_school'), $arrayGender);
        $mform->setType('gender', PARAM_INT);

        $mform->addElement('date_selector', 'birth_date', get_string('birth_date', 'local_school'));
        $mform->setType('birth_date', PARAM_TEXT);
        $mform->addElement('text', 'parent', get_string('parent', 'local_school'));
        $mform->setType('parent', PARAM_TEXT);
        $mform->addElement('text', 'parent_phone', get_string('parent_phone', 'local_school'));
        $mform->setType('parent_phone', PARAM_TEXT);

        $mform->setType('query', PARAM_RAW);
        $mform->setDefault('query', optional_param('query', '', PARAM_RAW));
        $this->add_action_buttons();
    }

}
