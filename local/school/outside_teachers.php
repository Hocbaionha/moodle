<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once $CFG->libdir .'/hbonlib/string_util.php';
require_once $CFG->libdir .'/hbonlib/lib.php';
require_login();
global $DB;
$sitecontext = context_system::instance();

$context = context_system::instance();

$url = new moodle_url('/local/school/outside_teachers.php');
$PAGE->set_context($context);
$PAGE->set_url($url);

$PAGE->set_title("title");
$PAGE->set_heading(get_string("teacher", "local_school"));
if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow","local_school");
    echo $OUTPUT->footer();die;
}

$PAGE->requires->jquery();
$PAGE->requires->css(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css'));
$PAGE->requires->css(new moodle_url('/local/school/css/custom.css'));

// page parameters
$delete = optional_param('delete', 0, PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);   //md5 confirmation hash
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 15, PARAM_INT);    // how many per page
$school = $DB->get_record("school", array("code" => "hbon"));

if(!$school){
    echo "not found hbon school";die;
}
$schoolid=$school->id;
$returnurl = new moodle_url('/local/school/outside_teachers.php', array('perpage' => $perpage, 'page' => $page, 'schoolid' => $schoolid));
if ($delete and confirm_sesskey()) {              // Delete a selected school, after confirmation
    $user = $DB->get_record('user', array('id' => $delete, 'mnethostid' => $CFG->mnet_localhost_id), '*', MUST_EXIST);
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

        echo $OUTPUT->confirm(get_string('deletecheckteacher', 'local_school', "'$fullname'"), $deletebutton, $returnurl);
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
}

echo $OUTPUT->header();

$hcolumns = array(
    
    'name' => get_string('name', 'local_school'),
    'username' => get_string('username'),
    'department' => get_string('department', 'local_school'),
    'phone' => get_string('phone', 'local_school'),
    'email' => get_string('email'),
);
$table = new html_table();
$table->head = array($hcolumns['name'], $hcolumns['username'], $hcolumns['department'], $hcolumns['phone'], $hcolumns['email'], get_string('edit'), "");
$table->colclasses = array('leftalign date', 'leftalign name', 'leftalign plugin', 'leftalign setting', 'leftalign newvalue', 'leftalign originalvalue');
$table->attributes['class'] = 'admintable generaltable';


$stredit = get_string('edit');
$strdelete = get_string('delete');


$sql = "select u.id, u.username,u.firstname,u.lastname,u.email from mdl_user_info_field f 
        join mdl_user_info_data d on f.id=d.fieldid join mdl_user u on d.userid=u.id 
        join mdl_cohort_members m on m.userid=u.id 
        join mdl_cohort c on c.id=m.cohortid
        where f.shortname='schoolid' and d.data=? and c.name='GV-hbon' group by u.id";
$teachers = $DB->get_records_sql($sql, array("data"=>$schoolid), $page * $perpage, $perpage);

foreach ($teachers as $teacher) {
    $buttons = array();
    $lastcolumn = '';
    if (has_capability('local/school:write', $sitecontext)) {
        $url = new moodle_url('/local/school/outside_teachers.php', array('delete' => $teacher->id, 'schoolid' => $schoolid, 'sesskey' => sesskey()));
        $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/delete', $strdelete));
    }
    if (has_capability('local/school:write', $sitecontext)) {
        // prevent editing of admins by non-admins
        if (is_siteadmin($USER) or ! is_siteadmin($user)) {
            $url = new moodle_url('/local/school/edit_outside_teacher.php', array('id' => $teacher->id, 'schoolid' => $schoolid));
            $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/edit', $stredit));
        }
    }
    $sql = "select f.shortname,d.data,u.username from mdl_user_info_data d join mdl_user_info_field f on d.fieldid=f.id join mdl_user u on u.id=d.userid where userid=$teacher->id";

    $other_fields = $DB->get_records_sql($sql);
    $fields = array();
    $fields["department"]=null;
    foreach ($other_fields as $o) {
        $fields["$o->shortname"] = $o->data;
        $fields["username"] = $o->username;
    }
    
    $row = array();
    $name = $teacher->lastname . " " . $teacher->firstname;
    $name = "<a href='/user/view.php?id=$teacher->id&course=1' title='$teacher->username'>$name</a>";
    $row[] = $name;
    
    $row[] = $fields["username"];
    $row[] = $fields["department"];
    $row[] = $fields["phone"];
    $row[] = $teacher->email;
    $row[] = implode(' ', $buttons);
    $row[] = $lastcolumn;
    $table->data[] = $row;
}

$back = new moodle_url('/admin/search.php#linkschools');
echo "<div class='row'><div class='col-12'>";
echo html_writer::link($back, "Back", array("class"=>"btn btn-secondary float-right"));
echo "</div></div><br/>";

$urladd =  new moodle_url('/local/school/edit_outside_teacher.php',array("schoolid"=>$schoolid));
$a = "<a href='$urladd'>" . get_string("add") . "</a>";
echo get_string("click_add_teacher", "local_school", $a);
echo html_writer::table($table);
$sql = "select count(*) from (select u.id from mdl_user_info_field f 
        join mdl_user_info_data d on f.id=d.fieldid join mdl_user u on d.userid=u.id 
        join mdl_cohort_members m on m.userid=u.id 
        join mdl_cohort c on c.id=m.cohortid
        where f.shortname='schoolid' and d.data=? and c.name='GV-hbon' group by u.id) as otc";
$count = $DB->count_records_sql($sql, array("data" => $schoolid));

echo $OUTPUT->paging_bar($count, $page, $perpage, $returnurl);
$script = '
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"></script>
    <script>
        $("#menuschool").selectize({
        sortField: "id"
        });
        document.getElementById("menuschool").onchange = function () {
            var id = document.getElementById("menuschool").value;
            if(isInt(id))
            window.location = document.URL.split("?")[0] + "?schoolid="+id;
        };
        function isInt(value) {
            var x = parseInt(value);
            return !isNaN(value) && (x | 0) === x;
          }
    </script>';
echo $script;
echo $OUTPUT->footer();
