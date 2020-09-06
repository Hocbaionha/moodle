<?php

function split_name($string) {
    $arr = explode(' ', $string);
    $s['first_name'] = array_pop($arr);
    $s['last_name'] = implode(" ", $arr);
    return $s;
}

function non_unicode($str) {
    
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ|à|ả|á|ạ)/", "a", $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ|ẽ|é|ẹ|ẻ|è)/", "e", $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ|ị|í)/", "i", $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ|ò|ó|õ|ọ)/", "o", $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ|ũ|ú|ù|ủ)/", "u", $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ|ỳ)/", "y", $str);
    $str = preg_replace("/(đ)/", "d", $str);
    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
    $str = preg_replace("/(Đ)/", "D", $str);
    return $str;
}

function getProvinceAcronym($str) {
    $str = str_replace("Tỉnh", "", $str);
    $str = str_replace("Thành phố", "", $str);
    $str = trim(non_unicode($str));
    if ($str == "Binh Dinh")
        return "bdi";
    if ($str == "Ha Nam")
        return "hna";
    if ($str == "Quang Nam")
        return "qna";
    if ($str == "Quang Ninh")
        return "qni";
    $ret = getAcronym($str);
    if ($ret == "br-vt")
        return "bvt";
    else
        return $ret;
}

function getDistrictAcronym($str) {
    $str = str_replace("Quận", "", $str);
    $str = str_replace("Thị xã", "", $str);
    $str = str_replace("Huyện", "", $str);
    return getAcronym($str);
}

function getAcronym($str) {
    $str = trim(non_unicode($str));
    $ret = '';
    foreach (explode(' ', $str) as $word)
        $ret .= strtolower($word[0]);
    return $ret;
}

function rand_string($length) {

    $chars = "0123456789";
    return "abcd" . substr(str_shuffle($chars), 0, $length);
}

function dd($object) {
    print_object($object);
    die;
}

function doFlush() {
    if (!headers_sent()) {
        // Disable gzip in PHP.
        ini_set('zlib.output_compression', 0);

        // Force disable compression in a header.
        // Required for flush in some cases (Apache + mod_proxy, nginx, php-fpm).
        header('Content-Encoding: none');
    }

    // Fill-up 4 kB buffer (should be enough in most cases).
    echo str_pad('', 4 * 1024);

    // Flush all buffers.
    do {
        $flushed = @ob_end_flush();
    } while ($flushed);

    @ob_flush();
    flush();
}

function startsWith($string, $startString) {
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}

function truncate($str, $width) {
    return strtok(wordwrap($str, $width, "...\n"), "\n");
}