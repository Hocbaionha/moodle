<?PHP 
require_once(__DIR__ . '/../../config.php');
require_once $CFG->libdir . '/hbonlib/string_util.php';

require_once($CFG->dirroot . "/cohort/lib.php");
require_login();
global $USER,$DB;
if($USER->id!=2){
    redirect("/login/index.php");
}
//set timeout: 5 minute
set_time_limit(300);


$site = get_site();

$context = context_system::instance();
$PAGE->set_context($context);

$PAGE->set_heading("Enrole toàn trường");
$url = new moodle_url('/local/sm/school_enrole.php');
$PAGE->set_url($url);

$cohortId = optional_param('cohortid', 0, PARAM_INT); 
$schoolid = optional_param('schoolid', 0, PARAM_INT); 

$time_start = microtime(true);
if (ob_get_level() == 0)
    ob_start();


    try{
        $percent = 1;
        $userids = $DB->get_records("cohort_members",array("cohortid"=>$schoolid));
        foreach($userids as $member){
            $userid = $member->userid;
            cohort_add_member($cohortId,$userid);
            echo '<script>
        parent.document.getElementById("progressbar").innerHTML="<div style=\"width:' . $percent . ';background:#1177d1; ;height:35px;\">&nbsp;</div>";
        parent.document.getElementById("information").innerHTML="<div style=\"text-align:center; font-weight:bold\">' . $percent . '% - Processing ...</div>";</script>';
            echo $userid."-".$cohortId;
            doFlush();
        }
        echo "Enrole success!!!";

    } catch (Exception $e) {
        serviceErrorLog("school_enrole error:" . json_encode($e->getTrace()));
    }
    
$time_end = microtime(true);
$execution_time = ($time_end - $time_start);
echo "DONE !!!<br/> Execution time:" . round($execution_time).'s';
// cohort_add_member($cohort->id, $userid); 