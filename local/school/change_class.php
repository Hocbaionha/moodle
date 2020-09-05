<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/group/lib.php');
require_once $CFG->libdir .'/hbonlib/string_util.php';

require_login();
$url = new moodle_url('/local/school/change_class.php');
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title("change class");
$PAGE->set_heading(get_string("change_class", "local_school"));
if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow","local_school");
    echo $OUTPUT->footer();die;
}
$userid = optional_param('id', 0, PARAM_INT);
$classid = optional_param('classid', "", PARAM_INT);
if ($classid == 0) {

    $script = '
function change_class(){
if(document.getElementById("menuclass")){
    var id = document.getElementById("menuclass").value;
    var userid = document.getElementById("userid").value;
    window.location = document.URL.split("?")[0] + "?classid="+id+"&id="+userid;
    }
}
';
    echo html_writer::script($script);

    echo $OUTPUT->header();
    $user = $DB->get_record('user', array('id' => $userid));
    $username = $user->username;
    $sql = "select f.shortname,d.data from mdl_user_info_data d join mdl_user_info_field f on d.fieldid=f.id where (f.shortname='classid' or f.shortname='schoolid') and userid=$userid";

    $other_fields = $DB->get_records_sql($sql);
    $fields = array();
    foreach ($other_fields as $o) {
        $fields["$o->shortname"] = $o->data;
    }
    
    
    $school = $DB->get_record('school', array('id' => $fields['schoolid']));
    $oldclass = $DB->get_record('class', array('id' => $fields['classid']));

    echo "School: " . $school->name;
    echo "<br/>";
    echo "Class: " . $oldclass->name;
    echo "<br/>";
    echo "Name: " . $user->lastname." ".$user->firstname;
    echo "<br/>Change to class:  ";
    $grade = $oldclass->code[0];
    $classes = $DB->get_records_sql("select id,code from mdl_class where schoolid=$school->id and code like '$grade%'");
    foreach ($classes as $class) {
        $key = $class->id;
        $value = $class->code;
        $selectClass[$key] = $value;
    }

    $attr['onchange'] = 'change_class()';
    echo html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'userid', 'value' => $userid));
    echo html_writer::select($selectClass, "class", $oldclass->id, null, $attr);
} else {
    $username = $DB->get_record('user', array('id' => $userid))->username;
    $sql = "select f.shortname,d.data from mdl_user_info_data d join mdl_user_info_field f on d.fieldid=f.id where f.shortname='schoolid' and userid=$userid";
    $schoolid = $DB->get_record_sql($sql)->data;
    $school = $DB->get_record('school', array('id' => $schoolid));

    $sql = "SELECT p.name as pname,d.name as dname from mdl_school s join mdl_district d on d.districtid=s.districtid join mdl_province p on p.provinceid=d.provinceid where s.id=$schoolid";

    $result = $DB->get_record_sql($sql);
    $class_code = $DB->get_record('class', array('id' => $classid))->code;
    $groupname = $class_code."-".$school->group_code;
    $groups = $DB->get_records('groups', array('name' => $groupname));
    $DB->delete_records('groups_members', array('userid' => $userid));
    foreach ($groups as $group) {
        $gid = $group->id;
        try {
            if (groups_add_member($gid, $userid)) {
                // add to group success
            } else {
                echo 'addedtogroupnot error:' . $gname;
            }
        } catch (moodle_exception $e) {
            echo 'addedtogroupnot error:' . $gname;
        }
    }
    $sql = "update mdl_user_info_data set data=? where userid=? and fieldid=(select id from mdl_user_info_field where shortname='classid')";
    $DB->execute($sql,array("data"=>$classid,"userid"=>$userid));
    $returnurl = new moodle_url('/local/school/change_student.php', array('schoolid' => $schoolid));
    redirect($returnurl);
}