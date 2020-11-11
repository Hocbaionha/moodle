<?PHP
require_once('../../../config.php');
require_once("$CFG->libdir/phpspreadsheet/vendor/autoload.php");
require_once($CFG->dirroot . "/lib/externallib.php");


$from = optional_param('from', "", PARAM_TEXT);//as
$to = optional_param('to', "", PARAM_TEXT);//as
// yyyy-mm-dd
if($from==""||$to==""){
    redirect($CFG->wwwroot);
}
$stfrom = strtotime($from);
$stto = strtotime($to);
global $DB;
$sql = "select a.num,u.id,u.username,d.data as phone, u.email,from_unixtime(u.timecreated) as created,from_unixtime(u.lastlogin) as last_login from mdl_user u 
join (SELECT count(*) as num,userid FROM moodle.mdl_logstore_standard_log group by userid ) a on u.id=a.userid 
join mdl_user_info_data d on d.userid=u.id where d.fieldid=19 and d.data!='' 
and u.timecreated > ? and u.timecreated < ?
order by num desc";
$users = $DB->get_records_sql($sql,array("from"=>$stfrom,"to"=>$stto));


$fullpath = $CFG->dataroot . '/school/';
$template = __DIR__ . '/activity _template.xlsx';
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $reader->load($template);
$spreadsheet->setActiveSheetIndex(0);
$activeSheet = $spreadsheet->getActiveSheet();
$activeSheet->getColumnDimension('A')->setWidth(5);
$activeSheet->getColumnDimension('B')->setWidth(5);
$activeSheet->getColumnDimension('C')->setWidth(10);
$activeSheet->getColumnDimension('D')->setWidth(20);
$activeSheet->getColumnDimension('E')->setWidth(20);
$activeSheet->getColumnDimension('F')->setWidth(30);
$activeSheet->getColumnDimension('G')->setWidth(20);
$activeSheet->getColumnDimension('H')->setWidth(20);

$i = 1;
foreach ($users as $student) {
    $i++;
    $activeSheet->setCellValue("A" . $i, $i - 1);
    $activeSheet->setCellValue("B" . $i, $student->num);
    $activeSheet->setCellValue("C" . $i, $student->id);
    $activeSheet->setCellValue("D" . $i, $student->username);
    $activeSheet->setCellValue("E" . $i, $student->phone);
    $activeSheet->setCellValue("F" . $i, $student->email);
    $activeSheet->setCellValue("G" . $i, $student->created);
    $activeSheet->setCellValue("H" . $i, $student->last_login);
}

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$filename =  "activity.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'. urlencode($filename).'"');
$writer->save('php://output');