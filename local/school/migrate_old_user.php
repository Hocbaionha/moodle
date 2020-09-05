
<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once $CFG->libdir . '/hbonlib/string_util.php';
require_once $CFG->libdir . '/hbonlib/lib.php';
require_once(__DIR__ . '/../../admin/tool/uploaduser/locallib.php');
require_login();
$context = context_system::instance();

//require_capability('moodle/category:manage', $context);

$url = new moodle_url('/local/school/migrate_old_user.php');

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_heading(get_string("migrate", "local_school"));
if (!has_capability('moodle/category:manage', $context)) {
    echo $OUTPUT->header();
    echo get_string("not_allow", "local_school");
    echo $OUTPUT->footer();
    die;
}
echo $OUTPUT->header();
$back = new moodle_url('/admin/search.php#linkschools');
echo "<div class='row'><div class='col-12'>";
echo html_writer::link($back, "Back", array("class" => "btn btn-secondary float-right"));
echo "</div></div><br/>";
$schoolname = optional_param('school', null, PARAM_TEXT);
if (null == $schoolname) {
    $selectSchools = array();
    $sql = 'select distinct(SUBSTRING_INDEX(SUBSTRING_INDEX(name,"-",2),"-",-1)) as name from mdl_groups 
            where SUBSTRING_INDEX(SUBSTRING_INDEX(name,"-",2),"-",-1) not in (select name from mdl_school)';
    $groups = $DB->get_records_sql($sql);
    foreach ($groups as $group) {
        $key = $group->name;
        $value = $group->name;
        $selectSchools[$key] = $value;
    }
    $schoolid = reset($selectSchools);

    $attributes['class'] = 'col-md-3';
    $attlabel['class'] = 'col-md-3';

    echo "<form action='$url' method='post'>";
    echo '<div class="form-group row">';
    echo html_writer::label(get_string("choose_school", 'local_school'), "school", true, $attlabel);
    echo html_writer::select($selectSchools, "school", $schoolid, null, $attributes);
    echo '<div class="col-md-6"><button id="approvebtn" class="btn btn-primary ">Approve</button> </div>';
    echo '</div>';
    echo "</form>";
} else {

    set_time_limit(400);
    $tx = $DB->start_delegated_transaction();
    $time_start = microtime(true);
    echo "start migrate $schoolname ...<br/>";
    $sql = "select distinct(name) from mdl_groups where name like '%$schoolname%'";
    $groups = $DB->get_records_sql($sql);
    $i = 0;
    $total = count($groups);
    if (ob_get_level() == 0)
        ob_start();
    foreach ($groups as $group) {
        $class_code = null;
        $i++;
        $percent = intval($i / $total * 100) . "%";
        $gname = $group->name;
        $groupids = $DB->get_records("groups", array("name" => $gname));

        $gArr = explode("-", $gname);
        $class_code = $gArr[0];
        $sname = $gArr[1];
        $pname = $gArr[2];
        $group_code = $sname . "-" . $pname;
        // create new if not exist school, class
        $schools = $DB->get_records("school", array("name" => $sname, "districtid" => $group_code));
        if (empty($schools)) {
            $school = new stdClass();
            $school->name = $sname;
            $school->code = strtolower($pname . "-" . $sname);
            $school->districtid = $group_code;
            $school->group_code = $group_code;
            $school->cohort_code = $group_code;
            $schoolid = $DB->insert_record("school", $school, true);
        } else {
            $school = reset($schools);
            $schoolid = $school->id;
        }
        $classes = $DB->get_records("class", array("code" => $class_code, "schoolid" => $schoolid));
        if (empty($classes)) {
            $class = new stdClass();
            $class->name = $gname;
            $class->code = $class_code;
            $class->schoolid = $schoolid;
            $classid = $DB->insert_record("class", $class, true);
        } else {
            $class = reset($classes);
            $classid = $class->id;
        }
        insert_to_student($DB, $gname, $classid, $schoolid, $class_code);
        insert_to_teacher($DB, $gname, $classid, $schoolid, $class_code);

        ;
        //update_school-student field
        $school = $DB->get_record("school", array("id" => $schoolid));
        if ($school) {
            $tcn = strtolower($sname . "-gv");
            $sql1 = "select max(SUBSTRING_INDEX(username,'$tcn',-1)) as num from mdl_user where username like ('%$tcn%')";
            $school->last_teacher = $DB->get_record_sql($sql1)->num == null ? 0 : $DB->get_record_sql($sql1)->num;

            $stn = strtolower($sname . "-hs");
            $sql2 = "select max(SUBSTRING_INDEX(username,'$stn',-1)) as num from mdl_user where username like ('%$stn%')";
            $school->last_student = $DB->get_record_sql($sql2)->num == null ? 0 : $DB->get_record_sql($sql2)->num;
            $school->approve = 2;
            $DB->update_record("school", $school);
        }
        echo "Processing $gname<br/>";
        doFlush();
    }
    $tx->allow_commit();
    echo "DONE<br/>";
    echo html_writer::link($url, "Back");
}
echo $OUTPUT->footer();

