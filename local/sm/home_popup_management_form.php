<?php

defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir . '/formslib.php';
require_once $CFG->libdir .'/hbonlib/lib.php';

class home_popup_management_form extends moodleform {

    function definition() {
        $maxbytes = 10240000;
        global $PAGE, $DB, $CFG;
        $mform = $this->_form;
        $id = $this->_customdata['id'];
        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);
        $mform->setConstant('id', $id);
        $mform->addElement('text', 'title', "Tiêu đề");
        $mform->setType('title', PARAM_TEXT);
        $mform->addElement('filepicker', 'image', "Ảnh", null, array('maxbytes' => $maxbytes, 'accepted_types' => array('web_image')));
        if(isset($id) && $id !==0 && $id!==''){
            $hbon_popup_home = $DB->get_record('hbon_popup_home', array('id' => $id));
            $image = $hbon_popup_home->image;
            if($image && $image!==''){
                $link = '/local/school/image.php?filename='.$image;
                $mform->addElement('static', 'elementName', 'Thumbnail', '<img crossorigin="anonymous" src="'.$link.'" class="png mw-mmv-dialog-is-open" width="300" height="200">');
            }
        }
        $mform->addElement('text', 'link', "Link");
        $mform->setType('link', PARAM_TEXT);
        $mform->addElement('date_time_selector', 'public_at', "Ngày active");
        $mform->setType('public_at', PARAM_TEXT);
        $mform->addElement('date_time_selector', 'expitime', "Ngày hết hạn");
        $mform->setType('expitime', PARAM_TEXT);
        $mform->addElement('select', 'status', "Trạng thái", array(0,1));
        $mform->setType('status', PARAM_TEXT);
        $mform->addElement('select', 'is_countdown', get_string('is_countdown', 'local_sm'), array(0,1));
        $mform->setType('is_countdown', PARAM_TEXT);
        $mform->addElement('select', 'replay',"Chạy hàng ngày", array(0,1));
        $mform->setType('replay', PARAM_TEXT);

        $sql = "select shortname from mdl_course";
        $courses = $DB->get_records_sql($sql,array());
        $coursenames = [];
        foreach ($courses as $key=> $course) {
            $coursenames[$course->shortname]=$course->shortname;
        }
//        print_object($coursenames);die();
        $options = array(
            'multiple' => true,
            'noselectionstring' => 'All Courses',
        );
        $mform->addElement('autocomplete', 'to_course', 'Hiển thị trong course', $coursenames, $options);
        $mform->setType('to_course', PARAM_RAW);

        $mform->setType('query', PARAM_RAW);
        $mform->setDefault('query', optional_param('query', '', PARAM_RAW));

        $this->add_action_buttons();

    }
}
