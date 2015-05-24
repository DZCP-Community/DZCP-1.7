<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    $where = _user_profile_of.'autor_'.$_GET['id'];
    $get = $sql->selectSingle("SELECT * FROM `{prefix_users}` WHERE `id` = ?;",array(intval($_GET['id'])));
    if (!$sql->rowCount()) {
        $index = error(_user_dont_exist, 1);
    } else {
        if (($userid != $get['id']) && (($get['profile_access'] >= 1 && checkme() == 'unlogged') || ($get['profile_access'] >= 2 && checkme() <= 1) || ($get['profile_access'] >= 3 && checkme() != 4))) {
            $index = error(_profile_access_error, 1);
        } else {
            if (count_clicks('userprofil', $get['id'])) {
                $sql->update("UPDATE `{prefix_userstats}` SET `profilhits` = (profilhits+1) WHERE `user` = ?;",array($get['id']));
            } //Update Userstats

            $sex = $get['sex'] == 1 ? _male : ($get['sex'] == 2 ? _female : '-');
            $hp = empty($get['hp']) ? "-" : "<a href=\"" . $get['hp'] . "\" target=\"_blank\">" . $get['hp'] . "</a>";
            $email = empty($get['email']) ? "-" : CryptMailto(re($get['email']), _user_mailto_texttop);
            $pn = show(_pn_write, array("id" => $_GET['id'], "nick" => $get['nick']));
            $hlsw = empty($get['hlswid']) ? "-" : show(_hlswicon, array("id" => re($get['hlswid']), "img" => "1", "css" => ""));
            $xboxu = empty($get['xboxid']) ? "-" : show(_xboxicon, array("id" => str_replace(" ", "%20", trim(re($get['xboxid']))), "img" => "1", "css" => ""));
            $xboxuser = empty($get['xboxid']) ? _noxboxavatar : show(_xboxpic, array("id" => str_replace(" ", "%20", trim(re($get['xboxid']))), "img" => "1", "css" => ""));
            $psnu = empty($get['psnid']) ? "-" : show(_psnicon, array("id" => str_replace(" ", "%20", trim(re($get['psnid']))), "img" => "1", "css" => ""));
            $originu = empty($get['originid']) ? '-' : show(_originicon, array("id" => str_replace(" ", "%20", trim(re($get['originid']))), "img" => "1", "css" => ""));
            $battlenetu = empty($get['battlenetid']) ? '-' : show(_battleneticon, array("id" => str_replace(" ", "%20", trim(re($get['battlenetid']))), "img" => "1", "css" => ""));
            $bday = (!$get['bday'] || empty($get['bday'])) ? "-" : date('d.m.Y', $get['bday']);

            $icq = "-";
            $icqnr = '';
            if (!empty($get['icq'])) {
                $icq = show(_icqstatus, array("uin" => re($get['icq'])));
                $icqnr = re($get['icq']);
            }

            $status = ($get['status'] == 1 || ($get['level'] != 1 && isset($_GET['sq']))) ? _aktiv_icon : _inaktiv_icon;
            $clan = "";
            if ($get['level'] != 1 || isset($_GET['sq'])) {
                $sq = $sql->select("SELECT * FROM `{prefix_userposis}` WHERE `user` = ?;",array($get['id']));
                $cnt = cnt('{prefix_userposis}', " WHERE `user` = ?",'id',array($get['id'])); $i = 1;

                if ($sql->rowCount() && !isset($_GET['sq'])) {
                    $pos = '';
                    foreach($sq as $getsq) {
                        $br = "-";
                        if ($i == $cnt) {
                            $br = "";
                        }

                        $pos .= " ".getrank($get['id'], $getsq['squad'], 1)." ".$br;
                        $i++;
                    }
                } elseif (isset($_GET['sq'])) {
                    $pos = getrank($get['id'], $_GET['sq'], 1);
                } else {
                    $pos = getrank($get['id']);
                }
                
                $pos = (empty($pos) ? '-' : $pos);

                //Custom Profile * CLAN *
                $qrycustom = $sql->select("SELECT * "
                                        . "FROM `{prefix_profile}` "
                                        . "WHERE kid = 2 AND shown = 1 "
                                        . "ORDER BY id ASC;");
                $custom_clan = '';
                foreach($qrycustom as $getcustom) {
                    $getcontent = $sql->selectSingle("SELECT `".$getcustom['feldname']."` "
                                                   . "FROM `{prefix_users}` "
                                                   . "WHERE `id` = ? "
                                                   . "LIMIT 1;", 
                                  array($get['id']));
                    if (!empty($getcontent[$getcustom['feldname']])) {
                        if ($getcustom['type'] == 2) {
                            $custom_clan .= show(_profil_custom_url, array("name" => pfields_name(re($getcustom['name'])), "value" => re($getcontent[$getcustom['feldname']])));
                        } else if ($getcustom['type'] == 3) {
                            $custom_clan .= show(_profil_custom_mail, array("name" => pfields_name(re($getcustom['name'])), "value" => CryptMailto(re($getcontent[$getcustom['feldname']]), _link_mailto)));
                        } else {
                            $custom_clan .= show(_profil_custom, array("name" => pfields_name(re($getcustom['name'])), "value" => re($getcontent[$getcustom['feldname']])));
                        }
                    }
                }

                $clan = show($dir . "/clan", array("position" => $pos,
                                                   "status" => $status,
                                                   "custom_clan" => $custom_clan));
            }

            $buddyadd = show(_addbuddyicon, array("id" => $_GET['id']));

            $edituser = "";
            if (permission("editusers")) {
                $edituser = str_replace("&amp;id=", "", show("page/button_edit_single", array("id" => "",
                                                                                              "action" => "action=admin&amp;edit=" . $_GET['id'],
                                                                                              "title" => _button_title_edit)));
            }

            if (isset($_GET['show']) && $_GET['show'] == "gallery") {
                $qrygl = $sql->select("SELECT * FROM `{prefix_usergallery}` "
                                    . "WHERE `user` = ? "
                                    . "ORDER BY `id` DESC;",
                                array($get['id']));
                $qryperm = $sql->selectSingle("SELECT `id`,`perm_gallery` "
                                            . "FROM `{prefix_users}` "
                                            . "WHERE `id` = ?;", 
                                array($get['id']));
                $qryuser = $sql->selectSingle("SELECT `level` "
                                            . "FROM `{prefix_users}` "
                                            . "WHERE `id` = ?;", 
                                array($userid)); 
                $gal = '';
                if (!$qryperm['perm_gallery'] || $qryperm['perm_gallery'] < $qryuser['level'] || $qryperm['id'] == $userid) {
                    foreach ($qrygl as $getgl) {
                        $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst";
                        $color++;
                        $gal .= show($dir . "/profil_gallery_show", array("picture" => img_size("inc/images/uploads/usergallery" . "/" . $qryperm['id'] . "_" . $getgl['pic']),
                                                                          "beschreibung" => bbcode($getgl['beschreibung']),
                                                                          "class" => $class));
                    }

                    if (empty($gal)) {
                        $gal = show(_no_entrys_yet, array("colspan" => "3"));
                    }

                    $show = show($dir . "/profil_gallery", array("showgallery" => $gal));
                } else {
                    $show = _gallery_no_perm;
                }
            } else if(isset($_GET['show']) && $_GET['show'] == "gb") {
                $addgb = show(_usergb_eintragen, array("id" => $_GET['id']));
                $qrygb = $sql->select("SELECT * FROM `{prefix_usergb}` "
                                    . "WHERE `user` = ? "
                                    . "ORDER BY `datum` DESC "
                                    . "LIMIT ".($page - 1) * config('m_usergb').",".config('m_usergb').";",array($get['id']));

                $entrys = cnt('{prefix_usergb}', " WHERE `user` = ".$get['id']);
                $i = $entrys - ($page - 1) * config('m_usergb');

                $membergb = '';
                foreach($qrygb as $getgb) {
                    $gbhp = $getgb['hp'] ? show(_hpicon, array("hp" => re($getgb['hp']))) : "";
                    $gbemail = $getgb['email'] ? CryptMailto(re($getgb['email']), _emailicon) : "";
                    $edit = ""; $delete = "";
                    if (permission('editusers') || $_GET['id'] == $userid) {
                        $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                                      "action" => "action=user&amp;show=gb&amp;do=edit&amp;gbid=" . $getgb['id'],
                                                                      "title" => _button_title_edit));

                        $delete = show("page/button_delete_single", array("id" => $_GET['id'],
                                                                          "action" => "action=user&amp;show=gb&amp;do=delete&amp;gbid=" . $getgb['id'],
                                                                          "title" => _button_title_del,
                                                                          "del" => convSpace(_confirm_del_entry)));
                    }

                    if (!$getgb['reg']) {
                        $www = "";
                        $hp = $getgb['hp'] ? show(_hpicon_forum, array("hp" => re($getgb['hp']))) : "";
                        $email = $getgb['email'] ? '<br />' . CryptMailto(re($getgb['email']), _emailicon_forum) : "";
                        $onoff = "";
                        $avatar = "";
                        $nick = CryptMailto(re($getgb['email']), _link_mailto, array("nick" => re($getgb['nick'])));
                    } else {
                        $www = data("hp", $getgb['reg']);
                        $hp = empty($www) ? '' : show(_hpicon_forum, array("hp" => $www));
                        $email = '<br />' . CryptMailto(re(data("email", $getgb['reg'])), _emailicon_forum);
                        $onoff = onlinecheck($getgb['reg']);
                        $nick = autor($getgb['reg']);
                    }

                    $titel = show(_eintrag_titel, array("postid" => $i,
                                                        "datum" => date("d.m.Y", $getgb['datum']),
                                                        "zeit" => date("H:i", $getgb['datum']) . _uhr,
                                                        "edit" => $edit,
                                                        "delete" => $delete));

                    $posted_ip = ($chkMe == 4 || permission('ipban') ? $getgb['ip'] : _logged);
                    $membergb .= show("page/comments_show", array("titel" => $titel,
                                                                  "comment" => bbcode($getgb['nachricht']),
                                                                  "nick" => $nick,
                                                                  "hp" => $hp,
                                                                  "editby" => bbcode($getgb['editby']),
                                                                  "email" => $email,
                                                                  "avatar" => useravatar($getgb['reg']),
                                                                  "onoff" => $onoff,
                                                                  "rank" => getrank($getgb['reg']),
                                                                  "ip" => $posted_ip));
                    $i--;
                }

                if (empty($membergb)) {
                    $membergb = show(_no_entrys_yet, array("colspan" => "1"));
                }

                $add = "";
                if (!ipcheck("mgbid(" . $_GET['id'] . ")", config('f_membergb'))) {
                    if ($userid >= 1) {
                        $form = show("page/editor_regged", array("nick" => autor($userid),
                                                                 "von" => _autor));
                    } else {
                        $form = show("page/editor_notregged", array("postnick" => '',
                                                                    "posthp" => '',
                                                                    "postemail" => ""));
                    }

                    $add = show($dir . "/usergb_add", array("form" => $form,
                                                            "ed" => "&amp;uid=" . $_GET['id'],
                                                            "whaturl" => "add",
                                                            "reg" => "",
                                                            "posteintrag" => "",
                                                            "id" => $_GET['id'],
                                                            "posteintrag" => "",
                                                            "error" => ""));
                }
                
                $seiten = nav($entrys, config('m_usergb'), "?action=user&amp;id=" . $_GET['id'] . "&show=gb");
                $qryperm = $sql->selectSingle("SELECT `perm_gb` FROM `{prefix_users}` WHERE id = ?;", array(intval($_GET['id'])));
                $add = $qryperm['perm_gb'] != 1 ? "" : $add;
                $show = show($dir . "/profil_gb", array("gbhead" => _membergb,
                                                        "show" => $membergb,
                                                        "seiten" => $seiten,
                                                        "entry" => $add));
            } else {
                $custom_about = custom_content(1);
                $custom_contact = custom_content(3);
                $custom_hardware = custom_content(5);
                $hardware_head = '';
                if ($custom_hardware['count'] != 0) {
                    $hardware_head = show(_profil_head_cont, array("what" => _profil_hardware));
                }

                $custom_favos = custom_content(4);
                $favos_head = '';
                if ($custom_favos['count'] != 0) {
                    $favos_head = show(_profil_head_cont, array("what" => _profil_favos));
                }

                $rlname = $get['rlname'] ? re($get['rlname']) : "-";
                $skypename = $get['skypename'] ? "<a href=\"skype:" . $get['skypename'] . "?chat\"><img src=\"http://mystatus.skype.com/smallicon/" . $get['skypename'] . "\" style=\"border: none;\" width=\"16\" height=\"16\" alt=\"" . $get['skypename'] . "\"/></a>" : "-";
                $steam = (!empty($get['steamid']) && steam_enable ? '<div id="infoSteam_' . md5(re($get['steamid'])) . '"><div style="width:100%;text-align:center"><img src="../inc/images/ajax-loader-mini.gif" alt="" /></div><script language="javascript" type="text/javascript">DZCP.initDynLoader("infoSteam_' . md5(re($get['steamid'])) . '","steam","&steamid=' . re($get['steamid']) . '");</script></div>' : '-');

                $city = re($get['city']);
                $beschreibung = bbcode($get['beschreibung']);
                $show = show($dir."/profil_show", array("hardware_head" => $hardware_head,
                                                        "country" => flag($get['country']),
                                                        "city" => (empty($city) ? '-' : $city),
                                                        "logins" => userstats("logins", $_GET['id']),
                                                        "hits" => userstats("hits", $_GET['id']),
                                                        "msgs" => userstats("writtenmsg", $_GET['id']),
                                                        "forenposts" => userstats("forumposts", $_GET['id']),
                                                        "votes" => userstats("votes", $_GET['id']),
                                                        "cws" => userstats("cws", $_GET['id']),
                                                        "regdatum" => date("d.m.Y H:i", $get['regdatum']) . _uhr,
                                                        "lastvisit" => date("d.m.Y H:i", userstats("lastvisit", $_GET['id'])) . _uhr,
                                                        "hp" => $hp,
                                                        "xfire" => re($get['hlswid']),
                                                        "xboxx" => re($get['xboxid']),
                                                        "psnn" => re($get['psnid']),
                                                        "originn" => re($get['originid']),
                                                        "battlenett" => re($get['battlenetid']),
                                                        "buddyadd" => $buddyadd,
                                                        "nick" => autor($get['id']),
                                                        "rlname" => $rlname,
                                                        "bday" => $bday,
                                                        "age" => getAge($get['bday']),
                                                        "sex" => $sex,
                                                        "email" => $email,
                                                        "icq" => $icq,
                                                        "icqnr" => $icqnr,
                                                        "skypename" => $skypename,
                                                        "skype" => $get['skypename'],
                                                        "pn" => $pn,
                                                        "edituser" => $edituser,
                                                        "hlswid" => $hlsw,
                                                        "xboxid" => $xboxu,
                                                        "xboxavatar" => $xboxuser,
                                                        "psnid" => $psnu,
                                                        "originid" => $originu,
                                                        "battlenetid" => $battlenetu,
                                                        "steam" => $steam,
                                                        "onoff" => onlinecheck($get['id']),
                                                        "clan" => $clan,
                                                        "picture" => userpic($get['id']),
                                                        "favos_head" => $favos_head,
                                                        "position" => getrank($get['id']),
                                                        "status" => $status,
                                                        "ich" => (empty($beschreibung) ? '-' : $beschreibung),
                                                        "custom_about" => $custom_about['content'],
                                                        "custom_contact" => $custom_contact['content'],
                                                        "custom_favos" => $custom_favos['content'],
                                                        "custom_hardware" => $custom_hardware['content']));
            }

            $navi_profil = show(_profil_navi_profil, array("id" => $_GET['id']));
            $navi_gb = show(_profil_navi_gb, array("id" => $_GET['id']));
            $navi_gallery = show(_profil_navi_gallery, array("id" => $_GET['id']));
            $profil_head = show(_profil_head, array("profilhits" => userstats("profilhits", $_GET['id'])));
            $index = show($dir . "/profil", array("profilhead" => $profil_head,
                                                  "show" => $show,
                                                  "nick" => autor($_GET['id']),
                                                  "profil" => $navi_profil,
                                                  "gb" => $navi_gb,
                                                  "gallery" => $navi_gallery));

            switch ($do) {
                case 'delete':
                    if ($chkMe == 4 || intval($_GET['id']) == $userid) {
                        $sql->delete("DELETE FROM `{prefix_usergb}` "
                                   . "WHERE `user` = ? AND `id` = ?;",
                                array(intval($_GET['id']),intval($_GET['gbid'])));
                        $index = info(_gb_delete_successful, "?action=user&amp;id=" . $_GET['id'] . "&show=gb");
                    } else {
                        $index = error(_error_wrong_permissions, 1);
                    }
                break;
                case 'edit':
                    $get = $sql->selectSingle("SELECT * FROM `{prefix_usergb}` "
                                            . "WHERE `id` = ?;", array(intval($_GET['gbid'])));

                    if ($get['reg'] == $userid || permission('editusers')) {
                            if ($get['reg'] != 0) {
                                $form = show("page/editor_regged", array("nick" => autor($get['reg']), "von" => _autor));
                            } else {
                                $form = show("page/editor_notregged", array("postemail" => re($get['email']),
                                                                            "posthp" => re($get['hp']),
                                                                            "postnick" => re($get['nick'])));
                            }

                            $index = show($dir . "/usergb_edit", array("whaturl" => "edit&gbid=" . $_GET['gbid'],
                                                                       "ed" => "&amp;do=edit&amp;uid=" . $_GET['id'] . "&amp;gbid=" . $_GET['gbid'],
                                                                       "reg" => $get['reg'],
                                                                       "id" => $_GET['id'],
                                                                       "form" => $form,
                                                                       "postemail" => re($get['email']),
                                                                       "posthp" => $get['hp'],
                                                                       "postnick" => re($get['nick']),
                                                                       "posteintrag" => re($get['nachricht']),
                                                                       "error" => ''));
                        } else {
                            $index = error(_error_edit_post, 1);
                        }
                    break;
            }
        }
    }
}