function insert_to_student($DB, $gname, $classid, $schoolid, $class_code) {
    $cohortname = str_replace($class_code, "HS", $gname);
    $sql = "select distinct(m.userid) from mdl_groups g 
            join mdl_groups_members m on g.id=m.groupid join mdl_user u on u.id=m.userid 
            join mdl_cohort_members cm on cm.userid=u.id
            join mdl_cohort co on co.id = cm.cohortid
            where g.name='$gname' and co.name= '$cohortname'";
    $ids = $DB->get_records_sql($sql);
    foreach ($ids as $u) {
        $id = $u->userid;
        $user = $DB->get_record("user", array("id" => $id));
        $user->profile_field_classid = $classid;
        $user->profile_field_schoolid = $schoolid;
        $user->profile_field_type = "student";
        $user = uu_pre_process_custom_profile_data($user);
        profile_save_data($user);
    }
}

function insert_to_teacher($DB, $gname, $classid, $schoolid, $class_code) {
    $cohortname = str_replace($class_code, "GV", $gname);
    $sql = "select distinct(m.userid) from mdl_groups g 
            join mdl_groups_members m on g.id=m.groupid join mdl_user u on u.id=m.userid 
            join mdl_cohort_members cm on cm.userid=u.id
            join mdl_cohort co on co.id = cm.cohortid
            where g.name='$gname' and co.name= '$cohortname'";
    $ids = $DB->get_records_sql($sql);
    foreach ($ids as $u) {
        $id = $u->userid;
        $user = $DB->get_record("user", array("id" => $id));
        $sql = "select m.id,userid,m.groupid,g.name,g.courseid,c.shortname from mdl_groups_members m 
                join mdl_groups g on g.id=m.groupid join mdl_course c on c.id=g.courseid 
                where userid=$id and g.name='$gname'";
        
        $courses = $DB->get_records_sql($sql);
        if (empty($courses)) {
            continue;
        } else {
            if (count($courses) == 4) {
                $gid = reset($courses)->groupid;
                $user->profile_field_type = "gvcn";
                $user->profile_field_classid = $gname;
                //update teacher class
                $tc = $DB->get_records("teacher_class", array("classid" => $classid, "groupid" => $gid, "userid" => $id, "type" => 1));
                if (empty($tc)) {
                    $tc = new stdClass();
                    $tc->classid = $classid;
                    $tc->groupid = $gid;
                    $tc->userid = $id;
                    $tc->type = 1; //gvcn
                    $DB->insert_record("teacher_class", $tc, true);
                }
                $sql = "select a.id,m.groupid,g.name,a.roleid,a.userid,a.contextid,cs.id as courseid,cs.shortname from mdl_role_assignments a 
                    join mdl_groups_members m on m.userid=a.userid 
                    join mdl_groups g on m.groupid=g.id
                    join mdl_role r on r.id=a.roleid 
                    join mdl_context c on c.id=a.contextid 
                    join mdl_course cs on cs.id=c.instanceid
                    where a.userid=? and  g.courseid=cs.id and g.name=? and r.shortname='teacher'";
                $ras = $DB->get_records_sql($sql, array("userid" => $id, "name" => $gname));
                $roleid = $DB->get_record("role", array("shortname" => "gvcn"))->id;
                foreach ($ras as $ra) {
                    $sql = "update mdl_role_assignments set roleid=$roleid where id=$ra->id";
                    $DB->execute($sql);
                }
            } else {
                if (empty($user->profile_field_type)) {
                    $user->profile_field_type = "gvbm";
                }
                foreach ($courses as $course) {
                    $gid = $course->groupid;
                    $type = 0;
                    if (startsWith($course->shortname, "SH") || startsWith($course->shortname, "DS")) {
                        $type = 2;
                        $user->profile_field_department = "math";
                    } else if (startsWith($course->shortname, "HH")) {
                        $type = 3;
                        $user->profile_field_department = "math";
                    } else if (startsWith($course->shortname, "TA")) {
                        $type = 4;
                        $user->profile_field_department = "english";
                    } else if (startsWith($course->shortname, "NV")) {
                        $type = 5;
                        $user->profile_field_department = "literature";
                    }
                    $tc = $DB->get_records("teacher_class", array("classid" => $classid, "groupid" => $gid, "userid" => $id, "type" => $type));
                    if (empty($tc)) {
                        $tc = new stdClass();
                        $tc->classid = $classid;
                        $tc->groupid = $gid;
                        $tc->userid = $id;
                        $tc->type = $type; //gvbm
                        $DB->insert_record("teacher_class", $tc, true);
                    }
                    $sql = "select a.id,m.groupid,a.roleid,a.userid,a.contextid,cs.id as courseid,cs.shortname from mdl_role_assignments a 
                        join mdl_groups_members m on m.userid=a.userid 
                        join mdl_role r on r.id=a.roleid 
                        join mdl_context c on c.id=a.contextid 
                        join mdl_course cs on cs.id=c.instanceid
                        where a.userid=? and  m.groupid=? and cs.id=? and r.shortname='teacher'";
                    $ras = $DB->get_records_sql($sql, array("userid" => $id, "groupid" => $gid, "id" => $course->courseid));
                    $roleid = $DB->get_record("role", array("shortname" => "gvbm"))->id;
                    foreach ($ras as $ra) {
                        $sql = "update mdl_role_assignments set roleid=$roleid where id=$ra->id";
                        $DB->execute($sql);
                    }
                }
            }
        }
        $user->profile_field_schoolid = $schoolid;
        $user = uu_pre_process_custom_profile_data($user);
        profile_save_data($user);
    }
}
