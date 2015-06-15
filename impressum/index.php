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
$dir = "impressum";
$where = _site_impressum;

## SECTIONS ##
$index = show($dir."/impressum", array("show_domain" => re(settings('i_domain')), 
                                       "show_autor" => bbcode(re(settings('i_autor')))));

## INDEX OUTPUT ##
$title = $pagetitle." - ".$where;
page($index, $title, $where);