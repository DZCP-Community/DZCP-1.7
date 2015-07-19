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
$dir = "linkus";
$where = _linkus;
define('_LinkUS', true);

if(file_exists(basePath."/linkus/case_".$action.".php"))
    require_once(basePath."/linkus/case_".$action.".php");

## INDEX OUTPUT ##
$title = $pagetitle." - ".$where;
page($index, $title, $where);