<?php

function display_object($object){
    $list = [
        1=>"Học sinh",
        2=>"Giáo Viên",
        3=>"Phụ Huynh"
    ];
    if($object){
        foreach ($list as $key=>$item){
            if($key ===$object){
                return $item;
            }
        }
    }else{
        return $list;
    }
}
function display_class($object){
    $list = [
        6=>"Lớp 6",
        7=>"Lớp 7",
        8=>"Lớp 8",
        9=>"Lớp 9"
    ];
    if($object){
        foreach ($list as $key=>$item){
            if($key ===$object){
                return $item;
            }
        }
    }else{
        return $list;
    }
}
function display_subject($object){
    $list = [
        1=>"Môn Toán",
        2=>"Môn Văn",
        3=>"Môn Tiếng Anh"
    ];
    if($object){
        foreach ($list as $key=>$item){
            if($key ===$object){
                return $item;
            }
        }
    }else{
        return $list;
    }
}
function display_level($object){
    $list = [
        1=>"Cơ bản",
        2=>"Nâng Cao"
    ];
    if($object){
        foreach ($list as $key=>$item){
            if($key ===$object){
                return $item;
            }
        }
    }else{
        return $list;
    }
}
