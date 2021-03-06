<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: Top Match
 */

function top_match() {
    global $sql,$picformat;
    
    $qry = $sql->select("SELECT s1.`datum`,s1.`gegner`,s1.`id`,s1.`bericht`,s1.`xonx`,s1.`clantag`,s1.`punkte`,s1.`gpunkte`,s1.`squad_id`,s2.`icon`,s2.`name` "
                      . "FROM `{prefix_clanwars}` AS `s1` "
                      . "LEFT JOIN `{prefix_squads}` AS `s2` ON s1.`squad_id` = s2.`id` "
                      . "WHERE `top` = 1 "
                      . "ORDER BY RAND();");

    $topmatch = ''; $hover = '';
    if($sql->rowCount()) {
        foreach($qry as $get) {
            $squad = '_defaultlogo.jpg'; $gegner = '_defaultlogo.jpg';
            foreach($picformat AS $end) {
                if(file_exists(basePath.'/inc/images/clanwars/'.$get['id'].'_logo.'.$end))
                    $gegner = $get['id'].'_logo.'.$end;

                if(file_exists(basePath.'/inc/images/squads/'.$get['squad_id'].'_logo.'.$end))
                    $squad = $get['squad_id'].'_logo.'.$end;
            }

            if(settings::get('allowhover') == 1 || settings::get('allowhover') == 2)
                $hover = 'onmouseover="DZCP.showInfo(\''.jsconvert(stringParser::decode($get['name'])).' vs. '.
                    jsconvert(stringParser::decode($get['gegner'])).'\', \''._played_at.';'._cw_xonx.';'._result.';'._comments_head.'\', \''.
                    date("d.m.Y H:i", $get['datum'])._uhr.';'.jsconvert(stringParser::decode($get['xonx'])).';'.
                    cw_result_nopic_nocolor($get['punkte'],$get['gpunkte']).';'.
                    cnt('{prefix_cw_comments}', "WHERE `cw` = ?","id",array($get['id'])).'\')" onmouseout="DZCP.hideInfo()"';

            $topmatch .= show("menu/top_match", array("id" => $get['id'],
                                                      "clantag" => cut(stringParser::decode($get['clantag']),settings::get('l_lwars')),
                                                      "team" => cut(stringParser::decode($get['name']),settings::get('l_lwars')),
                                                      "game" => substr(strtoupper(str_replace('.'.stringParser::decode($get['icon']), '', stringParser::decode($get['icon']))), 0, 5),
                                                      "id" => $get['id'],
                                                      "gegner" => $gegner,
                                                      "squad" => $squad,
                                                      "hover" => $hover,
                                                      "info" => ($get['datum'] > time() ? date("d.m.Y", $get['datum']) : cw_result_nopic($get['punkte'],$get['gpunkte']))));
        }
    }

    return empty($topmatch) ? '<center style="margin:3px 0">'._no_top_match.'</center>' : '<table class="navContent" cellspacing="0">'.$topmatch.'</table>';
}