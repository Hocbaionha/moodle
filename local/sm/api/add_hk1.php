
<?php

require_once('../../../config.php');

require_login();
global $USER,$OUTPUT,$PAGE;
if($USER->id!=2){
    redirect("/login/index.php");
}
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url("/local/sm/api/add_hk1.php");
$PAGE->requires->jquery();
$PAGE->requires->css(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css'));
$PAGE->requires->css(new moodle_url('/local/school/css/custom.css'));
echo $OUTPUT->header();

$approve_url = new moodle_url('/local/sm/api/do_add_hk1.php');
 $attributes['class'] = 'col-md-3';
$attlabel['class'] = 'col-md-3';
$back = new moodle_url('/admin/search.php#linkschools');
echo "<div class='row'><div class='col-12'>";
echo html_writer::link($back, "Back", array("class"=>"btn btn-secondary float-right"));
echo "</div></div><br/>";
echo '<div class="form-group row">';

echo '<div class="col-md-6"><button id="approvebtn" class="btn btn-primary ">Add to Hbon_hk1</button> </div>';
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

$script = '
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"></script>
    <script>
       
        $("#approvebtn").click(function(){
            document.getElementById("loadarea").src = "'.$approve_url.'";
            document.getElementById("progressbar").style.display="block";
            document.getElementById("loadarea").style.display="block";
            $(this).attr("disabled", "disabled");
        });
    </script>';
echo $script;
echo $OUTPUT->footer();
