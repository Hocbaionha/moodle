<?php

//GET MENU AND RENDER
$custommenuitems = $CFG->custommenuitems;
$main_menu = explode ("\n",$custommenuitems) ;
$string00 = '<ul id="menu-primary-menu" class="furgan-nav main-menu">';
$stringmobilemenu ='<ul id="menu-primary-menu" class="nav-bar">';
foreach ($main_menu as &$value) {
    if ($value !='') {
        $item_menu = explode('|', $value);
        if (strpos($item_menu[1], 'http') !== false){
            $href = $item_menu[1];
        }
        else {
            $href = $CFG->wwwroot.$item_menu[1];
        }
        $string_add = '<li class="menu-item menu-item-object-megamenu parent parent-megamenu item-megamenu menu-item-has-children">
                        <a class="furgan-menu-item-title" title="' . $item_menu[0] . '" href="' .$href . '">' . $item_menu[0] . '</a>
                                <span class="toggle-submenu"></span></li>';
        $string00 = $string00 . $string_add;
        $stringmobilemenu = $stringmobilemenu.'<li class="nav-item">
        <a class="nav-link" title="' . $item_menu[0] . '" href="' .$href . '">' . $item_menu[0] . '</a>
                <span class="toggle-submenu"></span></li>';
    }
}
$string00 = $string00 .'</ul>';
$stringmobilemenu = $stringmobilemenu."</ul>";
//-- /GET MENU AND RENDER