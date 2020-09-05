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
class edit_course_form extends moodleform {

    function definition() {
        $maxbytes = 10240000;
        $mform = $this->_form;
        $id = $this->_customdata['id'];
        
        //$mform->addElement('header', 'settingsheader', get_string('search', 'admin'));
        $attributes = 'maxlength="254" size="50"';
        $mform->addElement('static', 'fullname', get_string("fullname_course","local_school"),"");
        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);
        $mform->setConstant('id', $id);
        $mform->addElement('hidden', 'shortname', null);
        $mform->setType('shortname', PARAM_TEXT);
        
        $mform->addElement('hidden', 'courseid', null);
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('text', 'title', get_string('course_title', 'local_school'), $attributes);
        $mform->setType('title', PARAM_TEXT);
        $mform->addElement('editor', 'teacher_desc', get_string('teacher_desc', 'local_school')); 
        $mform->setType('teacher_desc', PARAM_RAW);
        $mform->addElement('filepicker', 'teacher_img', get_string('teacher_img','local_school'), null,
                   array('maxbytes' => $maxbytes, 'accepted_types' => array('web_image')));
        $mform->addElement('text', 'sample_title', get_string('sample_title', 'local_school'), $attributes);
        $mform->setType('sample_title', PARAM_TEXT);
        $mform->addElement('text', 'sample_link', get_string('sample_link', 'local_school'), $attributes); 
        $mform->setType('sample_link', PARAM_TEXT);
        $mform->addElement('filepicker', 'sample_img', get_string('sample_img','local_school'), null,
                   array('maxbytes' => $maxbytes, 'accepted_types' => array('web_image')));
        $mform->addElement('editor', 'review', get_string('review', 'local_school')); 
        $mform->setType('review', PARAM_RAW);
        $mform->addElement('filepicker', 'introduce_img', get_string('introduce_img','local_school'), null,
                   array('maxbytes' => $maxbytes, 'accepted_types' => array('web_image')));
        $mform->addElement('text', 'introduce_link', get_string('introduce_link', 'local_school'), $attributes); 
        $mform->setType('introduce_link', PARAM_TEXT);
        $mform->addElement('editor', 'introduce_desc', get_string('introduce_desc', 'local_school')); 
        $mform->setType('introduce_desc', PARAM_RAW);
        $mform->addElement('text', 'price', get_string('price', 'local_school'), $attributes); 
        $mform->setType('price', PARAM_TEXT);
        $mform->addElement('filepicker', 'thumb_img', get_string('thumb_img','local_school'), null,
                   array('maxbytes' => $maxbytes, 'accepted_types' => array('web_image')));
        
        $mform->addElement('text', 'thumb_desc', get_string('thumb_desc', 'local_school'), $attributes); 
        $mform->setType('thumb_desc', PARAM_TEXT);
        $mform->addElement('text', 'productid', get_string('productid', 'local_school'), $attributes);
        $mform->setType('productid', PARAM_INT);
        $this->add_action_buttons();
    }

}
