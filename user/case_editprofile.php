<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    $where = _site_user_editprofil;
    if (!$chkMe) {
        $index = error(_error_have_to_be_logged, 1);
    } else {
        if (isset($_GET['gallery']) && $_GET['gallery'] == "delete") {
            $getgl = $sql->fetch("SELECT `pic` FROM `{prefix_usergallery}` WHERE `user` = ? AND `id` = ?;",array($userid,intval($_GET['gid'])));
            if($sql->rowCount()) {
                $files = get_files(basePath."/inc/images/uploads/usergallery/",false,true,$picformat);
                foreach ($files as $file) {
                    $pic = explode('.', $getgl['pic']); $pic = $pic[0];
                    if(preg_match("#".$userid."_".$pic."_(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!= FALSE) {
                        $res = preg_match("#".$userid."_".$pic."_(.*)#",$file,$match);
                        if (file_exists(basePath."/inc/images/uploads/usergallery/".$userid."_".$pic."_".$match[1])) {
                            unlink(basePath."/inc/images/uploads/usergallery/".$userid."_".$pic."_".$match[1]);
                        }
                    }
                }

                if (file_exists(basePath . '/inc/images/uploads/usergallery/'.$userid.'_'.$getgl['pic'])) {
                    unlink(basePath . '/inc/images/uploads/usergallery/'.$userid.'_'.$getgl['pic']);
                }
                
                $sql->delete("DELETE FROM `{prefix_usergallery}` WHERE `id` = ?;",array(intval($_GET['gid'])));
            }

            $index = info(_info_edit_gallery_done, "?action=editprofile&show=gallery");
        } else {
            switch ($do) {
                case 'edit':
                    $check_user = false; $check_nick = false; $check_email = false;
                    if($sql->rows("SELECT `id` FROM `{prefix_users}` WHERE (`user`= ? OR `nick`= ? OR `email`= ?) AND `id` != ?;",
                            array(stringParser::encode($_POST['user']),stringParser::encode($_POST['nick']),stringParser::encode($_POST['email']),$userid))) {
                        $check_user  = $sql->rows("SELECT `id` FROM `{prefix_users}` WHERE `user` = ? AND `id` != ?;", array(stringParser::encode($_POST['user']),  $userid));
                        $check_nick  = $sql->rows("SELECT `id` FROM `{prefix_users}` WHERE `nick` = ? AND `id` != ?;", array(stringParser::encode($_POST['nick']),  $userid));
                        $check_email = $sql->rows("SELECT `id` FROM `{prefix_users}` WHERE `email`= ? AND `id` != ?;", array(stringParser::encode($_POST['email']), $userid));
                    }

                    if(!isset($_POST['user']) || empty($_POST['user'])) {
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
                        $newpwd = "";
                        if (isset($_POST['pwd']) && !empty($_POST['pwd'])) {
                            if(pwd_encoder($_POST['pwd']) == pwd_encoder($_POST['cpwd'])) {
                                $_SESSION['pwd'] = pwd_encoder($_POST['pwd']);
                                $newpwd = "`pwd` = '".stringParser::encode($_SESSION['pwd'])."',";
                                $newpwd .= "`pwd_encoder` = ".settings::get('default_pwd_encoder').",";
                            } else {
                                $index = error(_error_passwords_dont_match, 1);
                            }
                        }
                        
                        $bday = ($_POST['t'] && $_POST['m'] && $_POST['j'] ? cal($_POST['t']) . "." . cal($_POST['m']) . "." . $_POST['j'] : 0);
                        $qrycustom = $sql->select("SELECT `feldname`,`type` FROM `{prefix_profile}`"); $customfields = '';
                        foreach($qrycustom as $getcustom) {
                            $customfields .= " `".$getcustom['feldname']."` = '".($getcustom['type'] == 2 ? links($_POST[$getcustom['feldname']]) : stringParser::encode($_POST[$getcustom['feldname']]))."', ";
                        }
                        
                        if(empty($index) && (!dzcp_demo || (dzcp_demo && !rootAdmin($userid)))) {
                            $index = info(_info_edit_profile_done, "?action=user&amp;id=" . $userid . "");
                            $sql->update("UPDATE `{prefix_users}` SET " . $newpwd . " " . $customfields . " `country` = ?,`user` = ?, `nick` = ?, `rlname` = ?, `sex` = ?,`status` = ?, "
                            ."`bday` = ?, `email` = ?, `nletter` = ?, `pnmail` = ?, `city` = ?, `gmaps_koord` = ?, `hp` = ?, `icq` = ?, `hlswid` = ?, `xboxid` = ?, `psnid` = ?,"
                            ."`originid` = ?, `battlenetid` = ?,`steamid` = ?,`skypename` = ?,`signatur` = ?,`beschreibung` = ?, `perm_gb` = ?, `perm_gallery` = ?, `startpage` = ?, `profile_access` = ?"
                            ." WHERE id = ?;", array(stringParser::encode($_POST['land']),stringParser::encode($_POST['user']),stringParser::encode($_POST['nick']),stringParser::encode($_POST['rlname']),intval( $_POST['sex']),intval( $_POST['status']),
                                    (!$bday ? 0 : strtotime($bday)),stringParser::encode($_POST['email']),intval( $_POST['nletter']),intval( $_POST['pnmail']),stringParser::encode($_POST['city']),
                                    stringParser::encode($_POST['gmaps_koord']),stringParser::encode(links($_POST['hp'])),
                                intval($_POST['icq']),stringParser::encode(trim($_POST['hlswid'])),stringParser::encode(trim($_POST['xboxid'])),
                                    stringParser::encode(trim($_POST['psnid'])),stringParser::encode(trim($_POST['originid'])),stringParser::encode(trim($_POST['battlenetid'])),
                                stringParser::encode(trim($_POST['steamid'])),stringParser::encode(trim($_POST['skypename'])),
                                    stringParser::encode($_POST['sig']),stringParser::encode($_POST['ich']),stringParser::encode($_POST['visibility_gb']),
                                stringParser::encode($_POST['visibility_gallery']),intval($_POST['startpage']),intval($_POST['visibility_profile']),$userid));

                            $get = $sql->fetch("SELECT * FROM `{prefix_users}` WHERE `id` = ?;",array(intval($userid)));
                            dbc_index::setIndex('user_' . $get['id'], $get); //Update Cache
                        } else {
                            $index = error(_error_user_dont_change_in_demo, 1);
                        }
                    }
                break;
                case 'delete':
                    if(!rootAdmin($userid)) {
                        $getdel = $sql->fetch("SELECT `id`,`nick`,`email`,`hp` FROM `{prefix_users}` WHERE `id` = ?;",array($userid));
                        if($sql->rowCount()) {
                            $sql->update("UPDATE `{prefix_forumthreads}` SET `t_nick` = ?, `t_email` = ?, `t_hp` = ?, `t_reg` = 0, WHERE t_reg = ?;",
                            array($getdel['nick'],$getdel['email'],stringParser::encode(links($getdel['hp'])),$getdel['id']));
                            $sql->update("UPDATE `{prefix_forumposts}` SET `nick` = ?, `email` = ?, `hp` = ?, WHERE `reg` = ?;",
                            array($getdel['nick'],$getdel['email'],stringParser::encode(links($getdel['hp'])),$getdel['id']));
                            $sql->update("UPDATE `{prefix_newscomments}` SET `nick` = ?,`email` = ?, `hp` = ?, `reg` = 0, WHERE `reg` = ?;",
                            array($getdel['nick'],$getdel['email'],stringParser::encode(links($getdel['hp'])),$getdel['id']));
                            $sql->update("UPDATE `{prefix_acomments}` SET `nick` = ?, `email` = ?, `hp` = ?, `reg` = 0, WHERE `reg` = ?;",
                            array($getdel['nick'],$getdel['email'],stringParser::encode(links($getdel['hp'])),$getdel['id']));
                            $sql->delete("DELETE FROM `{prefix_messages}` WHERE `von` = ? OR   `an`  = ?;",array($getdel['id'],$getdel['id']));
                            $sql->update("UPDATE `{prefix_usergb}` SET `reg` = 0 WHERE `reg` = ?;",array($getdel['id']));
                            $sql->delete("DELETE FROM `{prefix_news}` WHERE `autor` = ?;",array($getdel['id']));
                            $sql->delete("DELETE FROM `{prefix_permissions}` WHERE `user` = ?;",array($getdel['id']));
                            $sql->delete("DELETE FROM `{prefix_squaduser}` WHERE `user` = ?;",array($getdel['id']));
                            $sql->delete("DELETE FROM `{prefix_userbuddys}` WHERE `user` = ? OR `buddy` = ?;",array($getdel['id'],$getdel['id']));
                            $sql->delete("DELETE FROM `{prefix_userstats}` WHERE `user` = ?;",array($getdel['id']));
                            $sql->delete("DELETE FROM `{prefix_users}` WHERE `id` = ?;",array($getdel['id']));
                            $sql->delete("DELETE FROM `{prefix_userstats}` WHERE `user` = ?;",array($getdel['id']));
                            $sql->delete("DELETE FROM `{prefix_clicks_ips}` WHERE `uid` = ?;",array($getdel['id']));

                            $qrygl = $sql->select("SELECT * FROM `{prefix_usergallery}` WHERE `user` = ?;",array($getdel['id']));
                            if($sql->rowCount()) {
                                foreach($qrygl as $getgl) {
                                    $files = get_files(basePath."/inc/images/uploads/usergallery/",false,true,$picformat);
                                    foreach ($files as $file) {
                                        $pic = explode('.', $getgl['pic']); $pic = $pic[0];
                                        if(preg_match("#".$getdel['id']."_".$pic."_(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!= FALSE) {
                                            $res = preg_match("#".$getdel['id']."_".$pic."_(.*)#",$file,$match);
                                            if (file_exists(basePath."/inc/images/uploads/usergallery/".$getdel['id']."_".$pic."_".$match[1])) {
                                                unlink(basePath."/inc/images/uploads/usergallery/".$getdel['id']."_".$pic."_".$match[1]);
                                            }
                                        }
                                    }

                                    if (file_exists(basePath . '/inc/images/uploads/usergallery/'.$getdel['id'].'_'.$getgl['pic'])) {
                                        unlink(basePath . '/inc/images/uploads/usergallery/'.$getdel['id'].'_'.$getgl['pic']);
                                    }

                                    $sql->delete("DELETE FROM `{prefix_usergallery}` WHERE `id` = ?;",array($getgl['id']));
                                }
                            }

                            $files = get_files(basePath."/inc/images/uploads/userpics/",false,true,$picformat);
                            foreach ($files as $file) {
                                if(preg_match("#".$getdel['id']."_(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!= FALSE) {
                                    $res = preg_match("#".$getdel['id']."_(.*)#",$file,$match);
                                    if (file_exists(basePath."/inc/images/uploads/userpics/".$getdel['id']."_".$match[1])) {
                                        unlink(basePath."/inc/images/uploads/userpics/".$getdel['id']."_".$match[1]);
                                    }
                                }
                            }

                            $files = get_files(basePath."/inc/images/uploads/useravatare/",false,true,$picformat);
                            foreach ($files as $file) {
                                if(preg_match("#".$getdel['id']."_(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!= FALSE) {
                                    $res = preg_match("#".$getdel['id']."_(.*)#",$file,$match);
                                    if (file_exists(basePath."/inc/images/uploads/useravatare/".$getdel['id']."_".$match[1])) {
                                        unlink(basePath."/inc/images/uploads/useravatare/".$getdel['id']."_".$match[1]);
                                    }
                                }
                            }

                            foreach ($picformat as $tmpendung) {
                                if (file_exists(basePath . "/inc/images/uploads/userpics/" . intval($getdel['id']) . "." . $tmpendung)) {
                                    @unlink(basePath . "/inc/images/uploads/userpics/" . intval($getdel['id']) . "." . $tmpendung);
                                }

                                if (file_exists(basePath . "/inc/images/uploads/useravatare/" . intval($getdel['id']) . "." . $tmpendung)) {
                                    @unlink(basePath . "/inc/images/uploads/useravatare/" . intval($getdel['id']) . "." . $tmpendung);
                                }
                            }

                            dzcp_session_destroy();
                            $index = info(_info_account_deletet, '../news/');
                        }
                    }
                break;
                default:
                    $get = $sql->fetch("SELECT * FROM `{prefix_users}` WHERE `id` = ?;",array($userid));
                    switch(isset($_GET['show']) ? $_GET['show'] : '') {
                        case 'gallery':
                            $qrygl = $sql->select("SELECT `id`,`pic`,`beschreibung` FROM `{prefix_usergallery}` WHERE `user` = ? ORDER BY `id` DESC;",array($userid)); 
                            $gal = ""; $color = 0;
                            foreach($qrygl as $getgl) {
                                $pic = show(_gallery_pic_link, array("img" => $getgl['pic'], "user" => $userid));
                                $delete = show(_gallery_deleteicon, array("id" => $getgl['id']));
                                $edit = show(_gallery_editicon, array("id" => $getgl['id']));
                                $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
                                $gal .= show($dir . "/edit_gallery_show", array("picture" => img_size("inc/images/uploads/usergallery" . "/" . $userid . "_" . $getgl['pic']),
                                                                                "beschreibung" => bbcode::parse_html($getgl['beschreibung']),
                                                                                "class" => $class,
                                                                                "delete" => $delete,
                                                                                "edit" => $edit));
                            }
                            
                            if(empty($gal))
                                $gal = '<tr><td colspan="3" class="contentMainSecond">'._no_entrys.'</td></tr>';

                            $show = show($dir . "/edit_gallery", array("showgallery" => $gal));
                        break;
                        case 'almgr':
                            switch ($do) {
                                case 'self_add':
                                    $permanent_key = md5(mkpwd(8));
                                    if($sql->rows("SELECT `id` FROM `{prefix_autologin}` WHERE `host` = ?;", array(gethostbyaddr($userip)))) {
                                        //Update Autologin
                                        $sql->update("UPDATE `{prefix_autologin}` SET "
                                                          . "`ssid` = ?, "
                                                          . "`pkey` = ?, "
                                                          . "`ip` = ?, "
                                                          . "`date` = ?, "
                                                          . "`update` = ?, "
                                                          . "`expires` = ? "
                                                    . "WHERE `host` = ?;", 
                                        array(session_id(),$permanent_key,$userip,$time=time(),$time,autologin_expire,
                                              gethostbyaddr($userip)));
                                    } else {
                                        //Insert Autologin
                                        $sql->insert("INSERT INTO `{prefix_autologin}` SET "
                                                               . "`uid` = ?,"
                                                               . "`ssid` = ?,"
                                                               . "`pkey` = ?,"
                                                               . "`ip` = ?,"
                                                               . "`name` = ?, "
                                                               . "`host` = ?,"
                                                               . "`date` = ?,"
                                                               . "`update` = 0,"
                                                               . "`expires` = ?;",
                                        array($get['id'],session_id(),$permanent_key,$userip,time(),autologin_expire,
                                              cut(gethostbyaddr($userip),20), gethostbyaddr($userip)));
                                    }
                                    
                                    cookie::put('id', $get['id']);
                                    cookie::put('pkey', $permanent_key);
                                    cookie::save(); unset($permanent_key);
                                    $index = info(_info_almgr_self_added, '../user/?action=editprofile&show=almgr');
                                break;
                                case 'self_remove':
                                    if($sql->rows("SELECT `id` FROM `{prefix_autologin}` WHERE `host` = ? AND `ssid` = ?;", array(gethostbyaddr($userip), session_id()))) {
                                        $sql->delete("DELETE FROM `{prefix_autologin}` WHERE `ssid` = ?;",array(session_id()));
                                        cookie::delete('pkey');
                                        cookie::delete('id');
                                        cookie::save();
                                        $index = info(_info_almgr_self_deletet, '../user/?action=editprofile&show=almgr');
                                    }
                                break;
                                case 'almgr_delete':
                                    if($sql->rows("SELECT `id` FROM `{prefix_autologin}` WHERE `id` = ?;", array(intval($_GET['id'])))) {
                                        $sql->delete("DELETE FROM `{prefix_autologin}` WHERE `id` = ?;",array(intval($_GET['id'])));
                                        cookie::delete('pkey');
                                        cookie::delete('id');
                                        cookie::save();
                                        $index = info(_info_almgr_deletet, '../user/?action=editprofile&show=almgr');
                                    }
                                break;
                                case 'almgr_edit':
                                    $get = $sql->fetch("SELECT * FROM `{prefix_autologin}` WHERE `id` = ?;", array(intval($_GET['id'])));
                                    if($sql->rowCount()) {
                                        $show = show($dir . "/edit_almgr_from", array("name" => stringParser::decode($get['name']),
                                                                                      "id" => stringParser::decode($get['id']),
                                                                                      "host" => stringParser::decode($get['host']),
                                                                                      "ip" => stringParser::decode($get['ip']),
                                                                                      "ssid" => stringParser::decode($get['ssid']),
                                                                                      "pkey" => stringParser::decode($get['pkey'])));
                                    }
                                break;
                                case 'almgr_edit_save':
                                    if($sql->rows("SELECT id FROM `{prefix_autologin}` WHERE `id` = ?;", array(intval($_GET['id'])))) {
                                        $sql->update("UPDATE `{prefix_autologin}` SET `name` = ? WHERE `id` = ?;", array(stringParser::encode($_POST['name']), intval($_GET['id'])));
                                        $index = info(_almgr_editd, '../user/?action=editprofile&show=almgr');
                                    }
                                break;
                            }
                            
                            if(empty($index)) {
                                $qry = $sql->select("SELECT * FROM `{prefix_autologin}` WHERE `uid` = ?;",array($userid)); $almgr = ""; $color = 0;
                                if($sql->rowCount()) {
                                    foreach($qry as $get) {
                                        $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
                                        $almgr .= show($dir . "/edit_almgr_show", array("delete" => show(_almgr_deleteicon, array("id" => $get['id'])),
                                                                                        "edit" => show(_almgr_editicon, array("id" => $get['id'])),                                            
                                                                                        "class" => $class,
                                                                                        "name" => stringParser::decode($get['name']),
                                                                                        "host" => stringParser::decode($get['host']),
                                                                                        "ip" => $get['ip'],
                                                                                        "create" => date('d.m.Y',$get['date']),
                                                                                        "lused" => !$get['update'] ? '-' : date('d.m.Y',$get['update']),
                                                                                        "expires" => date('d.m.Y',((!$get['update'] ? time() : $get['update'])+$get['expires']))));
                                    }
                                }

                                //Empty
                                if(empty($almgr))
                                    $almgr = '<tr><td colspan="6" class="contentMainSecond">'._no_entrys.'</td></tr>';

                                if(empty($show))
                                    $show = show($dir . "/edit_almgr", array("del" => _deleteicon_blank,"edit" => _editicon_blank,"showalmgr" => $almgr));
                            }
                        break;
                        default:
                            $sex = ($get['sex'] == 1 ? _pedit_male : ($get['sex'] == 2 ? _pedit_female : _pedit_sex_ka));
                            $perm_gb = ($get['perm_gb'] ? _pedit_perm_allow : _pedit_perm_deny);
                            $status = ($get['status'] ? _pedit_aktiv : _pedit_inaktiv);
 
                            $levels = array(0,1,2); $perm_gallery = "";
                            foreach ($levels as &$level) {
                                $selected = ($level == $get['perm_gallery'] ? ' selected="selected"' : '');
                                switch ($level) {
                                    case 0: $perm_gallery .= '<option'.$selected.' value="'.$level.'">'._pedit_perm_public.'</option>'; break;
                                    case 1: $perm_gallery .= '<option'.$selected.' value="'.$level.'">'._pedit_perm_user.'</option>'; break;
                                    case 2: $perm_gallery .= '<option'.$selected.' value="'.$level.'">'._pedit_perm_member.'</option>'; break;
                                    case 3: $perm_gallery .= '<option'.$selected.' value="'.$level.'">'._pedit_perm_admin.'</option>'; break;
                                }
                            }
                            
                            $levels = array(0,1,2,3); $perm_profile = "";
                            foreach ($levels as &$level) {
                                $selected = ($level == $get['profile_access'] ? ' selected="selected"' : '');
                                switch ($level) {
                                    case 0: $perm_profile .= '<option'.$selected.' value="'.$level.'">'._pedit_perm_public.'</option>'; break;
                                    case 1: $perm_profile .= '<option'.$selected.' value="'.$level.'">'._pedit_perm_user.'</option>'; break;
                                    case 2: $perm_profile .= '<option'.$selected.' value="'.$level.'">'._pedit_perm_member.'</option>'; break;
                                    case 3: $perm_profile .= '<option'.$selected.' value="'.$level.'">'._pedit_perm_admin.'</option>'; break;
                                }
                            }
                            
                            // Startpage
                            $sql_startpage = $sql->select("SELECT `name`,`id` FROM `{prefix_startpage}`;");
                            $startpage = '<option value="0">'._userlobby.'</option>';
                            if($sql->rowCount()) {
                                foreach($sql_startpage as $get_startpage) {
                                    $startpage .= show(_select_field,array('value' => $get_startpage['id'], 'sel' => ($get_startpage['id'] == $get['startpage'] ? 'selected="selected"' : ''), 'what' => $get_startpage['name'])); }
                            }

                            if ($get['level'] == 1) {
                                $clan = '<input type="hidden" name="status" value="1" />';
                            } else {
                                $qrycustom = $sql->select("SELECT `feldname`,`name` FROM `{prefix_profile}` WHERE `kid` = 2 AND `shown` = 1 ORDER BY `id` ASC;"); $custom_clan = "";
                                foreach($qrycustom as $getcustom) {
                                    $getcontent = $sql->fetch("SELECT `".$getcustom['feldname']."` FROM `{prefix_users}` WHERE `id` = ?;",array($userid));
                                    $custom_clan .= show(_profil_edit_custom, array("name" => pfields_name($getcustom['name']) . ":", 
                                                                                    "feldname" => $getcustom['feldname'],
                                                                                    "value" => stringParser::decode($getcontent[$getcustom['feldname']])));
                                }

                                $clan = show($dir . "/edit_clan", array("status" => $status,
                                                                        "exclans" => stringParser::decode($get['ex']),
                                                                        "custom_clan" => $custom_clan));
                            }

                            $bdayday = 0; $bdaymonth = 0; $bdayyear = 0;
                            if (!empty($get['bday']) && $get['bday'])
                                list($bdayday, $bdaymonth, $bdayyear) = explode('.', date('d.m.Y', $get['bday']));

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
                                                                                   "del" => _confirm_del_account));

                            $show = show($dir . "/edit_profil", array("country" => show_countrys($get['country']),
                                                                      "city" => stringParser::decode($get['city']),
                                                                      "v_steamid" => stringParser::decode($get['steamid']),
                                                                      "skypename" => stringParser::decode($get['skypename']),
                                                                      "pnl" => $pnl,
                                                                      "pnm" => $pnm,
                                                                      "pwd" => "",
                                                                      "dropdown_age" => $dropdown_age,
                                                                      "ava" => $avatar,
                                                                      "hp" => stringParser::decode($get['hp']),
                                                                      "gmaps" => $gmaps,
                                                                      "nick" => stringParser::decode($get['nick']),
                                                                      "name" => stringParser::decode($get['user']),
                                                                      "gmaps_koord" => stringParser::decode($get['gmaps_koord']),
                                                                      "rlname" => stringParser::decode($get['rlname']),
                                                                      "bdayday" => $bdayday,
                                                                      "bdaymonth" => $bdaymonth,
                                                                      "bdayyear" => $bdayyear,
                                                                      "sex" => $sex,
                                                                      "email" => stringParser::decode($get['email']),
                                                                      "visibility_gb" => $perm_gb,
                                                                      "visibility_gallery" => $perm_gallery,
                                                                      "visibility_profile" => $perm_profile,
                                                                      "icqnr" => $icq,
                                                                      "sig" => stringParser::decode($get['signatur']),
                                                                      "hlswid" => $get['hlswid'],
                                                                      "xboxid" => $get['xboxid'],
                                                                      "psnid" => $get['psnid'],
                                                                      "originid" => $get['originid'],
                                                                      "battlenetid" => $get['battlenetid'],
                                                                      "clan" => $clan,
                                                                      "pic" => $pic,
                                                                      "deleteava" => $deleteava,
                                                                      "deletepic" => $deletepic,
                                                                      "startpage" => $startpage,
                                                                      "position" => getrank($get['id']),
                                                                      "status" => $status,
                                                                      "custom_about" => getcustom(1),
                                                                      "custom_contact" => getcustom(3),
                                                                      "custom_favos" => getcustom(4),
                                                                      "custom_hardware" => getcustom(5),
                                                                      "ich" => stringParser::decode($get['beschreibung']),
                                                                      "delete" => $delete));
                        break;
                    }

                    if(empty($index))
                        $index = show($dir . "/edit", array("show" => $show),array("nick" => autor($get['id'])));
                break;
            }
        }
    }
}