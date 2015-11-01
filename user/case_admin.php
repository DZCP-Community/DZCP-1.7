<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    if (!permission("editusers")) {
        $index = error(_error_wrong_permissions, 1);
    } elseif (isset($_GET['edit']) && $_GET['edit'] == $userid) {
        $qrySquads = $sql->select("SELECT `id`,`name` FROM `{prefix_squads}` ORDER BY pos;");
        $esquads = '';
        foreach($qrySquads as $getsq) {
            $qrypos = $sql->select("SELECT `id`,`position` FROM `{prefix_positions}` ORDER BY pid;");
            $posi = "";
            foreach($qrypos as $getpos) {
                $sel = $sql->rows("SELECT `id` FROM `{prefix_userposis}` WHERE `posi` = ? AND `squad` = ? AND `user` = ?;",array($getpos['id'],$getsq['id'],intval($_GET['edit']))) ? 'selected="selected"' : '';
                $posi .= show(_select_field_posis, array("value" => $getpos['id'], "sel" => $sel, "what" => re($getpos['position'])));
            }

            $check = $sql->rows("SELECT `id` FROM `{prefix_squaduser}` WHERE `user` = ? AND `squad` = ?;", array(intval($_GET['edit']),$getsq['id'])) ? 'checked="checked"' : '';
            $esquads .= show(_checkfield_squads, array("id" => $getsq['id'],
                                                       "check" => $check,
                                                       "eposi" => $posi,
                                                       "squad" => re($getsq['name'])));
        }

        $index = show($dir . "/admin_self", array("showpos" => getrank($_GET['edit']), "esquad" => $esquads, "eposi" => $posi));
    } elseif (isset($_GET['edit']) && (data("level", intval($_GET['edit'])) == 4 || rootAdmin(intval($_GET['edit']))) && !rootAdmin($userid)) {
        $index = error(_error_edit_admin, 1);
    } else {
        if ($do == "identy") {
            if((data("level", intval($_GET['id'])) == 4 && !rootAdmin(intval($_GET['id'])) && !rootAdmin($userid))) {
                $index = error(_identy_admin, 1);
            } else {
                $msg = show(_admin_user_get_identy, array("nick" => autor($_GET['id'])));
                $sql->update("UPDATE `{prefix_users}` SET `online` = 0, `sessid` = '' WHERE id = ?;",array($userid)); //Logout
                session_regenerate_id();

                $_SESSION['id'] = intval($_GET['id']);
                $_SESSION['pwd'] = re(data("pwd", intval($_GET['id'])));
                $_SESSION['ip'] = $userip;

                $sql->update("UPDATE `{prefix_users}` SET `online` = 1, `sessid` = ?, `ip` = ? WHERE `id` = ?;",
                array(session_id(),$userip,intval($_GET['id'])));
                setIpcheck("ident(" . $userid . "_" . intval($_GET['id']) . ")");

                $index = info($msg, "?action=user&amp;id=" . $_GET['id'] . "");
            }
        } else if ($do == "update") {
            if ($_POST && isset($_GET['user'])) {
                $edituser = intval($_GET['user']);

                // Permissions Update
                if (empty($_POST['perm'])) {
                    $_POST['perm'] = array();
                }

                $qry_fields = $sql->show("SHOW FIELDS FROM `{prefix_permissions}`;");
                $sql_update = '';
                foreach($qry_fields as $get) {
                    if ($get['Field'] != 'id' && $get['Field'] != 'user' && $get['Field'] != 'pos' && $get['Field'] != 'intforum') {
                        $sql_qry = array_key_exists('p_' . $get['Field'], $_POST['perm']) ? '`' . $get['Field'] . '` = 1' : '`' . $get['Field'] . '` = 0';
                        $sql_update .= $sql_qry . ', ';
                    }
                }

                // Check User Permissions is exists
                if (!$sql->rows('SELECT `id` FROM `{prefix_permissions}` WHERE `user` = ? LIMIT 1;',array($edituser))) {
                    $sql->insert("INSERT INTO `{prefix_permissions}` SET `user` = ?;",array($edituser));
                }

                // Update Permissions
                $sql->update('UPDATE `{prefix_permissions}` SET '.substr($sql_update, 0, -2).' WHERE `user` = ?;',array($edituser));

                // Internal Boardpermissions Update
                if (empty($_POST['board'])) {
                    $_POST['board'] = array();
                }

                // Boardpermissions Cleanup
                $qry = $sql->select('SELECT `id`,`forum` FROM `{prefix_f_access}` WHERE `user` = ?;',array($edituser));
                foreach($qry as $get) {
                    if (!array_var_exists($get['forum'], $_POST['board'])) {
                        $sql->delete('DELETE FROM `{prefix_f_access}` WHERE `id` = ?;',array($get['id']));
                    }
                }

                //Add new Boardpermissions
                if (count($_POST['board']) >= 1) {
                    foreach ($_POST['board'] AS $boardpem) {
                        if (!$sql->rows("SELECT `id` FROM `{prefix_f_access}` WHERE `user` = ? AND `forum` = ?;", array($edituser,$boardpem))) {
                            $sql->insert("INSERT INTO `{prefix_f_access}` SET `user` = ?, `forum` = ?;",array($edituser,$boardpem));
                        }
                    }
                }

                $sql->delete("DELETE FROM `{prefix_squaduser}` WHERE `user` = ?;",array($edituser));
                $sql->delete("DELETE FROM `{prefix_userposis}` WHERE `user` = ?;",array($edituser));

                $sq = $sql->select("SELECT `id` FROM `{prefix_squads}`;");
                foreach($sq as $getsq) {
                    if (isset($_POST['squad' . $getsq['id']])) {
                        $sql->insert("INSERT INTO `{prefix_squaduser}` SET `user` = ?, `squad`  = ?;",
                        array($edituser,intval($_POST['squad' . $getsq['id']])));
                    }

                    if (isset($_POST['squad' . $getsq['id']])) {
                        $sql->insert("INSERT INTO {prefix_userposis} SET `user` = ?, `posi` = ?, `squad` = ?;",
                        array($edituser,intval($_POST['sqpos' . $getsq['id']]),intval($getsq['id'])));
                    }
                }

                $level = intval($_POST['level']);
                if(permission("editusers") && data("level") != 4 && !rootAdmin($userid) && $level == 4) {
                    $level = data("level",$edituser);
                }
                
                $newpwd = !empty($_POST['passwd']) ? "`pwd` = '" . md5($_POST['passwd']) . "'," : "";
                $update_level = $_POST['level'] == 'banned' ? 0 : $level;
                $update_banned = $_POST['level'] == 'banned' ? 1 : 0;
                $sql->update("UPDATE {prefix_users} SET ".$newpwd." "
                        . "`nick`   = ?, "
                        . "`email`  = ?, "
                        . "`user`   = ?, "
                        . "`listck` = ?, "
                        . "`level`  = ?, "
                        . "`banned`  = ? "
                        . "WHERE `id` = ?;",
                        array(up($_POST['nick']),up($_POST['email']),up($_POST['loginname']),(isset($_POST['listck']) ? intval($_POST['listck']) : 0),
                        intval($update_level),intval($update_banned),$edituser));

                setIpcheck("upduser(" . $userid . "_" . $edituser . ")");
            }

            $index = info(_admin_user_edited, "?action=userlist");
        } elseif ($do == "updateme") {
            $sql->delete("DELETE FROM `{prefix_squaduser}` WHERE `user` = ?;",array($userid));
            $sql->delete("DELETE FROM `{prefix_userposis}` WHERE `user` = ?;",array($userid));

            $squads = $sql->select("SELECT `id` FROM `{prefix_squads}`;");
            foreach($squads as $getsq) {
                if (isset($_POST['squad' . $getsq['id']])) {
                    $sql->insert("INSERT INTO `{prefix_squaduser}` SET `user`  = ?, `squad` = ?;",
                    array(intval($userid),intval($_POST['squad' . $getsq['id']])));
                }

                if (isset($_POST['squad' . $getsq['id']])) {
                    $sql->insert("INSERT INTO `{prefix_userposis}` SET `user` = ?, `posi` = ?, `squad`  = ?",
                    array(intval($userid),intval($_POST['sqpos'.$getsq['id']]),intval($getsq['id'])));
                }
            }

            $index = info(_admin_user_edited, "?action=user&amp;id=" . $userid . "");
        } elseif ($do == "delete") {
            $index = show(_user_delete_verify, array("user" => autor(intval($_GET['id'])), "id" => $_GET['id']));
            if ($_GET['verify'] == "yes") {
                if (data("level", intval($_GET['id'])) == 4 || data("level", intval($_GET['id'])) == 3 || rootAdmin($delUID))
                    $index = error(_user_cant_delete_admin, 2);
                else {
                    $delUID = intval($_GET['id']);
                    if($delUID >= 1) {
                        setIpcheck("deluser(" . $userid . "_" . $delUID . ")");
                        $sql->update("UPDATE `{prefix_forumposts}` SET `reg` = 0 WHERE `reg` = ?;",array($delUID));
                        $sql->update("UPDATE `{prefix_forumthreads}` SET `t_reg` = 0 WHERE `t_reg` = ?;",array($delUID));
                        $sql->update("UPDATE `{prefix_gb}` SET `reg` = 0 WHERE `reg` = ?;",array($delUID));
                        $sql->update("UPDATE `{prefix_newscomments}` SET `reg` = 0 WHERE `reg` = ?;",array($delUID));
                        $sql->delete("DELETE FROM `{prefix_messages}` WHERE `von` = ? OR `an` = ?;",array($delUID,$delUID));
                        $sql->delete("DELETE FROM `{prefix_news}` WHERE `autor` = ?;",array($delUID));
                        $sql->delete("DELETE FROM `{prefix_permissions}` WHERE `user` = ?;",array($delUID));
                        $sql->delete("DELETE FROM `{prefix_squaduser}` WHERE `user` = ?;",array($delUID));
                        $sql->delete("DELETE FROM `{prefix_userbuddys}` WHERE `user` = ? OR `buddy` = ?;",array($delUID,$delUID));
                        $sql->update("UPDATE `{prefix_usergb}` SET `reg` = 0 WHERE `reg` = ?;",array($delUID));
                        $sql->delete("DELETE FROM `{prefix_userposis}` WHERE `user` = ?;",array($delUID));
                        $sql->delete("DELETE FROM `{prefix_users}` WHERE `id` = ?;",array($delUID));
                        $sql->delete("DELETE FROM `{prefix_userstats}` WHERE `user` = ?;",array($delUID));
                        $sql->delete("DELETE FROM `{prefix_clicks_ips}` WHERE `uid` = ?;",array($delUID));
                        $index = info(_user_deleted, "?action=userlist");
                    }
                }
            }
        } else {
            $get = $sql->fetch("SELECT `id`,`user`,`nick`,`pwd`,`email`,`level`,`position`,`listck` "
                                    . "FROM `{prefix_users}` "
                                    . "WHERE `id` = ?;",array(intval($_GET['edit'])));
            if ($sql->rowCount()) {
                $where = _user_profile_of . 'autor_'.intval($_GET['edit']);
                $qrysq = $sql->select("SELECT `id`,`name` FROM `{prefix_squads}` ORDER BY `pos`;");
                $esquads = '';
                foreach($qrysq as $getsq) {
                    $qrypos = $sql->select("SELECT `id`,`position` FROM `{prefix_positions}` ORDER BY `pid`;");
                    $posi = "";
                    foreach($qrypos as $getpos) {
                        $check = $sql->rows("SELECT `id` FROM `{prefix_userposis}` WHERE `posi` = ? AND `squad` = ? AND `user` = ?;", 
                        array($getpos['id'],$getsq['id'],intval($_GET['edit'])));
                        $sel = $check ? 'selected="selected"' : '';
                        $posi .= show(_select_field_posis, array("value" => $getpos['id'], "sel" => $sel, "what" => re($getpos['position'])));
                    }

                    $checksquser = $sql->rows("SELECT `squad` FROM `{prefix_squaduser}` WHERE `user` = ? AND `squad` = ?;", 
                    array(intval($_GET['edit']),$getsq['id']));
                    $check = $checksquser ? 'checked="checked"' : '';
                    $esquads .= show(_checkfield_squads, array("id" => $getsq['id'],
                                                               "check" => $check,
                                                               "eposi" => $posi,
                                                               "squad" => re($getsq['name'])));
                }

                $get_identy = show(_admin_user_get_identitat, array("id" => intval($_GET['edit'])));
                $editpwd = show($dir . "/admin_editpwd", array("epwd" => ""));

                $selu = $get['level'] == 1 ? 'selected="selected"' : '';
                $selt = $get['level'] == 2 ? 'selected="selected"' : '';
                $selm = $get['level'] == 3 ? 'selected="selected"' : '';
                $sela = $get['level'] == 4 ? 'selected="selected"' : '';
                if ($chkMe == 4) {
                    $elevel = show(_elevel_admin_select, array("selu" => $selu,
                                                               "selt" => $selt,
                                                               "selm" => $selm,
                                                               "sela" => $sela));
                } elseif (permission("editusers")) {
                    $elevel = show(_elevel_perm_select, array("selu" => $selu,
                                                              "selt" => $selt,
                                                              "selm" => $selm));
                }

                $index = show($dir . "/admin", array("enick" => re($get['nick']),
                                                     "user" => intval($_GET['edit']),
                                                     "eemail" => re($get['email']),
                                                     "eloginname" => $get['user'],
                                                     "esquad" => $esquads,
                                                     "editpwd" => $editpwd,
                                                     "eposi" => $posi,
                                                     "getpermissions" => getPermissions(intval($_GET['edit'])),
                                                     "getboardpermissions" => getBoardPermissions(intval($_GET['edit'])),
                                                     "forenrechte" => _config_positions_boardrights,
                                                     "showpos" => getrank($_GET['edit']),
                                                     "listck" => (empty($get['listck']) ? '' : ' checked="checked"'),
                                                     "alvl" => $get['level'],
                                                     "elevel" => $elevel,
                                                     "get" => $get_identy));
            } else
                $index = error(_user_dont_exist, 1);
        }
    }
}