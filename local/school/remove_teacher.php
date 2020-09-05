<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/authlib.php');
require_once($CFG->dirroot . '/group/lib.php');
require_once(__DIR__ . '/../../admin/tool/uploaduser/locallib.php');
require_once $CFG->libdir .'/hbonlib/string_util.php';

require_login();
$context = context_system::instance();
$url = new moodle_url('/local/school/remove_teacher.php');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_heading(get_string("teachers", "local_school"));
if (!has_capability('local/school:write', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow","local_school");
    echo $OUTPUT->footer();die;
}

$userid = optional_param('id', 0, PARAM_INT);
$groupid = optional_param('groupid', 0, PARAM_INT);
$type = optional_param('type', 0, PARAM_INT);
$schoolid = optional_param('schoolid', 0, PARAM_INT);
$grade = optional_param('schoolid', 0, PARAM_INT);

$user = $DB->get_record("user", array("id" => $userid));
if ($user) {

    $sql = "select c.code from mdl_teacher_class tc join mdl_class c on c.id=tc.classid where userid=$userid and type=$type and groupid!=$groupid and c.code like '$grade%'";
    $result = $DB->get_records_sql($sql);
    
    if ($type != 1) {
        if (count($result) > 0) {
            $roleid = $DB->get_record("role",array("shortname"=>"gvbm"))->id;
            $sql = 'delete from mdl_role_assignments where contextid in
                (select distinct(instanceid) from mdl_context c join mdl_groups g on c.instanceid=g.courseid where g.id=?)
                 and roleid=? and userid=?';
            
            $DB->execute($sql, array("name" => $groupid, "roleid" => $roleid, "userid" => $userid));
        }
        groups_remove_member($groupid, $userid);
        
    } else if ($type == 1) {
        $groupname = $DB->get_record("groups", array("id" => $groupid))->name;
        $groups = $DB->get_records("groups", array("name" => $groupname));
        $roleid = $DB->get_record("role",array("shortname"=>"gvcn"))->id;
        if (count($result) > 0) {
            
            $sql = 'delete from mdl_role_assignments where contextid in
                (select distinct(instanceid) from mdl_context c join mdl_groups g on c.instanceid=g.courseid where g.name=?)
                 and roleid=? and userid=?';
            $DB->execute($sql, array("name" => $groupname, "roleid" => $roleid, "userid" => $userid));
        }
        $sql = "select a.id,m.groupid,g.name,a.roleid,a.userid,a.contextid,cs.id as courseid,cs.shortname from mdl_role_assignments a 
                join mdl_groups_members m on m.userid=a.userid 
                join mdl_groups g on m.groupid=g.id
                join mdl_role r on r.id=a.roleid 
                join mdl_context c on c.id=a.contextid 
                join mdl_course cs on cs.id=c.instanceid
                where a.userid=? and  g.courseid=cs.id and g.name=? and r.shortname='gvcn'";
        $ras = $DB->get_records_sql($sql,array("userid"=>$userid,"name"=>$groupname));
        foreach($ras as $ra){
            $sql = "delete from mdl_role_assignments where id=$ra->id";
            $DB->execute($sql);
        }
        $sql = "select groupid from mdl_groups_members where userid=? and groupid in (select id from mdl_groups where name=?) 
                and id not in (
                select m.id from mdl_groups_members m
                join mdl_role_assignments a  on m.userid=a.userid 
                join mdl_groups g on m.groupid=g.id
                join mdl_role r on r.id=a.roleid 
                join mdl_context c on c.id=a.contextid 
                join mdl_course cs on cs.id=c.instanceid
                where a.userid=? and  g.courseid=cs.id and g.name=? and r.shortname='gvbm')";
        $teachings = $DB->get_records_sql($sql,array($userid,$groupname,$userid,$groupname));
        
        foreach ($teachings as $g) {

            groups_remove_member($g->groupid, $userid);
        }
    }
    $DB->delete_records("teacher_class", array("groupid" => $groupid, "userid" => $userid, "type" => $type));
}

$back = new moodle_url('/local/school/teachers_assignment.php', array("school" => $schoolid));

redirect($back, get_string("unassign_success", "local_school"));
