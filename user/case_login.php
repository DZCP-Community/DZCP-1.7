<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    $where = _site_user_login;
    if($do == "yes") {
        ## Prüfe ob der Secure Code aktiviert ist und richtig eingegeben wurde ##
        switch (isset($_GET['from']) ? $_GET['from'] : 'default') {
            case 'menu': $securimage->namespace = 'menu_login'; break;
            default: $securimage->namespace = 'default'; break;
        }

        if (settings('securelogin') && (!isset($_POST['secure']) || !$securimage->check($_POST['secure']))) {
            $index = error(captcha_mathematic ? _error_invalid_regcode_mathematic : _error_invalid_regcode);
        } else {
            if($sql->rows("SELECT `id` FROM `{prefix_users}` WHERE `user` = ? AND `pwd` = ? AND `level` != 0;",array(up($_POST['user']),md5($_POST['pwd'])))) {
                $get = $sql->selectSingle("SELECT `id`,`user`,`nick`,`pwd`,`email`,`level`,`time` "
                        . "FROM `{prefix_users}` "
                        . "WHERE `user` = ? AND `pwd` = ? AND `level` != 0;", 
                array(up($_POST['user']), md5($_POST['pwd'])));
                if (!isBanned($get['id'])) {
                    $permanent_key = '';
                    if (isset($_POST['permanent'])) {
                        cookie::put('id', $get['id']);
                        $permanent_key = md5(mkpwd(8));
                        $gethostbyaddr = gethostbyaddr($userip);
                        if ($sql->rows("SELECT `id` FROM `{prefix_autologin}` WHERE `host` = ?;", array($gethostbyaddr)) >= 1) {
                            //Update Autologin
                            $sql->update("UPDATE `{prefix_autologin}` "
                                    . "SET `ssid` = ?,"
                                    . "`pkey` = ?,"
                                    . "`ip` = ?,"
                                    . "`date` = ?,"
                                    . "`update` = ?,"
                                    . "`expires` = ? "
                                    . "WHERE `host` = ?;",
                            array(session_id(), $permanent_key, $userip, $time = time(), $time, autologin_expire, $gethostbyaddr));
                        } else {
                            //Insert Autologin
                            $sql->insert("INSERT INTO `{prefix_autologin}` "
                                    . "SET `uid` = ?, "
                                    . "`ssid` = ?, "
                                    . "`pkey` = ?, "
                                    . "`ip` = ?, "
                                    . "`name` = ?, "
                                    . "`host` = ?, "
                                    . "`date` = ?, "
                                    . "`update` = 0, "
                                    . "`expires` = ?;",
                            array($get['id'], session_id(), $permanent_key, $userip, cut($gethostbyaddr, 20), $gethostbyaddr, time(), autologin_expire));
                        }

                        cookie::put('pkey', $permanent_key);
                        cookie::save();
                    }

                    //Set Sessions
                    $_SESSION['id'] = $get['id'];
                    $_SESSION['pwd'] = $get['pwd'];
                    $_SESSION['lastvisit'] = $get['time'];
                    $_SESSION['ip'] = $userip;

                    $sql->update("UPDATE `{prefix_userstats}` SET `logins` = (logins+1) WHERE `user` = ?;", array($get['id']));
                    $sql->update("UPDATE `{prefix_users}` SET `online` = 1, `sessid` = ?, `ip` = ? WHERE `id` = ?;", array(session_id(), $userip, $get['id']));
                    setIpcheck("login(" . $get['id'] . ")");

                    //-> Aktualisiere Ip-Count Tabelle
                    $qry = $sql->select("SELECT `id` FROM `{prefix_clicks_ips}` WHERE `ip` = ? AND `uid` = 0;", array($userip));
                    if ($sql->rowCount() >= 1) {
                        foreach ($qry as $get_ci) {
                            $sql->update("UPDATE `{prefix_clicks_ips}` SET `uid` = ? WHERE `id` = ?;", array($get['id'], $get_ci['id']));
                        }
                    }

                    header("Location: ?action=userlobby");
                } else {
                    $index = error(_login_banned);
                }
            } else {
                $get = $sql->selectSingle("SELECT `id` FROM `{prefix_users}` WHERE `user` = ?;",array(up($_POST['user'])));
                if($sql->rowCount()) {
                    setIpcheck("trylogin(".$get['id'].")");
                }

                cookie::put('id', '');
                cookie::put('pkey', '');
                $index = error(_login_pwd_dont_match);
            }
        }
    } else {
        if (!$chkMe) {
            $index = show($dir . "/login", array("secure" => (settings('securelogin') ? show($dir . "/secure") : '')));
        } else {
            $index = error(_error_user_already_in, 1);
        }
    }
}