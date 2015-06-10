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
$where = _site_stats;
$dir = "stats";
define('_Stats', true);

if(file_exists(basePath."/stats/case_".$action.".php"))
    require_once(basePath."/stats/case_".$action.".php");

$index = show($dir."/stats", array("stats" => $stats));

## INDEX OUTPUT ##
$title = $pagetitle." - ".$where;
page($index, $title, $where);