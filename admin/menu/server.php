<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit();
$where = $where.': '._server_admin_head;

switch ($do) {
    case 'menu':
        $get = $sql->fetch("SELECT `navi`,`game`,`id` FROM `{prefix_server}` WHERE `id` = ?;",array(intval($_GET['id'])));
        if($get['game'] != 'nope') {
            $sql->update("UPDATE `{prefix_server}` SET `navi` = ? WHERE `id` = ?;",array(($get['navi'] ? 0 : 1),$get['id']));
            header("Location: ?admin=server");
        } else {
            $show = error(_server_isnt_live);
        }
    break;
    case 'edit':
        $get = $sql->fetch("SELECT * FROM `{prefix_server}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $custom_icon = '<option value="">'._custom_game_icon_none.'</option>';
        $files = get_files(basePath.'/inc/images/gameicons/custom/',false,true,$picformat);
        if(count($files) >= 1) {
            foreach($files as $file) {
                $sel = ($file == $get['custom_icon'] ? 'selected="selected"' : '');
                $custom_icon .= show(_select_field, array("value" => $file, "what" => strtoupper(preg_replace("#\.(.*?)$#","",$file)), "sel" => $sel));
            }
        }

        $show = show($dir."/server_edit", array("sip" => stringParser::decode($get['ip']),
                                                "sname" => stringParser::decode($get['name']),
                                                "id" => $_GET['id'],
                                                "sport" => $get['port'],
                                                "qport" => $get['qport'],
                                                "games" => listgames($get['game']),
                                                "spwd" => $get['pwd'],
                                                "custom_icon" => $custom_icon));

    break;
    case 'editserver':
        if(empty($_POST['ip']) || empty($_POST['port'])) {
            $show = error(_empty_ip);
        } else if(empty($_POST['name'])) {
            $show = error(_empty_servername);
        } else {
            $get = $sql->fetch("SELECT `ip`,`port`,`game` FROM `{prefix_server}` WHERE `id` = ?;",array(intval($_GET['id'])));
            $cache_hash = md5($get['ip'].':'.$get['port'].'_'.$get['game']);
            $cache->delete('server_'.$cache_hash);

            $sql->update("UPDATE `{prefix_server}` SET `ip` = ?, `port` = ?, `qport` = ?, `name` = ?, `custom_icon`= ?, `icon` = '', `game` = ?, `pwd` = ? WHERE `id` = ?;",
                    array(stringParser::encode($_POST['ip']),intval($_POST['port']),stringParser::encode($_POST['qport']),stringParser::encode($_POST['name']),stringParser::encode($_POST['custom_game_icon']),
                         ($_POST['status'] != 'lazy' ? stringParser::encode($_POST['status']) : $get['game']),stringParser::encode($_POST['pwd']),intval($_GET['id'])));

            $show = info(_server_admin_edited, "?admin=server");
        }
    break;
    case 'delete':
        $get = $sql->fetch("SELECT `ip`,`port`,`game`,`name` FROM `{prefix_server}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $cache_hash = md5($get['ip'].':'.$get['port'].'_'.$get['game']);
        $cache->delete('server_'.$cache_hash);
        $sql->delete("DELETE FROM `{prefix_server}` WHERE `id` = ?;",array(intval($_GET['id'])));
        
        $show = info(show(_server_admin_deleted,array('host' => $get['name'])), "?admin=server");
    break;
    case 'new':
        $custom_icon = '<option value="">'._custom_game_icon_none.'</option>';
        $files = get_files(basePath.'/inc/images/gameicons/custom/',false,true,$picformat);
        if(count($files) >= 1) {
            foreach($files as $file) {
                $custom_icon .= show(_select_field, array("value" => $file, "what" => strtoupper(preg_replace("#\.(.*?)$#","",$file)), "sel" => ''));
            }
        }

        $show = show($dir."/server_add", array("games" => listgames(),"custom_icon" => $custom_icon));
    break;
    case 'add':
        if(empty($_POST['ip']) || empty($_POST['port'])) {
            $show = error(_empty_ip);
        } else if($_POST['status'] == "lazy") {
            $show = error(_empty_game);
        } else if(empty($_POST['name'])) {
            $show = error(_empty_servername);
        } else {
            $sql->insert("INSERT INTO `{prefix_server}` SET `ip` = ?, `port` = ?, `qport` = ?, `name` = ?, `pwd` = ?, `custom_icon`= ?, `game` = ?;",
                array(stringParser::encode($_POST['ip']),intval($_POST['port']),stringParser::encode($_POST['qport']),stringParser::encode($_POST['name']),stringParser::encode($_POST['pwd']),stringParser::encode($_POST['custom_game_icon']),stringParser::encode($_POST['status'])));

            $show = info(_server_admin_added, "?admin=server");
        }
    break;
    default:
        $color = 0; $show_servers = '';
        $qry = $sql->select("SELECT * FROM `{prefix_server}` ORDER BY id;");
        foreach($qry as $get) {
            $gameicon = show(_gameicon, array("icon" => 'unknown.gif')); $icon = false;
            if(!empty($get['custom_icon'])) {
                if(file_exists(basePath.'/inc/images/gameicons/custom/'.$get['custom_icon'])) {
                    $gameicon = show(_gameicon, array('icon' => $get['custom_icon']));
                    $icon = true;
                }
            } else {
                foreach($picformat AS $end) {
                    if(file_exists(basePath.'/inc/images/gameicons/'.$get['game'].'.'.$end)) {
                        $gameicon = show(_gameicon, array('icon' => $get['game'].'.'.$end));
                        $icon = true;
                        break;
                    }
                }
            }
            
            //Get Icon from runned Server Query
            if(!$icon && !empty($get['icon'])) {
                $game_icon_inp = GameQ::search_game_icon(stringParser::decode($get['icon']),false);
                if($game_icon_inp['found']) {
                    $gameicon = show(_gameicon_blank, array('icon' => $game_icon_inp['image']));
                }
                unset($game_icon_inp);
            }

            $edit = show("page/button_edit_single", array("id" => $get['id'], "action" => "admin=server&amp;do=edit", "title" => _button_title_edit));
            $delete = show("page/button_delete_single", array("id" => $get['id'], "action" => "admin=server&amp;do=delete", "title" => _button_title_del, "del" => _confirm_del_server));
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $menu = ($get['navi'] ? show(_server_menu_icon_no, array("id" => $get['id'])) : show(_server_menu_icon_yes, array("id" => $get['id'])));
            $show_servers .= show($dir."/server_show", array("gameicon" => $gameicon,
                                                             "serverip" => stringParser::decode($get['ip']).":".$get['port'],
                                                             "serverpwd" => stringParser::decode($get['pwd']),
                                                             "menu" => $menu,
                                                             "edit" => $edit,
                                                             "name" => stringParser::decode($get['name']),
                                                             "class" => $class,
                                                             "delete" => $delete));
        }

        if(empty($show_servers)) {
            $show_servers = show(_no_entrys_yet, array("colspan" => "4"));
        }

        $show = show($dir."/server", array("show" => $show_servers));
    break;
}
