<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    $where = _site_user_editprofil;
    if (!$chkMe) {
        $index = error(_error_have_to_be_logged, 1);
    } else {
        //ToDO: Neu schreiben * Thumbgen *
        if (isset($_GET['gallery']) && $_GET['gallery'] == "delete") {
            $qrygl = db("SELECT * FROM " . $db['usergallery'] . " WHERE user = '" . $userid . "' AND id = '" . intval($_GET['gid']) . "'");
            while ($getgl = _fetch($qrygl)) {
                db("DELETE FROM " . $db['usergallery'] . " WHERE id = '" . intval($_GET['gid']) . "'");
                $unlinkgallery = show(_gallery_edit_unlink, array("img" => $getgl['pic'], "user" => $userid));
                if (file_exists($unlinkgallery)) {
                    unlink($unlinkgallery);
                }
            }

            $index = info(_info_edit_gallery_done, "?action=editprofile&show=gallery");
        } else {
            switch ($do) {
                case 'edit':
                    $check_user = db_stmt("SELECT id FROM " . $db['users'] . " WHERE `user`= ? AND id != ?", array('si', up($_POST['user']), $userid), true, false);
                    $check_nick = db_stmt("SELECT id FROM " . $db['users'] . " WHERE `nick`= ? AND id != ?", array('si', up($_POST['nick']), $userid), true, false);
                    $check_email = db_stmt("SELECT id FROM " . $db['users'] . " WHERE `email`= ? AND id != ?", array('si', up($_POST['email']), $userid), true, false);

                    if(isset($_POST['user']) || empty($_POST['user'])) {
                        $index = error(_empty_user, 1);
                    } elseif (!isset($_POST['nick']) || empty($_POST['nick'])) {
                        $index = error(_empty_nick, 1);
                    } elseif (!isset($_POST['email']) || empty($_POST['email'])) {
                        $index = error(_empty_email, 1);
                    } elseif (!isset($_POST['email']) || !check_email($_POST['email'])) {
                        $index = error(_error_invalid_email, 1);
                    } elseif ($check_user) {
                        $index = error(_error_user_exists, 1);
                    } elseif ($check_nick) {
                        $index = error(_error_nick_exists, 1);
                    } elseif ($check_email) {
                        $index = error(_error_email_exists, 1);
                    } else {
                        $index = info(_info_edit_profile_done, "?action=user&amp;id=" . $userid . "");
                        $newpwd = "";
                        
                        if (isset($_POST['pwd'])) {
                            if ($_POST['pwd'] == $_POST['cpwd']) {
                                $_SESSION['pwd'] = md5($_POST['pwd']);
                                $newpwd = "pwd = '" . $_SESSION['pwd'] . "',";
                                $index = info(_info_edit_profile_done, "?action=user&amp;id=" . $userid . "");
                            } else {
                                $index = error(_error_passwords_dont_match, 1);
                            }
                        }

                        $icq = preg_replace("=-=Uis", "", $_POST['icq']);
                        $bday = ($_POST['t'] && $_POST['m'] && $_POST['j'] ? cal($_POST['t']) . "." . cal($_POST['m']) . "." . $_POST['j'] : 0);

                        $qrycustom = db("SELECT feldname,type FROM " . $db['profile']); $customfields = '';
                        while ($getcustom = _fetch($qrycustom)) {
                            $customfields .= " " . $getcustom['feldname'] . " = '" . ($getcustom['type'] == 2 ? links($_POST[$getcustom['feldname']]) : up($_POST[$getcustom['feldname']])) . "', ";
                        }

                        db("UPDATE " . $db['users'] . " SET " . $newpwd . "
                                                            " . $customfields . "
                                                            `country`      = '" . $_POST['land'] . "',
                                                            `user`         = '" . up($_POST['user']) . "',
                                                            `nick`         = '" . up($_POST['nick']) . "',
                                                            `rlname`       = '" . up($_POST['rlname']) . "',
                                                            `sex`          = '" . intval( $_POST['sex']) . "',
                                                            `status`       = '" . intval( $_POST['status']) . "',
                                                            `bday`         = '" . (!$bday ? 0 : strtotime($bday)) . "',
                                                            `email`        = '" . up($_POST['email']) . "',
                                                            `nletter`      = '" . intval( $_POST['nletter']) . "',
                                                            `pnmail`       = '" . intval( $_POST['pnmail']) . "',
                                                            `city`         = '" . up($_POST['city']) . "',
                                                            `gmaps_koord`  = '" . up($_POST['gmaps_koord']) . "',
                                                            `hp`           = '" . links($_POST['hp']) . "',
                                                            `icq`          = '" . intval( $icq) . "',
                                                            `hlswid`       = '" . up(trim($_POST['hlswid'])) . "',
                                                            `xboxid`       = '" . up(trim($_POST['xboxid'])) . "',
                                                            `psnid`        = '" . up(trim($_POST['psnid'])) . "',
                                                            `originid`     = '" . up(trim($_POST['originid'])) . "',
                                                            `battlenetid`  = '" . up(trim($_POST['battlenetid'])) . "',
                                                            `steamid`      = '" . up(trim($_POST['steamid'])) . "',
                                                            `skypename`    = '" . up(trim($_POST['skypename'])) . "',
                                                            `signatur`     = '" . up($_POST['sig']) . "',
                                                            `beschreibung` = '" . up($_POST['ich']) . "',
                                                            `perm_gb`      = '" . up($_POST['visibility_gb']) . "',
                                                            `perm_gallery` = '" . up($_POST['visibility_gallery']) . "'
                                                        WHERE id = " . $userid);
            }
                break;
                case 'delete':
                    $getdel = db("SELECT `id`,`nick`,`email`,`hp` FROM " . $db['users'] . " WHERE `id` = '" . intval($userid) . "'",false,true);

                    db("UPDATE " . $db['f_threads'] . " SET `t_nick`   = '" . $getdel['nick'] . "',
                                                            `t_email`  = '" . $getdel['email'] . "',
                                                            `t_hp`     = '" . links($getdel['hp']) . "',
                                                            `t_reg`    = 0
                                                       WHERE t_reg     = " . $getdel['id'] . ";");

                    db("UPDATE " . $db['f_posts'] . " SET `nick`   = '" . $getdel['nick'] . "',
                                                          `email`  = '" . $getdel['email'] . "',
                                                          `hp`     = '" . links($getdel['hp']) . "',
                                                    WHERE `reg`    = " . $getdel['id'] . ";");

                    db("UPDATE " . $db['newscomments'] . " SET `nick`     = '" . $getdel['nick'] . "',
                                                               `email`    = '" . $getdel['email'] . "',
                                                               `hp`       = '" . links($getdel['hp']) . "',
                                                               `reg`      = 0
                                                         WHERE `reg`      = " . $getdel['id'] . ";");

                    db("UPDATE " . $db['acomments'] . " SET `nick`     = '" . $getdel['nick'] . "',
                                                            `email`    = '" . $getdel['email'] . "',
                                                            `hp`       = '" . links($getdel['hp']) . "',
                                                            `reg`      = 0
                                                      WHERE `reg`      = " . $getdel['id'] . ";");

                    db("DELETE FROM " . $db['msg'] . " WHERE `von` = " . $getdel['id'] . "
                                                        OR   `an`  = " . $getdel['id'] . ";");

                    db("UPDATE " . $db['usergb'] . " SET `reg` = 0 WHERE `reg` = " . $getdel['id'] . ";");
                    db("DELETE FROM " . $db['news'] . " WHERE `autor` = " . $getdel['id'] . ";");
                    db("DELETE FROM " . $db['permissions'] . " WHERE `user` = " . $getdel['id'] . ";");
                    db("DELETE FROM " . $db['squaduser'] . " WHERE `user` = " . $getdel['id'] . ";");
                    db("DELETE FROM " . $db['buddys'] . " WHERE `user` = " . $getdel['id'] . "
                                                            OR `buddy` = " . $getdel['id'] . ";");
                    db("DELETE FROM " . $db['userpos'] . " WHERE `user` = " . $getdel['id'] . ";");
                    db("DELETE FROM " . $db['users'] . " WHERE `id` = " . $getdel['id'] . ";");
                    db("DELETE FROM " . $db['userstats'] . " WHERE `user` = " . $getdel['id'] . ";");
                    db("DELETE FROM " . $db['clicks_ips'] . " WHERE `uid` = " . $getdel['id']. ";");

                    //ToDO: Neu schreiben * Thumbgen *
                    foreach ($picformat as $tmpendung) {
                        if (file_exists(basePath . "/inc/images/uploads/userpics/" . intval($getdel['id']) . "." . $tmpendung)) {
                            @unlink(basePath . "/inc/images/uploads/userpics/" . intval($getdel['id']) . "." . $tmpendung);
                        }
                        
                        if (file_exists(basePath . "/inc/images/uploads/useravatare/" . intval($getdel['id']) . "." . $tmpendung)) {
                            @unlink(basePath . "/inc/images/uploads/useravatare/" . intval($getdel['id']) . "." . $tmpendung);
                        }
                    }

                    $index = info(_info_account_deletet, '../news/');
                break;
                default:
                    $get = db("SELECT * FROM " . $db['users'] . " WHERE `id` = " . $userid . ";", false, true);
                    $sex = ($get['sex'] == 1 ? _pedit_male : ($get['sex'] == 2 ? _pedit_female : _pedit_sex_ka));
                    $perm_gb = ($get['perm_gb'] ? _pedit_perm_allow : _pedit_perm_deny);
                    $status = ($get['status'] ? _pedit_aktiv : _pedit_inaktiv);

                    switch ($get['perm_gallery']) {
                        case 0: $perm_gallery = _pedit_perm_public;
                            break;
                        case 1: $perm_gallery = _pedit_perm_user;
                            break;
                        case 2: $perm_gallery = _pedit_perm_member;
                            break;
                    }

                    if ($get['level'] == 1) {
                        $clan = '<input type="hidden" name="status" value="1" />';
                    } else {
                        $qrycustom = db("SELECT `feldname`,`name` FROM " . $db['profile'] . " WHERE kid = 2 AND shown = 1 ORDER BY id ASC"); $custom_clan = "";
                        while ($getcustom = _fetch($qrycustom)) {
                            $getcontent = db("SELECT " . $getcustom['feldname'] . " FROM " . $db['users'] . " WHERE id = " . $userid . ";",false,true);
                            $custom_clan .= show(_profil_edit_custom, array("name" => pfields_name($getcustom['name']) . ":", 
                                                                            "feldname" => $getcustom['feldname'],
                                                                            "value" => re($getcontent[$getcustom['feldname']])));
                        }

                        $clan = show($dir . "/edit_clan", array("clan" => _profil_clan,
                                                                "pstatus" => _profil_status,
                                                                "pexclans" => _profil_exclans,
                                                                "status" => $status,
                                                                "exclans" => re($get['ex']),
                                                                "custom_clan" => $custom_clan));
                    }

                    $bdayday = 0; $bdaymonth = 0; $bdayyear = 0;
                    if (!empty($get['bday']) && $get['bday'])
                        list($bdayday, $bdaymonth, $bdayyear) = explode('.', date('d.m.Y', $get['bday']));

                    if (isset($_GET['show']) && $_GET['show'] == "gallery") {
                        $qrygl = db("SELECT * FROM " . $db['usergallery'] . " WHERE user = '" . $userid . "' ORDER BY id DESC"); $gal = "";
                        while ($getgl = _fetch($qrygl)) {
                            $pic = show(_gallery_pic_link, array("img" => $getgl['pic'], "user" => $userid));
                            $delete = show(_gallery_deleteicon, array("id" => $getgl['id']));
                            $edit = show(_gallery_editicon, array("id" => $getgl['id']));
                            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst";
                            $color++;

                            $gal .= show($dir . "/edit_gallery_show", array("picture" => img_size("inc/images/uploads/usergallery" . "/" . $userid . "_" . $getgl['pic']),
                                                                            "beschreibung" => bbcode($getgl['beschreibung']),
                                                                            "class" => $class,
                                                                            "delete" => $delete,
                                                                            "edit" => $edit));
                        }
                        
                        $show = show($dir . "/edit_gallery", array("galleryhead" => _gallery_head,
                                                                   "pic" => _gallery_pic,
                                                                   "new" => _gallery_edit_new,
                                                                   "del" => _deleteicon_blank,
                                                                   "edit" => _editicon_blank,
                                                                   "beschr" => _gallery_beschr,
                                                                   "showgallery" => $gal));
                    } else {
                        $dropdown_age = show(_dropdown_date, array("day" => dropdown("day", $bdayday, 1),
                                                                   "month" => dropdown("month", $bdaymonth, 1),
                                                                   "year" => dropdown("year", $bdayyear, 1)));

                        $icq = (!empty($get['icq']) && $get['icq'] != 0 ? $get['icq'] : "");
                        $pnl = ($get['nletter'] ? 'checked="checked"' : '');
                        $pnm = ($get['pnmail'] ? 'checked="checked"' : '');
                        $gmaps = show('membermap/geocoder', array('form' => 'editprofil'));

                        $pic = userpic($get['id']); $deletepic = '';
                        if (!preg_match("#nopic#", $pic))
                            $deletepic = "| " . _profil_delete_pic;

                        $avatar = useravatar($get['id']); $deleteava = '';
                        if (!preg_match("#noavatar#", $avatar))
                            $deleteava = "| " . _profil_delete_ava;

                        if (rootAdmin($userid))
                            $delete = _profil_del_admin;
                        else
                            $delete = show("page/button_delete_account", array("id" => $get['id'],
                                                                               "action" => "action=editprofile&amp;do=delete",
                                                                               "value" => _button_title_del_account,
                                                                               "del" => convSpace(_confirm_del_account)));

                        $show = show($dir . "/edit_profil", array("hardware" => _profil_hardware,
                                                                  "hphead" => _profil_hp,
                                                                  "visibility" => _pedit_visibility,
                                                                  "pvisibility_gb" => _pedit_visibility_gb,
                                                                  "pvisibility_gallery" => _pedit_visibility_gallery,
                                                                  "country" => show_countrys($get['country']),
                                                                  "pcountry" => _profil_country,
                                                                  "about" => _profil_about,
                                                                  "picturehead" => _profil_pic,
                                                                  "contact" => _profil_contact,
                                                                  "preal" => _profil_real,
                                                                  "pnick" => _nick,
                                                                  "pemail1" => _email,
                                                                  "php" => _hp,
                                                                  "pava" => _profil_avatar,
                                                                  "pbday" => _profil_bday,
                                                                  "psex" => _profil_sex,
                                                                  "pname" => _loginname,
                                                                  "ppwd" => _new_pwd,
                                                                  "cppwd" => _pwd2,
                                                                  "picq" => _icq,
                                                                  "psig" => _profil_sig,
                                                                  "ppic" => _profil_ppic,
                                                                  "phlswid" => _hlswid,
                                                                  "xboxidl" => _xboxid,
                                                                  "psnidl" => _psnid,
                                                                  "skypeidl" => _skypeid,
                                                                  "originidl" => _originid,
                                                                  "battlenetidl" => _battlenetid,
                                                                  "pcity" => _profil_city,
                                                                  "city" => re($get['city']),
                                                                  "psteamid" => _steamid,
                                                                  "v_steamid" => re($get['steamid']),
                                                                  "skypename" => $get['skypename'],
                                                                  "nletter" => _profil_nletter,
                                                                  "pnmail" => _profil_pnmail,
                                                                  "pnl" => $pnl,
                                                                  "pnm" => $pnm,
                                                                  "pwd" => "",
                                                                  "dropdown_age" => $dropdown_age,
                                                                  "ava" => $avatar,
                                                                  "hp" => re($get['hp']),
                                                                  "gmaps" => $gmaps,
                                                                  "nick" => re($get['nick']),
                                                                  "name" => re($get['user']),
                                                                  "gmaps_koord" => re($get['gmaps_koord']),
                                                                  "rlname" => re($get['rlname']),
                                                                  "bdayday" => $bdayday,
                                                                  "bdaymonth" => $bdaymonth,
                                                                  "bdayyear" => $bdayyear,
                                                                  "sex" => $sex,
                                                                  "email" => re($get['email']),
                                                                  "visibility_gb" => $perm_gb,
                                                                  "visibility_gallery" => $perm_gallery,
                                                                  "icqnr" => $icq,
                                                                  "sig" => re_bbcode($get['signatur']),
                                                                  "hlswid" => $get['hlswid'],
                                                                  "xboxid" => $get['xboxid'],
                                                                  "psnid" => $get['psnid'],
                                                                  "originid" => $get['originid'],
                                                                  "battlenetid" => $get['battlenetid'],
                                                                  "clan" => $clan,
                                                                  "pic" => $pic,
                                                                  "editpic" => _profil_edit_pic,
                                                                  "editava" => _profil_edit_ava,
                                                                  "deleteava" => $deleteava,
                                                                  "deletepic" => $deletepic,
                                                                  "favos" => _profil_favos,
                                                                  "pich" => _profil_ich,
                                                                  "pposition" => _profil_position,
                                                                  "pstatus" => _profil_status,
                                                                  "position" => getrank($get['id']),
                                                                  "value" => _button_value_edit,
                                                                  "status" => $status,
                                                                  "sonst" => _profil_sonst,
                                                                  "custom_about" => getcustom(1),
                                                                  "custom_contact" => getcustom(3),
                                                                  "custom_favos" => getcustom(4),
                                                                  "custom_hardware" => getcustom(5),
                                                                  "ich" => re_bbcode($get['beschreibung']),
                                                                  "del" => _profil_del_account,
                                                                  "delete" => $delete));
                    }

                    $index = show($dir . "/edit", array("profilhead" => _profil_edit_head,
                                                        "editgallery" => _profil_edit_gallery_link,
                                                        "editprofil" => _profil_edit_profil_link,
                                                        "nick" => autor($get['id']),
                                                        "show" => $show));
                break;
            }
        }
    }
}