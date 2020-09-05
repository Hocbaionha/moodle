<?php

require(__DIR__ . '/../../config.php');



require_once $CFG->libdir . '/formslib.php';

require_once $CFG->libdir . '/hbonlib/string_util.php';
require_once $CFG->libdir . '/hbonlib/lib.php';
require_login();

class copy_form extends moodleform {

    function definition() {
        global $PAGE, $DB;
        $mform = $this->_form;
        $roles = $DB->get_records("role", null);
        foreach ($roles as $role) {
//            echo "\$string['$role->shortname']='" . $role->shortname . "';</br>";
            $mform->addElement('checkbox', "$role->shortname", get_string("$role->shortname", 'local_school'));
        }
        $this->add_action_buttons();
    }

}

$context = context_system::instance();
$PAGE->set_context($context);
$check_copy_url = new moodle_url('/local/school/check_copy.php');
$PAGE->set_heading(get_string("school", "local_school"));
$PAGE->set_url($check_copy_url);
if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow", "local_school");
    echo $OUTPUT->footer();
    die;
}
$mform = new copy_form(null, null);

if ($mform->is_cancelled()) {

    redirect($check_copy_url);
} else if ($fromform = $mform->get_data()) {
    unset($fromform->submitbutton);
    $role_extends = array();
    $update_role_extends = array();
    foreach ($fromform as $key => $value) {
        $data = array("shortname" => $key, "copy" => $value);
        $sql = "select r.shortname,re.copy from mdl_role r join mdl_role_extend re on r.shortname=re.shortname where r.shortname=?";
        $role_extend = $DB->get_record_sql($sql, array("shortname" => $key));
        if (!$role_extend) {
            $role_extends[] = $data;
        } else {
            $update_role_extends[] = "'$key'";
        }
    }
    if (!empty($role_extends)) {
        $DB->insert_records("role_extend", $role_extends);
    }
    if (!empty($update_role_extends)) {

        $a = implode(",",$update_role_extends);
        $sql = "update mdl_role_extend set copy=0 where shortname not in ($a)";
        $DB->execute($sql);
        $sql = "update mdl_role_extend set copy=1 where shortname  in ($a)";
        $DB->execute($sql);
    }
    redirect($check_copy_url);
} else {
    $data = [];
    $mform = new copy_form();
    $sql = "select re.copy,r.shortname from mdl_role r join mdl_role_extend re on r.shortname=re.shortname where re.copy=1";
    $roles = $DB->get_records("role_extend");
    foreach ($roles as $re) {
        $data["$re->shortname"] = $re->copy;
    }
    $mform->set_data($data);

    echo $OUTPUT->header();
    echo "Tích chọn những role được quyền copy";
    $mform->display();
    echo $OUTPUT->footer();
}