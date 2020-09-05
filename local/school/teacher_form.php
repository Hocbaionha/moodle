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
class teacher_form extends moodleform {

    function definition() {
        $mform = $this->_form;
        $id = $this->_customdata['teacherid'];
        if (isset($this->_customdata['schoolid']))
            $schoolid = $this->_customdata['schoolid'];
        $provincename = null;
        $districtname = null;
        $schoolname = null;
        $attributes = array('size' => '20');
        $school = getDataSchools($schoolid);
        if (!empty($school)) {
            $provincename = $school->province->name;
            $schoolname = $school->name;
            $districtname = $school->district->name;
        }
        $departments = getDepartments();
        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);
        $mform->setConstant('id', $id);
        $mform->addElement('hidden', 'school', null);
        $mform->setType('school', PARAM_INT);
        $mform->setConstant('school', $schoolid);
        $mform->addElement("static", "description", get_string('province', 'local_school'), $provincename);
        $mform->addElement("static", "description", get_string('district', 'local_school'), $districtname);
        $mform->addElement("static", "description", get_string('school', 'local_school'), $schoolname);
        $mform->addElement('text', 'name', get_string('name', 'local_school'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addElement('select', 'department', get_string('department', 'local_school'), $departments);
        $mform->setType('department', PARAM_TEXT);
        $mform->addElement('text', 'phone', get_string('phone', 'local_school'));
        $mform->setType('phone', PARAM_TEXT);

        $mform->setType('query', PARAM_RAW);
        $mform->setDefault('query', optional_param('query', '', PARAM_RAW));
        $this->add_action_buttons();
    }

}
