<?php

require(__DIR__ . '/../../config.php');
require_once $CFG->libdir . '/hbonlib/lib.php';
$context = context_system::instance();
$PAGE->set_context($context);
$filename = optional_param('filename', '', PARAM_TEXT);
$accept_type=array("png","jpp","xlsx");
if ($filename != '') {
    $path = $CFG->dataroot . '/school/' . $filename;
        if(!isset(pathinfo($path)["extension"])){
            header("HTTP/1.0 404 Not Found");exit();
        }
    $ext = pathinfo($path)["extension"];
    if (is_readable($path) && in_array($ext,$accept_type)) {
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
    } else {
        header("HTTP/1.0 404 Not Found");exit();
    }
    
} else {
    header("HTTP/1.0 404 Not Found");
    echo "not found file name ";
}