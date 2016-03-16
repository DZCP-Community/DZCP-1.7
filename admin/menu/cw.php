<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._clanwars;

switch ($do) {
    case 'new':
        $qry = $sql->select("SELECT `name`,`game`,`id`,`icon` "
                . "FROM `{prefix_squads}` "
                . "WHERE `status` = 1 "
                . "ORDER BY `game` ASC;"); 
        $squads = '';
        foreach($qry as $get) {
            $squads .= show(_cw_add_select_field_squads, array("name" => stringParser::decode($get['name']),
                                                               "game" => stringParser::decode($get['game']),
                                                               "id" => $get['id'],
                                                               "icon" => $get['icon']));
        }

        $dropdown_date = show(_dropdown_date, array("day" => dropdown("day",date("d",time())),
                                                    "month" => dropdown("month",date("m",time())),
                                                    "year" => dropdown("year",date("Y",time()))));

        $dropdown_time = show(_dropdown_time, array("hour" => dropdown("hour",date("H",time())),
                                                    "minute" => dropdown("minute",date("i",time())),
                                                    "uhr" => _uhr));

        $show = show($dir."/form_cw", array("head" => _cw_admin_head,
                                            "nothing" => "",
                                            "do" => "add",
                                            "what" => _button_value_add,
                                            "cw_clantag" => "",
                                            "cw_gegner" => "",
                                            "cw_url" => "",
                                            "cw_xonx1" => "",
                                            "cw_xonx2" => "",
                                            "cw_maps" => "",
                                            "cw_servername" => "",
                                            "cw_serverip" => "",
                                            "cw_serverpwd" => "",
                                            "cw_punkte" => "",
                                            "cw_gpunkte" => "",
                                            "cw_matchadmins" => "",
                                            "cw_lineup" => "",
                                            "cw_glineup" => "",
                                            "cw_bericht" => "",
                                            "dropdown_date" => $dropdown_date,
                                            "dropdown_time" => $dropdown_time,
                                            "hour" => "",
                                            "minute" => "",
                                            "squads" => $squads,
                                            "cw_liga" => "",
                                            "countrys" => show_countrys(),
                                            "cw_gametype" => ""));
    break;
    case 'edit':
        $get = $sql->fetch("SELECT * FROM `{prefix_clanwars}` "
                . "WHERE `id` = ?;",array(intval($_GET['id'])));
        list($xonx1,$xonx2) = explode('on', stringParser::decode($get['xonx']));
        $qrym = $sql->select("SELECT `id`,`name`,`game`,`icon` "
                . "FROM `{prefix_squads}` "
                . "WHERE `status` = 1 "
                . "ORDER BY `game`;"); 
        $squads = '';
        foreach($qrym as $gets) {
            $sel = ($get['squad_id'] == $gets['id'] ? 'selected="selected"' : '');
            $squads .= show(_cw_edit_select_field_squads, array("id" => $gets['id'],
                                                                "name" => stringParser::decode($gets['name']),
                                                                "game" => stringParser::decode($gets['game']),
                                                                "sel" => $sel,
                                                                "icon" => stringParser::decode($gets['icon'])));
        }

        $dropdown_date = show(_dropdown_date, array("day" => dropdown("day",date("d",$get['datum'])),
                                                    "month" => dropdown("month",date("m",$get['datum'])),
                                                    "year" => dropdown("year",date("Y",$get['datum']))));

        $dropdown_time = show(_dropdown_time, array("hour" => dropdown("hour",date("H",$get['datum'])),
                                                    "minute" => dropdown("minute",date("i",$get['datum'])),
                                                    "uhr" => _uhr));

        $show = show($dir."/form_cw", array("head" => _cw_admin_head_edit,
                                            "do" => "editcw&amp;id=".$_GET['id']."",
                                            "what" => _button_value_edit,
                                            "cw_clantag" => stringParser::decode($get['clantag']),
                                            "cw_gegner" => stringParser::decode($get['gegner']),
                                            "cw_url" => link(stringParser::decode($get['url'])),
                                            "cw_xonx1" => $xonx1,
                                            "cw_xonx2" => $xonx2,
                                            "cw_maps" => stringParser::decode($get['maps']),
                                            "cw_matchadmins" => stringParser::decode($get['matchadmins']),
                                            "cw_lineup" => stringParser::decode($get['lineup']),
                                            "cw_glineup" => stringParser::decode($get['glineup']),
                                            "cw_servername" => stringParser::decode($get['servername']),
                                            "cw_serverip" => stringParser::decode($get['serverip']),
                                            "cw_serverpwd" => stringParser::decode($get['serverpwd']),
                                            "cw_punkte" => $get['punkte'],
                                            "cw_gpunkte" => $get['gpunkte'],
                                            "cw_bericht" => stringParser::decode($get['bericht']),
                                            "day" => date("d", $get['datum']),
                                            "dropdown_date" => $dropdown_date,
                                            "dropdown_time" => $dropdown_time,
                                            "month" => date("m", $get['datum']),
                                            "year" => date("Y", $get['datum']),
                                            "hour" => date("H", $get['datum']),
                                            "minute" => date("i", $get['datum']),
                                            "countrys" => show_countrys($get['gcountry']),
                                            "squads" => $squads,
                                            "cw_liga" => stringParser::decode($get['liga']),
                                            "cw_gametype" => stringParser::decode($get['gametype'])));
    break;
    case 'add':
        if(empty($_POST['gegner']) || empty($_POST['clantag']) || empty($_POST['t'])) {
            if(empty($_POST['gegner'])) 
                $show = error(_cw_admin_empty_gegner, 1);
            else if(empty($_POST['clantag'])) 
                $show = error(_cw_admin_empty_clantag, 1);
            else if(empty($_POST['t'])) 
                $show = error(_empty_datum, 1);
        } else {
            $xonx = (empty($_POST['xonx1']) && empty($_POST['xonx2']) ? "" : "`xonx` = '".intval($_POST['xonx1'])."on".intval($_POST['xonx2'])."', ");
            $datum = mktime($_POST['h'],$_POST['min'],0,$_POST['m'],$_POST['t'],$_POST['j']);
            $kid = ($_POST['land'] == "lazy" ? "" : " `gcountry` = '".$_POST['land']."', ");
            $sql->insert("INSERT INTO `{prefix_clanwars}` SET".$kid."".$xonx
                    . "`datum` = ?, `squad_id` = ?, `clantag` = ?, `gegner` = ?, `url` = ?, `liga` = ?, "
                    . "`gametype` = ?, `punkte` = ?, `gpunkte` = ?, `maps` = ?, `serverip` = ?, `servername` = ?, "
                    . "`serverpwd` = ?, `lineup` = ?, `glineup` = ?, `matchadmins` = ?, `bericht` = ?;",
                    array(intval($datum),intval($_POST['squad']),
                        stringParser::encode($_POST['clantag']),stringParser::encode($_POST['gegner']),stringParser::encode(links($_POST['url'])),stringParser::encode($_POST['gametype']),
                        intval($_POST['punkte']),intval($_POST['gpunkte']),stringParser::encode($_POST['maps']),stringParser::encode($_POST['serverip']),
                        stringParser::encode($_POST['servername']),stringParser::encode($_POST['serverpwd']),stringParser::encode($_POST['lineup']),stringParser::encode($_POST['match_admins']),
                        stringParser::encode($_POST['bericht'])));

            //Logo Upload
            $cwid = $sql->lastInsertId();
            $tmpname = $_FILES['logo']['tmp_name'];
            $type = $_FILES['logo']['type'];
            $end = explode(".", $_FILES['logo']['name']);
            $end = strtolower($end[count($end)-1]);
            if(!empty($tmpname)) {
                $img = getimagesize($tmpname);
                if ($img[0]) {
                    move_uploaded_file($tmpname, basePath . "/inc/images/clanwars/" . $cwid . "_logo." . strtolower($end));
                }
            }

            //Screenshot Upload
            for ($zaehler = 1; $zaehler <= 20; $zaehler++) {
                if(isset($_FILES['screen'.$zaehler])) {
                    $tmpname = $_FILES['screen'.$zaehler]['tmp_name'];
                    $type = $_FILES['screen'.$zaehler]['type'];
                    $end = explode(".", $_FILES['screen'.$zaehler]['name']);
                    $end = strtolower($end[count($end)-1]);
                    if(!empty($tmpname)) {
                        $img = @getimagesize($tmpname);
                        if ($img[0]) {
                            move_uploaded_file($tmpname, basePath . "/inc/images/clanwars/" . $cwid . "_" . $zaehler . "." . strtolower($end));
                        }
                    }
                } else break;
            }

            $show = info(_cw_admin_added, "?admin=cw");
        }
    break;
    case 'editcw':
        if(empty($_POST['gegner']) || empty($_POST['clantag']) || empty($_POST['t'])) {
            if(empty($_POST['gegner'])) 
                $show = error(_cw_admin_empty_gegner, 1);
            elseif(empty($_POST['clantag'])) 
                $show = error(_cw_admin_empty_clantag, 1);
            elseif(empty($_POST['t'])) 
                $show = error(_empty_datum, 1);
        } else {
            $xonx = (empty($_POST['xonx1']) && empty($_POST['xonx2']) ? '' : "`xonx` = '".$_POST['xonx1']."on".$_POST['xonx2']."',");
            $datum = mktime($_POST['h'],$_POST['min'],0,$_POST['m'],$_POST['t'],$_POST['j']);
            $kid = ($_POST['land'] == "lazy" ? "" : "`gcountry` = '".$_POST['land']."',");
            $sql->update("UPDATE `{prefix_clanwars}` SET ".$xonx." ".$kid." `datum` = ?,`squad_id` = ?, `clantag` = ?, `gegner` = ?,`url` = ?, `liga` = ?, `gametype` = ?,`punkte` = ?,
            `gpunkte` = ?,`maps` = ?,`serverip` = ?,`servername` = ?,`serverpwd` = ?,`lineup` = ?,`glineup` = ?,`matchadmins` = ?,`bericht` = ? WHERE id = ?;",
                array(intval($datum),intval($_POST['squad']),stringParser::encode($_POST['clantag']),stringParser::encode($_POST['gegner']),stringParser::encode(links($_POST['url'])),
                    stringParser::encode($_POST['liga']),stringParser::encode($_POST['gametype']),intval($_POST['punkte']),intval($_POST['gpunkte']),stringParser::encode($_POST['maps']),
                    stringParser::encode($_POST['serverip']),stringParser::encode($_POST['servername']),stringParser::encode($_POST['serverpwd']),stringParser::encode($_POST['lineup']),stringParser::encode($_POST['glineup']),
                    stringParser::encode($_POST['match_admins']),stringParser::encode($_POST['bericht']),intval($_GET['id'])));

            //Logo Upload
            $cwid = intval($_GET['id']);
            $tmpname = $_FILES['logo']['tmp_name'];
            $type = $_FILES['logo']['type'];
            $end = explode(".", $_FILES['logo']['name']);
            $end = strtolower($end[count($end)-1]);
            if(!empty($tmpname)) {
                $img = @getimagesize($tmpname);
                if($img[0]) {
                    foreach($picformat AS $end_del) {
                        if(file_exists(basePath.'/inc/images/clanwars/'.$cwid.'_logo.'.$end_del)) {
                            unlink(basePath.'/inc/images/clanwars/'.$cwid.'_logo.'.$end_del);
                            break;
                        }
                    }

                    move_uploaded_file($tmpname, basePath."/inc/images/clanwars/".$cwid."_logo.".strtolower($end));
                }
            }

            //Screenshot Upload
            for ($zaehler = 1; $zaehler <= 20; $zaehler++) {
                if(isset($_FILES['screen'.$zaehler])) {
                    $tmpname = $_FILES['screen'.$zaehler]['tmp_name'];
                    $type = $_FILES['screen'.$zaehler]['type'];
                    $end = explode(".", $_FILES['screen'.$zaehler]['name']);
                    $end = strtolower($end[count($end)-1]);
                    if(!empty($tmpname)) {
                        $img = @getimagesize($tmpname);
                        if($img[0]) {
                            foreach($picformat AS $end_del) {
                                if(file_exists(basePath.'/inc/images/clanwars/'.$cwid.'_'.$zaehler.'.'.$end_del)) {
                                    unlink(basePath.'/inc/images/clanwars/'.$cwid.'_'.$zaehler.'.'.$end_del);
                                    break;
                                }
                            }

                            move_uploaded_file($tmpname, basePath."/inc/images/clanwars/".$cwid."_".$zaehler.".".strtolower($end));
                        }
                    }
                } else break;
            }

            $show = info(_cw_admin_edited, "?admin=cw");
        }
    break;
    case 'delete':
        $sql->delete("DELETE FROM `{prefix_clanwars}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $sql->delete("DELETE FROM `{prefix_cw_comments}` WHERE `cw` = ?;",array(intval($_GET['id'])));
        $show = info(_cw_admin_deleted, "?admin=cw");
    break;
    case 'top':
        $sql->update("UPDATE `{prefix_clanwars}` SET `top` = ? WHERE `id` = ?;",array(intval($_GET['set']),intval($_GET['id'])));
        $show = info((empty($_GET['set']) ? _cw_admin_top_unsetted : _cw_admin_top_setted), "?admin=cw");
    break;
    default:
        $whereqry = (isset($_GET['squad']) && is_numeric($_GET['squad']) ? ' WHERE `squad_id` = '.intval($_GET['squad']) : '');
        $qry = $sql->select("SELECT `id`,`gegner`,`datum`,`clantag` FROM `{prefix_clanwars}`".$whereqry." ORDER BY `datum` DESC LIMIT ".($page - 1)*$maxadmincw.",".$maxadmincw.";");
        foreach($qry as $get) {
            $top = empty($get['top'])
                   ? '<a href="?admin=cw&amp;do=top&amp;set=1&amp;id='.$get['id'].'"><img src="../inc/images/no.gif" alt="" title="'._cw_admin_top_set.'" /></a>'
                   : '<a href="?admin=cw&amp;do=top&amp;set=0&amp;id='.$get['id'].'"><img src="../inc/images/yes.gif" alt="" title="'._cw_admin_top_unset.'" /></a>';

            $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                          "action" => "admin=cw&amp;do=edit",
                                                          "title" => _button_title_edit));

            $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                              "action" => "admin=cw&amp;do=delete",
                                                              "title" => _button_title_del,
                                                              "del" => _confirm_del_cw));

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show .= show($dir."/clanwars_show", array("class" => $class,
                                                       "cw" => stringParser::decode($get['clantag'])." - ".stringParser::decode($get['gegner']),
                                                       "datum" => date("d.m.Y H:i",$get['datum'])._uhr,
                                                       "top" => $top,
                                                       "id" => $get['id'],
                                                       "edit" => $edit,
                                                       "delete" => $delete));
        }

        $qry = $sql->select("SELECT `id`,`name` FROM `{prefix_squads}` WHERE `status` = 1 ORDER BY `game` ASC;"); $squads = "";
        $squads .= show(_cw_edit_select_field_squads, array("name" => _all, "sel" => "", "id" => "?admin=cw"));
        foreach($qry as $get) {
            $sel = (isset($_GET['squad']) && $get['id'] == intval($_GET['squad']) ? ' class="dropdownKat"' : '');
            $squads .= show(_cw_edit_select_field_squads, array("name" => stringParser::decode($get['name']), "sel" => $sel, "id" => "?admin=cw&amp;squad=".$get['id']));
        }

        if(empty($show)) {
            $show = '<tr><td colspan="5" class="contentMainSecond">' . _no_entrys . '</td></tr>';
        }

        $show = show($dir."/clanwars", array("squads" => $squads, "show" => $show, "navi" => nav(cnt('{prefix_clanwars}'),$maxadmincw,"?admin=cw&amp;squad=".(isset($_GET['squad']) ? $_GET['squad'] : ''))));
    break;
}