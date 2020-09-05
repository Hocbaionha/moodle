<?php

require(__DIR__ . '/../../config.php');

require_once $CFG->libdir . '/formslib.php';
require_once $CFG->libdir . '/hbonlib/lib.php';
require_login();

class department_form extends moodleform {

    function definition() {
        $mform = $this->_form;
        $id = $this->_customdata['id'];

        $attributes = array('size' => '20');
        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);
        $mform->setConstant('id', $id);
        $mform->addElement('text', 'code', get_string('code', 'local_school'), $attributes);
        $mform->setType('code', PARAM_TEXT);
        $mform->addElement('text', 'name', get_string('name', 'local_school')); // Add elements to your form
        $mform->setType('name', PARAM_TEXT);

        $this->add_action_buttons();
    }

}

$id = optional_param('id', 0, PARAM_INT);
$site = get_site();
$context = context_system::instance();
$PAGE->set_context($context);

$url = new moodle_url('/local/school/edit_class.php');
$PAGE->set_heading(get_string("class", "local_school"));
$PAGE->set_url($url);
if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow", "local_school");
    echo $OUTPUT->footer();
    die;
}
$mform = new department_form(null, array('id' => $id));
$schoolurl = new moodle_url('/local/school/departments.php', array('id' => $id));
if ($mform->is_cancelled()) {
    redirect($schoolurl);
} else if ($fromform = $mform->get_data()) {
    $fromform->name = trim(preg_replace('/\s+/', ' ', $fromform->name));
    if ($fromform->id != 0) {
        if (!$DB->update_record('departments', $fromform)) {
            print_error('updateerror', 'school');
        }
    } else {
        if (!$DB->insert_record('departments', $fromform)) {
            print_error('inserterror', 'school');
        }
    }
    redirect($schoolurl);
} else {

    if ($id) {
        //edit if have $id
        $mformpage = $DB->get_record('departments', array('id' => $id));
        $mform = new department_form(null, array('id' => $id));
        $mform->set_data($mformpage);
    }
    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
}

