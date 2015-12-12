<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Squads')) exit();

$get = $sql->fetch("SELECT `beschreibung`,`id`,`name` FROM `{prefix_squads}` WHERE `id` = ?;",array(intval($_GET['id'])));
$qrym = $sql->select("SELECT s1.`user`,s1.`squad`,s2.`id`,s2.`nick`,s2.`icq`,s2.`email`,"
                   . "s2.`hlswid`,s2.`rlname`,s2.`steamid`,s2.`level`,s2.`bday`,s2.`hp`,s3.`posi`,s4.`pid` "
                   . "FROM `{prefix_squaduser}` AS `s1` "
                   . "LEFT JOIN `{prefix_users}` AS `s2` "
                   . "ON s2.`id` = s1.`user` "
                   . "LEFT JOIN `{prefix_userposis}` AS `s3` "
                   . "ON s3.`squad` = s1.`squad` AND s3.`user` = s1.`user` "
                   . "LEFT JOIN `{prefix_positions}` AS `s4` "
                   . "ON s4.`id` = s3.`posi` "
                   . "WHERE s1.`squad` = ? "
                   . "ORDER BY s4.`pid`, s2.`nick`;",array(intval($_GET['id'])));

$member = "";
$t = 1; $c = 1;
foreach($qrym as $getm) {
    if(!$getm['icq']) {
        $icq = "-";
        $icqnr = "&nbsp;";
    } else {
        $icq = show(_icqstatus, array("uin" => $getm['icq']));
        $icqnr = $getm['icq'];
    }

    $steam = (!empty($getm['steamid']) && steam_enable ? '<div id="infoSteam_'.md5(stringParser::decode($getm['steamid'])).'">'
            . '<div style="width:100%"><img src="../inc/images/ajax-loader-mini.gif" alt="" /></div>'
            . '<script language="javascript" type="text/javascript">DZCP.initDynLoader("infoSteam_'.md5(stringParser::decode($getm['steamid'])).'","steam","&steamid='.
            stringParser::decode($getm['steamid']).'",true);</script></div>' : '-');
    
    $class = ($color % 2) ? "contentMainFirst" : "contentMainSecond"; $color++;
    $nick = autor($getm['user'],'','','','','&amp;sq='.$getm['squad']);

    if(!empty($getm['rlname'])) {
        $real = explode(" ", stringParser::decode($getm['rlname']));
        
        if(!isset($real[1]))
            $real[1] = "";
        
        $nick = '<b>'.$real[0].' &#x93;</b> '.$nick.' <b>&#x94; '.$real[1].'</b>';
    }

    $member .= show($dir."/squads_member", array("icqs" => $icq,
                                                 "icq" => $icqnr,
                                                 "emails" => CryptMailto(stringParser::decode($getm['email'])),
                                                 "id" => $getm['user'],
                                                 "steam" => $steam,
                                                 "class" => $class,
                                                 "nick" => $nick,
                                                 "onoff" => onlinecheck($getm['id']),
                                                 "posi" => getrank($getm['id'],$getm['squad']),
                                                 "pic" => userpic($getm['id'],60,80)));
}

$squad = stringParser::decode($get['name']); $style = '';
foreach($picformat AS $end) {
    if(file_exists(basePath.'/inc/images/squads/'.intval($get['id']).'.'.$end)) {
        $style = 'padding:0;';
        $squad = '<img src="../inc/images/squads/'.intval($get['id']).'.'.$end.'" alt="'.stringParser::decode($get['name']).'" />';
        break;
    }
}

$where = $where." - ".stringParser::decode($get['name']);
$index = show($dir."/squads_full", array("member" => (empty($member) ? _member_squad_no_entrys : $member),
                                         "desc" => empty($get['beschreibung']) ? '' : '<tr><td class="contentMainSecond">'.bbcode::parse_html(stringParser::decode($get['beschreibung'])).'</td></tr>',
                                         "squad" => $squad,
                                         "style" => $style,
                                         "id"   => intval($_GET['id'])));