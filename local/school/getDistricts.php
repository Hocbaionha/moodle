<?php 
require_once("../../config.php");
global $DB;

// Get the parameter
$provinceid = optional_param('provinceid',  0,  PARAM_TEXT);
$districtid = optional_param('districtid',  0,  PARAM_TEXT);
// If departmentid exists
if($provinceid) {
    $districts = $DB->get_records("district", array('provinceid' => $provinceid));
    foreach ($districts as $district) {
        echo "<option value=".$district->districtid.">" . $district->name . "</option>";  
    }
} else if($districtid){
    $schools = $DB->get_records("school",array("districtid"=>$districtid));
    foreach ($schools as $school) {
        echo "<option value=".$school->id.">" . $school->name . "</option>";  
    }
}
?>
