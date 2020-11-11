<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');


global $DB;
require_login();
$sitecontext = context_system::instance();


$url = new moodle_url('/local/class_regist/class.php');
$PAGE->set_context($sitecontext);
$PAGE->set_url($url);

$PAGE->set_title("title");
$PAGE->set_heading(get_string("class", "local_class_regist"));

if (!has_capability('local/school:write', $sitecontext)) {
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


$returnurl = new moodle_url('/local/class_regist/list_class.php', array('perpage' => $perpage, 'page' => $page));
if ($delete and confirm_sesskey()) {              // Delete a selected school, after confirmation
    require_capability('local/school:write', $sitecontext);

    $class = $DB->get_record('hbon_classes', array('id' => $delete), '*', MUST_EXIST);

    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('deleteschool', 'local_class_regist'));

        $optionsyes = array('delete' => $delete, 'confirm' => md5($delete), 'sesskey' => sesskey());
        $deleteurl = new moodle_url($returnurl, $optionsyes);
        $deletebutton = new single_button($deleteurl, get_string('delete'), 'post');

        echo $OUTPUT->confirm(get_string('deletechecksclass', 'local_class_regist', "'$class->name'"), $deletebutton, $returnurl);
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {
        redirect($returnurl);
    }
}
echo $OUTPUT->header();

$hcolumns = array('code' => get_string('code', 'local_class_regist'),
    'name' => get_string('name', 'local_class_regist'),
    'level' => get_string('level', 'local_class_regist'),
    'limit' => get_string('limit', 'local_class_regist'),
    'registed' => get_string('registed', 'local_class_regist'),
    'schedule' => get_string('schedule', 'local_class_regist'),
    'linkzoom' => get_string('linkzoom', 'local_class_regist'),
);

$strdelete = get_string('delete');
$stredit = get_string('edit');
$strdownload = get_string('download');

$table = new html_table();
$table->head = array($hcolumns['code'], $hcolumns['name'], $hcolumns['level'], $hcolumns['limit'], $hcolumns['registed'], $hcolumns['schedule'], $hcolumns['linkzoom'], get_string('edit'), "");
$table->colclasses = array('leftalign date', 'leftalign name', 'leftalign plugin', 'leftalign setting', 'leftalign newvalue', 'leftalign originalvalue');
$table->attributes['class'] = 'admintable generaltable';

    $sql = "SELECT * FROM mdl_hbon_classes ";
    $rs = $DB->get_recordset_sql($sql, array(), $page * $perpage, $perpage);
    $result = array();
    foreach ($rs as $s) {
        $count = $DB->count_records("hbon_classes_register",array("classid"=>$s->id));
        $buttons = array();
        $lastcolumn = '';
        if (has_capability('local/school:write', $sitecontext)) {
            $url = new moodle_url('/local/class_regist/list_class.php', array('delete' => $s->id, 'sesskey' => sesskey()));
                $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/delete', $strdelete));
        }
        if (has_capability('local/school:write', $sitecontext)) {
            if (is_siteadmin($USER) or ! is_siteadmin($user)) {
                $url = new moodle_url('/local/class_regist/edit_class.php', array('id' => $s->id));
                $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/edit', $stredit));
            }
        }
        if (has_capability('local/school:write', $sitecontext)) {
            if (is_siteadmin($USER) or ! is_siteadmin($user)) {
                $url = new moodle_url('/local/sm/export/index.php', array('classid' => $s->id));
                $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/download', $strdownload));
            }
        }
        if (has_capability('local/school:write', $sitecontext)) {
            if (is_siteadmin($USER) or ! is_siteadmin($user)) {
                $url = new moodle_url('/local/sm/export/index.php', array('classid' => $s->id));
                $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/cohort', $strdownload));
            }
        }
        $row = array();

        $row[] = $s->code;
        $row[] = $s->name;
        $row[] = $s->level;
        $row[] = $s->limited;
        $row[] = $count;
        $row[] = $s->schedule;
        $row[] = $s->linkzoom;
        $row[] = implode(' ', $buttons);
        $row[] = $lastcolumn;
        $table->data[] = $row;
    }
    $rs->close();

$back = new moodle_url('/admin/search.php#linkschools');
$export = new moodle_url('/local/sm/export/index.php');

echo "<div class='row'><div class='col-12'>";
echo html_writer::link($export, "Export All ".$OUTPUT->pix_icon('t/download', $stredit), array("class"=>"btn btn-success float-left"));
echo html_writer::link($back, "Back", array("class"=>"btn btn-secondary float-right"));
echo "</div></div><br/>";

$a = html_writer::link("edit_class.php", get_string("add", 'local_class_regist'));
echo get_string("click_add_class", "local_class_regist", $a);

echo html_writer::table($table);
$count = $DB->count_records('hbon_classes');
echo $OUTPUT->paging_bar($count, $page, $perpage, $returnurl);
echo $OUTPUT->footer();
