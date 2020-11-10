<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_login();
$sitecontext = context_system::instance();

$url = new moodle_url('/local/sm/home_popup_management.php');
$PAGE->set_context($sitecontext);
$PAGE->set_url($url);

$PAGE->set_title(get_string("popup","local_sm"));
$PAGE->set_heading(get_string("popup","local_sm"));

if (!has_capability('local/school:write', $sitecontext)) {
    echo $OUTPUT->header();
    echo get_string("not_allow","local_sm");
    echo $OUTPUT->footer();die;
}
// page parameters
$delete = optional_param('delete', 0, PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);   //md5 confirmation hash
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 15, PARAM_INT);    // how many per page


$returnurl = new moodle_url('/local/sm/home_popup_management.php', array('perpage' => $perpage, 'page'=>$page));
if ($delete and confirm_sesskey()) {              // Delete a selected school, after confirmation
    require_capability('local/school:write', $sitecontext);

    $class = $DB->get_record('hbon_popup_home', array('id' => $delete), '*', MUST_EXIST);

    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('deletepopup', 'local_sm'));

        $optionsyes = array('delete' => $delete, 'confirm' => md5($delete), 'sesskey' => sesskey());
        $deleteurl = new moodle_url($returnurl, $optionsyes);
        $deletebutton = new single_button($deleteurl, get_string('delete'), 'post');

        echo $OUTPUT->confirm(get_string('deletecheckfull', '', "'$class->title'"), $deletebutton, $returnurl);
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {
        $DB->delete_records('hbon_popup_home', array('id' => $delete));
    }
}

echo $OUTPUT->header();

$hcolumns = array('title' => get_string('title', 'local_sm'),
    'image' => get_string('image', 'local_sm'),
    'link' => get_string('link', 'local_sm'),
    'status' => get_string('status', 'local_sm'),
    'created_at' => get_string('created_at', 'local_sm'),
    'public_at' => get_string('public_at', 'local_sm'),
    'expitime' => get_string('expitime', 'local_sm'),
    'is_countdown' => get_string('is_countdown', 'local_sm'),
);

$table = new html_table();
$table->head = array($hcolumns['title'], $hcolumns['image'], $hcolumns['link'],$hcolumns['status'],$hcolumns['created_at'],$hcolumns['public_at'], $hcolumns['expitime'],$hcolumns['is_countdown'],get_string('edit'), "");
$table->colclasses = array('leftalign date', 'leftalign name', 'leftalign plugin', 'leftalign setting', 'leftalign newvalue', 'leftalign originalvalue');
$table->attributes['class'] = 'admintable generaltable';


$stredit = get_string('edit');
$strdelete = get_string('delete');

$sql = "SELECT * FROM {hbon_popup_home} ";
$rs = $DB->get_recordset_sql($sql, array(), $page * $perpage, $perpage);
$result = array();
foreach ($rs as $s) {
    $buttons = array();
    $lastcolumn = '';
    if (has_capability('local/school:write', $sitecontext)) {
        $url = new moodle_url('/local/sm/home_popup_management.php', array('delete' => $s->id, 'sesskey' => sesskey()));
            $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/delete', $strdelete));
    }
    if (has_capability('local/school:write', $sitecontext)) {
        // prevent editing of admins by non-admins
        if (is_siteadmin($USER) or ! is_siteadmin($user)) {
            $url = new moodle_url('/local/sm/edit_home_popup_management.php', array('id' => $s->id));
            $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/edit', $stredit));
        }
    }
    $row = array();

    $row[] = $s->title;
    $row[] = $s->image;
    $row[] = $s->link;
    $row[] = $s->status;
    $row[] = $s->created_at;
    $row[] = $s->public_at;
    $row[] = $s->expitime;
    $row[] = $s->is_countdown;
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
$sql = "select count(*) from {hbon_popup_home}";
$count = $DB->count_records_sql($sql);
echo $OUTPUT->paging_bar($count, $page, $perpage, $returnurl);
echo html_writer::link("edit_home_popup_management.php", get_string("add",'local_sm'));

echo $OUTPUT->footer();
