<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_SList')) exit();

$serverlist = '';
$qry = $sql->select("SELECT `ip`,`port`,`clanname`,`clanurl`,`pwd`,`checked`,`slots` "
        . "FROM `{prefix_serverliste}` WHERE `checked` = 1 "
        .orderby_sql(array("clanname","slots","ip"), 'ORDER BY `id` DESC').";");
if($sql->rowCount()) {
    foreach($qry as $get) {
        $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
        $serverlist .= show($dir."/serverliste_show", array("clanurl" => re($get['clanurl']),
                                                            "slots" => $get['slots'],
                                                            "class" => $class,
                                                            "serverip" => re($get['ip']),
                                                            "serverport" => $get['port'],
                                                            "clanname" => re($get['clanname']),
                                                            "serverpwd" => re($get['pwd'])));
    }
} else
    $serverlist = show(_no_entrys_yet, array("colspan" => "4"));

$index = show($dir."/serverliste", array("serverlist" => $serverlist,
                                         "order_clan" => orderby('clanname'),
                                         "order_ip" => orderby('ip'),
                                         "order_slots" => orderby('slots')));