<?PHP 
require_once(__DIR__ . '/../../config.php');

require_once $CFG->libdir . '/formslib.php';
require_once($CFG->dirroot . "/cohort/lib.php");
require_login();
//set timeout: 5 minute
set_time_limit(300);

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
        $sql2 = "SELECT id,name,idnumber FROM mdl_cohort where idnumber is  not null and idnumber !='' and  idnumber not like '%hbon%' and idnumber not like '%trial%'  and idnumber not like '%paid%';";
        $cohorts2 = $DB->get_records_sql($sql2);
        $selectArray2=array();
        foreach($cohorts2 as $cohort){
            $key = $cohort->id;
            $value = $cohort->name;
            $selectArray2[$key] = $value;
        }
        $mform->addElement('select', 'cohort', get_string('cohort', 'local_sm'), $selectArray); 
        $options = array(                                                                                                           
            'multiple' => false,
        );   
        $mform->addElement('autocomplete', 'school', "Chọn trường", $selectArray2, $options);
        $this->add_action_buttons($cancel = true, $submitlabel="Lưu");
    }

}

$site = get_site();

$context = context_system::instance();
$PAGE->set_context($context);

$PAGE->set_heading("Enrole toàn trường");
$url = new moodle_url('/local/sm/school_enrole.php');
$PAGE->set_url($url);


$mform = new import_form();
if ($mform->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $schoolurl = new moodle_url('/local/sm/school_enrole.php');
    redirect($schoolurl);
} else if ($fromform = $mform->get_data()) {
    try{
    $cohortId = $fromform->cohort;
    $userids = $DB->get_records("cohort_members",array("cohortid"=>$fromform->school));
    foreach($userids as $member){
        $userid = $member->userid;
        cohort_add_member($cohortId,$userid);
    }
    echo $OUTPUT->header();
    echo "Enrole success $fromform->cohort - $fromform->school";
    echo $OUTPUT->footer();
    } catch (Exception $e) {
        serviceErrorLog("school_enrole error:" . json_encode($e->getTrace()));
    }
}
else {
    $url = new moodle_url('/local/sm/school_enrole.php');
    $PAGE->set_url($url);

    echo $OUTPUT->header();

    $mform->display();
    echo $OUTPUT->footer();
    
}
