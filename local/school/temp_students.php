<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once $CFG->libdir .'/hbonlib/string_util.php';
require_once $CFG->libdir .'/hbonlib/lib.php';
require_login();
global $DB;
$sitecontext = context_system::instance();

$context = context_system::instance();
//echo $OUTPUT->heading(get_string('title', 'local_school'));
//require_capability('moodle/category:manage', $context);

$url = new moodle_url('/local/school/temp_students.php');
$PAGE->set_context($context);
$PAGE->set_url($url);

$PAGE->set_heading(get_string("student", "local_school"));
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
//TODO default first 
$classid = optional_param('classid', 0, PARAM_INT);

$selectArray = array();
$sql = "select * from mdl_school where approve=? and code!='hbon'";
$schools = $DB->get_records_sql($sql, array("approve" => 0));
foreach ($schools as $school) {
    $key = $school->id;
    $value = $school->name;
    $selectArray[$key] = $value;
}
if ($schoolid == 0) {
    $schoolid = reset($schools)->id;
}
$returnurl = new moodle_url('/local/school/temp_students.php', array('perpage' => $perpage, 'page' => $page, 'classid' => $classid, 'schoolid' => $schoolid));
if ($delete and confirm_sesskey()) {              // Delete a selected school, after confirmation
    require_capability('local/school:write', $sitecontext);

    $class = $DB->get_record('temp_student', array('id' => $delete), '*', MUST_EXIST);

    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('deleteschool', 'local_school'));

        $optionsyes = array('delete' => $delete, 'confirm' => md5($delete), 'sesskey' => sesskey());
        $deleteurl = new moodle_url($returnurl, $optionsyes);
        $deletebutton = new single_button($deleteurl, get_string('delete'), 'post');

        echo $OUTPUT->confirm(get_string('deletecheckstudent', 'local_school', "'$class->name'"), $deletebutton, $returnurl);
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {
        $DB->delete_records('temp_student', array('id' => $delete));
    }
}

echo $OUTPUT->header();

$hcolumns = array(
    'stt' => get_string('stt', 'local_school'),
    'name' => get_string('name', 'local_school'),
    'gender' => get_string('gender', 'local_school'),
    'birth_date' => get_string('birth_date', 'local_school'),
    'parent' => get_string('parent', 'local_school'),
    'parent_phone' => get_string('parent_phone', 'local_school'),
    'username' => get_string('username'),
    'email' => get_string('email'),
);
$table = new html_table();
$table->head = array($hcolumns['stt'], $hcolumns['name'], $hcolumns['gender'], $hcolumns['birth_date'], $hcolumns['parent'], $hcolumns['parent_phone'], $hcolumns['username'], $hcolumns['email'], get_string('edit'), "");
$table->colclasses = array('leftalign date', 'leftalign name', 'leftalign plugin', 'leftalign setting', 'leftalign newvalue', 'leftalign originalvalue');
$table->attributes['class'] = 'admintable generaltable';


$attributes['class'] = 'col-md-9';
$attlabel['class'] = 'col-md-3';
$classes = $DB->get_records("class", array('schoolid' => $schoolid));
foreach ($classes as $class) {
    $key = $class->id;
    $value = $class->name;
    $selectClass[$key] = $value;
}
if (empty($classes)) {

    echo '<div class="form-group row">';
    echo html_writer::label(get_string("choose_school", 'local_school'), "school", true, $attlabel);
    echo html_writer::select($selectArray, "school", $schoolid, null, $attributes);
    echo '</div>';
    echo '<div class="form-group row">';
    echo html_writer::label(get_string("choose_class", 'local_school'), "school", true, $attlabel);
    echo html_writer::select(array(), "class", null, null);
    echo '</div>';
} else {
    if ($classid == 0) {
        $classid = reset($classes)->id;
    }
    $stredit = get_string('edit');
    $strdelete = get_string('delete');
    $sql = "SELECT id,schoolid,classid,name,gender,DATE_FORMAT(birth_date,'%d-%m-%Y') as birth_date,parent,parent_phone,username,email FROM {temp_student} where classid=$classid ";
    $rs = $DB->get_recordset_sql($sql, array(), $page * $perpage, $perpage);
    $line = $page * $perpage;
    $result = array();
    foreach ($rs as $s) {
        $buttons = array();
        $lastcolumn = '';
        if (has_capability('local/school:write', $sitecontext)) {
            $url = new moodle_url('/local/school/temp_students.php', array('delete' => $s->id, 'schoolid' => $schoolid, 'sesskey' => sesskey()));
            $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/delete', $strdelete));
        }
        if (has_capability('local/school:write', $sitecontext)) {
            // prevent editing of admins by non-admins
            if (is_siteadmin($USER) or ! is_siteadmin($user)) {
                $url = new moodle_url('/local/school/edit_temp_student.php', array('id' => $s->id, 'schoolid' => $s->schoolid, 'classid' => $s->classid));
                $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/edit', $stredit));
            }
        }
        $line++;
        $row = array();
        $row['STT'] = $line;

        $row[] = $s->name;
        $row[] = $s->gender;
        $row[] = $s->birth_date;
        $row[] = $s->parent;
        $row[] = $s->parent_phone;
    $row[] = $s->username;
    $row[] = $s->email;
        $row[] = implode(' ', $buttons);
        $row[] = $lastcolumn;
        $table->data[] = $row;
    }
    $rs->close();

    $back = new moodle_url('/admin/search.php#linkschools');
    echo "<div class='row'><div class='col-12'>";
    echo html_writer::link($back, "Back", array("class" => "btn btn-secondary float-right"));
    echo "</div></div><br/>";
    echo '<div class="form-group row">';
    echo html_writer::label(get_string("choose_school", 'local_school'), "school", true, $attlabel);
    echo html_writer::select($selectArray, "school", $schoolid, null, $attributes);
    echo '</div>';
    if (empty($selectClass))
        $selectClass = array();

    $attr['onchange'] = 'change_class()';
    echo '<div class="form-group row">';
    echo html_writer::label(get_string("choose_class", 'local_school'), "school", true, $attlabel);
    echo html_writer::select($selectClass, "class", $classid, null, $attr);
    echo '</div>';
    $url = new moodle_url('/local/school/edit_temp_student.php', array('schoolid' => $s->schoolid, 'classid' => $s->classid));
    $a = html_writer::link($url, get_string("add", 'local_school'));
    echo get_string("click_add_student", "local_school", $a);
    echo html_writer::table($table);
    $sql = "SELECT count(*) FROM {temp_student} where classid=$classid";

    $count = $DB->count_records_sql($sql);
    echo $OUTPUT->paging_bar($count, $page, $perpage, $returnurl);
//    echo html_writer::link("edit_temp_student.php", get_string("add", 'local_school'));
}
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
        document.getElementById("menuclass").onchange = function () {
            var id = document.getElementById("menuclass").value;
            var schoolid = document.getElementById("menuschool").value;
            window.location = document.URL.split("?")[0] + "?classid="+id+"&schoolid="+schoolid;
        };
        function isInt(value) {
            var x = parseInt(value);
            return !isNaN(value) && (x | 0) === x;
          }
    </script>';
echo $script;

echo $OUTPUT->footer();
