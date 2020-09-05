<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/authlib.php');
require_once $CFG->libdir .'/hbonlib/string_util.php';
require_once $CFG->libdir .'/hbonlib/lib.php';
require_once("school_search_form.php");
require_login();
$schoolid = optional_param('schoolid', 0, PARAM_INT);

$context = context_system::instance();
$url = new moodle_url('/local/school/teachers_assignment.php');
$remove_url = new moodle_url('/local/school/remove_teacher.php');
$assign_url = new moodle_url('/local/school/assign_teacher.php');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_heading(get_string("teachers", "local_school"));
if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow","local_school");
    echo $OUTPUT->footer();die;
}
$PAGE->requires->jquery();
$PAGE->requires->css(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css'));
$PAGE->requires->css(new moodle_url('/local/school/css/custom.css'));
//$PAGE->requires->js(new moodle_url('/local/school/js/school.js'));

$strdelete = get_string('delete');
$strdeletecheck = get_string('deletecheck');

echo $OUTPUT->header();


$school = $DB->get_record("school", array("id" => $schoolid,"approve"=>1));
if (!$school) {
    $schools = getSchools();
    $school = reset($schools);
    if (!$school) {
        echo get_string("no_school", "local_school");
        echo $OUTPUT->footer();
        die;
    }
    $schoolid = $school->id;
}
$sql = "SELECT p.name as pname,d.name as dname from mdl_school s join mdl_district d on d.districtid=s.districtid join mdl_province p on p.provinceid=d.provinceid where s.id=$schoolid";

$result = $DB->get_record_sql($sql);
$provinceAcronym = $provinceAcronym = $city="";
if($result){
$provinceAcronym = getProvinceAcronym($result->pname);
$districtAcronym = getDistrictAcronym($result->dname);
$city = strtoupper($provinceAcronym);
}
$sname = strtolower(non_unicode(preg_replace('/\s+/', '', $school->name)));
$classes = $DB->get_records("class", array("schoolid" => $schoolid));

$hcolumns = array('stt' => get_string('stt', 'local_school'), 'class' => get_string('class', 'local_school'),
    'form_teacher' => get_string('form_teacher', 'local_school'),
    'algebra' => get_string('algebra', 'local_school'),
    'geometry' => get_string('geometry', 'local_school'),
    'english' => get_string('english_teacher', 'local_school'),
    'literature' => get_string('literature_teacher', 'local_school'),
);

$table = new html_table();
$table->head = array($hcolumns['stt'], $hcolumns['class'], $hcolumns['form_teacher'], $hcolumns['algebra'], $hcolumns['geometry'], $hcolumns['english'], $hcolumns['literature'], "");
//$table->colclasses = array('leftalign date', 'leftalign name', 'leftalign plugin', 'leftalign setting', 'leftalign newvalue', 'leftalign originalvalue');
//$table->attributes['class'] = 'admintable generaltable';
$line = 0;
foreach ($classes as $class) {
    $line++;
    $grade = $class->code[0];
    $row = array();
    $row['STT'] = $line;
    $row['class'] = "<a href=\"change_student.php?classid=$class->id&schoolid=$schoolid\">" . $class->code . "</a>";
    $group_name = $class->code . "-" . $school->code;
    $sql = 'select g.id,g.name,c.shortname,c.id as cid from mdl_groups g join mdl_course c on g.courseid=c.id  where name =?';
    $groups = $DB->get_records_sql($sql, array("name" => $group_name));
    $teachers = array("gvcn" => null, "algebra" => null, "geometry" => null, "english" => null, "literature" => null);
    $sql = "SELECT tc.id,tc.classid,tc.groupid,tc.type,tc.userid,u.firstname,u.lastname,u.username from mdl_teacher_class tc join mdl_user u on tc.userid=u.id where tc.classid=?";
    $tcusers = $DB->get_records_sql($sql, array("classid" => $class->id));
    foreach ($tcusers as $tc) {
        $name = $tc->lastname . " " . $tc->firstname;
        $name = "<a href='/user/view.php?id=$tc->userid&course=1' title='$tc->username'>$name</a>";
        if ($tc->type == 1) {
            $teachers['gvcn'] = $name . "<a href='$remove_url?id=$tc->userid&groupid=$tc->groupid&schoolid=$schoolid&type=$tc->type&grade=$grade' ><i class='icon fa fa-trash fa-fw pull-right'></i></a>";
        } else if ($tc->type == 2) {
            $teachers['algebra'] = $name . "<a href='$remove_url?id=$tc->userid&groupid=$tc->groupid&schoolid=$schoolid&type=$tc->type&grade=$grade'><i class='icon fa fa-trash fa-fw pull-right'></i></a>";
        } else if ($tc->type == 3) {
            $teachers['geometry'] = $name . "<a href='$remove_url?id=$tc->userid&groupid=$tc->groupid&schoolid=$schoolid&type=$tc->type&grade=$grade'><i class='icon fa fa-trash fa-fw pull-right'></i></a>";
        } else if ($tc->type == 4) {
            $teachers['english'] = $name . "<a href='$remove_url?id=$tc->userid&groupid=$tc->groupid&schoolid=$schoolid&type=$tc->type&grade=$grade'><i class='icon fa fa-trash fa-fw pull-right'></i></a>";
        } else if ($tc->type == 5) {
            $teachers['literature'] = $name . "<a href='$remove_url?id=$tc->userid&groupid=$tc->groupid&schoolid=$schoolid&type=$tc->type&grade=$grade'><i class='icon fa fa-trash fa-fw pull-right'></i></a>";
        }
    }
    if ($teachers['gvcn'] == null) {
        $teachers['gvcn'] = "<a href='$assign_url?classid=$class->id&groupname=$group_name&schoolid=$schoolid&type=1&grade=$grade'><i class='fa fa-user-plus pull-right' aria-hidden='true'></i></a>";
    }
    if ( $teachers['algebra'] == null ) {
        $teachers['algebra'] = "<a href='$assign_url?classid=$class->id&groupname=$group_name&schoolid=$schoolid&type=2&grade=$grade'><i class='fa fa-user-plus pull-right' aria-hidden='true'></i></a>";
    }
    if ($teachers['geometry'] == null) {
        $teachers['geometry'] = "<a href='$assign_url?classid=$class->id&groupname=$group_name&schoolid=$schoolid&type=3&grade=$grade'><i class='fa fa-user-plus pull-right' aria-hidden='true'></i></a>";
    }
    if ( $teachers['english'] == null) {
        $teachers['english'] = "<a href='$assign_url?classid=$class->id&groupname=$group_name&schoolid=$schoolid&type=4&grade=$grade'><i class='fa fa-user-plus pull-right' aria-hidden='true'></i></a>";
    }
    if ( $teachers['literature'] == null) {
        $teachers['literature'] = "<a href='$assign_url?classid=$class->id&groupname=$group_name&schoolid=$schoolid&type=5&grade=$grade'><i class='fa fa-user-plus pull-right' aria-hidden='true'></i></a>";
    }

    $row['gvcn'] = $teachers['gvcn'];
    $row['algebra'] = $teachers['algebra'];
    $row['geometry'] = $teachers['geometry'];
    $row['english'] = $teachers['english'];
    $row['literature'] = $teachers['literature'];
    $table->data[] = $row;
}

$back = new moodle_url('/admin/search.php#linkschools');
echo "<div class='row'><div class='col-12'>";
echo html_writer::link($back, "Back", array("class"=>"btn btn-secondary float-right"));
echo "</div></div><br/>";
$mform = new school_search_form($url, array('schoolid' => $schoolid, 'districtid' => $school->districtid));
echo $mform->render();

$urladd =  new moodle_url('/local/school/edit_teacher.php',array("schoolid"=>$schoolid));
$a = "<a href='$urladd'>" . get_string("add") . "</a>";
echo get_string("click_add_teacher", "local_school", $a);
echo html_writer::table($table);

echo $OUTPUT->footer();
