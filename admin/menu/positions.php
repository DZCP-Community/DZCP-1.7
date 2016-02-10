<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit();
$where = $where.': '._admin_pos;

switch ($do) {
    case 'edit':
        $qry = $sql->select("SELECT `pid`,`position` FROM `{prefix_positions}` ORDER BY `pid` DESC;"); $positions = '';
        foreach($qry as $get) {
            $positions .= show(_select_field, array("value" => ($get['pid']+1),
                                                    "what" => _nach.' '.stringParser::decode($get['position']),
                                                    "sel" => ""));
        }

        $id = intval($_GET['id']);
        $get = $sql->fetch("SELECT `position`,`color` FROM `{prefix_positions}` WHERE `id` = ?;",array($id));
        $show = show($dir."/form_pos", array("newhead" => _pos_edit_head,
                                             "do" => "editpos&amp;id=".$id."",
                                             "kat" => stringParser::decode($get['position']),
                                             "color" => stringParser::decode($get['color']),
                                             "getpermissions" => getPermissions($id, 1),
                                             "getboardpermissions" => getBoardPermissions($id, 1),
                                             "positions" => $positions,
                                             "what" => _button_value_edit));
        unset($positions,$qry,$get);
    break;
    case 'editpos':
        if(empty($_POST['kat'])) {
            $show = error(_pos_empty_kat,1);
        } else {
            $posid = intval($_POST['pos']);
            $sql->update("UPDATE `{prefix_positions}` SET `pid` = (pid+1) WHERE `pid` ".($_POST['pos'] == "1" || $_POST['pos'] == "2" ? ">= " : "> ")
                    ." ?;",array(intval($_POST['pos'])));
            $sql->update("UPDATE `{prefix_positions}` SET `position` = ? ".
                    ($_POST['pos'] == "lazy" ? "" : ",`pid` = ".intval($_POST['pos'])).", `color` = ? WHERE `id` = ?;",
                    array(stringParser::encode($_POST['kat']),stringParser::encode($_POST['color']),intval($_GET['id'])));

            // Permissions Update
            if(empty($_POST['perm'])) {
                $_POST['perm'] = array();
            }

            $qry_fields = $sql->show("SHOW FIELDS FROM `{prefix_permissions}`;"); $sql_update = '';
            foreach($qry_fields as $get) {
                if($get['Field'] != 'id' && $get['Field'] != 'user' && $get['Field'] != 'pos' && $get['Field'] != 'intforum') {
                    $qry = array_key_exists('p_'.$get['Field'], $_POST['perm']) ? '`'.$get['Field'].'` = 1' : '`'.$get['Field'].'` = 0';
                    $sql_update .= $qry.', ';
                }
            }

            // Check group Permissions is exists
            if(!$sql->rows('SELECT `id` FROM `{prefix_permissions}` WHERE `pos` = ? LIMIT 1;',array($id))) {
                $sql->insert("INSERT INTO `{prefix_permissions}` SET `pos` = ?;",array($id));
            }

            // Update Permissions
            $sql->update('UPDATE `{prefix_permissions}` SET '.substr($sql_update, 0, -2).' WHERE `pos` = ? LIMIT 1;',array($id));

            // Internal Boardpermissions Update
            if(empty($_POST['board'])) {
                $_POST['board'] = array();
            }

            // Cleanup Boardpermissions
            $qry = $sql->select('SELECT `id`,`forum` FROM `{prefix_f_access}` WHERE `pos` = ?;',array($id));
            foreach($qry as $get) {
                if(!array_var_exists($get['forum'],$_POST['board'])) {
                    $sql->delete('DELETE FROM `{prefix_f_access}` WHERE `id` = ?;',array($get['id'])); 
                }
            }

            //Add new Boardpermissions
            if(count($_POST['board']) >= 1) {
                foreach($_POST['board'] AS $boardpem) { 
                    if(!$sql->rows("SELECT `id` FROM `{prefix_f_access}` WHERE `pos` = ? AND `forum` = ?;",array($id,$boardpem))) {
                        $sql->insert("INSERT INTO `{prefix_f_access}` SET `pos` = ?, `forum` = ?;",array($id,$boardpem)); 
                    }
                }
            }

            $show = info(_pos_admin_edited, "?admin=positions");
        }
    break;
    case 'delete':
        $get = fetch("SELECT `id` FROM `{prefix_positions}` WHERE `id` = ?;",array(intval($_GET['id'])));
        if($sql->rowCount()) {
            $sql->delete("DELETE FROM `{prefix_positions}` WHERE `id` = ?;",array($get['id']));
            $sql->delete("DELETE FROM `{prefix_permissions}` WHERE `pos` = ?;",array($get['id']));
            $show = info(_pos_admin_deleted, "?admin=positions");
        }
    break;
    case 'new':
        $qry = $sql->select("SELECT `pid`,`position` FROM `{prefix_positions}` ORDER BY `pid` DESC;"); $positions = '';
        foreach($qry as $get) {
            $positions .= show(_select_field, array("value" => ($get['pid']+1),
                                                    "what" => _nach.' '.stringParser::decode($get['position']),
                                                    "sel" => ""));
        }

        $show = show($dir."/form_pos", array("newhead" => _pos_new_head,
                                             "do" => "add",
                                             "getpermissions" => getPermissions(),
                                             "getboardpermissions" => getBoardPermissions(),
                                             "nothing" => "",
                                             "positions" => $positions,
                                             "kat" => "",
                                             "color" => "#000000",
                                             "what" => _button_value_add));

        unset($positions,$qry,$get);
    break;
    case 'add':
        if(empty($_POST['kat'])) {
            $show = error(_pos_empty_kat,1);
        } else {
            $sql->update("UPDATE `{prefix_positions}` SET `pid` = (pid+1) WHERE `pid`;".
                    ($_POST['pos'] == "1" || $_POST['pos'] == "2" ? ">= " : "> ")." ?;",
                    array(intval($_POST['pos'])));
            $sql->insert("INSERT INTO `{prefix_positions}` SET `pid` = ?, `position` = ?, `color` = ?;",
                array(intval($_POST['pos']),stringParser::encode($_POST['kat']),stringParser::encode($_POST['color'])));
            
            $posID = $sql->lastInsertId();
            $qry = $sql->show("SHOW FIELDS FROM `{prefix_permissions}`;"); $sql_update = '';
            foreach($qry as $get) {
                if($get['Field'] != 'id' && $get['Field'] != 'user' && $get['Field'] != 'pos' && $get['Field'] != 'intforum') {
                    $qry = array_key_exists('p_'.$get['Field'], $_POST['perm']) ? '`'.$get['Field'].'` = 1' : '`'.$get['Field'].'` = 0';
                    $sql_update .= $qry.', ';
                }
            }
            
            // Add Permissions
            $sql->insert('INSERT INTO `{prefix_permissions}` SET '.$sql_update.'`pos` = ?;',array($posID));

            // Internal Boardpermissions Update
            if(empty($_POST['board'])) {
                $_POST['board'] = array();
            }

            //Add new Boardpermissions
            if(count($_POST['board']) >= 1) {
                foreach($_POST['board'] AS $boardpem) { 
                    if(!$sql->rows("SELECT `id` FROM `{prefix_f_access}` WHERE `pos` = ? AND `forum` = ?;",array($posID,$boardpem))) {
                        $sql->insert("INSERT INTO `{prefix_f_access}` SET `pos` = ?, `forum` = ?;",array($posID,$boardpem)); 
                    }
                }
            }

            $show = info(_pos_admin_added, "?admin=positions");
        }
    break;
    default:
        $qry = $sql->select("SELECT `id`,`position` FROM `{prefix_positions}` ORDER BY `pid` DESC;"); $show_pos = '';
        foreach($qry as $get) {
            $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                          "action" => "admin=positions&amp;do=edit",
                                                          "title" => _button_title_edit));

            $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                              "action" => "admin=positions&amp;do=delete",
                                                              "title" => _button_title_del,
                                                              "del" => convSpace(_confirm_del_entry)));

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show_pos .= show($dir."/positions_show", array("edit" => $edit,
                                                            "name" => stringParser::decode($get['position']),
                                                            "class" => $class,
                                                            "delete" => $delete));
        }

        if(empty($show_pos)) {
            $show_pos = show(_no_entrys_yet, array("colspan" => "3"));
        }

        $show = show($dir."/positions", array("show" => $show_pos));
        unset($show_pos,$qry,$get);
    break;
}