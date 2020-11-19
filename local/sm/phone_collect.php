<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');


global $DB,$USER;
require_login();
$sitecontext = context_system::instance();
$user = $USER->id;

$url = new moodle_url('/local/sm/phone_collect.php');
$PAGE->set_context($sitecontext);
$PAGE->set_url($url);

$PAGE->set_title("Thu thập số điện thoại");
$PAGE->set_heading(get_string("class", "local_class_regist"));

if (!has_capability('local/school:write', $sitecontext) && $user!==0 ) {
    echo $OUTPUT->header();
    echo get_string("not_allow","local_class_regist");
    echo $OUTPUT->footer();die;
}
$PAGE->requires->jquery();
$PAGE->requires->css(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css'));
$PAGE->requires->css(new moodle_url('/local/class_regist/css/custom.css'));
//$PAGE->requires->js(new moodle_url('/local/class_regist/js/school.js'));
// page parameters
$delete = optional_param('delete', 0, PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);   //md5 confirmation hash
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);    // how many per page
$id = optional_param('id', 0, PARAM_INT);
$returnurl = new moodle_url('/local/sm/phone_collect.php', array('perpage' => $perpage, 'page' => $page));
echo $OUTPUT->header();

$hcolumns = array('id' => "ID",
    'userid' => "User ID",
    'phone' => "Số điện thoại",
    'timecreated' => "Ngày nhập",
    'timemodified' => "Ngày cập nhật"
);

$strdelete = get_string('delete');
$stredit = get_string('edit');
$strdownload = get_string('download');
$view_part= "Danh sách thành viên";

$table = new html_table();
$table->head = array($hcolumns['id'], $hcolumns['userid'], $hcolumns['phone'], $hcolumns['timecreated'], $hcolumns['timemodified']);
$table->colclasses = array('leftalign date', 'leftalign name', 'leftalign plugin', 'leftalign setting', 'leftalign newvalue', 'leftalign originalvalue');
$table->attributes['class'] = 'admintable generaltable';

$sql = "SELECT * FROM mdl_hbon_has_check_phone";
$rs = $DB->get_recordset_sql($sql, array(), $page * $perpage, $perpage);
$result = array();
foreach ($rs as $s) {
    $row = array();
    $row[] = $s->id;
    $row[] = $s->userid;
    $row[] = $s->phone;
    $row[] = date('d-m-Y h:i:s',$s->timecreated);
    $row[] = isset($s->timemodified)?date('d-m-Y h:i:s',$s->timemodified):'';
    $table->data[] = $row;
}
$rs->close();

$back = new moodle_url('/admin/search.php#linkschools');

echo "<div class='row'><div class='col-12'>";
echo html_writer::link($back, "Back", array("class"=>"btn btn-secondary float-right"));
echo "</div></div><br/>";

echo html_writer::table($table);
$count = $DB->count_records('hbon_has_check_phone');
echo $OUTPUT->paging_bar($count, $page, $perpage, $returnurl);
echo $OUTPUT->footer();
