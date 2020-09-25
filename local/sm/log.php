
<?PHP 
require_once(dirname(dirname(__DIR__)) . '/config.php');
$timeSpent     = optional_param('timeSpent', 0, PARAM_INT);
$timeSpent = $timeSpent/1000;
$userid=optional_param('uid', 0, PARAM_TEXT);
$bodyattributes=optional_param('bodyattributes', 0, PARAM_TEXT);
global $CFG;
$locate = $CFG->dataroot."/school/";
echo  "done";
$file = fopen($locate."log.txt","w");
echo fwrite($file,"Hello World. Testing!\n");
echo fwrite($file,$timeSpent."\n");
echo fwrite($file,$userid."\n");
echo fwrite($file,$bodyattributes."\n");
fclose($file);
