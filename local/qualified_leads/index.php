<?php

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
global $DB, $USER;
require_login();
$sitecontext = context_system::instance();
$url = new moodle_url('/local/qualifield_leads/index.php');
$PAGE->set_context($sitecontext);
$PAGE->set_url($url);

$PAGE->set_title("qualified_leads");
$PAGE->set_heading(get_string("qualified_leads", "local_qualified_leads"));

if (!has_capability('local/school:write', $sitecontext)) {
    echo $OUTPUT->header();
    echo get_string("not_allow", "local_qualified_leads");
    echo $OUTPUT->footer();
    die;
}

//$PAGE->requires->jquery();
//$PAGE->requires->css(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css'));
//$PAGE->requires->css(new moodle_url('/local/class_regist/css/custom.css'));

$delete = optional_param('delete', 0, PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);   //md5 confirmation hash
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);    // how many per page
$id = optional_param('id', 0, PARAM_INT);

$returnurl = new moodle_url('/local/qualified_leads/index.php', array('perpage' => $perpage, 'page' => $page));
if ($delete and confirm_sesskey()) {              // Delete a selected school, after confirmation
    require_capability('local/school:write', $sitecontext);

    $class = $DB->get_record('hbon_qualified_leads', array('id' => $delete), '*', MUST_EXIST);

    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('deleteleads', 'local_qualified_leads'));

        $optionsyes = array('delete' => $delete, 'confirm' => md5($delete), 'sesskey' => sesskey());
        $deleteurl = new moodle_url($returnurl, $optionsyes);
        $deletebutton = new single_button($deleteurl, get_string('delete'), 'post');

        echo $OUTPUT->confirm(get_string('deletecheckleads', 'local_qualified_leads', "'$class->name'"), $deletebutton, $returnurl);
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {
        redirect($returnurl);
    }
}

echo $OUTPUT->header();

$hcolumns = array('id' => get_string('id', 'local_qualified_leads'),
    'camp' => get_string('camp', 'local_qualified_leads'),
    'url' => get_string('url', 'local_qualified_leads'),
    'ip' => get_string('ip', 'local_qualified_leads'),
    'device' => get_string('device', 'local_qualified_leads'),
    'time_created' => get_string('time_created', 'local_qualified_leads'),
);

$strdelete = get_string('delete');
$stredit = get_string('edit');
$strdownload = get_string('download');

$table = new html_table();
$table->head = array($hcolumns['id'], $hcolumns['camp'], $hcolumns['url'], $hcolumns['ip'], $hcolumns['device'], $hcolumns['time_created'], get_string('edit'), "");
$table->colclasses = array('leftalign date', 'leftalign name', 'leftalign plugin', 'leftalign setting', 'leftalign newvalue', 'leftalign originalvalue');
$table->attributes['class'] = 'admintable generaltable';

$sql = "SELECT * FROM mdl_hbon_qualified_leads ";
$rs = $DB->get_recordset_sql($sql, array(), $page * $perpage, $perpage);
$result = array();
date_default_timezone_set("Asia/Bangkok");
foreach ($rs as $s) {
    $buttons = array();
    $lastcolumn = '';
    if (has_capability('local/school:write', $sitecontext)) {
        $url = new moodle_url('/local/qualified_leads/index.php', array('delete' => $s->id, 'sesskey' => sesskey()));
        $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/delete', $strdelete));
    }
    $row = array();
    if (is_siteadmin($USER)) {
        $row[] = $s->id;
        $row[] = $s->camp;
        $row[] = $s->url;
        $row[] = $s->ip;
        $row[] = $s->device;
        $row[] = date('d-m-Y h:i:s',$s->time_created);
        $row[] = implode(' ', $buttons);
        $row[] = $lastcolumn;
        $table->data[] = $row;
    }
}
$rs->close();

$back = new moodle_url('/admin/search.php#linkschools');

echo "<div class='row'><div class='col-12'>";
echo html_writer::link($back, "Back", array("class" => "btn btn-secondary float-right"));
echo "</div></div><br/>";

echo html_writer::table($table);
$count = $DB->count_records('hbon_qualified_leads');
echo $OUTPUT->paging_bar($count, $page, $perpage, $returnurl);
echo $OUTPUT->footer();
