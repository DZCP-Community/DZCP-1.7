<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: last Wars
 */

function l_wars() {
    global $sql;
    
    $qry = $sql->select("SELECT s1.`datum`,s1.`gegner`,s1.`id`,s1.`bericht`,s1.`xonx`,s1.`clantag`,s1.`punkte`,s1.`gpunkte`,s1.`squad_id`,s2.`icon`,s2.`name` "
                      . "FROM `{prefix_clanwars}` AS `s1` "
                      . "LEFT JOIN `{prefix_squads}` AS `s2` ON s1.`squad_id` = s2.`id` "
                      . "WHERE `datum` < ? "
                      . "ORDER BY `datum` DESC LIMIT ".settings('m_lwars').";",array(time()));

    $lwars = '';
    if($sql->rowCount()) {
        foreach($qry as $get) {
            $info = '';
            if(settings('allowhover') == 1 || settings('allowhover') == 2)
                $info = 'onmouseover="DZCP.showInfo(\''.jsconvert(re($get['name'])).' vs. '.jsconvert(re($get['gegner'])).'\', \''.
                    _played_at.';'._cw_xonx.';'._result.';'._comments_head.'\', \''.date("d.m.Y H:i", $get['datum'])._uhr.';'.
                    jsconvert(re($get['xonx'])).';'.cw_result_nopic_nocolor($get['punkte'],$get['gpunkte']).';'.
                    cnt('{prefix_cw_comments}', "WHERE `cw` = ?;","id",array($get['id'])).'\')" onmouseout="DZCP.hideInfo()"';

            $lwars .= show("menu/last_wars", array("id" => $get['id'],
                                                   "clantag" => cut(re($get['clantag']),settings('l_lwars')),
                                                   "icon" => re($get['icon']),
                                                   "info" => $info,
                                                   "result" => cw_result_pic($get['punkte'],$get['gpunkte'])));
        }
    }

    return empty($lwars) ? '<center style="margin:2px 0">'._no_entrys.'</center>' : '<table class="navContent" cellspacing="0">'.$lwars.'</table>';
}