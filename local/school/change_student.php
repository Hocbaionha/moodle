<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/authlib.php');
require_once(__DIR__ . '/../../admin/tool/uploaduser/locallib.php');

require_once("school_search_form.php");
require_once $CFG->libdir .'/hbonlib/string_util.php';

require_login();

// page parameters
$delete = optional_param('delete', 0, PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);   //md5 confirmation hash
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 15, PARAM_INT);    // how many per page
$sort = optional_param('sort', 'timemodified', PARAM_ALPHA);
$dir = optional_param('dir', 'DESC', PARAM_ALPHA);
$schoolid = optional_param('schoolid', 0, PARAM_INT);
$classid = optional_param('classid', 0, PARAM_INT);
$suspend = optional_param('suspend', 0, PARAM_INT);
$unsuspend = optional_param('unsuspend', 0, PARAM_INT);

$sitecontext = context_system::instance();


//echo $OUTPUT->heading(get_string('title', 'local_school'));
//require_capability('moodle/category:manage', $context);

$url = new moodle_url('/local/school/change_student.php');

//$PAGE->requires->js(new moodle_url('/local/school/js/upload_result.js'));
$PAGE->set_context($sitecontext);
$PAGE->set_url($url);

$PAGE->set_title("title");
$PAGE->set_heading(get_string("school", "local_school"));
if (!has_capability('local/school:write', $sitecontext)) {
    echo $OUTPUT->header();
    echo get_string("not_allow","local_school");
    echo $OUTPUT->footer();die;
}
$PAGE->requires->jquery();
$PAGE->requires->css(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css'));
$PAGE->requires->css(new moodle_url('/local/school/css/custom.css'));

$stredit = get_string('change_class', "local_school");
$strdelete = get_string('delete');
$strsuspend = get_string('suspenduser', 'admin');
$strunsuspend = get_string('unsuspenduser', 'admin');
$strdeletecheck = get_string('deletecheck');
$strshowallusers = get_string('showallusers');
$schools = getSchools();
$selectArray = array();
foreach ($schools as $school) {
    $key = $school->id;
    $value = $school->name;
    $selectArray[$key] = $value;
}


if ($schoolid == 0 && !empty($schools))
    $schoolid = reset($schools)->id;

$classes = $DB->get_records("class", array('schoolid' => $schoolid));
foreach ($classes as $class) {
    $key = $class->id;
    $value = $class->name;
    $selectClass[$key] = $value;
}
if (sizeof($classes) == 0) {
    $classid = -1;
}

if ($classid == 0)
    $classid = reset($classes)->id;

$returnurl = new moodle_url('/local/school/change_student.php', array('perpage' => $perpage, 'page' => $page, 'classid' => $classid, 'schoolid' => $schoolid));
if ($delete and confirm_sesskey()) {              // Delete a selected user, after confirmation
    require_capability('moodle/user:delete', $sitecontext);

    $user = $DB->get_record('user', array('id' => $delete, 'mnethostid' => $CFG->mnet_localhost_id), '*', MUST_EXIST);
    $name = $user->username;
    if ($user->deleted) {
        print_error('usernotdeleteddeleted', 'error');
    }
    if (is_siteadmin($user->id)) {
        print_error('useradminodelete', 'error');
    }

    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        $fullname = fullname($user, true);
        echo $OUTPUT->heading(get_string('deleteuser', 'admin'));

        $optionsyes = array('delete' => $delete, 'confirm' => md5($delete), 'sesskey' => sesskey());
        $deleteurl = new moodle_url($returnurl, $optionsyes);
        $deletebutton = new single_button($deleteurl, get_string('delete'), 'post');

        echo $OUTPUT->confirm(get_string('deletecheckfull', '', "'$fullname'"), $deletebutton, $returnurl);
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {

        if (!is_siteadmin($user) and $USER->id != $user->id and $user->suspended != 1) {
            $user->suspended = 1;
            // Force logout.
            \core\session\manager::kill_user_sessions($user->id);
            update_user($user);
        }

        $DB->delete_records('groups_members', array('userid' => $user->id));
        $DB->delete_records('cohort_members', array('userid' => $user->id));
        $user->profile_field_classid = 0;
        $user = uu_pre_process_custom_profile_data($user);
        profile_save_data($user);
        redirect($returnurl);
    }
} else if ($suspend and confirm_sesskey()) {
    require_capability('moodle/user:update', $sitecontext);

    if ($user = $DB->get_record('user', array('id' => $suspend, 'mnethostid' => $CFG->mnet_localhost_id, 'deleted' => 0))) {
        if (!is_siteadmin($user) and $USER->id != $user->id and $user->suspended != 1) {
            $user->suspended = 1;
            // Force logout.
            \core\session\manager::kill_user_sessions($user->id);
            update_user($user);
        }
    }
    redirect($returnurl);
} else if ($unsuspend and confirm_sesskey()) {
    require_capability('moodle/user:update', $sitecontext);

    if ($user = $DB->get_record('user', array('id' => $unsuspend, 'mnethostid' => $CFG->mnet_localhost_id, 'deleted' => 0))) {
        if ($user->suspended != 0) {
            $user->suspended = 0;
            update_user($user);
        }
    }
    redirect($returnurl);
}


$hcolumns = array('stt' => get_string('stt', 'local_school'), 'name' => get_string('name', 'local_school'),
    'gender' => get_string('gender', 'local_school'),
    'birth_date' => get_string('birth_date', 'local_school'),
    'parent' => get_string('parent', 'local_school'),
    'parentphone' => get_string('parent_phone', 'local_school'),
    'username' => get_string('username'),
    'approve' => get_string('approve', 'local_school'),
);

$table = new html_table();
$table->head = array($hcolumns['stt'], $hcolumns['name'], $hcolumns['gender'], $hcolumns['birth_date'], $hcolumns['parent'], $hcolumns['parentphone'], $hcolumns['username'], $hcolumns['approve'], get_string('edit'), "");
$table->colclasses = array('leftalign date', 'leftalign name', 'leftalign plugin', 'leftalign setting', 'leftalign newvalue', 'leftalign originalvalue');
$table->attributes['class'] = 'admintable generaltable';

//$students = $DB->get_records("temp_student", array('classid' => $classid), '', '*', $page * $perpage, $perpage);

$sql="select u.id, u.username,u.firstname,u.lastname,u.email from mdl_user_info_field f 
        join mdl_user_info_data d on f.id=d.fieldid join mdl_user u on d.userid=u.id 
        join mdl_role_assignments a on u.id=a.userid 
        join mdl_role r on r.id=a.roleid
        where f.shortname='classid' and d.data=$classid and r.shortname='student' group by u.id";
$students = $DB->get_records_sql($sql, null, $page * $perpage, $perpage);
$line = $page * $perpage;
foreach ($students as $s) {
    $buttons = array();
    $user = $DB->get_record('user', array('username' => $s->username));
    if ($user) {
        $userid = $user->id;
        if (has_capability('local/school:write', $sitecontext)) {
            
            $url = new moodle_url('/local/school/change_student.php', array('delete' => $userid, 'sesskey' => sesskey(),'perpage' => $perpage, 'page' => $page, 'classid' => $classid, 'schoolid' => $schoolid));
            $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/delete', $strdelete));
        }
        // suspend button
        if (has_capability('moodle/user:update', $sitecontext)) {
            if (is_mnet_remote_user($user)) {
                // mnet users have special access control, they can not be deleted the standard way or suspended
                $accessctrl = 'allow';
                if ($acl = $DB->get_record('mnet_sso_access_control', array('username' => $user->username, 'mnet_host_id' => $user->mnethostid))) {
                    $accessctrl = $acl->accessctrl;
                }
                $changeaccessto = ($accessctrl == 'deny' ? 'allow' : 'deny');
                $buttons[] = " (<a href=\"?acl={$user->id}&amp;accessctrl=$changeaccessto&amp;sesskey=" . sesskey() . "\">" . get_string($changeaccessto, 'mnet') . " access</a>)";
            } else {
                if ($user->suspended) {
                    $url = new moodle_url($returnurl, array('unsuspend' => $user->id, 'sesskey' => sesskey()));
                    $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/show', $strunsuspend));
                } else {
                    if ($user->id == $USER->id or is_siteadmin($user)) {
                        // no suspending of admins or self!
                    } else {
                        $url = new moodle_url($returnurl, array('suspend' => $user->id, 'sesskey' => sesskey()));
                        $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/hide', $strsuspend));
                    }
                }

                if (login_is_lockedout($user)) {
                    $url = new moodle_url($returnurl, array('unlock' => $user->id, 'sesskey' => sesskey()));
                    $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/unlock', $strunlock));
                }
            }
        }
        if (has_capability('local/school:write', $sitecontext)) {
            if (is_siteadmin($USER) or ! is_siteadmin($user)) {
                $url = new moodle_url('/local/school/change_class.php', array('id' => $userid));
                $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/edit', $stredit));
            }
        }
    }
    $line++;
    $sql = "select f.shortname,d.data from mdl_user_info_data d join mdl_user_info_field f on d.fieldid=f.id where userid=$s->id";

    $other_fields = $DB->get_records_sql($sql);
    $fields = array();
    foreach ($other_fields as $o) {
        $fields["$o->shortname"] = $o->data;
    }

    $lastcolumn = '';
    $name = $s->lastname . " " . $s->firstname;
    $row = array();
    $row['STT'] = $line;
    if (has_capability('local/school:write', $sitecontext)) {
        // prevent editing of admins by non-admins
        if (is_siteadmin($USER) or ! is_siteadmin($user)) {

            $url = new moodle_url('/local/school/edit_core_student.php', array('id' => $s->id, 'schoolid' => $schoolid, 'classid' => $classid));
            $row['name'] = html_writer::link($url, $name, array("title" => $s->username));
        }
    } else {
        $row['name'] = $name;
    }
    $row['gender'] = $fields['gender'] == 1 ? "Nam" : "Nữ";
    if (empty($fields['birthdate']))
        $row['birthdate'] = null;
    else
        $row['birthdate'] = date('d/m/Y', $fields['birthdate']);
    $row['parent'] = $fields['parent'];
    $row['parentphone'] = $fields['parentphone'];
    $row['username'] = $s->username;
    $row['approve'] = "1";

    $row[] = implode(' ', $buttons);
    $table->data[] = $row;
}

echo $OUTPUT->header();
if (count($schools) == 0) {
    echo get_string("no_school", "local_school");
    echo $OUTPUT->footer();
    die;
}
$back = new moodle_url('/admin/search.php#linkschools');
echo "<div class='row'><div class='col-12'>";
echo html_writer::link($back, "Back", array("class" => "btn btn-secondary float-right"));
echo "</div></div><br/>";

$mform = new school_search_form($url, array('schoolid' => $schoolid, 'districtid' => $school->districtid));
echo $mform->render();
if (empty($selectClass))
    $selectClass = array();
$attlabel['class'] = 'col-md-3';
$attr['onchange'] = 'change_class()';
$attr['class'] = 'col-md-3';

echo '<form class="mform"><div class="form-group row">';
echo html_writer::label(get_string("choose_class", 'local_school'), "class", true, $attlabel);
echo '<div class="col-md-9 ">';
echo html_writer::select($selectClass, "class", $classid, null, $attr);
echo '</div></div></form>';

echo html_writer::tag("BR", null);
$icon = $OUTPUT->pix_icon('t/edit', $stredit);
echo html_writer::label(get_string("click_edit", 'local_school', $icon), null) . "<br/>";
//echo html_writer::tag("BR", null);
$urladd = new moodle_url('/local/school/edit_core_student.php', array("schoolid" => $schoolid, "classid" => $classid));
$a = html_writer::link($urladd, get_string("add", 'local_school'));
echo get_string("click_add_student", "local_school", $a);

echo html_writer::table($table);
$sql="select count(*) from (select u.id, u.username,u.firstname,u.lastname,u.email from mdl_user_info_field f 
        join mdl_user_info_data d on f.id=d.fieldid join mdl_user u on d.userid=u.id 
        join mdl_role_assignments a on u.id=a.userid 
        join mdl_role r on r.id=a.roleid
        where f.shortname='classid' and d.data=$classid and r.shortname='student' group by u.id) as ss";
$count = $DB->count_records_sql($sql);

echo $OUTPUT->paging_bar($count, $page, $perpage, $returnurl);
echo $OUTPUT->footer();

$script = '
function change_class(){
if(document.getElementById("menuclass")){
    var id = document.getElementById("menuclass").value;
    var schoolid = document.getElementById("dataschools").value;
    window.location = document.URL.split("?")[0] + "?classid="+id+"&schoolid="+schoolid;
    }
}
';
echo html_writer::script($script);
