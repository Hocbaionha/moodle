<?php

defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir . '/formslib.php';
require_once $CFG->libdir . '/hbonlib/string_util.php';
require_once $CFG->libdir . '/hbonlib/lib.php';

class school_search_form extends moodleform {

    function definition() {
        global $DB;
        $mform = $this->_form;
        $schoolid = $this->_customdata['schoolid'];
        $schools = $DB->get_records("school", array("approve" => 1));
        foreach ($schools as $school) {
            $key = $school->id;
            $dataSchool = getDataSchools($key);
            $value = $school->name . "  -  " . $dataSchool->district->name . " - " . $dataSchool->province->name;
            $selectSchools[$key] = $value;
        }

        $select = $mform->addElement('select', 'school', get_string('school', 'local_school'), $selectSchools, array("class" => " dataschools", "id" => "dataschools"));
        $select->setSelected($schoolid);
        $mform->setType('name', PARAM_TEXT);
        $mform->setDefault('school', $schoolid);
        $mform->addElement('hidden', 'schoolid', $schoolid);
        $mform->setType('schoolid', PARAM_INT);


        $mform->setType('query', PARAM_RAW);
        $mform->setDefault('query', optional_param('query', '', PARAM_RAW));


        $script = '<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"></script>
            <script>
                $("#dataschools").selectize({
                sortField: "id"
                });
                document.getElementById("dataschools").onchange = function () {
                    M.core_formchangechecker.reset_form_dirty_state()
                    var id = document.getElementById("dataschools").value;
                    if(isInt(id))
                    window.location = document.URL.split("?")[0] + "?schoolid="+id;
                };
                function isInt(value) {
                    var x = parseInt(value);
                    return !isNaN(value) && (x | 0) === x;
                  }
            </script>';
        $mform->addElement('html', $script);
    }

}
