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
class class_form extends moodleform {

    function definition() {
//        global $PAGE;
//        $PAGE->requires->js(new moodle_url('/local/school/js/school.js'));
        $mform = $this->_form;
        $id = $this->_customdata['id'];
        
        //$mform->addElement('header', 'settingsheader', get_string('search', 'admin'));
        $attributes = array('size' => '20');
        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);
        $mform->setConstant('id', $id);
        $mform->addElement('text', 'code', get_string('code', 'local_class_regist'), $attributes);
        $mform->setType('code', PARAM_TEXT);
        $mform->addElement('text', 'name', get_string('name', 'local_class_regist')); // Add elements to your form
        $mform->setType('name', PARAM_TEXT);
        $mform->addElement('text', 'level', get_string('level', 'local_class_regist')); // Add elements to your form
        $mform->setType('level', PARAM_TEXT);
        $mform->addElement('text', 'limited', get_string('limit', 'local_class_regist')); // Add elements to your form
        $mform->setType('limited', PARAM_INT);
        $mform->addElement('text', 'schedule', get_string('schedule', 'local_class_regist')); // Add elements to your form
        $mform->setType('schedule', PARAM_TEXT);
        $mform->addElement('textarea', 'linkzoom', get_string('linkzoom', 'local_class_regist')); // Add elements to your form
        $mform->setType('linkzoom', PARAM_TEXT);
        $mform->addElement('text', 'zoomid', get_string('zoomid', 'local_class_regist')); // Add elements to your form
        $mform->setType('zoomid', PARAM_TEXT);
        $mform->addElement('text', 'zoompass', get_string('zoompass', 'local_class_regist')); // Add elements to your form
        $mform->setType('zoompass', PARAM_TEXT);
        $mform->setType('query', PARAM_RAW);
        $mform->setDefault('query', optional_param('query', '', PARAM_RAW));
        $this->add_action_buttons();
    }

}
