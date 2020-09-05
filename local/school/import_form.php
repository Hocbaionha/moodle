<?php
defined('MOODLE_INTERNAL') || die();
require_once $CFG->libdir . '/formslib.php';
class import_form extends moodleform {
    
    protected function definition() {
        $maxbytes = 10240000;
        $mform = $this->_form;
        $schools = getSchools(null,0);
        $selectArray=array();
        foreach($schools as $school){
            $key = $school->id;
            $value = $school->name;
            $selectArray[$key] = $value;
        }
        $mform->addElement('select', 'school', get_string('school', 'local_school'), $selectArray); 
        
        $mform->addElement('filepicker', 'userfile', get_string('file'), null,
                   array('maxbytes' => $maxbytes, 'accepted_types' => '*'));
        $this->add_action_buttons();
    }

}