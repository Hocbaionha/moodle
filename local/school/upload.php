<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(__DIR__ . '/../../config.php');
require_once("import_form.php");
require_once $CFG->libdir . '/hbonlib/string_util.php';
require_once $CFG->libdir . '/hbonlib/lib.php';
require_once("$CFG->libdir/phpspreadsheet/vendor/autoload.php");


require_login();

$site = get_site();

$context = context_system::instance();
$PAGE->set_context($context);

$PAGE->set_heading(get_string("student", "local_school"));
$url = new moodle_url('/local/school/upload.php');
$PAGE->set_url($url);
if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow", "local_school");
    echo $OUTPUT->footer();
    die;
}
$PAGE->requires->jquery();
$PAGE->requires->css(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css'));
$PAGE->requires->css(new moodle_url('/local/school/css/custom.css'));
$mform = new import_form();
if ($mform->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $schoolurl = new moodle_url('/local/school/upload.php');
    redirect($schoolurl);
} else if ($fromform = $mform->get_data()) {
    $fullpath = __DIR__ . '/upload/';
    $schoolurl = new moodle_url('/local/school/upload.php');
    $name = $mform->get_new_filename('userfile');
    $schoolid = $fromform->school;
    $fullpath = $fullpath . $name;
    $content = $mform->get_file_content('userfile');
    $success = $mform->save_file('userfile', $fullpath, true);
    if (!$success) {
        print_error('cant_upload', 'local_school');
    }
    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($fullpath);
//    if(4!=$spreadsheet->getSheetCount()){
//        print_error('wrong_file_format', 'local_school');
//    }
    $student_sheet = $spreadsheet->getSheet(1);
    $teacher_sheet = $spreadsheet->getSheet(2);
    $class_sheet = $spreadsheet->getSheet(3);

    echo $OUTPUT->header();
    $school = $DB->get_record("school", array("id" => $schoolid));
    $tid = $school->last_teacher;
    $sid = $school->last_student;
    $sql = "SELECT p.name as pname,d.name as dname from mdl_school s join mdl_district d on d.districtid=s.districtid join mdl_province p on p.provinceid=d.provinceid where s.id=$schoolid";
    $result = $DB->get_record_sql($sql);
    $provinceAcronym = getProvinceAcronym($result->pname);
    $districtAcronym = getDistrictAcronym($result->dname);
    echo insertTeacher($teacher_sheet, $schoolid, $school->code) . "<BR/>";
    echo assignTeacher($class_sheet, $schoolid) . "<BR/>";
    echo insertStudent($student_sheet, $schoolid, $school->code) . "<BR/>";
    echo "Upload Done";
    echo $OUTPUT->footer();
//    redirect($schoolurl);
} else {
    $url = new moodle_url('/local/school/upload.php');
    $PAGE->set_url($url);

    echo $OUTPUT->header();

    $mform->display();
    $script = '
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"></script>
    <script>
        $("#id_school").selectize({
        sortField: "id"
        });
        document.getElementById("id_school").onchange = function () {
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
}

function insertTeacher($teacher_sheet, $schoolid, $school_code) {
    global $DB;
    $DB->execute("update mdl_school set approve=0 where id=$schoolid");
    $classes = $DB->get_records('class', array('schoolid' => $schoolid));
    foreach ($classes as $class) {
        $DB->delete_records('temp_teacher_class', array('classid' => $class->id));
    }
    $DB->delete_records('temp_teacher', array('schoolid' => $schoolid));
    $DB->delete_records('temp_student', array('schoolid' => $schoolid));

    $line = 0;
    $count = 0;
    foreach ($teacher_sheet->getRowIterator() as $row) {
        if ($line === 0) {
            $line++;
            continue;
        }
        $cellIterator = $row->getCellIterator();
        $col = 0;
        $teacherData = array();
        $teacherData['schoolid'] = $schoolid;
        foreach ($cellIterator as $cell) {
            $value = $cell->getValue();
            if (!isset($value)) {
                $col++;
                continue;
            }
            if ($col == 1) {
                if (empty($value))
                    break;
                $teacherData['name'] = $value;
                $info = getInfo($value, "gv");
                $teacherData["username"] = $info->username;
                $teacherData["email"] = $info->email;
            }
            if ($col == 2)
                $teacherData['department'] = $value;
            if ($col == 3)
                $teacherData['phone'] = $value;

            $col++;
        }
        if (false === ($DB->get_record('temp_teacher', array('name' => $teacherData['name'], 'schoolid' => $schoolid)))) {
            if (!$DB->insert_record('temp_teacher', $teacherData)) {
                print_error('inserterror', 'local_school');
            } else {
                $count++;
            }
        } else {
            echo html_writer::tag("font", get_string("user_existed", "local_school", $teacherData['name']), array("color" => "red")) . "<br/>";
        }
        $line++;
    }
    return get_string("insert_teacher_success", 'local_school', $count);
}

function assignTeacher($assignSheet, $schoolid) {
    global $DB;
    $school_name = $DB->get_record('school', array('id' => $schoolid))->name;

    $line = 0;
    foreach ($assignSheet->getRowIterator() as $row) {
        if ($line === 0) {
            $line++;
            continue;
        }
        $cellIterator = $row->getCellIterator();
        $col = 0;
        foreach ($cellIterator as $cell) {
            $value = $cell->getValue();
            if ($col == 0 || empty($value)) {
                $col++;
                continue;
            }
            if ($col > 5) {
                break;
            }
            if ($col == 1) {
                if (empty($value)) {
                    break;
                }
                $class = $DB->get_record('class', array('code' => $value, 'schoolid' => $schoolid));

                if (false === $class) {
                    $classid = $DB->insert_record('class', array('code' => $value, 'name' => $value . " " . $school_name, 'schoolid' => $schoolid));
                } else {
                    $classid = $class->id;
                }
            } else {
                $teacher = $DB->get_record('temp_teacher', array('name' => $value, 'schoolid' => $schoolid));
                if (false === $teacher) {
                    print_error('teachererror', 'error', 'local_school', $value);
                    return;
                }
                $type = 0;
                if ($col == 2) {
                    $type = 1; //gvcn
                    assign($DB, $classid, $teacher->id, $type);
                } else if ($col == 4) {
                    $type = 4; //english
                    assign($DB, $classid, $teacher->id, $type);
                    $teacher->department = "english";
                } else if ($col == 5) {
                    $type = 5; //literature
                    assign($DB, $classid, $teacher->id, $type);
                    $teacher->department = "literature";
                }
                if ($col == 3) {
                    $type = 2; //algebra,geometry
                    assign($DB, $classid, $teacher->id, $type);
                    $type = 3;
                    assign($DB, $classid, $teacher->id, $type);
                    $teacher->department = "math";
                }
                $DB->update_record("temp_teacher", $teacher);
            }
            $col++;
        }
        $line++;
    }
    return get_string("assign_teacher_success", 'local_school');
}

function assign($DB, $classid, $teacherid, $type) {
    if (false === ($DB->get_record('temp_teacher_class', array('classid' => $classid, 'teacher_id' => $teacherid, 'type' => $type)))) {
        $DB->insert_record('temp_teacher_class', array('classid' => $classid, 'teacher_id' => $teacherid, 'type' => $type));
    }
}

function insertStudent($student_sheet, $schoolid, $school_code) {
    global $DB;
    $line = 0;
    $count = 0;
    $school_name = $DB->get_record('school', array('id' => $schoolid))->name;

    foreach ($student_sheet->getRowIterator() as $row) {
        if ($line === 0) {
            $line++;
            continue;
        }
        $cellIterator = $row->getCellIterator();
        $col = 0;
        $studentData = array();
        $studentData['schoolid'] = $schoolid;
        foreach ($cellIterator as $cell) {
            $value = trim($cell->getValue());
            if (!isset($value)) {
                $col++;
                continue;
            }
            if ($col == 1) {
                if (empty($value))
                    break;
                $class = $DB->get_record('class', array('code' => $value, 'schoolid' => $schoolid));

                if (false === $class) {
                    $classid = $DB->insert_record('class', array('code' => $value, 'name' => $value . " " . $school_name, 'schoolid' => $schoolid));
                } else {
                    $classid = $class->id;
                }
                $studentData['classid'] = $classid;
            }
            if ($col == 2) {
                $studentData['name'] = $value;
                $info = getInfo($value, "hs");
                $studentData["username"] = $info->username;
                $studentData["email"] = $info->email;
            }
            if ($col == 3) {
                if (strcasecmp('nam', $value) == 0) {
                    $studentData['gender'] = 1;
                } else {
                    $studentData['gender'] = 0;
                }
            }
            if ($col == 4) {
                if (!empty($value)) {

                    $date = \DateTime::createFromFormat('d/m/Y', $value);
                    if ($date)
                        $studentData['birth_date'] = $date->format('Y-m-d');
                }
            }

            if ($col == 5)
                $studentData['parent'] = $value;
            if ($col == 6)
                $studentData['parent_phone'] = $value;

            $col++;
        }
        if (empty($studentData['name']))
            continue;
        if (false === ($DB->get_record('temp_student', array('name' => $studentData['name'], 'schoolid' => $schoolid, 'classid' => $studentData['classid'])))) {
            if (!$DB->insert_record('temp_student', $studentData)) {
                print_error('insertstudenterror', 'local_school', $class->name . "-" . $studentData['name']);
            } else {
                $count++;
            }
        } else {
            echo html_writer::tag("font", get_string("user_existed", "local_school", $class->name . "-" . $studentData['name']), array("color" => "red")) . "<br/>";
        }
        $line++;
    }
    return get_string("insert_student_success", 'local_school', $count);
}

function getInfo($fullname, $prefix) {
    global $tid, $sid, $school, $provinceAcronym, $districtAcronym;
    $schoolid = $school->id;
    $stt = 0;
    $user = new stdClass();

    if ($fullname == "Ban Giám Hiệu") {
        $user->username = $school->code . "-bgh";
        $user->email = "bgh@" . $school->code . ".edu.vn";
        $user->firstname = "BGH";
        $user->lastname = $school->name;
    } else {
        if ($prefix == "hs") {
            $stt = sprintf("%04d", ++$sid);
            $user->username = $school->code . "-hs" . $stt;
        } else if ($prefix == "gv") {
            $stt = sprintf("%02d", ++$tid);
            $user->username = $school->code . "-gv" . $stt;
        }
        $arrName = split_name($fullname);
        $user->firstname = $arrName['first_name'];
        $user->lastname = $arrName['last_name'];
        $firstname = strtolower(non_unicode($user->firstname));
        $lastname = strtolower(preg_replace('/\s+/', '', non_unicode($user->lastname)));
        $s = explode("(", $firstname);
        $firstname = trim($s[0]);
        $user->email = $prefix . $stt . "-" . $lastname . "-" . $firstname . "@" . $school->code . ".edu.vn";
    }
    if (!validate_email($user->email)) {
        $user->email = non_unicode($user->email);
        if (!validate_email($user->email)) {
            echo $user->email . "<br/>";
            $user->email = "";
        }
    }
    return $user;
}
