<?PHP

require_once(dirname(dirname(__DIR__)) . '/config.php');
require_once(__DIR__.'/helper.php');
global $USER, $CFG, $DB,$HTTP_SERVER_VARS;
echo "expired";die();
$class = optional_param('class', "", PARAM_TEXT);
$subject = optional_param('subject', "", PARAM_TEXT);
$name = optional_param('name', "", PARAM_TEXT);
$url = optional_param('url', "", PARAM_TEXT);

$c_class = display_class();
$c_subject = display_subject();
$c_origin_url = ladipage_origin_url();
$table = 'hbon_mapping_test';
if(in_array($class, $c_class) && in_array($subject, $c_subject) && in_array($url, $c_origin_url)){
    if($url !== ""){
        $conditions = array(
            "url_input"=>array_search($url, $c_origin_url),
            "class"=>array_search($class, $c_class),
            "subject"=>array_search($subject, $c_subject)
        );
        $result  = $DB->get_record($table, $conditions,'url_output');
        if(!empty($result)){
            redirect($result->url_output);
        }
    }else{
        echo "RESTRICTED ACCESS";
    }
}else{
    echo "Not exist class or subject";
}
