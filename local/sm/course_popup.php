<?PHP

require_once(dirname(dirname(__DIR__)) . '/config.php');
global $USER,$CFG,$DB;
$phone = optional_param('phone', "", PARAM_TEXT);
if(!isset($SESSION->wantsurl)){
    $SESSION->wantsurl = optional_param('wanturl', "", PARAM_TEXT);
}
if($USER->id>2){
    //not admin or guest
    $sql = "select u.id,u.username,ud.data from mdl_user u join mdl_user_info_data ud on ud.userid=u.id
    join mdl_user_info_field uf on uf.id=ud.fieldid where u.id=? and uf.shortname='phone' and ud.data is not null and ud.data !=''";
    $check = $DB->get_record_sql($sql,array("id"=>$USER->id));
    if(!$check){
        $sql = "insert into mdl_user_info_data (userid,fieldid,data) values(?,(select id from mdl_user_info_field where shortname='phone'),?)";
        $DB->execute($sql,array("userid"=>$USER->id,"data"=>$phone));
        echo "done";
    }
} 

if($USER->username=="guest"){    
    $time = time();
    $sql = "insert into mdl_hbon_collect_data (phone,created_at) values(?,$time)";
    $DB->execute($sql,array("data"=>$phone));
    $_SESSION["registed"]=true;
}
redirect($SESSION->wantsurl);

