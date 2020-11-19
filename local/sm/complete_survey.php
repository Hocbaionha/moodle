<?PHP

require_once(dirname(dirname(__DIR__)) . '/config.php');
global $USER, $CFG, $DB;
$object = optional_param('object', 0, PARAM_INT);
$class = optional_param('class', 0, PARAM_INT);
$subject = optional_param('subject', 0, PARAM_INT);
$level = optional_param('level', 0, PARAM_INT);

if ($USER->id > 2) {
  if($object == 0 or $class ==0 or $subject == 0 or $level == 0){
      result(2, "Update false");
  }else{
      $check = $DB->get_record('hbon_collect_info', array('userid'=>$USER->id));
      if(isset($check)){
            $update_data = [
                "id" =>$check->id,
                "userid" =>$check->userid,
                "phone" =>$check->phone,
                "object" =>$object,
                "class" =>$class,
                "subject" =>$subject,
                "level" =>$level,
                "status_survey" =>1,
            ];
            $res = $DB->update_record('hbon_collect_info', (object)$update_data);
            if($res){
                result(1, "Update success");
            }else{
                result(2, "Update false");
            }
      }else{
          result(2, "Update false");
      }
  }
}

if ($USER->username == "guest") {
    $time = time();
    $sql = "insert into mdl_hbon_collect_data (phone,created_at) values(?,$time)";
    $DB->execute($sql, array("data" => $phone));
    $_SESSION["registed"] = true;
}
//redirect($SESSION->wantsurl);

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

