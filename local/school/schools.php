<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_login();
$sitecontext = context_system::instance();

$url = new moodle_url('/local/school/schools.php');
$PAGE->set_context($sitecontext);
$PAGE->set_url($url);

$PAGE->set_title(get_string("school","local_school"));
$PAGE->set_heading(get_string("school","local_school"));

if (!has_capability('local/school:write', $sitecontext)) {
    echo $OUTPUT->header();
    echo get_string("not_allow","local_school");
    echo $OUTPUT->footer();die;
}
// page parameters
$delete = optional_param('delete', 0, PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);   //md5 confirmation hash
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 15, PARAM_INT);    // how many per page


$returnurl = new moodle_url('/local/school/schools.php', array('perpage' => $perpage, 'page'=>$page));
if ($delete and confirm_sesskey()) {              // Delete a selected school, after confirmation
    require_capability('local/school:write', $sitecontext);

    $class = $DB->get_record('school', array('id' => $delete), '*', MUST_EXIST);

    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('deleteschool', 'local_school'));

        $optionsyes = array('delete' => $delete, 'confirm' => md5($delete), 'sesskey' => sesskey());
        $deleteurl = new moodle_url($returnurl, $optionsyes);
        $deletebutton = new single_button($deleteurl, get_string('delete'), 'post');

        echo $OUTPUT->confirm(get_string('deletecheckfull', '', "'$class->name'"), $deletebutton, $returnurl);
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {
        $classes = $DB->get_records("class",array("schoolid"=>$delete));
        foreach($classes as $class){
            $DB->delete_records('temp_teacher_class', array('classid' => $class->id));
        }
        $DB->delete_records('temp_student', array('schoolid' => $delete));
        $DB->delete_records('class', array('schoolid' => $delete));
        $DB->delete_records('school', array('id' => $delete));
    }
}

echo $OUTPUT->header();

$hcolumns = array('code' => get_string('code', 'local_school'),
    'name' => get_string('name', 'local_school'),
    'districtid' => get_string('district', 'local_school'),
    'cohort_code' => get_string('cohort_code', 'local_school'),
    'group_code' => get_string('group_code', 'local_school'),
    'approve' => get_string('approve', 'local_school'),
    'student' => get_string('student', 'local_school'),
    'teacher' => get_string('teacher', 'local_school'),
);

$table = new html_table();
$table->head = array($hcolumns['code'], $hcolumns['name'], $hcolumns['districtid'],$hcolumns['cohort_code'],$hcolumns['group_code'],$hcolumns['student'],$hcolumns['teacher'],$hcolumns['approve'], get_string('edit'), "");
$table->colclasses = array('leftalign date', 'leftalign name', 'leftalign plugin', 'leftalign setting', 'leftalign newvalue', 'leftalign originalvalue');
$table->attributes['class'] = 'admintable generaltable';


$stredit = get_string('edit');
$strdelete = get_string('delete');

$sql = "SELECT s.id,s.code,s.name,d.name as district,s.cohort_code,s.group_code,s.last_student,s.last_teacher,s.approve FROM {school} s left join {district} d on s.districtid=d.districtid where s.code!='hbon'";
$rs = $DB->get_recordset_sql($sql, array(), $page * $perpage, $perpage);
$result = array();
foreach ($rs as $s) {
    $buttons = array();
    $lastcolumn = '';
    if (has_capability('local/school:write', $sitecontext)) {
        $url = new moodle_url('/local/school/schools.php', array('delete' => $s->id, 'sesskey' => sesskey()));
        if($s->approve==0){
            $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/delete', $strdelete));
        }
    }
    if (has_capability('local/school:write', $sitecontext)) {
        // prevent editing of admins by non-admins
        if (is_siteadmin($USER) or ! is_siteadmin($user)) {
            $url = new moodle_url('/local/school/edit_school.php', array('id' => $s->id));
            $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/edit', $stredit));
        }
    }
    $row = array();

    $row[] = $s->code;
    $row[] = $s->name;
    $row[] = $s->district;
    $row[] = $s->cohort_code;
    $row[] = $s->group_code;
    $row[] = $s->last_student;
    $row[] = $s->last_teacher;
    $row[] = $s->approve==1?"Yes":"";
    $row[] = implode(' ', $buttons);
    $row[] = $lastcolumn;
    $table->data[] = $row;
}
$rs->close();

$back = new moodle_url('/admin/search.php#linkschools');
echo "<div class='row'><div class='col-12'>";
echo html_writer::link($back, "Back", array("class"=>"btn btn-secondary float-right"));
echo "</div></div><br/>";
echo html_writer::table($table);
$sql = "select count(*) from mdl_school where code!='hbon'";
$count = $DB->count_records_sql($sql);
    echo $OUTPUT->paging_bar($count, $page, $perpage, $returnurl);
echo html_writer::link("edit_school.php", get_string("add",'local_school'));

echo $OUTPUT->footer();
