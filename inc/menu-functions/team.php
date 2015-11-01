<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: Teamausgabe
 */

function team($tID = '') {
    global $sql;

    if(!empty($tID)) {
        $where = "WHERE `id` = ? AND `navi` = 1";
        $params = array(intval($tID));
    } else {             
        $where = "WHERE `navi` = 1 ORDER BY RAND()";
        $params = array();
    }

    $get = $sql->fetch("SELECT `id`,`name` FROM `{prefix_squads}` ".$where.";",$params);

    //Members
    $qrym = $sql->select("SELECT s1.`squad`,s2.`id`,s2.`level`,s2.`nick`,s2.`status`,s2.`rlname`,s2.`bday`,s4.`position` "
            . "FROM `{prefix_squaduser}` AS `s1` "
            . "LEFT JOIN `{prefix_users}` AS `s2` ON s2.`id` = s1.`user` "
            . "LEFT JOIN `{prefix_userposis}` AS `s3` ON s3.`squad` = s1.`squad` AND s3.`user` = s1.`user` "
            . "LEFT JOIN `{prefix_positions}` AS `s4` ON s4.`id` = s3.`posi` "
            . "WHERE s1.`squad` = ? AND s2.`level` != 0 "
            . "ORDER BY s4.`pid`;",array($get['id']));

    $i=1; $cnt=0; $member = '';
    foreach($qrym as $getm) {
        $tr1 = ''; $tr2 = '';
        if($i == 0 || $i == 1) $tr1 = "<tr>";
        if($i == settings::get('teamrow')) {
            $tr2 = "</tr>";
            $i = 0;
        }

        $status = ($getm['status'] == 1 || $getm['level'] == 1) ? "aktiv" : "inaktiv";
        $info = 'onmouseover="DZCP.showInfo(\''.fabo_autor($getm['id']).'\', \''._posi.';'._status.';'._age.'\', \''.
                getrank($getm['id'],$get['id']).';'.$status.';'.getAge($getm['bday']).'\', \''.
                hoveruserpic($getm['id']).'\')" onmouseout="DZCP.hideInfo()"';
        
        $member .= show("menu/team_show", array("pic" => userpic($getm['id'],40,50),
                                                "tr1" => $tr1,
                                                "tr2" => $tr2,
                                                "squad" => $get['id'],
                                                "info" => $info,
                                                "id" => $getm['id'],
                                                "width" => round(100/settings::get('teamrow'),0)));
        $i++;
        $cnt++;
    }

    $end = '';
    if(is_float($cnt/settings::get('teamrow'))) {
        for($e=$i;$e<=settings::get('teamrow');$e++) {
            $end .= '<td></td>';
        }

        $end = $end."</tr>";
    }

    // Next / last ID
    if(!$sql->rows("SELECT `id` FROM `{prefix_squads}` WHERE `navi` = 1 AND `id` > ? ORDER BY `id` ASC LIMIT 1;",array($get['id'])))
        $next = $sql->fetch("SELECT `id` FROM `{prefix_squads}` WHERE `navi` = 1 ORDER BY `id` ASC LIMIT 1;");

    if(!$sql->rows("SELECT `id` FROM `{prefix_squads}` WHERE `navi` = 1 AND `id` < ? ORDER BY `id` DESC LIMIT 1;",array($get['id'])))
        $last = $sql->fetch("SELECT `id` FROM `{prefix_squads}` WHERE `navi` = 1 ORDER BY `id` DESC LIMIT 1;");

    //Output
    $all = cnt("{prefix_squads}", "WHERE `navi` = 1");
    $team = show("menu/team", array("row" => settings::get('teamrow'),
                                    "team" => cut(re($get['name']),settings::get('l_team')),
                                    "id" => $get['id'],
                                    "next" => $next['id'],
                                    "last" => $last['id'],
                                    "br1" => ($all <= 1 ? '<!--' : ''),
                                    "br2" => ($all <= 1 ? '-->' : ''),
                                    "member" => $member,
                                    "end" => $end));

    return '<div id="navTeam">'.$team.'</div>';
}