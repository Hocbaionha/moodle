<?PHP
require(__DIR__ . '/../../config.php');

require_once $CFG->libdir .'/hbonlib/string_util.php';
require_once $CFG->libdir .'/hbonlib/lib.php';

//$arr=array(1,2,3,4,"",6,7,8,9);
//$brr=array(1,2,3,4,5,6,7,8,9);
//
//if (ob_get_level() == 0)
//    ob_start();
//foreach($arr as $a){
//    echo $a;
//    $i=0;
//    foreach($brr as $b){
//        if($i>5) break;
//        if($a=="") break;
//        echo $b;
//        doFlush();
//        sleep(1);
//        $i++;
//    }
//    
//    echo "<br/>";
//}
//----------------------------------------------------
//$context = context_system::instance();
//$PAGE->set_context($context);
//$url = new moodle_url('/local/school/test.php');
//$PAGE->set_url($url);
//$PAGE->set_heading("test");
//$PAGE->requires->jquery();
//$PAGE->requires->css(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css'));
//$PAGE->requires->css(new moodle_url('/local/school/css/custom.css'));
////$length = 10000;
////$select = array();
////for ($i = 0; $i < $length; $i++) {
////    $select[$i] = rand_string(8);
////}
//echo $OUTPUT->header();
////echo count($select);
////echo html_writer::select($select, "test");
////$script = '<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"></script>
////            <script>
////                $("#menutest").selectize({
////                sortField: "id"
////                });
////            </script>';
////echo $script;
//echo '<p  translate="no">';
//$raw = "Trần Thị Hồng Giang";
//echo $raw."<br/>";
//$html = html_entity_decode($raw, ENT_COMPAT, 'UTF-8');
//echo non_unicode($html)."<br/>";
//echo $html."<br/>";
//echo 'a&#x300;  o&#x300;
//&#xE0; &#xF2; 
//a o 
//u&#x309; &#x1EE7;
//$str = preg_replace("/(&#xE0;|&#xE1;|&#x1EA1;|&#x1EA3;|&#xE3;|&#xE2;|&#x1EA7;|&#x1EA5;|&#x1EAD;|&#x1EA9;|&#x1EAB;|&#x103;|&#x1EB1;|&#x1EAF;|&#x1EB7;|&#x1EB3;|&#x1EB5;|a&#x300;|a&#x309;|a&#x301;|a&#x323;)/", "a", $str);
//    $str = preg_replace("/(&#xE8;|&#xE9;|&#x1EB9;|&#x1EBB;|&#x1EBD;|&#xEA;|&#x1EC1;|&#x1EBF;|&#x1EC7;|&#x1EC3;|&#x1EC5;|e&#x303;|e&#x301;|e&#x323;|e&#x309;)/", "e", $str);
//    $str = preg_replace("/(&#xEC;|&#xED;|&#x1ECB;|&#x1EC9;|&#x129;|i&#x323;|i&#x301;)/", "i", $str);
//    $str = preg_replace("/(&#xF2;|&#xF3;|&#x1ECD;|&#x1ECF;|&#xF5;|&#xF4;|&#x1ED3;|&#x1ED1;|&#x1ED9;|&#x1ED5;|&#x1ED7;|&#x1A1;|&#x1EDD;|&#x1EDB;|&#x1EE3;|&#x1EDF;|&#x1EE1;|o&#x301;|o&#x303;|o&#x323;)/", "o", $str);
//    $str = preg_replace("/(&#xF9;|&#xFA;|&#x1EE5;|&#x1EE7;|&#x169;|&#x1B0;|&#x1EEB;|&#x1EE9;|&#x1EF1;|&#x1EED;|&#x1EEF;|u&#x303;|u&#x301;|u&#x300;)/", "u", $str);
//    $str = preg_replace("/(&#x1EF3;|&#xFD;|&#x1EF5;|&#x1EF7;|&#x1EF9;|y&#x300;)/", "y", $str);
//    $str = preg_replace("/(&#x111;)/", "d", $str);
//    $str = preg_replace("/(&#xC0;|&#xC1;|&#x1EA0;|&#x1EA2;|&#xC3;|&#xC2;|&#x1EA6;|&#x1EA4;|&#x1EAC;|&#x1EA8;|&#x1EAA;|&#x102;|&#x1EB0;|&#x1EAE;|&#x1EB6;|&#x1EB2;|&#x1EB4;)/", "A", $str);
//    $str = preg_replace("/(&#xC8;|&#xC9;|&#x1EB8;|&#x1EBA;|&#x1EBC;|&#xCA;|&#x1EC0;|&#x1EBE;|&#x1EC6;|&#x1EC2;|&#x1EC4;)/", "E", $str);
//    $str = preg_replace("/(&#xCC;|&#xCD;|&#x1ECA;|&#x1EC8;|&#x128;)/", "I", $str);
//    $str = preg_replace("/(&#xD2;|&#xD3;|&#x1ECC;|&#x1ECE;|&#xD5;|&#xD4;|&#x1ED2;|&#x1ED0;|&#x1ED8;|&#x1ED4;|&#x1ED6;|&#x1A0;|&#x1EDC;|&#x1EDA;|&#x1EE2;|&#x1EDE;|&#x1EE0;)/", "O", $str);
//    $str = preg_replace("/(&#xD9;|&#xDA;|&#x1EE4;|&#x1EE6;|&#x168;|&#x1AF;|&#x1EEA;|&#x1EE8;|&#x1EF0;|&#x1EEC;|&#x1EEE;)/", "U", $str);
//    $str = preg_replace("/(&#x1EF2;|&#xDD;|&#x1EF4;|&#x1EF6;|&#x1EF8;)/", "Y", $str);
//    $str = preg_replace("/(&#x110;)/", "D", $str);';
//
//echo "</p>";
//echo $OUTPUT->footer();
//
//function mb_htmlentities($string)
//{
//    $string = htmlentities($string, ENT_COMPAT, mb_internal_encoding());
//    return $string;
//}
//---------------------------------------------------
?>
<script>
// Opera 8.0+
var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;

// Firefox 1.0+
var isFirefox = typeof InstallTrigger !== 'undefined';

// Safari 3.0+ "[object HTMLElementConstructor]" 
var isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || (typeof safari !== 'undefined' && safari.pushNotification));

// Internet Explorer 6-11
var isIE = /*@cc_on!@*/false || !!document.documentMode;

// Edge 20+
var isEdge = !isIE && !!window.StyleMedia;

// Chrome 1 - 79
var isChrome = !!window.chrome && (!!window.chrome.webstore || !!window.chrome.runtime);

// Edge (based on chromium) detection
var isEdgeChromium = isChrome && (navigator.userAgent.indexOf("Edg") != -1);

// Blink engine detection
var isBlink = (isChrome || isOpera) && !!window.CSS;


var output = 'Detecting browsers by ducktyping:<hr>';
output += 'isFirefox: ' + isFirefox + '<br>';
output += 'isChrome: ' + isChrome + '<br>';
output += 'isSafari: ' + isSafari + '<br>';
output += 'isOpera: ' + isOpera + '<br>';
output += 'isIE: ' + isIE + '<br>';
output += 'isEdge: ' + isEdge + '<br>';
output += 'isEdgeChromium: ' + isEdgeChromium + '<br>';
output += 'isBlink: ' + isBlink + '<br>';
console.log(output);
</script>