<?php
/**
 * DZCP - deV!L`z ClanPortal
 * http://www.dzcp.de
 * Menu: Login
 */

function login() {
    global $chkMe;
    
    if(!$chkMe) {
        $secure = config('securelogin') ? show("menu/secure") : '';
        return show("menu/login", array("secure" => $secure));
    }

    return '';
}