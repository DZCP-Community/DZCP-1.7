<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Kalender')) exit();

$qry = $sql->select("SELECT * FROM `{prefix_events}` WHERE DATE_FORMAT(FROM_UNIXTIME(datum), '%d.%m.%Y') = ? ORDER BY `datum`;",array(date("d.m.Y",intval($_GET['time']))));
$events = '';
foreach($qry as $get) {
    $edit = permission("editkalender") ? show("page/button_edit_url", 
            array("action" => "../admin/?admin=kalender&do=edit&id=".$get['id'], "title" => _button_title_edit)) : '';

    $events .= show($dir."/event_show", array("edit" => $edit,
                                              "show_time" => date("H:i", $get['datum'])._uhr,
                                              "show_event" => bbcode::parse_html($get['event']),
                                              "show_title" => stringParser::decode($get['title'])));
}

$head = show(_kalender_events_head, array("datum" => date("d.m.Y",$_GET['time'])));
$index = show($dir."/event", array("head" => $head, "events" => $events));