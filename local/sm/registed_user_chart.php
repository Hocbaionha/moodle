<style>
    #fitem_id_byface .col-md-3,#fitem_id_bymail .col-md-3,#fitem_id_byschool .col-md-3,#fitem_id_total .col-md-3{
        flex: 0 0 75%;
        max-width: 75%;
        padding-left:0;
    }
    #fitem_id_byface .col-md-9,#fitem_id_bymail .col-md-9,#fitem_id_byschool .col-md-9,#fitem_id_total .col-md-9{
        flex: 0 0 25%;
        max-width: 25%;
        font-weight:bold;
    }
</style>
<?PHP 

require_once(dirname(dirname(__DIR__)) . '/config.php');


require_once $CFG->libdir . '/formslib.php';

class registed_user_chart_form extends moodleform {

    function definition() {
        $mform = $this->_form;

        $mform->addElement('html', '<div class="col-md-12"><div class="row">');
        $mform->addElement('html', '<div class="col-md-3">');
        $mform->addElement('static', 'byface', "Số user đăng ký bằng facebook: ","");
        $mform->addElement('html', '</div>');   
        $mform->addElement('html', '<div class="col-md-3">');
        $mform->addElement('static', 'bymail', "Số user đăng ký bằng email: ","");
        $mform->addElement('html', '</div>');   
        $mform->addElement('html', '<div class="col-md-3">');
        $mform->addElement('static', 'total', "Tổng số  khách lẻ: ","");
        $mform->addElement('html', '</div>');   
        $mform->addElement('html', '<div class="col-md-3" >');
        $mform->addElement('static', 'byschool', "Số user đăng ký qua trường học: ","");
        $mform->addElement('html', '</div>');   

        
        
        
        $mform->addElement('html', '</div></div>');  
        $mform->addElement('html', '<div class="row">');
        $mform->addElement('html', '<div class="col-md-6">');
        $mform->addElement('date_selector', 'start_date', get_string('from_date', 'local_sm'));
        $mform->setType('start_date', PARAM_INT);
        $mform->addElement('html', '</div>');  

        $mform->addElement('html', '<div class="col-md-6">');
        $mform->addElement('select', 'period', get_string('period', 'local_sm'), array("date"=>"ngày","week"=>"tuần","month"=>"tháng"));
        $mform->setType('period', PARAM_TEXT);
        $mform->addElement('html', '</div>');   
  

        $mform->addElement('html', '<div class="col-md-6">');
        $mform->addElement('date_selector', 'end_date', get_string('to_date', 'local_sm'));
        $mform->setType('end_date', PARAM_INT);
        $mform->addElement('html', '</div>');
        $mform->addElement('html', '<div class="col-md-6">');
        $this->add_action_buttons(false,"Xem");
        $mform->addElement('html', '</div>');
        $mform->addElement('html', '</div>');
        
        
    }

}

$sitecontext = context_system::instance();
$url = new moodle_url('/local/sm/registed_user_chart.php');
    $PAGE->set_context($sitecontext);
    $PAGE->set_url($url);

    $total=0;

$mform = new registed_user_chart_form(null, array('total' => $total));

if ($mform->is_cancelled()) {
    $schoolurl = new moodle_url($url, array('id' => $id));
    redirect($schoolurl);
} else if ($fromform = $mform->get_data()) {

    $toform = $fromform;

    $t= 86400;
    switch ($fromform->period){
        case "date":
            $t= 86400;
            break;
        case "week":
            $t= 86400*7;
            break;
    }
    global $DB;
    $labels=[];
    $byface=0;
    $bymail=0;
    $byschool=0;
    if($fromform->period!="month"){
        for($i=$fromform->start_date;$i<$fromform->end_date;$i=$i+$t){
            $date = date("Y-m-d",$i);
            $labels[] = $date;
            $sql = "select count(*) from mdl_user where timecreated>=$i and timecreated<".($i+$t)." and  email  like 'fb%'";
            $result = $DB->count_records_sql($sql);
            $fb[] = $result;
            $sql = "select count(*) from mdl_user where timecreated>=$i and timecreated<".($i+$t)." and email not like 'fb%' and email not like '%hocbaionha.com' and email not like '%dschool.vn'";
            $result = $DB->count_records_sql($sql);
            $gmail[] = $result;
        
            $sql = "select count(*) from mdl_user where timecreated>=$i and timecreated<".($i+$t)." and  email not like 'fb%' and (email like '%hocbaionha.com' or email like '%dschool.vn')";
            $result = $DB->count_records_sql($sql);
            $school[] = $result;
        
        }


    } else {
        for($i=$fromform->start_date;$i<$fromform->end_date;$i = strtotime("+1 month", $i)){
            $date = date("Y-m-d",$i);
            $labels[] = $date;
            $sql = "select count(*) from mdl_user where timecreated>=$i and timecreated<".(strtotime("+1 month", $i))." and  email  like 'fb%'";
            $result = $DB->count_records_sql($sql);
            $fb[] = $result;
            $sql = "select count(*) from mdl_user where timecreated>=$i and timecreated<".(strtotime("+1 month", $i))." and email not like 'fb%' and email not like '%hocbaionha.com' and email not like '%dschool.vn'";
            $result = $DB->count_records_sql($sql);
            $gmail[] = $result;
        
            $sql = "select count(*) from mdl_user where timecreated>=$i and timecreated<".(strtotime("+1 month", $i))." and  email not like 'fb%' and (email like '%hocbaionha.com' or email like '%dschool.vn')";
            $result = $DB->count_records_sql($sql);
            $school[] = $result;
        
        }
    }

    $sql = "select count(*) from mdl_user where timecreated>=$fromform->start_date and timecreated<".($fromform->end_date)." and  email  like 'fb%'";
    $toform->byface = $DB->count_records_sql($sql);

    $sql = "select count(*) from mdl_user where timecreated>=$fromform->start_date and timecreated<".($fromform->end_date)." and email not like 'fb%' and email not like '%hocbaionha.com' and email not like '%dschool.vn'";
    $toform->bymail = $DB->count_records_sql($sql);

    $sql = "select count(*) from mdl_user where timecreated>=$fromform->start_date and timecreated<".($fromform->end_date)." and email not like 'fb%' and (email like '%hocbaionha.com' or email like '%dschool.vn')";
    $toform->byschool = $DB->count_records_sql($sql);
    
    $toform->total=$toform->byface+$toform->bymail;
    $mform->set_data($toform);

 $CFG->chart_colorset = ['#EB4734','#1DA1F2'];   
    echo $OUTPUT->header();
    $mform->display();
    $chart = new core\chart_line();
    $chart->set_title('User đăng ký mới ');
    
//    $chart->set_stacked(true);
    $serie1 = new core\chart_series('Facebook', $fb);
    $serie2 = new core\chart_series('Email', $gmail);
    $serie3 = new core\chart_series('School', $school);
    $chart->add_series($serie1);
    $chart->add_series($serie2);
    // $chart->add_series($serie3);
    $chart->set_labels($labels);
    
    $chart2 = new core\chart_line();
    $serie3 = new core\chart_series('School', $school);
    $chart2->add_series($serie3);
    $chart2->set_labels($labels);
    echo $OUTPUT->render($chart);
     $CFG->chart_colorset = ['#09AC0E'];

    echo $OUTPUT->render($chart2);
    echo $OUTPUT->footer();
} else {
echo $OUTPUT->header();

    $mform->display();
    echo $OUTPUT->footer();
}
