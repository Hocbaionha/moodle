<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_login();
$sitecontext = context_system::instance();

$url = new moodle_url('/local/school/export.php');
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


$returnurl = new moodle_url('/local/school/export.php', array('perpage' => $perpage, 'page'=>$page));
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
        $DB->delete_records('school', array('id' => $delete));
    }
}

echo $OUTPUT->header();

$hcolumns = array('code' => get_string('code', 'local_school'),
    'name' => get_string('name', 'local_school'),
    'districtid' => get_string('district', 'local_school'),
    'approve' => get_string('approve', 'local_school'),
    'student' => get_string('student', 'local_school'),
    'teacher' => get_string('teacher', 'local_school'),
);

$table = new html_table();
$table->head = array($hcolumns['code'], $hcolumns['name'], $hcolumns['districtid'],$hcolumns['student'],$hcolumns['teacher'],$hcolumns['approve'], "");
$table->colclasses = array('leftalign date', 'leftalign name', 'leftalign plugin', 'leftalign setting', 'leftalign newvalue', 'leftalign originalvalue');
$table->attributes['class'] = 'admintable generaltable';


$stredit = get_string('edit');
$strdelete = get_string('delete');

$sql = "SELECT s.id,s.code,s.name,d.name as district,s.last_student,s.last_teacher,s.approve,s.filename FROM {school} s join {district} d on s.districtid=d.districtid where s.approve=1";
$rs = $DB->get_recordset_sql($sql, array(), $page * $perpage, $perpage);
$result = array();
foreach ($rs as $s) {
    $download = 
            new moodle_url('/local/school/image.php?filename='.$s->filename);
    
    $buttons = array();
    $lastcolumn = '';
    $row = array();

    $row[] = $s->code;
    $row[] = "<a href='$download'>$s->name</a>";
    $row[] = $s->district;
    $row[] = $s->last_student;
    $row[] = $s->last_teacher;
    $row[] = $s->approve==1?"Yes":"";
    $row[] = $lastcolumn;
    $table->data[] = $row;
}
$rs->close();

$back = new moodle_url('/admin/search.php#linkschools');
echo "<div class='row'><div class='col-12'>";
echo html_writer::link($back, "Back", array("class"=>"btn btn-secondary float-right"));
echo "</div></div><br/>";

echo html_writer::table($table);
$count = $DB->count_records('school');
    echo $OUTPUT->paging_bar($count, $page, $perpage, $returnurl);


echo $OUTPUT->footer();
