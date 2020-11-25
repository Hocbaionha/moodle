<?php

defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir . '/formslib.php';
require_once $CFG->libdir .'/hbonlib/lib.php';
require_once(__DIR__.'/helper.php');

class mapping_test_management_form extends moodleform {


    function definition() {
        global $DB,$USER;
        $mform = $this->_form;
        $id = $this->_customdata['id'];
        $attributes = array('size' => '20');
        $only_read = array('readonly' => 'readonly');
        if($id !=null && $id!=''){
            $mform->addElement('hidden', 'id', null);
            $mform->setType('id', PARAM_INT);
            $mform->setConstant('id', $id);

            $list = ladipage_origin_url();

//            $cond
//            foreach ($list as $key=>$object){
//
//            }

            $mform->addElement('select', 'url_input', get_string('url_input', 'local_sm'),$list);
            $mform->setType('url_input', PARAM_TEXT);

            $mform->addElement('text', 'url_output', get_string('url_output', 'local_sm')); // Add elements to your form
            $mform->setType('url_output', PARAM_TEXT);

            $mform->addElement('text', 'class', get_string('class', 'local_sm'),$only_read); // Add elements to your form
            $mform->setType('class', PARAM_TEXT);

            $mform->addElement('text', 'subject', get_string('subject', 'local_sm'),$only_read); // Add elements to your form
            $mform->setType('subject', PARAM_INT);

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
                if(isset($object->is_accept)){
                    $accept_user = explode(',', $object->is_accept);
                    if(in_array($USER->id, $accept_user) ){
                        $cond[$object->id] = $object->code;
                    }elseif (is_siteadmin($USER)){
                        $cond[$object->id] = $object->code;
                    }
                }
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


