<?php

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../../admin/tool/uploaduser/locallib.php');
require_once($CFG->dirroot . '/group/lib.php');
require_once($CFG->dirroot . '/cohort/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once $CFG->libdir . '/hbonlib/string_util.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function local_class_regist_extend_navigation(global_navigation $navigation) {
    global $USER;
    $condition =  [27774];
    if (isset($USER->username) && in_array($USER->id, $condition)) {
        $url = new moodle_url('/local/class_regist/list_class.php');
        $node = $navigation->add('Quản lý lớp live', $url,
            navigation_node::TYPE_CATEGORY, null, 'myclass', new pix_icon('i/dashboard', ''));
        $node->showinflatnavigation = true;
    }
}

function local_class_regist_extend_settings_navigation() {
// do nothing
}
