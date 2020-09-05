
<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once $CFG->libdir .'/hbonlib/lib.php';
require_login();
$script = '
function change_school(){
    var id = document.getElementById("menuschool").value;
    window.location = document.URL.split("?")[0] + "?schoolid="+id;
}
';
    echo html_writer::script($script);
$sitecontext = context_system::instance();

$context = context_system::instance();
//echo $OUTPUT->heading(get_string('title', 'local_school'));
//require_capability('moodle/category:manage', $context);

$url = new moodle_url('/local/school/upload_result.php');

//$PAGE->requires->js(new moodle_url('/local/school/js/upload_result.js'));
$PAGE->set_context($context);
$PAGE->set_url($url);

$PAGE->set_heading(get_string("student","local_school"));
if (!has_capability('local/school:write', $sitecontext)) {
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
$sort = optional_param('sort', 'timemodified', PARAM_ALPHA);
$dir = optional_param('dir', 'DESC', PARAM_ALPHA);
$schoolid = optional_param('schoolid', 0, PARAM_INT); 

$selectArray = array();
$sql = "select * from mdl_school where approve=? and code!='hbon'";
$schools = $DB->get_records_sql($sql, array("approve" => 0));
foreach ($schools as $school) {
    $key = $school->id;
    $value = $school->name;
    $selectArray[$key] = $value;
}
if(empty($selectArray)) {
    echo $OUTPUT->header();
    echo get_string("no_school","local_school");
    echo $OUTPUT->footer();die;
}

if (!$DB->get_record("school",array("id"=>$schoolid,"approve"=>0))) {
    $schoolid = reset($schools)->id;
}

$returnurl = new moodle_url('/local/school/upload_result.php', array('perpage' => $perpage, 'page' => $page,'schoolid'=>$schoolid));
$hcolumns = array('stt' => get_string('stt', 'local_school'), 'class' => get_string('class', 'local_school'),
    'form_teacher' => get_string('form_teacher', 'local_school'),
    'math_teacher' => get_string('math_teacher', 'local_school'),
    'english_teacher' => get_string('english_teacher', 'local_school'),
    'literature_teacher' => get_string('literature_teacher', 'local_school'),
);

$table = new html_table();
$table->head = array($hcolumns['stt'], $hcolumns['class'], $hcolumns['form_teacher'], $hcolumns['math_teacher'], $hcolumns['english_teacher'], $hcolumns['literature_teacher'], "");
$table->colclasses = array('leftalign date', 'leftalign name', 'leftalign plugin', 'leftalign setting', 'leftalign newvalue', 'leftalign originalvalue');
$table->attributes['class'] = 'admintable generaltable';

$classes = $DB->get_records("class", array('schoolid'=>$schoolid), '', '*', $page * $perpage, $perpage);
$line = $page * $perpage;
foreach ($classes as $class) {
    $line++;
    $row = array();
    $row['STT'] = $line;
    $row['class'] = "<a href=\"upload_result_student.php?classid=$class->id&schoolid=$schoolid\">" . $class->code . "</a>";
    $sql = "SELECT t.name,t.department from mdl_temp_teacher_class tc join mdl_temp_teacher t on tc.teacher_id=t.id where tc.classid=$class->id  and tc.type=1";
    $result = $DB->get_record_sql($sql);
        $row['form_teacher'] = '';
    if($result){
        $row['form_teacher'] = $result->name;
    }
    $row['math_teacher'] = '';
    $sql = "SELECT tc.id,t.name,t.department from mdl_temp_teacher_class tc join mdl_temp_teacher t on tc.teacher_id=t.id where tc.classid=$class->id  and (type=2)";
    $result = $DB->get_record_sql($sql);
    if($result)
    $row['math_teacher'] = $result->name;
    $sql = "SELECT t.name,t.department from mdl_temp_teacher_class tc join mdl_temp_teacher t on tc.teacher_id=t.id where tc.classid=$class->id  and type=4";
    $result = $DB->get_record_sql($sql);
    $row['english_teacher'] = '';
    if($result)
    $row['english_teacher'] = $result->name;
    $sql = "SELECT t.name,t.department from mdl_temp_teacher_class tc join mdl_temp_teacher t on tc.teacher_id=t.id where tc.classid=$class->id  and type=5";
    $result = $DB->get_record_sql($sql);
    $row['literature_teacher'] = '';
    if($result)
    $row['literature_teacher'] = $result->name;


    $table->data[] = $row;
}
echo $OUTPUT->header();

$approve_url = new moodle_url('/local/school/approve.php', array('schoolid'=>$schoolid));
 $attributes['class'] = 'col-md-3';
$attlabel['class'] = 'col-md-3';
$back = new moodle_url('/admin/search.php#linkschools');
echo "<div class='row'><div class='col-12'>";
echo html_writer::link($back, "Back", array("class"=>"btn btn-secondary float-right"));
echo "</div></div><br/>";
echo '<div class="form-group row">';
echo html_writer::label(get_string("choose_school", 'local_school'), "school", true, $attlabel);
echo html_writer::select($selectArray, "school", $schoolid, null, $attributes);
echo '<div class="col-md-6"><button id="approvebtn" class="btn btn-primary ">Approve</button> </div>';
//echo html_writer::tag("button", "Approve", array("id"=>"approvebtn","class"=>"btn btn-primary ","style"=>"float:right"));
echo '</div>';
echo '<div class="col-md-12">
		<div id="progressbar" style="border:1px solid #1177d1; border-radius: 5px; display:none"></div>
		<!-- Progress information -->
		<br>
		<div id="information" ></div>
	</div>';
echo '<iframe id="loadarea" style="display:none;width:100%;height:200px;"></iframe><br />';
echo html_writer::tag("BR", null);
echo html_writer::label(get_string("click_class",'local_school'), null);
//$mform->addElement('select', 'school', get_string('schoolid', 'local_school'), $selectArray); 
echo html_writer::table($table);
$count = $DB->count_records('class', array('schoolid'=>$schoolid));

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
        $("#approvebtn").click(function(){
            var id = document.getElementById("menuschool").value;
            document.getElementById("loadarea").src = "'.$approve_url.'";
            document.getElementById("progressbar").style.display="block";
            document.getElementById("loadarea").style.display="block";
            $(this).attr("disabled", "disabled");
        });
    </script>';
echo $script;
echo $OUTPUT->footer();
