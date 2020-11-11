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
class member_form extends moodleform {

    function definition() {
        global $DB;
//        $PAGE->requires->js(new moodle_url('/local/school/js/school.js'));
        $mform = $this->_form;
        $id = $this->_customdata['id'];

        //$mform->addElement('header', 'settingsheader', get_string('search', 'admin'));
        $attributes = array('size' => '20');
        $only_read = array('readonly' => 'readonly');
        if($id !=null && $id!=''){
            $mform->addElement('hidden', 'id', null);
            $mform->setType('id', PARAM_INT);
            $mform->setConstant('id', $id);

            $mform->addElement('text', 'classid', get_string('classid', 'local_class_regist'), $only_read);
            $mform->setType('classid', PARAM_TEXT);

            $mform->addElement('text', 'name', get_string('name', 'local_class_regist'),$only_read); // Add elements to your form
            $mform->setType('name', PARAM_TEXT);

            $mform->addElement('text', 'class', get_string('class', 'local_class_regist'),$only_read); // Add elements to your form
            $mform->setType('class', PARAM_TEXT);

            $mform->addElement('text', 'school', get_string('school', 'local_class_regist'),$only_read); // Add elements to your form
            $mform->setType('school', PARAM_INT);
            $mform->addElement('text', 'province', get_string('province', 'local_class_regist'),$only_read); // Add elements to your form
            $mform->setType('province', PARAM_TEXT);

            $mform->addElement('text', 'phone', get_string('phone', 'local_class_regist'),$only_read); // Add elements to your form
            $mform->setType('phone', PARAM_TEXT);

            $mform->addElement('textarea', 'comments', get_string('comments', 'local_class_regist'),array('cols'=>'100','rows'=>'10')); // Add elements to your form
            $mform->setType('comments', PARAM_TEXT);

            $mform->setType('query', PARAM_RAW);
            $mform->setDefault('query', optional_param('query', '', PARAM_RAW));
            $this->add_action_buttons();
        }else{
            $mform->addElement('hidden', 'id', null);
            $mform->setType('id', PARAM_INT);
            $mform->setConstant('id', $id);
            $classid = $DB->get_records('hbon_classes');
            $cond = [];
            foreach ($classid as $object){
                $cond[$object->id] = $object->code;
            }
            $mform->addElement('select', 'classid', get_string('classid', 'local_class_regist'),$cond);
            $mform->setType('classid', PARAM_TEXT);

            $mform->addElement('text', 'name', get_string('name', 'local_class_regist'),$attributes); // Add elements to your form
            $mform->setType('name', PARAM_TEXT);

            $mform->addElement('text', 'class', get_string('class', 'local_class_regist'),$attributes); // Add elements to your form
            $mform->setType('class', PARAM_TEXT);

            $mform->addElement('text', 'school', get_string('school', 'local_class_regist'),$attributes); // Add elements to your form
            $mform->setType('school', PARAM_INT);
            $mform->addElement('text', 'province', get_string('province', 'local_class_regist'),$attributes); // Add elements to your form
            $mform->setType('province', PARAM_TEXT);

            $mform->addElement('text', 'phone', get_string('phone', 'local_class_regist'),$attributes); // Add elements to your form
            $mform->setType('phone', PARAM_TEXT);

            $mform->addElement('textarea', 'comments', get_string('comments', 'local_class_regist'),array('cols'=>'100','rows'=>'10')); // Add elements to your form
            $mform->setType('comments', PARAM_TEXT);

            $mform->setType('query', PARAM_RAW);
            $mform->setDefault('query', optional_param('query', '', PARAM_RAW));
            $this->add_action_buttons();
        }

    }

}
