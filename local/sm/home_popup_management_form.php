<?php

defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir . '/formslib.php';
require_once $CFG->libdir .'/hbonlib/lib.php';

class home_popup_management_form extends moodleform {

    function definition() {
        $maxbytes = 10240000;
        global $PAGE, $DB;
        $mform = $this->_form;
        $id = $this->_customdata['id'];
        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);
        $mform->setConstant('id', $id);
        $mform->addElement('text', 'title', get_string('title', 'local_sm'));
        $mform->setType('title', PARAM_TEXT);
        $mform->addElement('filepicker', 'image', "Ảnh", null,
                   array('maxbytes' => $maxbytes, 'accepted_types' => array('web_image')));
        $mform->addElement('text', 'link', get_string('link', 'local_sm'));
        $mform->setType('link', PARAM_TEXT);
        $mform->addElement('date_time_selector', 'public_at', "ngày active");
        $mform->setType('public_at', PARAM_TEXT);
        $mform->addElement('date_time_selector', 'expitime', "ngày hết hạn");
        $mform->setType('expitime', PARAM_TEXT);
        $mform->addElement('select', 'status', get_string('status', 'local_sm'), array(0,1));
        $mform->setType('status', PARAM_TEXT);
        $mform->addElement('select', 'is_countdown', get_string('is_countdown', 'local_sm'), array(0,1));
        $mform->setType('is_countdown', PARAM_TEXT);

        $mform->setType('query', PARAM_RAW);
        $mform->setDefault('query', optional_param('query', '', PARAM_RAW));
        $this->add_action_buttons();

    }

}
