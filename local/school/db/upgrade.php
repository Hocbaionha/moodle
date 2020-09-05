<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function xmldb_local_school_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();
    /// Add a new column newcol to the mdl_myqtype_options
    if ($oldversion < 2020040400) {
        $sql = "INSERT INTO `mdl_departments` (`code`, `name`) VALUES ('math', 'Toán'),('english', 'Tiếng Anh'),('literature', 'Ngữ Văn')";
        $DB->execute($sql);
    }

    return true;
}