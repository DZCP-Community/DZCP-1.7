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
$where = _side_membermap;
$dir = "membermap";

## SECTIONS ##
$level = settings('gmaps_who');
if(!($level == 0 || $level == 1)) {
    $level = 0;
}

$mm_qry = $sql->select('SELECT user.`id`, user.`nick`, user.`city`, user.`gmaps_koord` '
        . 'FROM `{prefix_users}` as `user` '
        . 'WHERE user.`gmaps_koord` != "" AND user.`level` > ? '
        . 'ORDER BY user.`gmaps_koord`, user.`id`;',array($level));

$mm_coords = ''; $mm_infos = "'<tr>"; $mm_markerIcon = '';$mm_lastCoord = ''; $i = 0; $mm_users = '';
$realCount = 0;$markerCount = 0;$userListPic = '';$userListName = ''; $userListRank = '';$userListCity = '';
$entrys = $sql->rowCount();

foreach($mm_qry as $mm_get) {
    if($mm_lastCoord != $mm_get['gmaps_koord']) {
        if($i > 0) {
            $mm_coords .= ',';
            $mm_infos .= "</tr>','<tr>";
        }

        $mm_infos .= '<td><b style="font-size:13px">&nbsp;'.re($mm_get['city']).'</td></tr><tr>';
        $mm_coords .= 'new google.maps.LatLng' . $mm_get['gmaps_koord'];
        $realCount++;
    } else {
        if($markerCount > 0) {
            $mm_markerIcon .= ',';
        }

        $mm_markerIcon .= ($realCount - 1) . ':true';
        $markerCount++;
    }

    $userInfos = '<b>'.rawautor($mm_get['id']).'</b><br /><b>'._position.
    ':</b> '.getrank($mm_get['id']).'<br />'.userpic($mm_get['id']);
    $mm_infos .= '<td><div id="memberMapInner">' . $userInfos . '</div></td>';
    $mm_lastCoord = $mm_get['gmaps_koord'];
    $i++;
}

$mm_qry = $sql->select('SELECT user.`id`, user.`nick`, user.`city` '
        . 'FROM `{prefix_users}` as `user` '
        . 'WHERE user.`gmaps_koord` != "" AND user.`level` > ? '
        . 'ORDER BY user.`gmaps_koord`, user.`id` LIMIT '.($page - 1)*settings('m_membermap').','.settings('m_membermap').';',array($level));
foreach($mm_qry as $mm_user_get) {
    $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
    $mm_users .= show($dir.'/membermap_users',array('id' => $mm_user_get['id'],
                                                    'userListPic' => userpic($mm_user_get['id'],40,50),
                                                    'userListName' => autor($mm_user_get['id']),
                                                    'userListRank' => getrank($mm_user_get['id']),
                                                    'userListCity' => re($mm_user_get['city']),
                                                    'class' => $class));
}

$mm_infos .= "</tr>'";
$seiten = nav($entrys,settings('m_membermap'));
$index = show($dir."/membermap", array('mm_coords' => $mm_coords,
                                       'mm_infos' => $mm_infos,
                                       'membermapusers' => $mm_users,
                                       'mm_markerIcon' => $mm_markerIcon,
                                       'nav' => $seiten));
## INDEX OUTPUT ##
$title = $pagetitle." - ".$where;
page($index, $title, $where);