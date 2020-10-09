<?PHP 
require_once(__DIR__ . '/../../config.php');

require_once $CFG->libdir . '/formslib.php';
require_once($CFG->dirroot . "/cohort/lib.php");
require_login();

class import_form extends moodleform {
    
    protected function definition() {
        global $DB;
        $maxbytes = 10240000;
        $mform = $this->_form;
        $sql = "SELECT id,name FROM mdl_cohort where idnumber like '%hbon%'";
        $cohorts = $DB->get_records_sql($sql);
        $selectArray=array();
        foreach($cohorts as $cohort){
            $key = $cohort->id;
            $value = $cohort->name;
            $selectArray[$key] = $value;
        }
        $mform->addElement('select', 'cohort', get_string('cohort', 'local_sm'), $selectArray); 
        $mform->addElement('textarea', 'usernamelist', get_string("usernamelist", "local_sm"), 'wrap="virtual" rows="20" cols="50"');
        $this->add_action_buttons($cancel = true, $submitlabel="Xóa");
    }

}

$site = get_site();

$context = context_system::instance();
$PAGE->set_context($context);

$PAGE->set_heading(get_string("title", "local_sm"));
$url = new moodle_url('/local/sm/remove_cohort.php');
$PAGE->set_url($url);


$mform = new import_form();
if ($mform->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $schoolurl = new moodle_url('/local/sm/remove_cohort.php');
    redirect($schoolurl);
} else if ($fromform = $mform->get_data()) {
    
    $cohortId = $fromform->cohort;
    $usernamelist = $fromform->usernamelist;
    $userstoremove = explode("\n",$usernamelist);
    foreach ($userstoremove as $username) {
        $username = trim($username);
        $user = $DB->get_record("user",array("username"=>$username));
        if($user!=false){
            cohort_remove_member($cohortId, $user->id);
        }
    }
    echo $OUTPUT->header();
    echo "Xóa khỏi cohort thành công";

    echo $OUTPUT->footer();
}
else {
    $url = new moodle_url('/local/sm/remove_cohort.php');
    $PAGE->set_url($url);

    echo $OUTPUT->header();

    $mform->display();
    echo $OUTPUT->footer();
    
}
