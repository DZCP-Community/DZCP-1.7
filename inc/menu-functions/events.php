<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: Events
 */

function events() {
    global $sql;
    
    $qry = $sql->select("SELECT `id`,`datum`,`title`,`event` "
                      . "FROM `{prefix_events}` "
                      . "WHERE `datum` > ? "
                      . "ORDER BY `datum` LIMIT ".settings::get('m_events').";",array(time()));
    $eventbox = '';
    if($sql->rowCount()) {
        foreach($qry as $get) {
            $info = 'onmouseover="DZCP.showInfo(\''.jsconvert(re($get['title'])).'\', \''._kalender_uhrzeit.';'.
                    _datum.'\', \''.date("H:i", $get['datum'])._uhr.';'.
                    date("d.m.Y", $get['datum']).'\')" onmouseout="DZCP.hideInfo()"';
            
            $events = show(_next_event_link, array("datum" => date("d.m.",$get['datum']),
                                                   "timestamp" => $get['datum'],
                                                   "event" => re($get['title']),
                                                   "info" => $info));

            $eventbox .= show("menu/event", array("events" => $events, "info" => $info));
        }
    }

    return empty($eventbox) ? '<center style="margin:2px 0">'._no_events.'</center>' : '<table class="navContent" cellspacing="0">'.$eventbox.'</table>';;
}
