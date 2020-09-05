<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_login();
global $DB;
$sitecontext = context_system::instance();

$context = context_system::instance();
//echo $OUTPUT->heading(get_string('title', 'local_school'));
//require_capability('moodle/category:manage', $context);

$url = new moodle_url('/local/school/temp_teachers.php');
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
$schoolid = optional_param('schoolid', 0, PARAM_INT);

$returnurl = new moodle_url('/local/school/temp_teachers.php', array('perpage' => $perpage, 'page' => $page, 'schoolid' => $schoolid));
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
    'schoolname' => get_string('schoolname', 'local_school'),
    'name' => get_string('name', 'local_school'),
    'department' => get_string('department', 'local_school'),
    'phone' => get_string('phone', 'local_school'),
    'username' => get_string('username'),
    'email' => get_string('email'),
);
$table = new html_table();
$table->head = array($hcolumns['schoolname'], $hcolumns['name'], $hcolumns['department'], $hcolumns['phone'], $hcolumns['username'], $hcolumns['email'], get_string('edit'), "");
$table->colclasses = array('leftalign date', 'leftalign name', 'leftalign plugin', 'leftalign setting', 'leftalign newvalue', 'leftalign originalvalue');
$table->attributes['class'] = 'admintable generaltable';


$stredit = get_string('edit');
$strdelete = get_string('delete');

$sql = "select * from mdl_school where approve=? and code!='hbon'";
$schools = $DB->get_records_sql($sql, array("approve" => 0));
$selectArray = array();
foreach ($schools as $school) {
    $key = $school->id;
    $value = $school->name;
    $selectArray[$key] = $value;
}
if ($schoolid == 0 && count($schools) > 0) {
    $schoolid = reset($schools)->id;
}
$sql = "SELECT * FROM {temp_teacher} where schoolid=$schoolid";
$rs = $DB->get_recordset_sql($sql, array(), $page * $perpage, $perpage);
$result = array();
foreach ($rs as $s) {
    $buttons = array();
    $lastcolumn = '';
    if (has_capability('local/school:write', $sitecontext)) {
        $url = new moodle_url('/local/school/temp_teachers.php', array('delete' => $s->id, 'schoolid' => $s->schoolid, 'sesskey' => sesskey()));
        $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/delete', $strdelete));
    }
    if (has_capability('local/school:write', $sitecontext)) {
        // prevent editing of admins by non-admins
        if (is_siteadmin($USER) or ! is_siteadmin($user)) {
            $url = new moodle_url('/local/school/edit_temp_teacher.php', array('id' => $s->id, 'schoolid' => $s->schoolid));
            $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/edit', $stredit));
        }
    }
    $row = array();
    $schoolname = $DB->get_record('school', array('id' => $s->schoolid))->name;
    $row[] = $schoolname;
    $row[] = $s->name;

    if ("math" == $s->department || "english" == $s->department || "literature" == $s->department) {
        $row[] = get_string("$s->department", "local_school");
    } else {
        $row[] = $s->department;
    }
    $row[] = $s->phone;
    $row[] = $s->username;
    $row[] = $s->email;
    $row[] = implode(' ', $buttons);
    $row[] = $lastcolumn;
    $table->data[] = $row;
}
$rs->close();
$attributes['class'] = 'col-md-6';
$attlabel['class'] = 'col-md-3';
$back = new moodle_url('/admin/search.php#linkschools');
echo "<div class='row'><div class='col-12'>";
echo html_writer::link($back, "Back", array("class" => "btn btn-secondary float-right"));
echo "</div></div><br/>";
echo '<div class="form-group row">';
echo html_writer::label(get_string("choose_school", 'local_school'), "school", true, $attlabel);
echo html_writer::select($selectArray, "school", $schoolid, null, $attributes);
echo '<div class="col-md-6">' . html_writer::link("edit_temp_teacher.php", get_string("add", 'local_school'), array("class" => "btn btn-primary")) . '</div>';
echo '</div>';

echo html_writer::table($table);
$count = $DB->count_records('temp_teacher', array("schoolid" => $schoolid));
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
