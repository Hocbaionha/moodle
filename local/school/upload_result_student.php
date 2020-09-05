<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();

$sitecontext = context_system::instance();

$context = context_system::instance();
//echo $OUTPUT->heading(get_string('title', 'local_school'));
//require_capability('moodle/category:manage', $context);

$url = new moodle_url('/local/school/upload_result.php');

//$PAGE->requires->js(new moodle_url('/local/school/js/upload_result.js'));
$PAGE->set_context($context);
$PAGE->set_url($url);

$PAGE->set_title("title");
$PAGE->set_heading(get_string("school", "local_school"));
if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow","local_school");
    echo $OUTPUT->footer();die;
}

// page parameters
$delete = optional_param('delete', 0, PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);   //md5 confirmation hash
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 15, PARAM_INT);    // how many per page
$sort = optional_param('sort', 'timemodified', PARAM_ALPHA);
$dir = optional_param('dir', 'DESC', PARAM_ALPHA);
$classid = optional_param('classid', 0, PARAM_INT); 
$schoolid = optional_param('schoolid', 0, PARAM_INT); 
$class_name = $DB->get_record('class',array('id'=>$classid))->name;
$returnurl = new moodle_url('/local/school/upload_result_student.php', array('perpage' => $perpage, 'page' => $page,'classid'=>$classid,'schoolid'=>$schoolid));
$hcolumns = array('stt' => get_string('stt', 'local_school'), 'name' => get_string('name', 'local_school'),
    'gender' => get_string('gender', 'local_school'),
    'birth_date' => get_string('birth_date', 'local_school'),
    'parent' => get_string('parent', 'local_school'),
    'parent_phone' => get_string('parent_phone', 'local_school'),
);

$table = new html_table();
$table->head = array($hcolumns['stt'], $hcolumns['name'], $hcolumns['gender'], $hcolumns['birth_date'], $hcolumns['parent'], $hcolumns['parent_phone'], "");
$table->colclasses = array('leftalign date', 'leftalign name', 'leftalign plugin', 'leftalign setting', 'leftalign newvalue', 'leftalign originalvalue');
$table->attributes['class'] = 'admintable generaltable';

$students = $DB->get_records("temp_student", array('classid'=>$classid), '', '*', $page * $perpage, $perpage);
$line = $page * $perpage;
foreach ($students as $s) {
    $line++;
    $row = array();
    $row['STT'] = $line;
    $row['name'] = $s->name;
    $row['gender'] = $s->gender;
    $row['birth_date'] = $s->birth_date;
    $row['parent'] = $s->parent;
    $row['parent_phone'] = $s->parent_phone;
    $table->data[] = $row;
}
echo $OUTPUT->header();
echo get_string("class_list",'local_school',$class_name);
echo html_writer::tag("BR", null);
echo html_writer::table($table);
$count = $DB->count_records("temp_student", array('classid'=>$classid));

echo $OUTPUT->paging_bar($count, $page, $perpage, $returnurl);
$backurl = new moodle_url('/local/school/upload_result.php', array('schoolid'=>$schoolid));
echo html_writer::link($backurl, get_string("goback",'local_school'));