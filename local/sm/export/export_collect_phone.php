<?php
require_once(__DIR__ . '/../../../config.php');
require_once("$CFG->libdir/phpspreadsheet/vendor/autoload.php");
require_login();

global $DB,$CFG;
$sitecontext = context_system::instance();

$url = new moodle_url('/local/sm/export/export_collect_phone.php');

//param input
$from = optional_param('from', '', PARAM_TEXT);
$to = optional_param('to', '', PARAM_TEXT);

if ($classid != '' && $classid != null) {
    $header_info =  $DB->get_record('mdl_hbon_has_check_phone',array('id'=>$classid));
    if(!empty($header_info)){
        $b1 =$header_info->code;
        $b2 =$header_info->name;
        $b3 =$header_info->schedule;
        $b4 =$header_info->limited;
    }
    $fullpath = $CFG->dataroot . '/school/';
    $template = __DIR__ . '/template/collect_phone.xlsx';
    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($template);
    $spreadsheet->setActiveSheetIndex(0);
    $activeSheet = $spreadsheet->getActiveSheet();
    $activeSheet->getColumnDimension('A')->setWidth(5);
    $activeSheet->getColumnDimension('B')->setWidth(25);
    $activeSheet->getColumnDimension('C')->setWidth(10);
    $activeSheet->getColumnDimension('D')->setWidth(25);
    $activeSheet->getColumnDimension('E')->setWidth(20);
    $activeSheet->getColumnDimension('F')->setWidth(20);
    $activeSheet->getColumnDimension('G')->setWidth(20);
    $activeSheet->getColumnDimension('H')->setWidth(20);
    $activeSheet->getColumnDimension('I')->setWidth(20);
    $activeSheet->getColumnDimension('J')->setWidth(30);

    $temp_students = $DB->get_records('hbon_classes_register', array("classid" => $classid));
    $activeSheet->setCellValue("B2", $b2);
    $activeSheet->setCellValue("B3", $b3);
    $activeSheet->setCellValue("B4", count($temp_students)."/".$b4);
    $i = 7;
    foreach ($temp_students as $student) {
        $i++;
        $activeSheet->setCellValue("A" . $i, $i - 7);
        $activeSheet->setCellValue("B" . $i, $student->name);
        $activeSheet->setCellValue("C" . $i, $student->class);
        $activeSheet->setCellValue("D" . $i, $student->school);
        $activeSheet->setCellValue("E" . $i, $student->phone);
        $activeSheet->setCellValue("F" . $i, $student->province);
        $activeSheet->setCellValue("G" . $i, $b1);
        $activeSheet->setCellValue("H" . $i, $student->created_at == null ? "" : date("d/m/Y", strtotime($student->created_at)));
        $activeSheet->setCellValue("I" . $i, $student->comments);
        $activeSheet->setCellValue("J" . $i, $CFG->wwwroot.'/eed?phone='.$student->phone);
    }
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $filename = "Danh sách học sinh lớp".$b1.".xlsx";
//    $writer->save($fullpath . $filename);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. urlencode($filename).'"');
    $writer->save('php://output');
}
else{
    $fullpath = $CFG->dataroot . '/school/';
    $template = __DIR__ . '/template/classes_total_template.xlsx';
    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($template);
    $spreadsheet->setActiveSheetIndex(0);
    $activeSheet = $spreadsheet->getActiveSheet();
    $activeSheet->getColumnDimension('A')->setWidth(25);
    $activeSheet->getColumnDimension('B')->setWidth(25);
    $activeSheet->getColumnDimension('C')->setWidth(10);
    $activeSheet->getColumnDimension('D')->setWidth(25);
    $activeSheet->getColumnDimension('E')->setWidth(20);
    $activeSheet->getColumnDimension('F')->setWidth(20);
    $activeSheet->getColumnDimension('G')->setWidth(20);
    $activeSheet->getColumnDimension('H')->setWidth(20);

    $temp_students = $DB->get_records('hbon_classes_register',array());
    $activeSheet->setCellValue("B2", count($temp_students));
    $i = 3;
    foreach ($temp_students as $student) {
        $i++;
        $activeSheet->setCellValue("A" . $i, $i - 3);
        $activeSheet->setCellValue("B" . $i, $student->name);
        $activeSheet->setCellValue("C" . $i, $student->class);
        $activeSheet->setCellValue("D" . $i, $student->school);
        $activeSheet->setCellValue("E" . $i, $student->phone);
        $activeSheet->setCellValue("F" . $i, $student->province);
        $activeSheet->setCellValue("G" . $i, $student->classid);
        $activeSheet->setCellValue("H" . $i, $student->created_at == null ? "" : date("d/m/Y", strtotime($student->created_at)));
    }
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $filename = "Danh sách toàn bộ học sinh đăng kí EED.xlsx";
//    $writer->save($fullpath . $filename);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. urlencode($filename).'"');
    $writer->save('php://output');
}




