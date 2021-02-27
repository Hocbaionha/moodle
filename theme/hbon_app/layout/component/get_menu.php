<?php
//GET MENU AND RENDER

$menu = $OUTPUT->custom_menu_frontend();
//var_dump($menu);die();

$string00 = '<ul id="menu-primary-menu" class="furgan-nav main-menu">';
    $stringmobilemenu ='<ul id="menu-primary-menu" class="nav-bar">';
        $string00 = $string00 . $menu;
        $string00 = $string00 .'</ul>';
    $stringmobilemenu = $stringmobilemenu."</ul>";
//-- /GET MENU AND RENDER
