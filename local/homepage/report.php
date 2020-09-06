<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Listing of all sessions for current user.
 *
 * @package   report_usersessions
 * @copyright 2014 Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 */

require(__DIR__ . '/../../config.php');

// redirect(new moodle_url('/report/usersessions/user.php'));
global $PAGE;
// Require login if the plugin or Moodle is configured to force login.
require_login();
// Set page context.
$PAGE->set_context(context_system::instance());

$userid = $USER->id;
// Set page layout.
$PAGE->set_pagelayout('standard');

$PAGE->navbar->add('Báo cáo học tập');

// load header
echo $OUTPUT->header();

$course_records = get_user_courses();

if (!empty($_POST)) {
	$v_course = $_POST["course"];
} 
else{
	if(!empty($course_records)){
		$flag = true;
		foreach ($course_records as $value ){
			if ( $flag ){
				$v_course = $value->id;
				$flag = false;
			}
			else{
				break;
			}
		}
	}
}
?>

<body>
	<script type='text/javascript'>
		$(document).ready(function () {
			$("#inputklevel option[value=" + '<?php echo $default_state ?>' + "]").attr('selected', 'selected');
			$("#inputksubject option[value=" + '<?php echo $default_state2 ?>' + "]").attr('selected', 'selected');
			$("#inputperiod option[value=" + '<?php echo $default_state3 ?>' + "]").attr('selected', 'selected');
		});
	</script>

	<?php if (@$_GET['debug'] == '1') echo 1111; ?>

	<?php 
	if(!empty($course_records)){
		?>
		<form action="https://hocbaionha.com/report.php" method="post">
			<div class="container">
				<div class="row">
					<div class="form-group col-md-12">
						<label for="inputcourse">Khóa học</label>
						<select id="inputcourse" name="course" class="form-control">
							<?php 
							foreach ($course_records as $value) {
								if(isset($v_course) && $value->id == $v_course){
									?>
									<option selected value=<?php echo $value->id ?>> <?php echo $value->fullname ?></option>
									<?php	
								}
								else{
									?>
									<option value=<?php echo $value->id ?>> <?php echo $value->fullname ?></option>
									<?php
								}
							}	
							?>
						</select>
					</div>
				</div>
				<button type="submit" class="btn btn-primary">Xem báo cáo</button>
			</div>
		</form>
		<?php
	}
	?>

	<?php
	
    //echo get_right_total($v_period, $v_subject, $v_klevel);

    /* $sales = new core\chart_series($v_subject, createChartSeries($v_period, $v_subject, $v_klevel));
    $labels = createChartLabels($v_period);    
    $chart = new core\chart_bar();
    $chart->set_stacked(false);
    $chart->add_series($sales);
    $chart->set_labels($labels);
    echo '<div class="row"><div class="col-sm-12">' . $OUTPUT->render($chart) . '</div></div>'; */
    echo drawCustomChartCss();
    if (isset($userid) && !empty($course_records))
    {
    	$total_quiz = get_grade_and_time_to_do_quizzes($v_course);
    	$total_time = get_time_on_course($v_course);
    	$quiz_name = [];
    	$quiz_time = [];
    	$quiz_grade = [];
    	$sum_time = 0;
    	$sum_grade = 0;

    	if(!empty($total_quiz)){
    		$count = sizeof($total_quiz);
    		foreach($total_quiz as $quiz){
    			$quiz_name[] = $quiz->name;
    			$quiz_time[] = $quiz->time_range;
    			$quiz_grade[] = $quiz->grade;
    			$sum_time += $quiz->time_range;
    			$sum_grade += $quiz->grade;
    		}
    		$avg_time = $sum_time/$count;
    		$avg_grade = $sum_grade/$count;
    	}

    	$complete = sizeof(get_user_quiz_finished($v_course));
    	$uncomplete = get_user_quizzes_total($v_course) - $complete;

    	?>
    	<div class="row  box-all"> 
    		<div class="col-md-1"></div>
    		<?php 
    		if(isset($complete) && isset($uncomplete)){
    			?>
    			<div class="col-md-2 col-sm-4">
    				<div class="ba-detail">
    					<div class="bad-text color-ffd80c">
    						<p>Số lượng quiz đã làm</p>
    						<h2><?php echo $complete; ?></h2>
    					</div>
    				</div>
    			</div>	
    			<div class="col-md-2 col-sm-4">
    				<div class="ba-detail">
    					<div class="bad-text color-c73c27">
    						<p>Số lượng quiz chưa làm</p>
    						<h2><?php echo $uncomplete; ?></h2>
    					</div>
    				</div>
    			</div>
    			<?php	
    		}
    		?>
    		<?php 
    		if(isset($avg_time) && isset($avg_grade)){
    			?>
    			<div class="col-md-2 col-sm-4">
    				<div class="ba-detail">
    					<div class="bad-text color-3dbed3">
    						<p>Thời gian làm bài trung bình (phút)</p>
    						<h2><?php echo round(($avg_time), 2); ?></h2>
    					</div>
    				</div>
    			</div>
    			<div class="col-md-2 col-sm-4">
    				<div class="ba-detail">
    					<div class="bad-text color-85c303">
    						<p>Điểm trung bình khóa học</p>
    						<h2><?php echo round(($avg_grade), 2); ?></h2>
    					</div>
    				</div>
    			</div>
    			<?php	
    		}
    		?>
    		<div class="col-md-2 col-sm-4">
    			<div class="ba-detail">
    				<div class="bad-text color-fbb040">
    					<p>Tổng thời gian đã tham gia học</p>
    					<!-- <h2><?php echo round(($sum_time/3600),2); ?><span> giờ</span></h2> -->
    					<h2><?php echo round(($total_time),2); ?><span>phút</span></h2>
    				</div>
    			</div>
    		</div>
    		<div class="col-md-1"></div>
    	</div>
    	<?php
    	if(isset($complete) && isset($uncomplete)){
    		$sales = new \core\chart_series('Hoàn thành quiz', [$complete]);
    		$expenses = new \core\chart_series('Chưa hoàn thành quiz', [$uncomplete]);
    		$chart = new core\chart_bar();
    		$chart->set_horizontal(true); 
    		$chart->add_series($sales);
    		$chart->add_series($expenses);
    		$CFG->chart_colorset = ['#09AC0E', '#FC0090'];
    		echo '<div class="row box-all"><div class="col-sm-6">' .$OUTPUT->render($chart) . '</div>';

    		$chart = new \core\chart_pie();
		    $chart->set_doughnut(true); // Calling set_doughnut(true) we display the chart as a doughnut.
		    $serie1 = new core\chart_series('Biểu đồ phân bố câu hỏi', [$complete, $uncomplete]);
		    $chart->set_labels(['Hoàn thành', 'Chưa hoàn thành']);
		    $chart->add_series($serie1);
		    $CFG->chart_colorset = ['#FC2C09', '#E8E9EA'];
		    echo '<div class="col-sm-6">' . $OUTPUT->render($chart) . '</div></div>';
		}

		if(!empty($quiz_name) && !empty($quiz_grade)){
			$sales = new \core\chart_series('Điểm số', $quiz_grade);
			$labels = $quiz_name;
			$chart = new core\chart_bar();
			$chart->set_labels($labels, 'Biểu đồ phân bố điểm số');
			$chart->add_series($sales);
			$CFG->chart_colorset = ['#8F00BC'];
			echo '<div class="row box-all"><div class="col-sm-12">' .$OUTPUT->render($chart) . '</div>';
		}

		if(!empty($quiz_name) && !empty($quiz_time)){
			$sales = new \core\chart_series('Thời gian (phút)', $quiz_time);
			$labels = $quiz_name;
			$chart = new core\chart_bar();
			$chart->set_labels($labels, 'Biểu đồ phân bố thời gian ');
			$chart->add_series($sales);
			$CFG->chart_colorset = ['#8F00BC'];
			echo '<div class="row box-all"><div class="col-sm-12">' .$OUTPUT->render($chart) . '</div>';
		}

		if(!empty($quiz_name) && !empty($quiz_time) && !empty($quiz_grade)){
			$sales = new \core\chart_series('Thời gian', $quiz_time);
			$expenses = new \core\chart_series('Điểm', $quiz_grade);
			$labels = $quiz_name;
			$chart = new core\chart_bar();
			$chart->add_series($sales);
			$chart->add_series($expenses);
			$chart->set_labels($labels);
			$CFG->chart_colorset = ['#09AC0E', '#FC0090'];
			echo '<div class="col-sm-12"><div class="col-sm-12">' .$OUTPUT->render($chart) . '</div>';
		}
	}
	else {
		echo drawCustomChart();
	}

	echo $OUTPUT->footer();

	function drawCustomChartCss() {
		return ' <style type="text/css">.box-all{margin:50px 0;border:1px #ccc solid;padding:20px 0;font-weight:700} .bad-img{width:100px;height:100px;background:#FFF;margin:0 auto 20px;}.bad-img img{max-width:100%;max-height:100%}.bad-text{text-align:center}.bad-text p{font-size:11px;margin-bottom:0;color:#333!important}.bad-text h2{margin:0;font-size:60px}.bad-text h2 span{font-size:12px}.color-ffd80c{color:#ffd80c}.color-3dbed3{color:#3dbed3}.color-85c303{color:#85c303}.color-c73c27{color:#c73c27}.color-fbb040{color:#fbb040} </style>';
	}

	function drawCustomChart() {
		$html = ' <div class="row  box-all"> 
		<div class="col-md-1"></div>
		<div class="col-md-2 col-sm-4">
		<div class="ba-detail">
		<div class="bad-text color-ffd80c">
		<p>Số lượng quiz hoàn thành</p>
		<h2>0</h2>
		</div>
		</div>
		</div>	
		<div class="col-md-2 col-sm-4">
		<div class="ba-detail">
		<div class="bad-text color-3dbed3">
		<p>Số lượng quiz chưa làm</p>
		<h2>0</h2>
		</div>
		</div>
		</div>
		<div class="col-md-2 col-sm-4">
		<div class="ba-detail">
		<div class="bad-text color-85c303">
		<p>Thời gian làm bài trung bình (phút)</p>
		<h2>0</h2>
		</div>
		</div>
		</div>
		<div class="col-md-2 col-sm-4">
		<div class="ba-detail">
		<div class="bad-text color-c73c27">
		<p>Điểm trung bình khóa học</p>
		<h2>0</h2>
		</div>
		</div>
		</div>
		<div class="col-md-2 col-sm-4">
		<div class="ba-detail">
		<div class="bad-text color-fbb040">
		<p>Tổng thời gian đã tham gia học</p>
		<h2>0</h2>
		</div>
		</div>
		</div>
		<div class="col-md-1"></div>
		</div>
		';
		return $html;
	}

	function drawCustomChartBlank() {
		$html = ' <div class="row  box-all"> 
		<div class="col-md-1"></div>
		<div class="col-md-2 col-sm-4">
		<div class="ba-detail">
		<div class="bad-text color-ffd80c">
		<p>Ngày học liên tục</p>
		<h2>0</h2>
		<label>Kỷ lục 0 ngày</label>
		</div>
		</div>
		</div>
		<div class="col-md-2 col-sm-4">
		<div class="ba-detail">
		<div class="bad-text color-3dbed3">
		<p>Bài tập đã hoàn thành</p>
		<h2>0</h2>
		<label>Hoạt động</label>
		</div>
		</div>
		</div>
		<div class="col-md-2 col-sm-4">
		<div class="ba-detail">
		<div class="bad-text color-85c303">
		<p>Câu hỏi đã hoàn thành</p>
		<h2>0</h2>
		<label>Câu hỏi</label>
		</div>
		</div>
		</div>
		<div class="col-md-2 col-sm-4">
		<div class="ba-detail">
		<div class="bad-text color-c73c27">
		<p>Chủ đề có tiến bộ</p>
		<h2>0</h2>
		<label>Chủ đề</label>
		</div>
		</div>
		</div>
		<div class="col-md-2 col-sm-4">
		<div class="ba-detail">
		<div class="bad-text color-fbb040">
		<p>Tổng thời gian học</p>
		<h2>0<span> ngày</span></h2>
		<label>00h00p00s</label>
		</div>
		</div>
		</div>
		<div class="col-md-1"></div>
		</div>
		';
		return $html;
	}

	function myfunction($v) {
		return($v->bb);
	}

	function funcRightFilter($v) {
		return ($v->ss == 'gradedright');
	}

	function get_right_total($period, $subject, $klevel) {
		global $DB;
		global $USER;
		$userid = $USER->id;
		$sqlparam = array([1]);
		$sql2 = "SELECT DATE(FROM_UNIXTIME(QS.timecreated)) as aa, C.fullname as cc, QS.state as ss, C.klevel as cklevel, C.subject as csubject
		FROM 
		{question_attempt_steps} AS QS LEFT JOIN  
		{question_attempts} AS QA   ON QS.questionattemptid = QA.id LEFT JOIN  
		{quiz_attempts} AS Q        ON QA.questionusageid = Q.uniqueid LEFT JOIN
		{quiz} AS QQ                ON QQ.id = Q.quiz LEFT JOIN
		{course} AS C               ON QQ.course = C.ID
		WHERE 
		FROM_UNIXTIME(QS.timecreated) > date_sub(now(), interval " . $period . " day) AND 
		QS.userid = " . $userid . " AND 
		(QS.state ='gradedright' OR QS.state='gradedwrong') AND 
		C.klevel =" . $klevel . " AND
		C.subject ='" . $subject . "'";
		$sql2 = "SELECT DATE(FROM_UNIXTIME(QS.timecreated)) as aa, C.fullname as cc, QS.state as ss, C.klevel as cklevel, C.subject as csubject, count(*) as bb
		FROM 
		{question_attempt_steps} AS QS LEFT JOIN  
		{question_attempts} AS QA   ON QS.questionattemptid = QA.id LEFT JOIN  
		{quiz_attempts} AS Q        ON QA.questionusageid = Q.uniqueid LEFT JOIN
		{quiz} AS QQ                ON QQ.id = Q.quiz LEFT JOIN
		{course} AS C               ON QQ.course = C.ID
		WHERE 
		FROM_UNIXTIME(QS.timecreated) > date_sub(now(), interval " . $period . " day) AND 
		QS.userid = " . $userid . " AND 
		(QS.state ='gradedright' OR QS.state='gradedwrong') AND 
		C.klevel =" . $klevel . " AND
		C.subject ='" . $subject . "'
		GROUP BY QS.state";
		$raw_list = $DB->get_records_sql($sql2, $sqlparam);
		$mapped_full_list = array_map("myfunction", $raw_list);
		$summall = array_sum($mapped_full_list);
		$right_list = array_filter($raw_list, "funcRightFilter");
		$mapped_right_list = array_map("myfunction", $right_list);
		$summright = array_sum($mapped_right_list);
		return '';
	}

	function createChartSeries($period, $subject, $klevel) {
		global $DB;
		global $USER;
		$userid = $USER->id;
        //$userid = 532;
		$sqlparam = array([]);
		$sql = "SELECT DATE(FROM_UNIXTIME(QS.timecreated)) as aa, C.fullname as cc, QS.state as ss, C.klevel as cklevel, C.subject as csubject, count(*) as bb 
		FROM 
		{question_attempt_steps} AS QS LEFT JOIN  
		{question_attempts} AS QA   ON QS.questionattemptid = QA.id LEFT JOIN  
		{quiz_attempts} AS Q        ON QA.questionusageid = Q.uniqueid LEFT JOIN
		{quiz} AS QQ                ON QQ.id = Q.quiz LEFT JOIN
		{course} AS C               ON QQ.course = C.ID
		WHERE 
		FROM_UNIXTIME(QS.timecreated) > date_sub(now(), interval " . $period . " day) AND 
		QS.userid = " . $userid . " AND 
		(QS.state ='gradedright' OR QS.state='gradedwrong') AND 
		C.klevel =" . $klevel . " AND
		C.subject ='" . $subject . "'
		GROUP BY aa ";

		$raw_list = $DB->get_records_sql($sql, $sqlparam);
		$mapped_full_list = array_map("myfunction", $raw_list);
		$summall = array_sum($mapped_full_list);
		$right_list = array_filter($raw_list, "funcRightFilter");
		$mapped_right_list = array_map("myfunction", $right_list);
		$summright = array_sum($mapped_right_list);
		$datelist14 = createChartLabels($period);
		$a1 = array_fill_keys($datelist14, 0);
		foreach ($mapped_full_list as $xx => $x_value) {
			$a1[$xx] = $x_value;
		}
		return array_values($a1);
		;
	}

	function get_number_of_improved_skills($period, $subject, $klevel) {
		global $DB;
		global $USER;
		$userid = $USER->id;
		$userid = 4;
		$sqlparam = array([]);
		$sql = "
		SELECT QA.questionusageid,  DATE(FROM_UNIXTIME(QS.timecreated)) as aa, C.fullname as cc, QS.state as ss, C.klevel as cklevel, C.subject as csubject, count(*) as bb
		FROM mdl_question_attempt_steps AS QS 
		LEFT JOIN  mdl_question_attempts AS QA ON QS.questionattemptid = QA.id
		LEFT JOIN  mdl_quiz_attempts AS Q ON QA.questionusageid = Q.uniqueid
		LEFT JOIN  mdl_quiz AS QQ ON QQ.id = Q.quiz
		LEFT JOIN  mdl_course AS C ON QQ.course = C.ID     
		WHERE FROM_UNIXTIME(QS.timecreated)>date_sub(now(), interval 7 day) AND QS.userid = 579 AND (QS.state='gradedright' OR QS.state='gradedwrong') AND C.klevel>0
		GROUP BY QA.questionusageid
		";
		$raw_list = $DB->get_records_sql($sql, $sqlparam);
		$right_list = array_filter($raw_list, "funcRightFilter");
		$mapped_right_list = array_map("myfunction", $right_list);
		$summright = array_sum($mapped_right_list);
		$summall = array_sum($raw_list);
		echo 'Làm đúng: .' . $summright . '/' . $summall;
		$mapped_full_list = array_map("myfunction", $raw_list);
		$datelist14 = createChartLabels($period);
		$a1 = array_fill_keys($datelist14, 0);
		foreach ($mapped_full_list as $xx => $x_value) {
			$a1[$xx] = $x_value;
		}
		return array_values($a1);
		;
	}

	function createChartLabels($period) {
		$end = new DateTime(date("Y-m-d"));
		$begin = $end->sub(new DateInterval('P' . $period . 'D'));
		$end = new DateTime(date("Y-m-d"));
		$end->add(new DateInterval('P1D'));
        $interval = new DateInterval('P1D'); // 1 Day
        $dateRange = new DatePeriod($begin, $interval, $end);
        $range = [];
        foreach ($dateRange as $date) {
        	$range[] = $date->format("Y-m-d");
        }
        return $range;
    }

    function get_user_activities($period, $subject, $klevel) {
    	global $DB;
    	global $USER;
    	$userid = $USER->id;
        //$userid = 532;
    	$sqlparam = array([]);
    	$sql = " ";
        /* Xem wiki:    action = viewed AND component = mod_wiki
        Làm hết bài: action = graded AND component = mod_quiz
        Xem quiz:    action = viewed AND component = mod_quiz
        SELECT l.action, l.userid, l.component, l.target, l.objectid, l.crud, l.other , c.fullname, c.klevel, c.subject, l.timecreated FROM `mdl_logstore_standard_log` as l JOIN `mdl_course` as c ON l.courseid=c.id
        WHERE l.userid = 1467  ORDER By l.timecreated 
        
        */
        $raw_list = $DB->get_records_sql($sql, $sqlparam);
        $mapped_full_list = array_map("myfunction", $raw_list);
        $summall = array_sum($mapped_full_list);
        $right_list = array_filter($raw_list, "funcRightFilter");
        $mapped_right_list = array_map("myfunction", $right_list);
        $summright = array_sum($mapped_right_list);
        $datelist14 = createChartLabels($period);
        $a1 = array_fill_keys($datelist14, 0);
        foreach ($mapped_full_list as $xx => $x_value) {
        	$a1[$xx] = $x_value;
        }
        return array_values($a1);
        ;
    }

    function get_user_courses(){
    	global $DB;
    	global $USER;
    	$userid = $USER->id; 
    	$sqlparam = array([]);
        // WHERE ue.id = " . $userid . "
    	$sql = "SELECT c.id, c.category, c.fullname
    	FROM mdl_user u 
    	JOIN mdl_user_enrolments ue ON u.id = ue.userid 
    	JOIN mdl_enrol e ON ue.enrolid = e.id 
    	JOIN mdl_course c ON e.courseid = c.id
    	WHERE u.id = " . $userid . "
    	";

    	$raw_list = $DB->get_records_sql($sql, $sqlparam);
    	foreach ($raw_list as $xx => $x_value) {
    		$courses[$xx] = $x_value;
    	}
    	return array_values($courses);
    }

    // count quizzes total in a course
    function get_user_quizzes_total($v_course){
    	global $DB;
    	global $USER;
    	$userid = $USER->id; 
    	$sqlparam = array($v_course);

    	$sql = "SELECT COUNT(q.id)
    	FROM mdl_quiz q
    	WHERE q.course = ?
    	";

    	$quizzes_total = $DB->count_records_sql($sql, $sqlparam);
    	return $quizzes_total;
    }

    // count quiz finished in a course
    function get_user_quiz_finished($v_course){
    	global $DB;
    	global $USER;
    	$userid = $USER->id; 
    	$sqlparam = array('finished', $userid, $v_course);

    	// $sql = "SELECT COUNT(qa.id)
    	// FROM mdl_user u 
    	// JOIN mdl_user_enrolments ue ON u.id = ue.userid 
    	// JOIN mdl_enrol e ON ue.enrolid = e.id 
    	// JOIN mdl_course c ON e.courseid = c.id 
    	// JOIN mdl_course_sections cs ON c.id = cs.course 
    	// JOIN mdl_course_modules cm ON (CONCAT(',', cs.sequence, ',') LIKE CONCAT('%,', cm.id, ',%'))
    	// JOIN mdl_quiz q ON q.id = cm.instance 
    	// JOIN mdl_quiz_attempts qa ON qa.quiz = q.id
    	// WHERE u.id = " . $userid . " AND c.id = " . $v_course . " AND q.course = " . $v_course . " AND qa.state = ?
    	// ";

    	$sql1 = "SELECT DISTINCT(qa.quiz)
    	FROM mdl_quiz_attempts qa
    	JOIN mdl_quiz q ON qa.quiz = q.id
    	WHERE qa.state = ? AND qa.userid = ? AND q.course = ?
    	";

    	$quiz_finished = $DB->get_records_sql($sql1, $sqlparam);
    	return $quiz_finished;
    }

    // grade quizzes and time to do quizzes
    function get_grade_and_time_to_do_quizzes($v_course){
    	global $DB;
    	global $USER;
    	$userid = $USER->id; 
    	$sqlparam = array('finished', $userid, $v_course);

    	// $sql = "SELECT DISTINCT(q.name), (qa.timefinish-qa.timestart)/60 as time_range, qg.grade
    	// FROM mdl_user u 
    	// JOIN mdl_user_enrolments ue ON u.id = ue.userid 
    	// JOIN mdl_enrol e ON ue.enrolid = e.id 
    	// JOIN mdl_course c ON e.courseid = c.id 
    	// JOIN mdl_course_sections cs ON c.id = cs.course 
    	// JOIN mdl_course_modules cm ON (CONCAT(',', cs.sequence, ',') LIKE CONCAT('%,', cm.id, ',%')) 
    	// JOIN mdl_quiz q ON q.id = cm.instance 
    	// JOIN mdl_quiz_attempts qa ON qa.quiz = q.id 
    	// JOIN mdl_quiz_grades qg ON qg.quiz = qa.id AND qg.userid = qa.userid
    	// WHERE u.id = " . $userid . " AND q.course = " . $v_course . "  AND qa.state = ?
    	// ORDER BY qa.timestart
    	// ";

    	$sql1 = "SELECT tem.name, tem.time_range, qg.grade
    	FROM 
    	(SELECT
    	qa.quiz, q.name,
    	(SUM(qa.timefinish - qa.timestart)/COUNT(qa.id))/60 as time_range
    	FROM mdl_quiz_attempts qa
    	JOIN mdl_quiz q ON qa.quiz = q.id
    	WHERE qa.state = ? AND qa.userid = ? AND q.course = ?
    	GROUP BY quiz) tem 
    	JOIN mdl_quiz_grades qg ON tem.quiz = qg.quiz
    	";

    	$raw_list = $DB->get_records_sql($sql1, $sqlparam);
    	foreach ($raw_list as $xx => $x_value) {
    		$quizzes[$xx] = $x_value;
    	}

    	return array_values($quizzes);
    }

    // time on course display 
    function get_time_on_course($v_course){
    	global $DB;
    	global $USER;
    	$userid = $USER->id; 
    	$sqlparam = array('finished', $userid, $v_course);

    	$sql = "SELECT SUM(qa.timefinish - qa.timestart)/60 as total_time
    	FROM mdl_quiz_attempts qa
    	JOIN mdl_quiz q ON qa.quiz = q.id
    	WHERE qa.state = ? AND qa.userid = ? AND q.course = ?
    	";

    	$raw_list = $DB->get_records_sql($sql, $sqlparam);
    	foreach ($raw_list as $x_value) {
    		$total_time = $x_value->total_time;
    	}

    	return $total_time;
    }

    // function get_user_course_sections(){
    // 	global $DB;
    // 	global $USER;
    // 	$userid = $USER->id; 
    // 	$sqlparam = array([]);
    // 	$sql = "SELECT cs.sequence
    // 	FROM mdl_user u 
    // 	JOIN mdl_user_enrolments ue ON u.id = ue.userid 
    // 	JOIN mdl_enrol e ON ue.enrolid = e.id 
    // 	JOIN mdl_course c ON e.courseid = c.id 
    // 	JOIN mdl_course_sections cs ON c.id = cs.course
    // 	WHERE u.id = " . $userid . "
    // 	";

    // 	$raw_list = $DB->get_records_sql($sql, $sqlparam);
    // 	$result_sections = array();
    // 	foreach ($raw_list as $xx => $x_value) {
    // 		if(!empty($x_value)){
    // 			$temp_arr = explode(",",$x_value->sequence);
    // 			if(!empty($temp_arr)){
    // 				$result_sections = array_merge($result_sections, $temp_arr);
    // 			}
    // 		}
    // 	}
    // 	return array_filter($result_sections, 'strlen');
    // }


