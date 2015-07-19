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
$where = _site_rankings;
$dir = "rankings";

## SECTIONS ##
$qry = $sql->select("SELECT s1.`id`,s1.`lastranking`,s1.`rank`,s1.`squad`,s1.`league`,s1.`url`,s2.`name` "
                  . "FROM `{prefix_rankings}` AS `s1` "
                  . "LEFT JOIN `{prefix_squads}` AS `s2` "
                  . "ON s1.`squad` = s2.`id`".orderby_sql(array("rank","league"), orderby_sql(array("name"), ''
                 . 'ORDER BY s1.`postdate DESC`', 's2'), 's1').";");

if($sql->rowCount()) {
    foreach($qry as $get) {
        $squad = '<a href="../squads/?showsquad='.$get['squad'].'">'.re($get['name']).'</a>';
        $league = '<a href="'.$get['url'].'" target="_blank">'.$get['league'].'</a>';
        $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
        $show .= show($dir."/rankings_show", array("class" => $class,
                                                   "squad" => $squad,
                                                   "league" => $league,
                                                   "old" => $get['lastranking'],
                                                   "place" => $get['rank']));
    }
} else
    $show = show(_no_entrys_yet, array("colspan" => "5"));

$index = show($dir."/rankings", array("show" => $show,
                                      "order_squad" => orderby('name'),
                                      "order_place" => orderby('rank'),
                                      "order_league" => orderby('league')));

## INDEX OUTPUT ##
$title = $pagetitle." - ".$where;
page($index, $title, $where);