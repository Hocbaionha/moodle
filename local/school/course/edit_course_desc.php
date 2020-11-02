<?php

require(__DIR__ . '/../../../config.php');
require("edit_course_form.php");
require_once $CFG->libdir . '/hbonlib/string_util.php';
require_once $CFG->libdir . '/hbonlib/lib.php';
require_login();

$id = optional_param('id', 0, PARAM_INT);

$site = get_site();

$context = context_system::instance();
$PAGE->set_context($context);
$url = new moodle_url('/local/school/course/edit_course_form.php');
$PAGE->set_heading("edit course description");
$PAGE->set_url($url);
if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow", "local_school");
    echo $OUTPUT->footer();
    die;
}
$mform = new edit_course_form(null, array('id' => $id));
//$url = new moodle_url('/course/editcategory.php');
$coursedescurl = new moodle_url('/local/school/course/courses_desc.php', array('id' => $id));
if ($mform->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    redirect($coursedescurl);
} else if ($fromform = $mform->get_data()) {
    $fs = get_file_storage();
    $fileinfo = array(
        'component' => 'local_school', // usually = table name
        'filearea' => 'schoolplugin', // usually = table name
        'itemid' => 0, // usually = ID of row in table
        'contextid' => $context->id, // ID of context
        'filepath' => '/', // any path beginning and ending in /
        'filename' => ''); // any filename
    $fullpath = $CFG->dataroot . '/school/';
    if (!file_exists($fullpath)) {
        mkdir($fullpath, 0755, true);
    }

    $teacher_img_name = $mform->get_new_filename('teacher_img');
    if ($teacher_img_name) {
        $teacher_img_path = $fullpath . $fromform->shortname."teacher";
        
        $success = $mform->save_file('teacher_img', $teacher_img_path, true);
        if (!$success) {
            print_error('cant_upload', 'local_school');
        }
        $fileinfo['filename'] = $fromform->shortname."teacher";
        $fromform->teacher_img = $fromform->shortname."teacher";

//         getFileFromStorage('schoolplugin',$fileinfo,true,array());
    } else {
        unset($fromform->teacher_img);
    }
    $thumb_img_name = $mform->get_new_filename('thumb_img');
    if ($thumb_img_name) {
        $thumb_img_path = $fullpath . $fromform->shortname."thumb";
        
        $success = $mform->save_file('thumb_img', $thumb_img_path, true);
        if (!$success) {
            print_error('cant_upload', 'local_school');
        }
        
        $fileinfo['filename'] = $fromform->shortname."thumb";
        $fromform->thumb_img = $fromform->shortname."thumb";
    } else {
        unset($fromform->thumb_img);
    }
    $introduce_img_name = $mform->get_new_filename('introduce_img');

    if ($introduce_img_name) {
        $introduce_img_path = $fullpath . $fromform->shortname."introduce";
        $success = $mform->save_file('introduce_img', $introduce_img_path, true);
        if (!$success) {
            print_error('cant_upload', 'local_school');
        }
        $fileinfo['filename'] = $fromform->shortname."introduce";
        $fromform->introduce_img = $fromform->shortname."introduce";
    } else {
        unset($fromform->introduce_img);
    }
    $sample_img_name = $mform->get_new_filename('sample_img');
    if ($sample_img_name) {
        
        $sample_img_path = $fullpath . $fromform->shortname."sample";
        $success = $mform->save_file('sample_img', $sample_img_path, true);
        if (!$success) {
            print_error('cant_upload', 'local_school');
        }
        $fileinfo['filename'] = $fromform->shortname."sample";
        $fromform->sample_img = $fromform->shortname."sample";
        
    } else {
        unset($fromform->sample_img);
    }
    $fromform->teacher_desc = $fromform->teacher_desc['text'];
    $fromform->review = $fromform->review['text'];
    $fromform->introduce_desc = $fromform->introduce_desc['text'];

    $popupimg_name = $mform->get_new_filename('popupimg');
    if ($popupimg_name) {
        $popupimg_path = $fullpath . "popup-".$fromform->shortname;
        
        $success = $mform->save_file('popupimg', $popupimg_path, true);
        if (!$success) {
            print_error('cant_upload', 'local_school');
        }
        
        $fileinfo['filename'] = "popup-".$fromform->shortname;
        $fromform->popupimg = "popup-".$fromform->shortname;
    } else {
        unset($fromform->popupimg);
    }
//    dd($fromform);

    if (!$DB->update_record('course_desc', $fromform)) {
        print_error('updateerror', 'school');
    }
    redirect($coursedescurl);
} else {

    if ($id) {
        //edit if have $id
        $mformpage = $DB->get_record('course_desc', array('courseid' => $id));

        if (!$mformpage) {
            //create if not existed
            $course_desc = new stdClass();
            $course_desc->courseid = $id;
            $mformpageid = $DB->insert_record('course_desc', $course_desc);
            $mformpage = $course_desc;
        } else {
            $mformpageid = $mformpage->id;
        }
        $course = $DB->get_record('course', array('id' => $id));

        $mformpage->fullname = $course->fullname;
        $mformpage->shortname = $course->shortname;
        if (isset($mformpage->teacher_desc)) {
            $desc = array('text' => $mformpage->teacher_desc, 'format' => 1);
            $mformpage->teacher_desc = $desc;
        }
        if (isset($mformpage->review)) {
            $review = array('text' => $mformpage->review, 'format' => 1);
            $mformpage->review = $review;
        }
        if (isset($mformpage->introduce_desc)) {
            $introduce_desc = array('text' => $mformpage->introduce_desc, 'format' => 1);
            $mformpage->introduce_desc = $introduce_desc;
        }
        $mform = new edit_course_form(null, array('id' => $mformpageid));
        $mform->set_data($mformpage);
    }
    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
}