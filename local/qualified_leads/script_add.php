<?php
require_once(__DIR__ . '/../../config.php');

function collect_leads_infomation($param=array()){
    global $DB;
    $table = 'hbon_qualified_leads';
    if(!empty($param)){
        $DB->insert_record($table, (object)$param);
    }
}
