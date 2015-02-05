<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit();
$where = $where.': '._admin_pos;

switch ($do) {
    case 'edit':
        $qry = db("SELECT `pid`,`position` FROM `".$db['pos']."` ORDER BY pid DESC;"); $positions = '';
        while($get = _fetch($qry)) {
            $positions .= show(_select_field, array("value" => ($get['pid']+1),
                                                    "what" => _nach.' '.re($get['position']),
                                                    "sel" => ""));
        }

        $id = intval($_GET['id']);
        $get = db("SELECT `position`,`color` FROM `".$db['pos']."` WHERE `id` = ".$id.";",false,true);
        $show = show($dir."/form_pos", array("newhead" => _pos_edit_head,
                                             "do" => "editpos&amp;id=".$id."",
                                             "kat" => re($get['position']),
                                             "color" => re($get['color']),
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
            $posid = intval($_POST['pos']); $id = intval($_GET['id']);
            $pid = ($_POST['pos'] == "lazy" ? "" : ",`pid` = ".$posid);
            $sign = ($_POST['pos'] == "1" || $_POST['pos'] == "2" ? ">= " : "> ");
            db("UPDATE `".$db['pos']."` SET `pid` = (pid+1) WHERE `pid` ".$sign." ".$posid.";");
            db("UPDATE `".$db['pos']."` SET `position` = '".up($_POST['kat'])."' ".$pid.", `color` = '".up($_POST['color'])."' WHERE `id` = ".$id.";");

            // Permissions Update
            if(empty($_POST['perm'])) {
                $_POST['perm'] = array();
            }

            $qry_fields = db("SHOW FIELDS FROM `".$db['permissions']."`;"); $sql_update = '';
            while($get = _fetch($qry_fields)) {
                if($get['Field'] != 'id' && $get['Field'] != 'user' && $get['Field'] != 'pos' && $get['Field'] != 'intforum') {
                    $sql = array_key_exists('p_'.$get['Field'], $_POST['perm']) ? '`'.$get['Field'].'` = 1' : '`'.$get['Field'].'` = 0';
                    $sql_update .= $sql.', ';
                }
            }

            // Check group Permissions is exists
            if(!db('SELECT `id` FROM `'.$db['permissions'].'` WHERE `pos` = '.$id.' LIMIT 1;',true)) {
                db("INSERT INTO `".$db['permissions']."` SET `pos` = ".$id.";");
            }

            // Update Permissions
            db('UPDATE `'.$db['permissions'].'` SET '.substr($sql_update, 0, -2).' WHERE `pos` = '.$id.' LIMIT 1;');

            // Internal Boardpermissions Update
            if(empty($_POST['board'])) {
                $_POST['board'] = array();
            }

            // Cleanup Boardpermissions
            $sql = db('SELECT `id`,`forum` FROM `'.$db['f_access'].'` WHERE `pos` = '.$id.';');
            while($get = _fetch($sql)) { 
                if(!array_var_exists($get['forum'],$_POST['board'])) {
                    db('DELETE FROM `'.$db['f_access'].'` WHERE `id` = '.$get['id'].';'); 
                }
            }

            //Add new Boardpermissions
            if(count($_POST['board']) >= 1) {
                foreach($_POST['board'] AS $boardpem) { 
                    if(!db("SELECT `id` FROM `".$db['f_access']."` WHERE `pos` = ".$id." AND `forum` = '".$boardpem."';",true)) {
                        db("INSERT INTO `".$db['f_access']."` SET `pos` = ".$id.", `forum` = '".$boardpem."';"); 
                    }
                }
            }

            $show = info(_pos_admin_edited, "?admin=positions");
        }
    break;
    case 'delete':
        db("DELETE FROM `".$db['pos']."` WHERE `id` = ".intval($_GET['id']).";");
        db("DELETE FROM `".$db['permissions']."` WHERE `pos` = ".intval($_GET['id']).";");
        $show = info(_pos_admin_deleted, "?admin=positions");
    break;
    case 'new':
        $qry = db("SELECT `pid`,`position` FROM `".$db['pos']."` ORDER BY pid DESC;"); $positions = '';
        while($get = _fetch($qry)) {
            $positions .= show(_select_field, array("value" => ($get['pid']+1),
                                                    "what" => _nach.' '.re($get['position']),
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
            $sign = ($_POST['pos'] == "1" || $_POST['pos'] == "2" ? ">= " : "> ");
            db("UPDATE ".$db['pos']." SET `pid` = (pid+1) WHERE pid ".$sign." '".intval($_POST['pos'])."';");
            db("INSERT INTO ".$db['pos']." SET `pid` = '".intval($_POST['pos'])."', `position` = '".up($_POST['kat'])."', `color` = '".up($_POST['color'])."';");
            
            $posID = _insert_id();
            $qry_fields = db("SHOW FIELDS FROM `".$db['permissions']."`;"); $sql_update = '';
            while($get = _fetch($qry_fields)) {
                if($get['Field'] != 'id' && $get['Field'] != 'user' && $get['Field'] != 'pos' && $get['Field'] != 'intforum') {
                    $sql = array_key_exists('p_'.$get['Field'], $_POST['perm']) ? '`'.$get['Field'].'` = 1' : '`'.$get['Field'].'` = 0';
                    $sql_update .= $sql.', ';
                }
            }
            
            // Add Permissions
            db('INSERT INTO `'.$db['permissions'].'` SET '.$sql_update.'`pos` = '.$posID.';');

            // Internal Boardpermissions Update
            if(empty($_POST['board'])) {
                $_POST['board'] = array();
            }

            //Add new Boardpermissions
            if(count($_POST['board']) >= 1) {
                foreach($_POST['board'] AS $boardpem) { 
                    if(!db("SELECT `id` FROM `".$db['f_access']."` WHERE `pos` = ".$posID." AND `forum` = '".$boardpem."';",true)) {
                        db("INSERT INTO `".$db['f_access']."` SET `pos` = ".$posID.", `forum` = '".$boardpem."';"); 
                    }
                }
            }

          $show = info(_pos_admin_added, "?admin=positions");
        }
    break;
    default:
        $qry = db("SELECT `id`,`position` FROM `".$db['pos']."` ORDER BY pid DESC;"); $show_pos = '';
        while($get = _fetch($qry)) {
            $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                          "action" => "admin=positions&amp;do=edit",
                                                          "title" => _button_title_edit));

            $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                              "action" => "admin=positions&amp;do=delete",
                                                              "title" => _button_title_del,
                                                              "del" => convSpace(_confirm_del_entry)));

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show_pos .= show($dir."/positions_show", array("edit" => $edit,
                                                      "name" => re($get['position']),
                                                      "class" => $class,
                                                      "delete" => $delete));
        }

        if(empty($show_pos)) {
            $show_pos = show(_no_entrys_yet, array("colspan" => "3"));
        }

        $show = show($dir."/positions", array("show" => $show_pos,
                                              "edit" => _editicon_blank,
                                              "delete" => _deleteicon_blank));
        unset($show_pos,$qry,$get);
    break;
}