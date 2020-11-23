<?PHP

require_once(dirname(dirname(__DIR__)) . '/config.php');
global $USER, $CFG, $DB;

$c = optional_param('class', "", PARAM_TEXT);
$l = optional_param('level', "", PARAM_TEXT);
echo $c."<br>";
echo $l;