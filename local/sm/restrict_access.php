<?php

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once('restrict_access_form.php');
require_login();

$PAGE->set_url('/local/sm/restrict_access.php');

$PAGE->requires->jquery();
$PAGE->requires->css(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css'));
$PAGE->requires->css(new moodle_url('/local/school/css/custom.css'));
// $id = required_param('id',0, PARAM_INT);    // course_sections.id

$context = context_system::instance();
$PAGE->set_context($context);


$courseid = optional_param('courseid', 0, PARAM_INT);
$availability=null;
$mform = new restrictaccess_form($PAGE->url,array('availability' => $availability,"courseid"=>$courseid));


if ($mform->is_cancelled()){
    // Form cancelled, return to course.
    redirect("/admin/search.php#linkschools");
} else if ($data = $mform->get_data()) {
if(!isset($data->topic)){
    $data->topic=false;
}
    $mod = array();
    foreach($data as $key=>$value){
        if(strpos($key,"module-")===0){
            if($value==1)
                $mod[]= substr($key,7);
        }
    }

    if($cra){
        $cra->availability = $data->availabilityconditionsjson;
        $did = changeActivity($cra,$mod,$data->topic);
    } else {
        $new = new stdClass();
        $new->course = $data->course;
        $new->availability = $data->availabilityconditionsjson;
        $did = changeActivity($new,$mod,$data->topic);
    }
    // Data submitted and validated, update and return to course.
    rebuild_course_cache( $courseid, true );
    $PAGE->navigation->clear_cache();
    redirect("/admin/search.php#linkschools","change activity success");
}
echo $OUTPUT->header();

echo $OUTPUT->heading("restrict access");

$mform->display();
$script = '
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"></script>
    <script>
        $("#id_course").selectize({
        sortField: "id"
        });
        document.getElementById("id_course").onchange = function () {
            var id = document.getElementById("id_course").value;
            console.log("courseid:",id);
            window.location = document.URL.split("?")[0] + "?courseid="+id;
        };
    </script>';
    echo $script;
echo $OUTPUT->footer();


function changeActivity($cra,$mod,$isChangeSession){
    global $DB;
    if($isChangeSession){
        $sql = "update mdl_course_sections set availability=? where course=? and visible=1";
        $DB->execute($sql,array("availability"=>$cra->availability,"course"=>$cra->course));
        // print_object($cra);die;
    }
    if(empty($mod)){
        return;
    }
    $moduleids = implode(",", $mod);
    $sql = "update mdl_course_modules set availability=? where course=? and visible=1 and module in($moduleids)";
    $DB->execute($sql,array("availability"=>$cra->availability,"course"=>$cra->course));
}