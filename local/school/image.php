<?php

require(__DIR__ . '/../../config.php');
require_once $CFG->libdir . '/hbonlib/lib.php';
$context = context_system::instance();
$PAGE->set_context($context);
$filename = optional_param('filename', '', PARAM_TEXT);
//$fileinfo = array(
//    'component' => 'local_school',
//    'filearea' => 'schoolplugin',
//    'itemid' => 0,
//    'contextid' => $context->id,
//    'filepath' => '/',
//    'filename' => $filename);
//getFileFromStorage('schoolplugin', $fileinfo, true, array());
//var_dump($filename);die;

if ($filename != '') {
    $path = $CFG->dataroot . '/school/' . $filename;
    if (is_readable($path)) {
        $info = getimagesize($path);
        if ($info !== FALSE) {
            header("Content-type: {$info['mime']}");
            readfile($path);
            exit();
        } else {
            header('Content-Disposition: attachment; filename=' . $filename );
            header("Content-type: {mime_content_type($path)}");
            header('Content-Length: ' . filesize($path));
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            readfile($path);
            exit();
        }
    }
} else {
    echo "not found file name ";
}