
<?php

require_once('../../../config.php');
require_once $CFG->libdir . '/hbonlib/string_util.php';
require_once($CFG->dirroot . "/cohort/lib.php");
require_login();
global $USER,$DB;
if($USER->id!=2){
    redirect("/login/index.php");
}

$sn = optional_param('sn', '', PARAM_TEXT); 
$arrSn = explode(",",$sn);
var_dump($arrSn);
$time_start = microtime(true);
if (ob_get_level() == 0)
    ob_start();


$cohort = $DB->get_record('cohort', array('idnumber' => "Hbon_ky1"), '*', MUST_EXIST);
foreach($arrSn as $s) {
    $percent = 1;
    $sql = "select id,username,email,timecreated,lastaccess from mdl_user where deleted=0 and email not like 'fb%' and (email like '%hocbaionha.com' or email like '%dschool.vn') and email like '$s%'";
    $users = $DB->get_records_sql($sql);
    foreach($users as $user){
        cohort_add_member($cohort->id, $user->id);
        echo "add to Hbon_hk1 userid:$user->id,   email:$user->email<br/>";
        echo '<script>
    parent.document.getElementById("progressbar").innerHTML="<div style=\"width:' . $percent . ';background:#1177d1; ;height:35px;\">&nbsp;</div>";
    parent.document.getElementById("information").innerHTML="<div style=\"text-align:center; font-weight:bold\">' . $percent . '% - Processing ...</div>";</script>';

        doFlush();
    }
}
$time_end = microtime(true);
$execution_time = ($time_end - $time_start);
echo "DONE !!!<br/> Execution time:" . round($execution_time).'s';
// cohort_add_member($cohort->id, $userid); 