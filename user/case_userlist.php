<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    $where = _site_ulist;
    $entrys = cnt('{prefix_users}'," WHERE level != 0");
    $show_sql = isset($_GET['show']) ? $_GET['show'] : '';
    $limit_sql = ($page - 1)*settings('m_userlist').",".settings('m_userlist');
    $select_sql = "`id`,`nick`,`level`,`email`,`hp`,`steamid`,`hlswid`,`skypename`,"
                . "`xboxid`,`psnid`,`originid`,`battlenetid`,`bday`,`sex`,`icq`,`status`,"
                . "`position`,`regdatum`";
    
    switch (isset($_GET['show']) ? $_GET['show'] : '') {
        case 'search':
            $qry = $sql->select("SELECT ".$select_sql." "
                              . "FROM `{prefix_users}` "
                              . "WHERE `nick` LIKE ? AND `level` != 0 "
                              . orderby_sql(array("nick","bday"), 'ORDER BY `nick`')." "
                              . "LIMIT ".$limit_sql.";",
                    array('%'.up($_GET['search']).'%'));
        break;
        case 'newreg':
            $qry = $sql->select("SELECT ".$select_sql." "
                              . "FROM `{prefix_users}` "
                              . "WHERE regdatum > ? AND `level` != 0 "
                              . orderby_sql(array("nick","bday"), 'ORDER BY `regdatum` DESC,`nick`')." "
                              . "LIMIT ".$limit_sql.";",
                    array($_SESSION['lastvisit']));
        break;
        case 'lastlogin':
            $qry = $sql->select("SELECT ".$select_sql." "
                              . "FROM `{prefix_users}` "
                              . "WHERE `level` != 0 "
                              . orderby_sql(array("nick","bday"), 'ORDER BY `time` DESC,`nick`')." "
                              . "LIMIT ".$limit_sql.";");
        break;
        case 'lastreg':
            $qry = $sql->select("SELECT ".$select_sql." "
                              . "FROM `{prefix_users}` "
                              . "WHERE `level` != 0 "
                              . orderby_sql(array("nick","bday"), 'ORDER BY `regdatum` DESC,`nick`')." "
                              . "LIMIT ".$limit_sql.";");
        break;
        case 'online':
            $qry = $sql->select("SELECT ".$select_sql." "
                              . "FROM `{prefix_users}` "
                              . "WHERE `level` != 0 "
                              . orderby_sql(array("nick","bday"), 'ORDER BY `time` DESC,`nick`')." "
                              . "LIMIT ".$limit_sql.";");
        break;
        case 'country':
            $qry = $sql->select("SELECT ".$select_sql." "
                              . "FROM `{prefix_users}` "
                              . "WHERE `level` != 0 "
                              . orderby_sql(array("nick","bday"), 'ORDER BY `country`,`nick`')." "
                              . "LIMIT ".$limit_sql.";");
        break;
        case 'sex':
            $qry = $sql->select("SELECT ".$select_sql." "
                              . "FROM `{prefix_users}` "
                              . "WHERE `level` != 0 "
                              . orderby_sql(array("nick","bday"), 'ORDER BY `sex` DESC')." "
                              . "LIMIT ".$limit_sql.";");
        break;
        case 'banned':
            $qry = $sql->select("SELECT ".$select_sql." "
                              . "FROM `{prefix_users}` "
                              . "WHERE `level` = 0 "
                              . orderby_sql(array("nick","bday"), 'ORDER BY `nick`')." "
                              . "LIMIT ".$limit_sql.";");
        break;
        default:
            $qry = $sql->select("SELECT ".$select_sql." "
                              . "FROM `{prefix_users}` "
                              . "WHERE `level` != 0 "
                              . orderby_sql(array("nick","bday"), 'ORDER BY `level` DESC,`nick`')." "
                              . "LIMIT ".$limit_sql.";");
        break;
    }

    $userliste = '';
    foreach($qry as $get) {
        $hlsw = empty($get['hlswid']) ? "-" : show(_hlswicon, array("id" => re($get['hlswid']), "img" => "1", "css" => ""));
        $xboxu = empty($get['xboxid']) ? "-" : show(_xboxicon, array("id" => re($get['xboxid']), "img" => "1", "css" => ""));
        $psnu = empty($get['psnid']) ? "-" : show(_psnicon, array("id" => re($get['psnid']), "img" => "1", "css" => ""));
        $originu = empty($get['originid']) ? "-" : show(_originicon, array("id" => re($get['originid']), "img" => "1", "css" => ""));
        $battlenetu = empty($get['battlenetid']) ? "-" : show(_battleneticon, array("id" => re($get['battlenetid']), "img" => "1", "css" => ""));
        $skypename = empty($get['skypename']) ? "-" : "<a href=\"skype:".$get['skypename']."?chat\"><img src=\"http://mystatus.skype.com/smallicon/".$get['skypename']."\" style=\"border: none;\" width=\"16\" height=\"16\" alt=\"".$get['skypename']."\"/></a>";
        $hp = empty($get['hp']) ? "-" : show(_hpicon, array("hp" => $get['hp']));

        $icq = "-";
        if(!empty($get['icq'])) {
            $uin = show(_icqstatus, array("uin" => $get['icq']));
            $icq = '<a href="http://www.icq.com/whitepages/about_me.php?uin='.$get['icq'].'" target="_blank">'.$uin.'</a>';
        }

        $getstatus = $get['status'] ? _aktiv_icon : _inaktiv_icon;
        $sql->selectSingle("SELECT `id` FROM `{prefix_squaduser}` WHERE `user` = 1;");
        $status = data("level",$get['id']) > 1 && $sql->rowCount() ? $getstatus : "-";
        $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;

        $edit = ""; $delete = "";
        if(permission("editusers")) {
            $edit = str_replace("&amp;id=","",show("page/button_edit", array("id" => "",
                                                   "action" => "action=admin&amp;edit=".$get['id'],
                                                   "title" => _button_title_edit)));
            
            $delete = show("page/button_delete", array("id" => $get['id'],
                                                       "action" => "action=admin&amp;do=delete",
                                                       "title" => _button_title_del));
        }

        $steam = '-';
        if (!empty($get['steamid'])) {
            $steam = '<div id="infoSteam_' . md5($get['steamid']) . '">
            <div style="width:100%;text-align:center"><img src="../inc/images/ajax-loader-mini.gif" alt="" /></div>
            <script language="javascript" type="text/javascript">DZCP.initDynLoader("infoSteam_' . md5($get['steamid']) . '","steam","&steamid=' . $get['steamid'] . '&list=true",true);</script></div>';
        }

        $userliste .= show($dir."/userliste_show", array("nick" => autor($get['id'],'','',10),
                                                         "level" => getrank($get['id']),
                                                         "status" => $status,
                                                         "age" => getAge($get['bday']),
                                                         "mf" => ($get['sex'] == 1 ? _maleicon : ($get['sex'] == 2 ? _femaleicon : '-')),
                                                         "edit" => $edit,
                                                         "delete" => $delete,
                                                         "class" => $class,
                                                         "icq" => $icq,
                                                         "skypename" => $skypename,
                                                         "icquin" => $get['icq'],
                                                         "onoff" => onlinecheck($get['id']),
                                                         "hp" => $hp,
                                                         "steam" => $steam,
                                                         "xboxu" => $xboxu,
                                                         "psnu" => $psnu,
                                                         "originu" => $originu,
                                                         "battlenetu" => $battlenetu,
                                                         "hlsw" => $hlsw));
    }
    
    $userliste = (empty($userliste) ? show(_no_entrys_found, array("colspan" => "13")) : $userliste);
    $seiten = nav($entrys,settings('m_userlist'),"?action=userlist".(!empty($show_sql) ? "&show=".$show_sql : "").orderby_nav());
    $edel = permission("editusers") ? '<td class="contentMainTop" colspan="2">&nbsp;</td>' : "";
    $search = isset($_GET['search']) && !empty($_GET['search']) ? $_GET['search'] : _nick;
    $index = show($dir."/userliste", array("cnt" => $entrys." "._user,
                                           "edel" => $edel,
                                           "search" => $search,
                                           "nav" => $seiten,
                                           "order_nick" => orderby('nick'),
                                           "order_age" => orderby('bday'),
                                           "show" => $userliste));
}