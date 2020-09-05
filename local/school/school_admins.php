<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_login();
global $DB;
$sitecontext = context_system::instance();


//echo $OUTPUT->heading(get_string('title', 'local_school'));
//require_capability('moodle/category:manage', $context);

$url = new moodle_url('/local/school/school_admins.php');
$PAGE->set_context($sitecontext);
$PAGE->set_url($url);

$PAGE->set_title(get_string("school_admins","local_school"));
$PAGE->set_heading(get_string("school_admins","local_school"));
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

$returnurl = new moodle_url('/local/school/school_admins.php', array('perpage' => $perpage, 'page'=>$page));
if ($delete and confirm_sesskey()) {              // Delete a selected school, after confirmation
    require_capability('local/school:write', $sitecontext);

    $class = $DB->get_record('temp_teacher', array('id' => $delete), '*', MUST_EXIST);

    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('deleteschool', 'local_school'));

        $optionsyes = array('delete' => $delete, 'confirm' => md5($delete), 'sesskey' => sesskey());
        $deleteurl = new moodle_url($returnurl, $optionsyes);
        $deletebutton = new single_button($deleteurl, get_string('delete'), 'post');

        echo $OUTPUT->confirm(get_string('deletecheckteacher', 'local_school', "'$class->name'"), $deletebutton, $returnurl);
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {
        $DB->delete_records('temp_teacher', array('id' => $delete));
    }
}

echo $OUTPUT->header();

$hcolumns = array(
    'username' => get_string('username'),
    'name' => get_string('name'),
    'email' => get_string('email'),
    'phone' => get_string('phone', 'local_school'),
);

$table = new html_table();
$table->head = array($hcolumns['username'],$hcolumns['name'], $hcolumns['email'], $hcolumns['phone'], get_string('edit'), "");
$table->colclasses = array('leftalign date', 'leftalign name', 'leftalign plugin', 'leftalign setting', 'leftalign newvalue', 'leftalign originalvalue');
$table->attributes['class'] = 'admintable generaltable';


$stredit = get_string('edit');
$strdelete = get_string('delete');
$sql = "SELECT id,username,firstname,lastname,email FROM mdl_user where id=(select userid from mdl_user_info_data where data='admin' and fieldid = (select id from mdl_user_info_field where shortname='type'))";
$rs = $DB->get_recordset_sql($sql, array(), $page * $perpage, $perpage);
$result = array();
foreach ($rs as $user) {
    $buttons = array();
    $lastcolumn = '';
    if (has_capability('local/school:write', $sitecontext)) {
        $url = new moodle_url('/local/school/school_admins.php', array('delete' => $user->id, 'sesskey' => sesskey()));
        $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/delete', $strdelete));
    }
    if (has_capability('local/school:write', $sitecontext)) {
    }
    $row = array();
    $row[] = $user->username;
    $row[] = $user->lastname . " " . $user->firstname;
    $row[] = $user->email;
    $row[] = "";//$user->phone;
    $row[] = implode(' ', $buttons);
    $row[] = $lastcolumn;
    $table->data[] = $row;
}
$rs->close();


echo html_writer::table($table);
$sql = "SELECT count(*) FROM mdl_user where id=(select userid from mdl_user_info_data where data='admin' and fieldid = (select id from mdl_user_info_field where shortname='type'))";
$count = $DB->count_records_sql($sql);
echo $OUTPUT->paging_bar($count, $page, $perpage, $returnurl);
echo html_writer::link("edit_admin.php", get_string("add",'local_school'));
echo $OUTPUT->footer();
