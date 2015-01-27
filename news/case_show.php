<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_News') && isset($_GET['id']) && !empty($_GET['id'])) {
    $news_id = intval($_GET['id']); $add = ''; $notification_p = '';
    $check = db("SELECT `intern` FROM `".$db['news']."` WHERE `id` = ".$news_id.";",false,true);

    if($check['intern'] && !permission("intnews"))
        $index = error(_error_wrong_permissions, 1);
    else {
        $qry = db("SELECT * FROM `".$db['news']."` WHERE `id` = ".$news_id."".(permission("news") ? ";" : " AND public = 1;") );
        if(!_rows($qry)) {
            $index = error(_id_dont_exist,1);
        } else {
            switch($do) {
                case 'add':
                    if(db("SELECT `id` FROM `".$db['news']."` WHERE `id` = ".$news_id.";",true,false) != 0) {
                        if(settings("reg_newscomments") && !$chkMe)
                            $index = error(_error_have_to_be_logged, 1);
                        else {
                            if(!ipcheck("ncid(".$_GET['id'].")", config('f_newscom'))) {
                                if($userid >= 1) {
                                    $toCheck = empty($_POST['comment']);
                                } else {
                                    $toCheck = empty($_POST['nick']) || empty($_POST['email']) || empty($_POST['comment']) || !check_email($_POST['email']) || !$securimage->check($_POST['secure']);
                                }

                                if($toCheck) {
                                    javascript::set('AnchorMove','comForm');
                                    if($userid >= 1) {
                                        if(empty($_POST['eintrag'])) {
                                            notification::add_error(_empty_eintrag);
                                        }

                                        $form = show("page/editor_regged", array("nick" => autor($userid)));
                                    } else {
                                        if(empty($_POST['nick'])) {
                                            notification::add_error(_empty_nick);
                                        } else if(empty($_POST['email'])) {
                                            notification::add_error(_empty_email);
                                        } else if(!check_email($_POST['email'])) {
                                            notification::add_error(_error_invalid_email);
                                        } else if(empty($_POST['eintrag'])) {
                                            notification::add_error(_empty_eintrag);
                                        } else if(!$securimage->check($_POST['secure'])) {
                                            notification::add_error(captcha_mathematic ? _error_invalid_regcode_mathematic : _error_invalid_regcode);
                                        }
                                        
                                        $form = show("page/editor_notregged", array("posthp" => (isset($_POST['hp']) ? $_POST['hp'] : ''), 
                                                                                    "postemail" => (isset($_POST['email']) ? $_POST['email'] : ''), 
                                                                                    "postnick" => (isset($_POST['nick']) ? $_POST['nick'] : '')));
                                    }
                                } else {
                                    db("INSERT INTO `".$db['newscomments']."` SET `news`     = ".$news_id.",
                                                                                  `datum`    = ".time().",
                                                                                  `nick`     = '".(isset($_POST['nick']) && !$userid ? up($_POST['nick']) : data('nick'))."',
                                                                                  `email`    = '".(isset($_POST['email']) && !$userid ? up($_POST['email']) : data('email'))."',
                                                                                  `hp`       = '".(isset($_POST['hp']) && !$userid ? links($_POST['hp']) : links(data('hp')))."',
                                                                                  `reg`      = ".intval($userid).",
                                                                                  `comment`  = '".up($_POST['comment'])."',
                                                                                  `ip`       = '".$userip."'");

                                    setIpcheck("ncid(".$news_id.")");
                                    notification::set_global(false);
                                    javascript::set('AnchorMove','notification-box');
                                    $_POST = array(); //Clear Post
                                    $notification_p = notification::add_success(_comment_added);
                                    notification::set_global(true);
                                }
                            } else {
                                notification::add_error(show(_error_flood_post, array("sek" => config('f_newscom'))));
                            }
                        }
                    } else {
                        notification::add_error(_id_dont_exist);
                    }
                break;
                case 'delete':
                    javascript::set('AnchorMove','notification-box');
                    notification::set_global(false);
                    $get = db("SELECT `reg` FROM ".$db['newscomments']." WHERE `id` = '".($cid=intval($_GET['cid']))."'",false,true);
                    if($get['reg'] == $userid || permission('news')) {
                        db("DELETE FROM ".$db['newscomments']." WHERE `id` = '".$cid."'");
                        $notification_p = notification::add_success(_comment_deleted);
                    } else {
                        $notification_p = notification::add_error(_error_wrong_permissions);
                    }
                    
                    notification::set_global(true);
                break;
                case 'editcom':
                    notification::set_global(false);
                    javascript::set('AnchorMove','notification-box');
                    $sql = db("SELECT `reg` FROM `".$db['newscomments']."` WHERE `id` = ".($cid=intval($_GET['cid'])).";");
                    if(_rows($sql) && !empty($_POST['comment'])) {
                        $get = _fetch($sql);
                        if($get['reg'] == $userid || permission('news')) {
                            $editedby = show(_edited_by, array("autor" => autor($userid), "time" => date("d.m.Y H:i", time())._uhr));
                            db("UPDATE ".$db['newscomments']."
                                       SET `nick`     = '".(isset($_POST['nick']) ? up($_POST['nick']) : '')."',
                                           `email`    = '".(isset($_POST['email']) ? up($_POST['email']) : '')."',
                                           `hp`       = '".(isset($_POST['hp']) ? links($_POST['hp']) : '')."',
                                           `comment`  = '".(isset($_POST['comment']) ? up($_POST['comment']) : '')."',
                                           `editby`   = '".up($editedby)."'
                                       WHERE `id` = ".$cid);
                            
                            $_POST = array(); //Clear Post
                            $notification_p = notification::add_success(_comment_edited);
                        } else {
                            $notification_p = notification::add_error(_error_edit_post);
                        }
                    } else {
                        $notification_p = notification::add_error(_empty_eintrag);
                    }
  
                    notification::set_global(true);
                break;
                case 'edit':
                    $get = db("SELECT `reg`,`comment`,`hp`,`email`,`nick` FROM ".$db['newscomments']." WHERE `id` = '".intval($_GET['cid'])."'",false,true);
                    if($get['reg'] == $userid || permission('news')) {
                        javascript::set('AnchorMove','comForm');
                        if($get['reg'] != 0) {
                            $form = show("page/editor_regged", array("nick" => autor($get['reg']), "von" => _autor));
                        } else {
                            $form = show("page/editor_notregged", array("posthp" => re($get['hp']),  "postemail" => re($get['email']), "postnick" => re($get['nick'])));
                        }
                        
                        $add = show("page/comments_add", array("titel" => _comments_edit,
                                                               "form" => $form,
                                                               "what" => _button_value_edit,
                                                               "prevurl" => '../news/?action=compreview&do=edit&id='.$_GET['id'].'&cid='.$_GET['cid'],
                                                               "action" => '?action=show&amp;do=editcom&amp;id='.$_GET['id'].'&amp;cid='.$_GET['cid'],
                                                               "id" => (isset($_GET['id']) ? $_GET['id'] : '1'),
                                                               "posteintrag" => re_bbcode($get['comment'])));
                    } else {
                        javascript::set('AnchorMove','notification-box');
                        notification::set_global(false);
                        $notification_p = notification::add_error(_error_edit_post);
                        notification::set_global(true);
                    }
                    
                    break;
                }
            
                //Update viewed
                if(count_clicks('news',$news_id))
                    db("UPDATE ".$db['news']." SET `viewed` = viewed+1 WHERE id = '".$news_id."'");

                $get = _fetch($qry);
                $getkat = db("SELECT katimg FROM ".$db['newskat']." WHERE id = '".$get['kat']."'",false,true);

                $klapp = "";
                if($get['klapptext'])
                    $klapp = show(_news_klapplink, array("klapplink" => re($get['klapplink']),
                            "which" => "expand",
                            "id" => $get['id']));

                $viewed = show(_news_viewed, array("viewed" => $get['viewed']));

                $links1 = ""; $rel = "";
                if(!empty($get['url1'])) {
                    $rel = _related_links;
                    $links1 = show(_news_link, array("link" => re($get['link1']),
                            "url" => $get['url1']));
                }

                $links2 = "";
                if(!empty($get['url2'])) {
                    $rel = _related_links;
                    $links2 = show(_news_link, array("link" => re($get['link2']),
                            "url" => $get['url2']));
                }

                $links3 = "";
                if(!empty($get['url3'])) {
                    $rel = _related_links;
                    $links3 = show(_news_link, array("link" => re($get['link3']),
                            "url" => $get['url3']));
                }

                $links = "";
                if(!empty($links1) || !empty($links2) || !empty($links3))
                    $links = show(_news_links, array("link1" => $links1,
                            "link2" => $links2,
                            "link3" => $links3,
                            "rel" => $rel));

                $qryc = db("SELECT * FROM ".$db['newscomments']."
                            WHERE news = ".$news_id."
                            ORDER BY datum DESC
                            LIMIT ".($page - 1)*config('m_comments').",".config('m_comments')."");

                $entrys = cnt($db['newscomments'], " WHERE news = ".$news_id);
                $i = ($entrys-($page - 1)*config('m_comments')); $comments = '';
                while($getc = _fetch($qryc)) {
                    $edit = ""; $delete = "";
                    if(($chkMe >= 1 && $getc['reg'] == $userid) || permission("news")) {
                        $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                                      "action" => "action=show&amp;do=edit&amp;cid=".$getc['id'],
                                                                      "title" => _button_title_edit));

                        $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                                         "action" => "action=show&amp;do=delete&amp;cid=".$getc['id'],
                                                                         "title" => _button_title_del,
                                                                         "del" => convSpace(_confirm_del_entry)));
                    }

                    $email = ""; $hp = ""; $onoff = onlinecheck($getc['reg']); $nick = autor($getc['reg']); $avatar = ""; $onoff = "";
                    if($getc['reg'] == "0") {
                        if($getc['hp'])
                            $hp = show(_hpicon_forum, array("hp" => links(re($getc['hp']))));

                        if($getc['email'])
                            $email = '<br />'.CryptMailto(re($getc['email']),_emailicon_forum);

                        $nick = show(_link_mailto, array("nick" => re($getc['nick']), "email" => $email));
                    }

                    $titel = show(_eintrag_titel, array("postid" => $i,
                                                        "datum" => date("d.m.Y", $getc['datum']),
                                                        "zeit" => date("H:i", $getc['datum'])._uhr,
                                                        "edit" => $edit,
                                                        "delete" => $delete));

                    $posted_ip = $chkMe == 4 || permission('ipban') ? $getc['ip'] : _logged;
                    $comments .= show("page/comments_show", array("titel" => $titel,
                                                                  "comment" => bbcode($getc['comment']),
                                                                  "nick" => $nick,
                                                                  "hp" => $hp,
                                                                  "editby" => bbcode($getc['editby']),
                                                                  "email" => $email,
                                                                  "avatar" => useravatar($getc['reg']),
                                                                  "onoff" => $onoff,
                                                                  "rank" => getrank($getc['reg']),
                                                                  "ip" => $posted_ip));
                $i--;
            }

            if(settings("reg_newscomments") && !$chkMe) {
                $add = _error_unregistered_nc;
            } else {
                if(empty($form)) {
                    if($userid >= 1) {
                        $form = show("page/editor_regged", array("nick" => autor($userid)));
                    } else {
                        $form = show("page/editor_notregged", array("postnick" => '', "postemail" => '', "posthp" => ''));
                    }
                }
                
                if(!ipcheck("ncid(".$_GET['id'].")", config('f_newscom')) && empty($add)) {
                    $add = show("page/comments_add", array("titel" => _news_comments_write_head,
                                                           "form" => $form,
                                                           "what" => _button_value_add,
                                                           "action" => '?action=show&amp;do=add&amp;id='.(isset($_GET['id']) ? $_GET['id'] : '1'),
                                                           "prevurl" => '../news/?action=compreview&id='.(isset($_GET['id']) ? $_GET['id'] : '1'),
                                                           "id" => (isset($_GET['id']) ? $_GET['id'] : '1'),
                                                           "posteintrag" => (isset($_POST['comment']) ? $_POST['comment'] : '')));
                }
            }

            $seiten = nav($entrys,config('m_comments'),"?action=show&amp;id=".$_GET['id']."");
            $showmore = show($dir."/comments",array("head" => _comments_head,
                                                    "show" => $comments,
                                                    "seiten" => $seiten,
                                                    "add" => $add));

            $intern = $get['intern'] ? _votes_intern : "";
            $newsimage = '../inc/images/newskat/'.$getkat['katimg'];
            foreach($picformat as $tmpendung) {
                if(file_exists(basePath."/inc/images/uploads/news/".$get['id'].".".$tmpendung)) {
                    $newsimage = '../inc/images/uploads/news/'.$get['id'].'.'.$tmpendung;
                    break;
                }
            }

            $where = $where." - ".re($get['titel']);
            $index = show($dir."/news_show_full", array("titel" => re($get['titel']),
                                                   "kat" => $newsimage,
                                                   "id" => $get['id'],
                                                   "comments" => "",
                                                   "dp" => "compact",
                                                   "notification_page" => (!empty($notification_p) ? notification::get($notification_p) : ''),
                                                   "nautor" => _autor,
                                                   "dir" => $designpath,
                                                   "ndatum" => _datum,
                                                   "rel" => $rel,
                                                   "sticky" => "",
                                                   "intern" => $intern,
                                                   "ncomments" => "",
                                                   "showmore" => $showmore,
                                                   "klapp" => $klapp,
                                                   "more" => bbcode($get['klapptext']),
                                                   "viewed" => "",
                                                   "text" => bbcode($get['text']),
                                                   "datum" => date("j.m.y H:i", (empty($get['datum']) ? time() : $get['datum']))._uhr,
                                                   "links" => $links,
                                                   "autor" => autor($get['autor'])));
        }
    }
}