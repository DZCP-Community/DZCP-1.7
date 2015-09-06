<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: next Wars
 */

function n_wars() {
    global $sql;
    
    $qry = $sql->select("SELECT s1.`id`,s1.`datum`,s1.`clantag`,s1.`maps`,s1.`gegner`,s1.`squad_id`,s2.`icon`,s1.`xonx`,s2.`name` "
                      . "FROM `{prefix_clanwars}` AS `s1` "
                      . "LEFT JOIN `{prefix_squads}` AS `s2` ON s1.`squad_id` = s2.`id` "
                      . "WHERE s1.`datum` > ? "
                      . "ORDER BY s1.`datum` LIMIT ".settings('m_nwars').";",array(time()));
    
    $nwars = '';
    if($sql->rowCount()) {
        foreach($qry as $get) {
            if(settings('allowhover') == 1 || settings('allowhover') == 2)
                $info = 'onmouseover="DZCP.showInfo(\''.jsconvert(re($get['name'])).' vs. '.jsconvert(re($get['gegner'])).'\', \''.
                    _datum.';'._cw_xonx.';'._cw_maps.';'._comments_head.'\', \''.date("d.m.Y H:i", $get['datum'])._uhr.';'.
                    jsconvert(re($get['xonx'])).';'.jsconvert(re($get['maps'])).';'.
                    cnt("{prefix_cw_comments}","WHERE `cw` = ?","id",array($get['id'])).'\')" onmouseout="DZCP.hideInfo()"';

            $nwars .= show("menu/next_wars", array("id" => $get['id'],
                                                   "clantag" => cut(re($get['clantag']),settings('l_nwars')),
                                                   "icon" => re($get['icon']),
                                                   "info" => $info,
                                                   "datum" => date("d.m.:", $get['datum'])));
        }
    }

    return empty($nwars) ? '<center style="margin:2px 0">'._no_entrys.'</center>' : '<table class="navContent" cellspacing="0">'.$nwars.'</table>';
}