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
$where = _site_dl;
$dir = "downloads";
define('_Downloads', true);

//-> Funktion um ein Datenbankinhalt zu highlighten
function highlight($word) {
    if (substr(phpversion(), 0, 1) == 5) {
        return str_ireplace($word, '<span class="fontRed">' . $word . '</span>', $word);
    } else {
        return str_replace($word, '<span class="fontRed">' . $word . '</span>', $word);
    }
}

if(file_exists(basePath."/downloads/case_".$action.".php"))
    require_once(basePath."/downloads/case_".$action.".php");

## INDEX OUTPUT ##
$title = $pagetitle." - ".$where;
page($index, $title, $where);