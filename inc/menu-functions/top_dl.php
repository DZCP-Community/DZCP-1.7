<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: Top Downloads
 */

function top_dl() {
    global $sql;
    
    $qry = $sql->select("SELECT `id`,`kat`,`download`,`date`,`hits` "
                      . "FROM `{prefix_downloads}`".(permission('dlintern') ? "" : " WHERE `intern` = 0")." "
                      . "ORDER BY `hits` ".(!settings::get('m_topdl') ? "DESC LIMIT ".settings::get('m_topdl').";" : ";"));
    $top_dl = '';
    if($sql->rowCount()) {
        foreach($qry as $get) {
            $info = '';
            if(settings::get('allowhover') == 1) {
                $getkat = $sql->fetch("SELECT `name` FROM `{prefix_download_kat}` WHERE `id` = ?;",array($get['kat']));
                $info = 'onmouseover="DZCP.showInfo(\''.jsconvert(re($get['download'])).'\', \''._datum.';'._dl_dlkat.';'._hits.'\', \''.date("d.m.Y H:i", $get['date'])._uhr.';'.jsconvert(re($getkat['name'])).';'.$get['hits'].'\')" onmouseout="DZCP.hideInfo()"';
            }

            $top_dl .= show("menu/top_dl", array("id" => $get['id'],
                                                 "titel" => cut(re($get['download']),settings::get('l_topdl')),
                                                 "info" => $info,
                                                 "hits" => $get['hits']));
        }
    }

    return empty($top_dl) ? '<center style="margin:2px 0">'._no_entrys.'</center>' : '<table class="navContent" cellspacing="0">'.$top_dl.'</table>';
}