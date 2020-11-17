<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');


global $DB,$USER;
require_login();
$sitecontext = context_system::instance();

$user = $USER->id;
$url = new moodle_url('/local/class_regist/list_member.php');
$PAGE->set_context($sitecontext);
$PAGE->set_url($url);

$PAGE->set_title("title_member");
$PAGE->set_heading(get_string("member", "local_class_regist"));
$accept_user = [27774];
if (!has_capability('local/school:write', $sitecontext) && !in_array($user, $accept_user)) {
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
$classid = optional_param('classid', 0, PARAM_INT);

if($classid == null or $classid == ''){
    echo $OUTPUT->header();
    echo get_string("not_exist_classid","local_class_regist");
    echo $OUTPUT->footer();die;
}

$returnurl = new moodle_url('/local/class_regist/list_member.php', array('classid'=>$classid,'perpage' => $perpage, 'page' => $page));
if ($delete and confirm_sesskey()) {              // Delete a selected school, after confirmation
    require_capability('local/school:write', $sitecontext);

    $member = $DB->get_record('hbon_classes_register', array('id' => $delete), '*', MUST_EXIST);

    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('title_deletemember', 'local_class_regist'));

        $optionsyes = array('delete' => $delete, 'confirm' => md5($delete), 'sesskey' => sesskey());
        $deleteurl = new moodle_url($returnurl, $optionsyes);
        $deletebutton = new single_button($deleteurl, get_string('delete'), 'post');

        echo $OUTPUT->confirm(get_string('deletemember', 'local_class_regist', "'$member->name'"), $deletebutton, $returnurl);
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {
        redirect($returnurl);
    }
}
echo $OUTPUT->header();



$hcolumns = array('id' => get_string('id', 'local_class_regist'),
    'classid' => get_string('classid', 'local_class_regist'),
    'name' => get_string('name', 'local_class_regist'),
    'class' => get_string('class', 'local_class_regist'),
    'school' => get_string('school', 'local_class_regist'),
    'province' => get_string('province', 'local_class_regist'),
    'phone' => get_string('phone', 'local_class_regist'),
    'comments' => get_string('comments', 'local_class_regist'),
    'created_at' => get_string('created_at', 'local_class_regist'),
);

$strdelete = get_string('delete');
$stredit = get_string('edit');
$strdownload = get_string('download');
$strshare = "link share";

$table = new html_table();
$table->head = array($hcolumns['id'], $hcolumns['classid'], $hcolumns['name'], $hcolumns['class'], $hcolumns['school'], $hcolumns['province'], $hcolumns['phone'], $hcolumns['comments'],$hcolumns['created_at'], get_string('edit'), "");
$table->colclasses = array('leftalign date', 'leftalign name', 'leftalign plugin', 'leftalign setting', 'leftalign newvalue', 'leftalign originalvalue');
$table->attributes['class'] = 'admintable generaltable';

//$rs = $DB->get_records('hbon_classes_register', array('classid'=>$classid));
$sql = "SELECT * FROM mdl_hbon_classes_register where classid=?";
$rs = $DB->get_recordset_sql($sql, array('classid'=>$classid), $page * $perpage, $perpage);
$result = array();
foreach ($rs as $s) {
    $buttons = array();
    $lastcolumn = '';
//    if (has_capability('local/school:write', $sitecontext)) {
//        $url = new moodle_url('/local/class_regist/list_member.php', array('classid'=>$classid,'delete' => $s->id, 'sesskey' => sesskey()));
//        $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/delete', $strdelete));
//    }
    if (has_capability('local/school:write', $sitecontext) or in_array($user, $accept_user)) {
        if (is_siteadmin($USER) or ! is_siteadmin($user)) {
            $url = new moodle_url('/local/class_regist/edit_member.php', array('classid'=>$classid,'id' => $s->id));
            $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/edit', $stredit));
        }
    }
    $url = new moodle_url('/eed', array('phone'=>$s->phone));
    $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('i/publish', $strshare));

    $row = array();

    $row[] = $s->id;
    $row[] = $s->classid;
    $row[] = $s->name;
    $row[] = $s->class;
    $row[] = $s->school;
    $row[] = $s->province;
    $row[] = $s->phone;
    $row[] = $s->comments;
    $row[] = $s->created_at;
    $row[] = implode(' ', $buttons);
    $row[] = $lastcolumn;
    $table->data[] = $row;
}
$rs->close();
$back = new moodle_url('/local/class_regist/list_class.php');
$export = new moodle_url('/local/sm/export/index.php?classid='.$classid);
$creat = new moodle_url('/local/class_regist/edit_member.php');

echo "<div class='row'><div class='col-12'>";
echo html_writer::link($export, "Export All ".$OUTPUT->pix_icon('t/download', $stredit), array("class"=>"btn btn-success float-left"));
echo html_writer::link($back, "Back", array("class"=>"btn btn-secondary float-right"));
echo html_writer::link($creat, "Add ".$OUTPUT->pix_icon('t/add', $stredit), array("class"=>"btn btn-success float-right"));
echo "</div></div><br/>";

//$a = html_writer::link("edit_class.php", get_string("add", 'local_class_regist'));
//echo get_string("click_add_class", "local_class_regist", $a);

echo html_writer::table($table);
$count = $DB->count_records('hbon_classes_register', array('classid'=>$classid));
echo $OUTPUT->paging_bar($count, $page, $perpage, $returnurl);
echo $OUTPUT->footer();
