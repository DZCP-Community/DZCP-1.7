<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Stats')) exit();

$get = $sql->fetch("SELECT `email`,`reg`,`nick`,`datum` FROM `{prefix_gb}` ORDER BY `datum` ASC LIMIT 1;");
$first = '-';
if($sql->rowCount()) {
    if($get['reg']) 
        $first = date("d.m.Y H:i", $get['datum'])."h "._from." ".autor($get['reg']);
    else 
        $first = date("d.m.Y H:i", $get['datum'])."h "._from." ".autor($get['reg'],'',stringParser::decode($get['nick']),stringParser::decode($get['email']));
}

$get = $sql->fetch("SELECT `email`,`reg`,`nick`,`datum` FROM `{prefix_gb}` ORDER BY `datum` DESC LIMIT 1;");
$last = '-';
if($sql->rowCount()) {
    if($get['reg']) 
        $last = date("d.m.Y H:i", $get['datum'])."h "._from." ".autor($get['reg']);
    else 
        $last = date("d.m.Y H:i", $get['datum'])."h "._from." ".autor($get['reg'],'',stringParser::decode($get['nick']),stringParser::decode($get['email']));
}

$stats = show($dir."/gb", array("nposter" => cnt("{prefix_gb}"," WHERE `reg` = 0")."/".cnt("{prefix_gb}"," WHERE `reg` != 0"),
                                "nall" => cnt("{prefix_gb}"),
                                "nfirst" => $first,
                                "nlast" => $last));