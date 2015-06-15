<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

## OUTPUT BUFFER START ##
include("../inc/buffer.php");

## INCLUDES ##
include(basePath."/inc/common.php");

## SETTINGS ##
$where = _site_member;
$dir = "squads";
define('_Squads', true);

if(!empty($_GET['showsquad'])) 
    header('Location: ?action=shows&id='.intval($_GET['showsquad']));
else if(!empty($_GET['show'])) 
    header('Location: ?action=shows&id='.intval($_GET['show']));
else {
    if(file_exists(basePath."/squads/case_".$action.".php"))
        require_once(basePath."/squads/case_".$action.".php");
}

## INDEX OUTPUT ##
$title = $pagetitle." - ".$where;
page($index, $title, $where);