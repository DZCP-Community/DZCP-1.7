<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Squads')) exit();

$qry = $sql->select("SELECT `name`,`icon`,`id`,`beschreibung` FROM `{prefix_squads}` WHERE `team_show` = 1 ORDER BY `pos`;");
if($cnt_squads = $sql->rowCount()) {
    foreach($qry as $get) {
        $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
        $squad = show(_gameicon, array("icon" => re($get['icon']))).' '.re($get['name']); $style = '';
        foreach($picformat AS $end) {
            if(file_exists(basePath.'/inc/images/squads/'.intval($get['id']).'.'.$end)) {
                $style = 'text-align:center;padding:0';
                $squad = '<img src="../inc/images/squads/'.intval($get['id']).'.'.$end.'" alt="'.re($get['name']).'" />';
                break;
            }
        }

        $show .= show($dir."/squads_show", array("id" => $get['id'],
                                                 "squad" => $squad,
                                                 "style" => $style,
                                                 "class" => $class,
                                                 "beschreibung" => bbcode(re($get['beschreibung'])),
                                                 "squadname" => re($get['name'])));
    }
}

$weare = show(_member_squad_weare, array("cm" => cnt('{prefix_squaduser}'),"cs" => $cnt_squads));
$index = show($dir."/squads", array("weare" => $weare,"show" => $show));