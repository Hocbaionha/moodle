<?php


$path = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$tesmonial = $DB->get_records('classon_tesmonial', null, 'sequence');
	$string01_tesmonial ='<div class="furgan-testimonial style-02">';
	foreach ($tesmonial as &$value) {
        $string_tesmonial_inner = '<div class="testimonial-inner">'.
                                    '<p class="desc">'.$value->user_comment.'</p>'.
                                    '<div class="testimonial-info"><div class="intro"><h3 class="name">'.
                                    '<a href="#" target="_self">'. $value->user_name.'</a></h3>
                                    <div class="position">'.$value->user_title.'</div></div>
                                    <div class="thumb">
                                    <img src="'.$path.'/theme/classon/pix/tesmo/'.$value->path_to_image.'.png" 
                                    class="attachment-full size-full" alt="img" width="100" height="100">'.
                                    '</div></div></div>';
        $string01_tesmonial = $string01_tesmonial . $string_tesmonial_inner;

    }
    $string01_tesmonial = $string01_tesmonial .'</div>';
    
