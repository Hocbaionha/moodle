<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once("school_search_form.php");

global $DB;
require_login();
$sitecontext = context_system::instance();


$url = new moodle_url('/local/school/class.php');
$PAGE->set_context($sitecontext);
$PAGE->set_url($url);

$PAGE->set_title("title");
$PAGE->set_heading(get_string("class", "local_school"));

if (!has_capability('local/school:write', $sitecontext)) {
    echo $OUTPUT->header();
    echo get_string("not_allow","local_school");
    echo $OUTPUT->footer();die;
}
$PAGE->requires->jquery();
$PAGE->requires->css(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css'));
$PAGE->requires->css(new moodle_url('/local/school/css/custom.css'));
//$PAGE->requires->js(new moodle_url('/local/school/js/school.js'));
// page parameters
$delete = optional_param('delete', 0, PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);   //md5 confirmation hash
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);    // how many per page
$schoolid = optional_param('schoolid', 0, PARAM_INT);
$school = $DB->get_record("school", array("id" => $schoolid));

if (!$school) {
    $schools = getSchools();
    $school = reset($schools);
    if (!$school) {
        echo $OUTPUT->header();
        echo get_string("no_school", "local_school");
        echo $OUTPUT->footer();
        die;
    }
    $schoolid = $school->id;
}
$returnurl = new moodle_url('/local/school/classes.php', array('perpage' => $perpage, 'page' => $page, "schoolid" => $schoolid));
if ($delete and confirm_sesskey()) {              // Delete a selected school, after confirmation
    require_capability('local/school:write', $sitecontext);

    $class = $DB->get_record('class', array('id' => $delete), '*', MUST_EXIST);

    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('deleteschool', 'local_school'));

        $optionsyes = array('delete' => $delete, 'confirm' => md5($delete), 'sesskey' => sesskey());
        $deleteurl = new moodle_url($returnurl, $optionsyes);
        $deletebutton = new single_button($deleteurl, get_string('delete'), 'post');

        echo $OUTPUT->confirm(get_string('deletechecksclass', 'local_school', "'$class->name'"), $deletebutton, $returnurl);
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {
        $DB->delete_records('temp_teacher_class', array('classid' => $delete));
        $DB->delete_records('class', array('id' => $delete));
        redirect($returnurl);
    }
}
echo $OUTPUT->header();

$hcolumns = array('code' => get_string('code', 'local_school'),
    'name' => get_string('name', 'local_school'),
    'schoolid' => get_string('school', 'local_school'),
);

$table = new html_table();
$table->head = array($hcolumns['code'], $hcolumns['name'], $hcolumns['schoolid'], get_string('edit'), "");
$table->colclasses = array('leftalign date', 'leftalign name', 'leftalign plugin', 'leftalign setting', 'leftalign newvalue', 'leftalign originalvalue');
$table->attributes['class'] = 'admintable generaltable';
$districts=$DB->get_record("district",array("districtid"=>$school->districtid));

if ($districts) {
    $stredit = get_string('edit');
    $strdelete = get_string('delete');
    $sql = "SELECT * FROM {class} where schoolid=$schoolid";
    $rs = $DB->get_recordset_sql($sql, array(), $page * $perpage, $perpage);
    $result = array();
    foreach ($rs as $s) {
        $buttons = array();
        $lastcolumn = '';
        if (has_capability('local/school:write', $sitecontext)) {
            $url = new moodle_url('/local/school/classes.php', array('delete' => $s->id, "schoolid" => $schoolid, 'sesskey' => sesskey()));
            if ($school->approve == 0) {
                $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/delete', $strdelete));
            }
        }
        if (has_capability('local/school:write', $sitecontext)) {
            if (is_siteadmin($USER) or ! is_siteadmin($user)) {
                $url = new moodle_url('/local/school/edit_class.php', array('id' => $s->id, "schoolid" => $schoolid));
                $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/edit', $stredit));
            }
        }
        $row = array();

        $row[] = $s->code;
        $row[] = $s->name;
        $row[] = $s->schoolid;
        $row[] = implode(' ', $buttons);
        $row[] = $lastcolumn;
        $table->data[] = $row;
    }
    $rs->close();
}
$back = new moodle_url('/admin/search.php#linkschools');
echo "<div class='row'><div class='col-12'>";
echo html_writer::link($back, "Back", array("class"=>"btn btn-secondary float-right"));
echo "</div></div><br/>";
$mform = new school_search_form($url, array('schoolid' => $schoolid, 'districtid' => $school->districtid));
echo $mform->render();
$a = html_writer::link("edit_class.php?schoolid=$schoolid", get_string("add", 'local_school'));
echo get_string("click_add_class", "local_school", $a);
echo html_writer::table($table);
$count = $DB->count_records('class', array("schoolid" => $schoolid));
echo $OUTPUT->paging_bar($count, $page, $perpage, $returnurl);
$script = '
//    document.getElementById("id_school").onchange = function () {
//            M.core_formchangechecker.reset_form_dirty_state()
//            var id = document.getElementById("id_school").value;
//            if(isInt(id))
//            window.location = document.URL.split("?")[0] + "?schoolid="+id;
//        };
        function isInt(value) {
            var x = parseInt(value);
            return !isNaN(value) && (x | 0) === x;
          }';
echo html_writer::script($script);
echo $OUTPUT->footer();
