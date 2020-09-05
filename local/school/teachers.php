<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/authlib.php');
require_once(__DIR__ . '/../../admin/tool/uploaduser/locallib.php');

require_once("school_search_form.php");
require_once $CFG->libdir .'/hbonlib/lib.php';
require_login();

// page parameters
$delete = optional_param('delete', 0, PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);   //md5 confirmation hash
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 15, PARAM_INT);    // how many per page
$sort = optional_param('sort', 'timemodified', PARAM_ALPHA);
$dir = optional_param('dir', 'DESC', PARAM_ALPHA);
$schoolid = optional_param('schoolid', 0, PARAM_INT);
$suspend = optional_param('suspend', 0, PARAM_INT);
$unsuspend = optional_param('unsuspend', 0, PARAM_INT);

$sitecontext = context_system::instance();


//echo $OUTPUT->heading(get_string('title', 'local_school'));
//require_capability('moodle/category:manage', $context);

$url = new moodle_url('/local/school/teachers.php');

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

$returnurl = new moodle_url('/local/school/teachers.php', array('perpage' => $perpage, 'page' => $page, 'schoolid' => $schoolid));
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
        //TODO delete cohort
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
    'department' => get_string('department', 'local_school'),
    'phone' => get_string('phone', 'local_school'),
    'username' => get_string('username'),
    'email' => get_string('email'),
);

$table = new html_table();
$table->head = array($hcolumns['stt'], $hcolumns['name'], $hcolumns['department'], $hcolumns['phone'], $hcolumns['username'], $hcolumns['email'], get_string('edit'), "");
$table->colclasses = array('leftalign date', 'leftalign name', 'leftalign plugin', 'leftalign setting', 'leftalign newvalue', 'leftalign originalvalue');
$table->attributes['class'] = 'admintable generaltable';

$school = $DB->get_record("school",array("id"=>$schoolid));
$sql = "select u.id, u.username,u.firstname,u.lastname,u.email from mdl_user_info_field f 
join mdl_user_info_data d on f.id=d.fieldid join mdl_user u on d.userid=u.id 
join mdl_cohort_members m on m.userid=u.id
join mdl_cohort c on c.id=m.cohortid
where f.shortname='schoolid' and d.data=? and c.name='GV-$school->cohort_code' group by u.id";
$teachers = $DB->get_records_sql($sql, array("data"=>$schoolid), $page * $perpage, $perpage);
$line = $page * $perpage;
foreach ($teachers as $teacher) {
    $buttons = array();
    $user = $DB->get_record('user', array('username' => $teacher->username));
    if ($user) {
        $userid = $user->id;
        if (has_capability('local/school:write', $sitecontext)) {
            $url = new moodle_url('/local/school/teachers.php', array('delete' => $userid, 'sesskey' => sesskey(), 'schoolid' => $schoolid));
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
            // prevent editing of admins by non-admins
            if (is_siteadmin($USER) or ! is_siteadmin($user)) {
                $url = new moodle_url('/local/school/edit_teacher.php', array('id' => $teacher->id, 'schoolid' => $schoolid));
                $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/edit', $stredit));
            }
        }
    }
    $line++;
    $sql = "select f.shortname,d.data from mdl_user_info_data d join mdl_user_info_field f on d.fieldid=f.id where userid=$teacher->id";

    $other_fields = $DB->get_records_sql($sql);
    $fields = array();
    foreach ($other_fields as $o) {
        $fields["$o->shortname"] = $o->data;
    }

    $lastcolumn = '';
    $name = $teacher->lastname . " " . $teacher->firstname;
    $name = "<a href='/user/view.php?id=$teacher->id&course=1' title='$teacher->username'>$name</a>";
    $row = array();
    $row['STT'] = $line;

    $row['name'] = $name;


    if ("math" == $fields['department'] || "english" == $fields['department'] || "literature" == $fields['department']) {
        $row[] = get_string($fields['department'], "local_school");
    } else {
        $row[] = $fields['department'];
    }
    if (isset($fields['phone']))
        $row['phone'] = $fields['phone'];
    else
        $row['phone'] = null;
    $row['username'] = $teacher->username;
    $row['email'] = $teacher->email;

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

$mform = new school_search_form($url, array('schoolid' => $schoolid));
echo $mform->render();

echo html_writer::tag("BR", null);
$icon = $OUTPUT->pix_icon('t/edit', $stredit);
echo html_writer::label(get_string("click_edit", 'local_school', $icon), null) . "<br/>";
//echo html_writer::tag("BR", null);
$urladd = new moodle_url('/local/school/edit_teacher.php', array("schoolid" => $schoolid));
$a = "<a href='$urladd'>" . get_string("add") . "</a>";
echo get_string("click_add_teacher", "local_school", $a);

echo html_writer::table($table);
$sql = "select count(*) from (select u.id, u.username,u.firstname,u.lastname,u.email from mdl_user_info_field f 
        join mdl_user_info_data d on f.id=d.fieldid join mdl_user u on d.userid=u.id 
        join mdl_cohort_members m on m.userid=u.id
        join mdl_cohort c on c.id=m.cohortid
        where f.shortname='schoolid' and d.data=? and c.name='GV-$school->cohort_code' group by u.id) as tt";
$count = $DB->count_records_sql($sql,array("data"=>$schoolid));

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
