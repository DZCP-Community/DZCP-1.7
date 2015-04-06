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
$where = _site_online;
$dir = "online";

## SECTIONS ##
if($chkMe){
    $sql->update("UPDATE `{prefix_users}` SET `time` = ".time().", `whereami` = ? WHERE `id` = ?;",array(up($where),$userid));
}

//Users
$qry = $sql->select("SELECT `id`,`ip`,`nick`,`whereami` FROM `{prefix_users}` "
                  . "WHERE (time+?) > ".time()." AND `online` = 1 ".orderby_sql(array("whereami","ip"), 'ORDER BY nick').";",array($useronline));

if($sql->rowCount()) {
    foreach($qry as $get) {
        if (!preg_match("#autor_#is", $get['whereami'])) {
            $whereami = re($get['whereami']);
        } else {
            $whereami = preg_replace_callback("#autor_(.*?)$#", create_function('$id', 'return autor("$id[1]");'), $get['whereami']);
        }

        $online_ip = '';
        if($chkMe == 4) {
            $online_ip = $get['ip'];
            $DNS = $sql->selectSingle("SELECT `dns` FROM `{prefix_iptodns}` WHERE `ip` = ?;",array($online_ip),'dns');
            $online_host = ($gethostbyaddr=$DNS);
            $online_ip = ' * '.($get['ip'] == $gethostbyaddr ? $online_ip : $online_ip.' ('.$online_host.')');
        }

        $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
        $show .= show($dir."/online_show", array("nick" => autor($get['id']).$online_ip,
                                                 "whereami" => $whereami,
                                                 "class" => $class));
    }
}

//Gast
$qry = $sql->select("SELECT * FROM `{prefix_counter_whoison}` "
                  . "WHERE (online+?) > ".time()." AND `login` = 0 ".orderby_sql(array("whereami","ip"), 'ORDER BY whereami').";",array($useronline));

if($sql->rowCount()) {
    foreach($qry as $get) {
        if (!preg_match("#autor_#is", $get['whereami'])) {
            $whereami = re($get['whereami']);
        } else {
            $whereami = preg_replace_callback("#autor_(.*?)$#", create_function('$id', 'return autor("$id[1]");'), $get['whereami']);
        }

        if($chkMe == 4) {
            $online_ip = $get['ip'];
            $DNS = $sql->selectSingle("SELECT `dns` FROM `{prefix_iptodns}` WHERE `ip` = ?;",array($online_ip),'dns');
            $online_host = ($gethostbyaddr=$DNS);
        } else {
            $online_ip = preg_replace("#^(.*)\.(.*)#","$1",$get['ip']);
            $DNS = $sql->selectSingle("SELECT `dns` FROM `{prefix_iptodns}` WHERE `ip` = ?;",array($get['ip']),'dns');
            $online_host = preg_replace("#^(.*?)\.(.*)#","$2",($gethostbyaddr=$DNS));
        }

        $online_ip = ($get['ip'] == $gethostbyaddr ? $online_ip.'.XX' : $online_ip.'.XX (*.'.$online_host.')');
        $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
        $show .= show($dir."/online_show", array("nick" => $online_ip,
                                                 "whereami" => $whereami,
                                                 "class" => $class));
    }
}

$index = show($dir."/online", array("show" => $show,
                                    "head" => _online_head,
                                    "user" => _status_user.'/'._server_ip,
                                    "order_user" => orderby('ip'),
                                    "order_where" => orderby('whereami'),
                                    "where" => _online_whereami));

## INDEX OUTPUT ##
$title = $pagetitle." - ".$where;
page($index, $title, $where);