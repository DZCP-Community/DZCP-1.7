<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Away')) {
    $where = $where.' - '._info;
    if(!$chkMe || $chkMe < 2) {
        $index = error(_error_wrong_permissions, 1);
    } else {
        $get = $sql->fetch("SELECT * FROM `{prefix_away}` WHERE `id` = ?;",array(intval($_GET['id'])));
        if($get['start'] > time()) 
            $status = _away_status_new;

        if($get['start'] <= time() && $get['end'] >= time()) 
            $status = _away_status_now;

        if($get['end'] < time()) 
            $status = _away_status_done;

        $edit = empty($get['lastedit']) ? "&nbsp;" : bbcode(re($get['lastedit']));
        $index = show($dir."/info", array("nick" => autor($get['userid']),
                                          "von" => date("d.m.Y",$get['start']),
                                          "bis" => date("d.m.Y",$get['end']),
                                          "text" => bbcode(re($get['reason'])),
                                          "titel" => re($get['titel']),
                                          "edit" => $edit,
                                          "status" => $status,
                                          "addnew" => date("d.m.Y",$get['date'])." "._away_on." ".date("H:i",$get['date'])._uhr));
    }
}