<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('INSTALLER')) exit();
setcookie('agb',false);

if(!is_php('5.4.0'))
    $index = writemsg(php_version_error,true);
else
    $index = show("welcome",array("lizenz" => htmlentities(file_get_contents(basePath.'/_installer/system/lizenz.txt'), ENT_COMPAT, 'iso-8859-1'))); //Willkommen & AGB