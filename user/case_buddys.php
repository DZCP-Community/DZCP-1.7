<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    $where = _site_user_buddys;
    if(!$chkMe) {
        $index = error(_error_have_to_be_logged, 1);
    } else {
        switch ($do) {
            case 'add':
                if($_POST['users'] == "-") {
                    $index = error(_error_select_buddy, 1);
                } elseif($_POST['users'] == $userid) {
                    $index = error(_error_buddy_self, 1);
                } elseif(!check_buddy($_POST['users'])) {
                    $index = error(_error_buddy_already_in, 1);
                } else {
                    $sql->insert("INSERT INTO `{prefix_userbuddys}` SET `user` = ?, `buddy` = ?;",
                    array(intval($userid),intval($_POST['users'])));
                    $msg = show(_buddy_added_msg, array("user" => autor($userid)));
                    $title = _buddy_title;
                    $sql->insert("INSERT INTO `{prefix_messages}` SET "
                               . "`datum` = ".time().", "
                               . "`von` = 0, "
                               . "`an` = ?, "
                               . "`titel` = ?, "
                               . "`nachricht` = ?;",array(intval($_POST['users']),up($title),up($msg)));

                    $index = info(_add_buddy_successful, "?action=buddys");
                }
            break;
            case 'addbuddy':
                $user = isset($_GET['id']) ? $_GET['id'] : $_POST['users'];
                if($user == "-") {
                    $index = error(_error_select_buddy, 1);
                } elseif($user == $userid) {
                    $index = error(_error_buddy_self, 1);
                } elseif(!check_buddy($user)) {
                    $index = error(_error_buddy_already_in, 1);
                } else {
                    db("INSERT INTO `{prefix_userbuddys}` SET `user` = ?, `buddy` = ?;",array(intval($userid),intval($user)));

                    $msg = show(_buddy_added_msg, array("user" => addslashes(autor($userid))));
                    $title = _buddy_title;
                    $sql->insert("INSERT INTO `{prefix_messages}` SET "
                               . "`datum` = ".time().", "
                               . "`von` = 0, "
                               . "`an` = ?, "
                               . "`titel` = ?, "
                               . "`nachricht` = ?;",array(intval($user),up($title),up($msg)));

                    $index = info(_add_buddy_successful, "?action=buddys");
                }
            break;
            case 'delete':
                if(isset($_GET['id']) && intval($_GET['id']) >= 1) {
                    $sql->delete("DELETE FROM `{prefix_userbuddys}` "
                               . "WHERE `buddy` = ? AND `user` = ?;",array(intval($_GET['id']),$userid));

                    $msg = show(_buddy_del_msg, array("user" => addslashes(autor($userid))));
                    $title = _buddy_title;
                    $sql->insert("INSERT INTO `{prefix_messages}` SET "
                               . "`datum` = ".time().", "
                               . "`von` = 0, "
                               . "`an` = ?, "
                               . "`titel` = ?, "
                               . "`nachricht` = ?;",array(intval($_GET['id']),up($title),up($msg)));

                    $index = info(_buddys_delete_successful, "../user/?action=buddys");
                }
            break;
            default:
                $qry = $sql->select("SELECT `buddy` FROM `{prefix_userbuddys}` WHERE `user` = ?;",array($userid));
                $too = ""; $buddys = ""; $usersNL=array();
                foreach($qry as $get) {
                    $pn = show(_pn_write, array("id" => $get['buddy'], "nick" => re(data("nick",$get['buddy']))));
                    $delete = show(_buddys_delete, array("id" => $get['buddy']));
                    $too = $sql->rows("SELECT `id` FROM `{prefix_userbuddys}` where `user` = ? AND `buddy` = ?;",array($get['buddy'],$userid)) ? _buddys_yesicon : _buddys_noicon;
                    $usersNL[$get['buddy']] = true;
                    $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
                    $buddys .= show($dir."/buddys_show", array("nick" => autor($get['buddy']),
                                                               "onoff" => onlinecheck($get['buddy']),
                                                               "pn" => $pn,
                                                               "class" => $class,
                                                               "too" => $too,
                                                               "delete" => $delete));
                }

                $buddys = (empty($buddys) ? show(_no_entrys_found, array("colspan" => "5")) : $buddys);
                $qry = $sql->select("SELECT `id`,`nick` FROM `{prefix_users}` WHERE `level` != 0 ORDER BY `nick`;");
                $users = "";
                foreach($qry as $get) {
                    if(!array_key_exists($get['id'], $usersNL) && $get['id'] != $userid) {
                        $users .= show(_to_users, array("id" => $get['id'], "nick" => re(data("nick",$get['id']))));
                    }
                }

                $add = show($dir."/buddys_add", array("users" => $users));
                $index = show($dir."/buddys", array("show" => $buddys, "add" => $add));
            break;
        }
    }
}