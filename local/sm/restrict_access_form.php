<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->libdir.'/gradelib.php');

/**
 * Default form for editing course section
 *
 * Course format plugins may specify different editing form to use
 */
class restrictaccess_form extends moodleform {

    function definition() {
        global $CFG, $DB;
        $mform  = $this->_form;
        $courseid = $this->_customdata['courseid'];
        $courses=$DB->get_records("course",array("visible"=>1));
        $modules = $DB->get_records("modules",array("visible"=>1),'name');
        $length = count($modules);
        // print_object($courses);die;
        foreach($courses as $course){
            $key = $course->id;
            $value = $course->shortname;
            $selectArray[$key] = $value;
        }
        $mform->addElement('select', 'course', get_string('course', 'local_sm'), $selectArray); 
        $mform->setDefault('course', $courseid);
        $i=0;
        $mform->addElement('html', '<div class="row">');
        $mform->addElement('html', '<div class="col-md-3">');
        foreach($modules as $module){
            $i++;
            $mform->addElement('checkbox', "module-".$module->id, $module->name);
                $mform->addElement('html', '</div>');    
                $mform->addElement('html', '<div class="col-md-3">');
            
        }
        $mform->addElement('html', '</div>');
        $mform->addElement('html', '</div>');
        $mform->addElement('checkbox', "topic", "topic");
        // additional fields that course format has defined
        
        $mform->_registerCancelButton('cancel');
    }

    public function definition_after_data() {
        global $CFG, $DB;
        $availability = $this->_customdata['availability'];
        $courseid = $this->_customdata['courseid'];
        if(!$courseid){
            $courseid=1;
        }
        $course=$DB->get_record("course",array("id"=>$courseid));
        $mform  = $this->_form;
        if (!empty($CFG->enableavailability)&&$course) {
            $mform->addElement('header', 'availabilityconditions',
                    get_string('restrictaccess', 'availability'));
            $mform->setExpanded('availabilityconditions', true);
            $mform->addElement('textarea', 'availabilityconditionsjson',
                    get_string('accessrestrictions', 'availability'));
                    $mform->setDefault('availabilityconditionsjson', $availability);
            \core_availability\frontend::include_all_javascript($course, null, null);
        }

        $this->add_action_buttons();
    }

    /**
     * Load in existing data as form defaults
     *
     * @param stdClass|array $default_values object or array of default values
     */
    function set_data($default_values) {
        if (!is_object($default_values)) {
            // we need object for file_prepare_standard_editor
            $default_values = (object)$default_values;
        }
        $editoroptions = $this->_customdata['editoroptions'];
        $default_values = file_prepare_standard_editor($default_values, 'summary', $editoroptions,
                $editoroptions['context'], 'course', 'section', $default_values->id);
        if (strval($default_values->name) === '') {
            $default_values->name = false;
        }
        parent::set_data($default_values);
    }

}
