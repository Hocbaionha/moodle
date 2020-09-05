<?php

require_once(__DIR__ . '/../../config.php');
require_once("$CFG->libdir/phpspreadsheet/vendor/autoload.php");
require_login();


function export($schoolid, $filename) {
    global $DB,$CFG;
    $school = $DB->get_record("school", array("id" => $schoolid));
    if (empty($school)) {
        echo "school not found";
        die;
    }
    $fullpath = $CFG->dataroot . '/school/';
    $template = __DIR__ . '/school_template.xlsx';
    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($template);
    $spreadsheet->setActiveSheetIndex(0);
    $activeSheet = $spreadsheet->getActiveSheet();
    $activeSheet->getColumnDimension('A')->setWidth(5);
    $activeSheet->getColumnDimension('B')->setWidth(5);
    $activeSheet->getColumnDimension('C')->setWidth(25);
    $activeSheet->getColumnDimension('D')->setWidth(10);
    $activeSheet->getColumnDimension('E')->setWidth(10);
    $activeSheet->getColumnDimension('F')->setWidth(25);
    $activeSheet->getColumnDimension('G')->setWidth(20);
    $activeSheet->getColumnDimension('H')->setWidth(20);
    $activeSheet->getColumnDimension('I')->setWidth(20);

    $sql = "select s.id,c.code,s.name,s.gender,s.birth_date,s.parent,s.parent_phone,s.username,s.password from mdl_temp_student s join mdl_class c on s.classid=c.id where s.schoolid=?";
    $temp_students = $DB->get_records_sql($sql, array("schoolid" => $schoolid));
    $i = 1;
    foreach ($temp_students as $student) {
        $i++;
        $activeSheet->setCellValue("A" . $i, $i - 1);
        $activeSheet->setCellValue("B" . $i, $student->code);
        $activeSheet->setCellValue("C" . $i, $student->name);
        $activeSheet->setCellValue("D" . $i, $student->gender == 1 ? "Nam" : "Nữ");
        $activeSheet->setCellValue("E" . $i, $student->birth_date == null ? "" : date("d/m/Y", strtotime($student->birth_date)));
        $activeSheet->setCellValue("F" . $i, $student->parent);
        $activeSheet->setCellValue("G" . $i, $student->parent_phone);
        $activeSheet->setCellValue("H" . $i, $student->username);
        $activeSheet->setCellValue("I" . $i, $student->password);
    }
    $spreadsheet->setActiveSheetIndex(1);
    $activeSheet = $spreadsheet->getActiveSheet();
    $activeSheet->getColumnDimension('A')->setWidth(5);
    $activeSheet->getColumnDimension('B')->setWidth(25);
    $activeSheet->getColumnDimension('C')->setWidth(20);
    $activeSheet->getColumnDimension('D')->setWidth(20);
    $activeSheet->getColumnDimension('E')->setWidth(20);
    $activeSheet->getColumnDimension('F')->setWidth(20);
    $sql = "select s.name,s.department,s.phone,s.username,s.password from mdl_temp_teacher s where s.schoolid=?";
    $temp_teachers = $DB->get_records_sql($sql, array("schoolid" => $schoolid));
    $i = 1;
    foreach ($temp_teachers as $teacher) {
        $i++;
        $activeSheet->setCellValue("A" . $i, $i - 1);
        $activeSheet->setCellValue("B" . $i, $teacher->name);
        $department = $teacher->department;
        if ($department == "math") {
            $department = "Toán";
        } else if ($department == "english") {
            $department = "Tiếng Anh";
        } else if ($department == "literature") {
            $department = "Ngữ Văn";
        }
        $activeSheet->setCellValue("C" . $i, $department);
        $activeSheet->setCellValue("D" . $i, $teacher->phone);
        $activeSheet->setCellValue("E" . $i, $teacher->username);
        $activeSheet->setCellValue("F" . $i, $teacher->password);
    }
    $spreadsheet->setActiveSheetIndex(2);
    $activeSheet = $spreadsheet->getActiveSheet();
    $activeSheet->getColumnDimension('A')->setWidth(5);
    $activeSheet->getColumnDimension('B')->setWidth(5);
    $activeSheet->getColumnDimension('C')->setWidth(25);
    $activeSheet->getColumnDimension('D')->setWidth(25);
    $activeSheet->getColumnDimension('E')->setWidth(25);
    $activeSheet->getColumnDimension('F')->setWidth(25);
    $classes = $DB->get_records("class", array("schoolid" => $schoolid));
    $i = 1;
    foreach ($classes as $c) {
        $i++;
        $activeSheet->setCellValue("A" . $i, $i - 1);
        $activeSheet->setCellValue("B" . $i, $c->code);
        $sql = "select tc.id,t.name,t.department,tc.type from mdl_temp_teacher_class tc join mdl_temp_teacher t on tc.teacher_id=t.id where tc.classid=?";
        $teachers = $DB->get_records_sql($sql, array("schoolid" => $c->id));
        foreach ($teachers as $t) {
            if ($t->type == 1) {
                $activeSheet->setCellValue("C" . $i, $t->name);
            }
            if ($t->type == 2) {
                $activeSheet->setCellValue("D" . $i, $t->name);
            }
            if ($t->type == 4) {
                $activeSheet->setCellValue("E" . $i, $t->name);
            }
            if ($t->type == 5) {
                $activeSheet->setCellValue("F" . $i, $t->name);
            }
        }
    }
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

    $writer->save($fullpath . $filename);
    $school->filename = $filename;
    $DB->update_record("school", $school);
}
