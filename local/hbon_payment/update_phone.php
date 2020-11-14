<?php

require_once('../../config.php');

global $USER,$DB;

$phone = optional_param('phone', 0, PARAM_TEXT);

//echo $phone;die();
if($phone){
    if(preg_match('/^[0-9]{10}+$/', $phone)) {
        if($USER->id){
            $sql = "UPDATE mdl_user_info_data SET `data`=? WHERE userid=? AND fieldid=(SELECT id FROM mdl_user_info_field WHERE `name`='phone')";
            $rs = $DB->execute($sql, array('data'=>$phone,"userid"=>$USER->id));
            if($rs){
                result(1, "Update success");
            }else{
                result(2, "Update false");
            }
        }
    }else{
        result(2, "Update false");
    }
}else{
    result(2, "Update false");
}
function result($code, $message){
    if($code ===1 ){
        $result = [
            "status"=>"success",
            "code"=>$code,
            "message"=> $message
        ];
        echo json_encode($result);
    }else{
        $result = [
            "status"=>"error",
            "code"=>$code,
            "message"=>$message
        ];
        echo json_encode($result);
    }
}
