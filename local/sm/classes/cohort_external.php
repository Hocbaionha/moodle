<?PHP
require_once($CFG->dirroot . "/cohort/externallib.php");

class local_sm_cohort_external extends external_api{

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function create_product_parameters() {
        return new external_function_parameters(
            array(
                'cohort' => 
                    new external_single_structure(
                        array(
                'name' => new external_value(PARAM_TEXT, 'name'),
                'idnumber' => new external_value(PARAM_TEXT, 'idnumber'),
                'description' => new external_value(PARAM_RAW, 'description', VALUE_OPTIONAL),
                'descriptionformat' => new external_format_value('description', VALUE_DEFAULT),
                'visible' => new external_value(PARAM_BOOL, 'cohort visible', VALUE_OPTIONAL, true),
                'theme' => new external_value(PARAM_THEME,
                    'the cohort theme. The allowcohortthemes setting must be enabled on Moodle',
                    VALUE_OPTIONAL
                ),
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'shortname' => new external_value(PARAM_TEXT, 'The value of the custom field'),
                        )
                    ), 'course fields', VALUE_OPTIONAL)
            ))
            )
        );
    }
    function create_product($params){
        global $CFG, $DB;
        require_once("$CFG->dirroot/cohort/lib.php");
        $name = json_encode($params['name']);
        $name=trim($name,'"');
        $name=trim($name,"'");
        $idnumber = json_encode($params['idnumber']);
        $idnumber=trim($idnumber,'"');
        $idnumber=trim($idnumber,"'");
        $courses = json_encode($params['courses']);
        
        $transaction = $DB->start_delegated_transaction();
        $cohort = new stdClass();
        $cohort->name=trim($name," ");
        $cohort->idnumber==trim($idnumber," ");
        $syscontext = context_system::instance();
        $cohort->categorytype = 'system';
        $cohort->contextid = $syscontext->id;
        //check existed idnumber
        if ($DB->record_exists('cohort', array('idnumber' => $cohort->idnumber))) {
            return ["status"=>"exited:".$idnumber];
        }

        $cohort->id = cohort_add_cohort($cohort);
        $sql = "select cs.id,cs.course,cs.section,cs.sequence,cs.availability from mdl_course_sections cs join mdl_course c on c.id=cs.course where c.shortname=?";
        foreach ($params['courses'] as $coursep) {
            $shortname = $coursep["shortname"];
            $course_sections = $DB->get_records_sql($sql,array("shortname"=>$shortname));
            foreach($course_sections as $section){
                $availability = $section->availability;
                if(isset($availability)){
                    $availability = json_decode($section->availability);
                    $op = $availability->op;
                    $c = $availability->c;
                    //cohort type availability
                    $cohorst_existed=false;
                    foreach($c as $co){
                        if($co->type="cohort"&&$co->id=$cohort->id){
                            $cohorst_existed=true;
                        }
                    }
                    if(!$cohorst_existed){
                        $ca = new stdClass();
                        $ca->type="cohort";
                        $ca->id = $cohort->id;
                        array_push($availability->c,$ca);
                        $sql = "update mdl_course_sections set availability=? where id=?";
                        $availability->op="|";
                        $DB->execute($sql,array("availability"=>json_encode($availability),"id"=>$section->id));
                    }
                } else {
                    $availability ='{"op":"|","c":[{"type":"cohort","id":'+$cohort->id+'}],"show":true}';
                    $sql = "update mdl_course_sections set availability=? where id=?";
                    $DB->execute($sql,array("availability"=>$availability,"id"=>$section->id));
                    rebuild_course_cache($courseid, true);
                }
            } 
        }
        $transaction->allow_commit();

        return ["status"=>"success:".$cohort->id];
    }
    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.9
     */
    public static function create_product_returns() {
        return new external_single_structure(
            array(
            'status' => new external_value(PARAM_TEXT, 'return')
            )
        );
    }
}