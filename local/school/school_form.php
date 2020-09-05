<?php

defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir . '/formslib.php';
require_once $CFG->libdir .'/hbonlib/lib.php';

class school_form extends moodleform {

    function definition() {
        global $PAGE, $DB;
        $PAGE->requires->jquery();
        $PAGE->requires->css(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css'));
        $PAGE->requires->css(new moodle_url('/local/school/css/custom.css'));
        $PAGE->requires->js(new moodle_url('/local/school/js/school.js?a=1'));
        $mform = $this->_form;
        $id = $this->_customdata['schoolid'];
        $districtid = $this->_customdata['districtid'];
        //$mform->addElement('header', 'settingsheader', get_string('search', 'admin'));
        $attributes = array('id' => 'id_province');
        $provinces = getProvinces();
        foreach ($provinces as $province) {
            $key = $province->provinceid;
            $value = $province->name;
            $selectProvince[$key] = $value;
        }
        $districts = array();
        $selectDistrict = array();
        if (isset($districtid)) {
            $district = $DB->get_record("district", array('districtid' => $districtid));
            if ($district) {
                $provinceid = $district->provinceid;
                $districts = getDistricts($provinceid);
            }
        }
        if (empty($districts)) {
            $districts = getDistricts('01TTT');
        }

        foreach ($districts as $district) {
            $key = $district->districtid;
            $value = $district->name;
            $selectDistrict[$key] = $value;
        }
        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);
        $mform->setConstant('id', $id);
        $mform->addElement('hidden', 'approve', null);
        $mform->setType('approve', PARAM_INT);
        $mform->addElement('select', 'province', get_string('province', 'local_school'), $selectProvince, $attributes);
        $mform->addElement('select', 'district', get_string('district', 'local_school'), $selectDistrict);
        $mform->addElement('text', 'name', get_string('name', 'local_school'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addElement("text","desc","code suggest",array("id"=>"autogen","disabled"=>true));
        $mform->setType('desc', PARAM_RAW);
        $mform->addElement('text', 'code', get_string('code', 'local_school'));
        $mform->setType('code', PARAM_TEXT);
        $mform->addElement('text', 'cohort_code', get_string('cohort_code', 'local_school'));
        $mform->setType('cohort_code', PARAM_TEXT);
        $mform->addElement('text', 'group_code', get_string('group_code', 'local_school'));
        $mform->setType('group_code', PARAM_TEXT);
        if (isset($districtid)) {
            $mform->setDefault('district', $districtid);
        }
        if (isset($provinceid)) {
            $mform->setDefault('province', $provinceid);
        }
        $mform->setType('query', PARAM_RAW);
        $mform->setDefault('query', optional_param('query', '', PARAM_RAW));
        $this->add_action_buttons();
        $script = '
            
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"></script>
    <script>
        $("#id_province").selectize({
            sortField: "id"
        });
        $("#id_name").change(function(){
            var name=$("#id_name").val();
            var province = $("#id_province").find("option:selected").text();
            var district = $("#id_district").find("option:selected").text();
            name=getAcronym(name)+getDistrictAcronym(district)+getProvinceAcronym(province);
            $("#autogen").val(name);
        });
    </script>';
        $mform->addElement('html', $script);
    }

}
