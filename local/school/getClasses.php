<?php 
require_once("../../config.php");
global $DB;

// Get the parameter
$schoolid = optional_param('schoolid',  0,  PARAM_INT);
// If departmentid exists
if($schoolid) {
    $classes = $DB->get_records('class',array('schoolid'=>$schoolid));
    // echo your results, loop the array of objects and echo each one
    
    foreach ($classes as $class) {
        echo "<option value=".$class->id.">" . $class->name . "</option>";  
    }
} 
?>
