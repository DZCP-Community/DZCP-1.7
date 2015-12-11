<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('INSTALLER')) exit();

if(isset($_COOKIE['agb']) && $_COOKIE['agb'] == true)
    $index = show("installtype"); //Auswahl: Update oder Neuinstallation
else if(isset($_POST['agb_checkbox'])) {
    $index = show("installtype"); //Auswahl: Update oder Neuinstallation
    setcookie('agb',true);
} else
    $index = show("/msg/agb_error"); //AGB nicht akzeptiert!