<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

global $DB;
require_login();
$sitecontext = context_system::instance();


$url = new moodle_url('/local/school/departments.php');
$PAGE->set_context($sitecontext);
$PAGE->set_url($url);

$PAGE->set_title("title");
$PAGE->set_heading(get_string("class", "local_school"));

if (!has_capability('local/school:write', $sitecontext)) {
    echo $OUTPUT->header();
    echo get_string("not_allow","local_school");
    echo $OUTPUT->footer();die;
}
$delete = optional_param('delete', 0, PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);   //md5 confirmation hash
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);    // how many per page

$returnurl = new moodle_url('/local/school/departments.php', array('perpage' => $perpage, 'page' => $page));
if ($delete and confirm_sesskey()) {              // Delete a selected school, after confirmation
    require_capability('local/school:write', $sitecontext);

    $departments = $DB->get_record('departments', array('id' => $delete), '*', MUST_EXIST);

    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('delete_department', 'local_school'));

        $optionsyes = array('delete' => $delete, 'confirm' => md5($delete), 'sesskey' => sesskey());
        $deleteurl = new moodle_url($returnurl, $optionsyes);
        $deletebutton = new single_button($deleteurl, get_string('delete'), 'post');

        echo $OUTPUT->confirm(get_string('deletechecksdepartment', 'local_school', "'$departments->name'"), $deletebutton, $returnurl);
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {
        $DB->delete_records('departments', array('id' => $delete));
        redirect($returnurl);
    }
}
echo $OUTPUT->header();

$hcolumns = array('code' => get_string('code', 'local_school'),
    'name' => get_string('name', 'local_school')
);

$table = new html_table();
$table->head = array($hcolumns['code'], $hcolumns['name'], get_string('edit'), "");
$table->colclasses = array('leftalign date', 'leftalign name', 'leftalign plugin', 'leftalign setting', 'leftalign newvalue', 'leftalign originalvalue');
$table->attributes['class'] = 'admintable generaltable';



$stredit = get_string('edit');
$strdelete = get_string('delete');

$rs = $DB->get_records("departments", array(),"","*", $page * $perpage, $perpage);
$result = array();
foreach ($rs as $s) {
    $buttons = array();
    $lastcolumn = '';
    if (has_capability('local/school:write', $sitecontext)) {
        $url = new moodle_url('/local/school/departments.php', array('delete' => $s->id, 'sesskey' => sesskey()));
            $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/delete', $strdelete));
    }
    if (has_capability('local/school:write', $sitecontext)) {
        if (is_siteadmin($USER) or ! is_siteadmin($user)) {
            $url = new moodle_url('/local/school/edit_department.php', array('id' => $s->id));
            $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/edit', $stredit));
        }
    }
    $row = array();

    $row[] = $s->code;
    $row[] = $s->name;
    $row[] = implode(' ', $buttons);
    $row[] = $lastcolumn;
    $table->data[] = $row;
}


$back = new moodle_url('/admin/search.php#linkschools');
echo "<div class='row'><div class='col-12'>";
echo html_writer::link($back, "Back", array("class" => "btn btn-secondary float-right"));
echo "</div></div><br/>";

$a = html_writer::link("edit_department.php", get_string("add", 'local_school'));
echo get_string("click_add_department", "local_school", $a);
echo html_writer::table($table);
$count = $DB->count_records('departments');
echo $OUTPUT->paging_bar($count, $page, $perpage, $returnurl);
echo $OUTPUT->footer();

