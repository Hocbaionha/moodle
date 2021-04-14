<?PHP

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
require_once(dirname(dirname(__DIR__)) . '/config.php');
require_once($CFG->dirroot . "/lib/externallib.php");
global $CFG, $USER, $SESSION, $DB;

$popup_id = optional_param('popup_id', 0, PARAM_INT);
$link_live = optional_param('link_live', '', PARAM_TEXT);
$security = optional_param('security', '', PARAM_TEXT);
$type = optional_param('type', 'get', PARAM_TEXT);
if($type === 'get'){
    $now =date("Y-m-d H:i:s");
    $sql = "select * from mdl_hbon_popup_home where status=1 and public_at >= ? order by created_at DESC";
    $popup_event = $DB->get_records_sql($sql,array("public_at"=>$now));
    echo  json_encode($popup_event);
}else{
    if (isset($security) && $security ==="hocbaieditor"  && $popup_id !== 0 && filter_var($link_live, FILTER_VALIDATE_URL)) {
        $res = [
            "status"=> 200,
            "exe_code"=>0,
            "message"=>"update failed"
        ];
        $checkexist = $DB->count_records('hbon_popup_home', array('id' => $popup_id));
        if ($checkexist !== 0) {
            $now = date("Y-m-d H:i:s");
            $old = $DB->get_record('hbon_popup_home', array('id' =>  $popup_id));
            $newData = array(
                'id' => (int)$old->id,
                'title'=>$old->title,
                'image'=>$old->image,
                'link'=>$link_live,
                'status'=>$old->status,
                'created_at'=>$old->created_at,
                'public_at'=>$now,
                'expitime'=>$old->expitime,
                'is_countdown'=>$old->is_countdown,
                'to_course'=>$old->to_course,
                'replay'=>$old->replay,
            );
            $result = $DB->update_record('hbon_popup_home', (object)$newData);
            if($result == true){
                echo  json_encode([
                    "status"=> 200,
                    "exe_code"=>1,
                    "message"=>"update success"
                ]);
            }
        }else{
            echo  json_encode($res);
        }
    }
}



