<?php

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');


global $DB;
require_login();
$sitecontext = context_system::instance();


$url = new moodle_url('/local/school/course/courses_desc.php');
$PAGE->set_context($sitecontext);
$PAGE->set_url($url);

$PAGE->set_title("title");
$PAGE->set_heading(get_string("course_desc", "local_school"));

$stredit = get_string('edit');
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);    // how many per page


$hcolumns = array('id' => "id",
    'shortname' => get_string('shortname', 'local_school'),
    'name' => get_string('name', 'local_school'),
);
$table = new html_table();
$table->head = array($hcolumns['id'], $hcolumns['shortname'], $hcolumns['name'], get_string('edit'), "");
$table->colclasses = array('leftalign date', 'leftalign name', 'leftalign plugin', 'leftalign setting', 'leftalign newvalue', 'leftalign originalvalue');
$table->attributes['class'] = 'admintable generaltable';
$courses = $DB->get_records("course", array(),"","*", $page * $perpage, $perpage);
foreach ($courses as $s) {
    $buttons = array();
    $lastcolumn = '';
    if (has_capability('local/school:write', $sitecontext)) {
        if (is_siteadmin($USER) or ! is_siteadmin($user)) {
            $url = new moodle_url('/local/school/course/edit_course_desc.php', array('id' => $s->id));
            $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/edit', $stredit));
        }
    }
    $row = array();

    $row[] = $s->id;
    $row[] = $s->shortname;
    $row[] = $s->fullname;
    $row[] = implode(' ', $buttons);
    $row[] = $lastcolumn;
    $table->data[] = $row;
}

echo $OUTPUT->header();
$back = new moodle_url('/admin/search.php#linkschools');
echo "<div class='row'><div class='col-12'>";
echo html_writer::link($back, "Back", array("class" => "btn btn-secondary float-right"));
echo "</div></div><br/>";

echo html_writer::table($table);
$count = $DB->count_records('course');
$returnurl = new moodle_url('/local/school/course/courses_desc.php', array('perpage' => $perpage, 'page' => $page));
echo $OUTPUT->paging_bar($count, $page, $perpage, $returnurl);
echo $OUTPUT->footer();
