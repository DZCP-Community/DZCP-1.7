<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_News') && isset($_GET['id']) && !empty($_GET['id'])) {
    $news_id = intval($_GET['id']); $add = ''; $notification_p = '';
    if ($sql->fetch("SELECT `intern` FROM `{prefix_news}` WHERE `id` = ?;",array($news_id),'intern') && !permission("intnews")) {
        $index = error(_error_wrong_permissions, 1);
    } else {
        $get_news = $sql->fetch("SELECT * FROM `{prefix_news}` WHERE `id` = ?".(permission("news") ? ";" : " AND public = 1;"),array($news_id));
        if (!$sql->rowCount()) {
            $index = error(_id_dont_exist, 1);
        } else {
            switch ($do) {
                case 'add':
                    if ($sql->rows("SELECT `id` FROM `{prefix_news}` WHERE `id` = ?;",array($news_id)) != 0) {
                        if (settings::get("reg_newscomments") && !$chkMe) {
                            $index = error(_error_have_to_be_logged, 1);
                        } else {
                            if (!ipcheck("ncid(" . $_GET['id'] . ")", settings::get('f_newscom'))) {
                                if ($userid >= 1) {
                                    $toCheck = empty($_POST['comment']);
                                } else {
                                    $toCheck = empty($_POST['nick']) || empty($_POST['email']) || empty($_POST['comment']) || !check_email($_POST['email']) || !$securimage->check($_POST['secure']);
                                }

                                if ($toCheck) {
                                    javascript::set('AnchorMove', 'comForm');
                                    if ($userid >= 1) {
                                        if (empty($_POST['eintrag'])) {
                                            notification::add_error(_empty_eintrag);
                                        }

                                        $form = show("page/editor_regged", array("nick" => autor($userid)));
                                    } else {
                                        if (empty($_POST['nick'])) {
                                            notification::add_error(_empty_nick);
                                        } else if (empty($_POST['email'])) {
                                            notification::add_error(_empty_email);
                                        } else if (!check_email($_POST['email'])) {
                                            notification::add_error(_error_invalid_email);
                                        } else if (empty($_POST['eintrag'])) {
                                            notification::add_error(_empty_eintrag);
                                        } else if (!$securimage->check($_POST['secure'])) {
                                            notification::add_error(captcha_mathematic ? _error_invalid_regcode_mathematic : _error_invalid_regcode);
                                        }

                                        $form = show("page/editor_notregged", array("posthp" => (isset($_POST['hp']) ? $_POST['hp'] : ''),
                                                                                    "postemail" => (isset($_POST['email']) ? $_POST['email'] : ''),
                                                                                    "postnick" => (isset($_POST['nick']) ? $_POST['nick'] : '')));
                                    }
                                } else {
                                    $sql->insert("INSERT INTO `{prefix_newscomments}` SET `news` = ?,`datum` = ?,`nick` = ?,`email` = ?,`hp` = ?,`reg` = ?,`comment` = ?, `ip` = ?;",
                                    array($news_id,time(),(isset($_POST['nick']) && !$userid ? stringParser::encode($_POST['nick']) : data('nick')),(isset($_POST['email']) && !$userid ? stringParser::encode($_POST['email']) : data('email')),
                                    (isset($_POST['hp']) && !$userid ? links($_POST['hp']) : links(data('hp'))),intval($userid),stringParser::encode($_POST['comment']),$userip));
                                    setIpcheck("ncid(" . $news_id . ")");
                                    notification::set_global(false);
                                    javascript::set('AnchorMove', 'notification-box');
                                    $_POST = array(); //Clear Post
                                    $notification_p = notification::add_success(_comment_added);
                                    notification::set_global(true);
                                }
                            } else {
                                notification::add_error(show(_error_flood_post, array("sek" => settings::get('f_newscom'))));
                            }
                        }
                    } else {
                        notification::add_error(_id_dont_exist);
                    }
                    break;
                case 'delete':
                    javascript::set('AnchorMove', 'notification-box');
                    notification::set_global(false);
                    $reg = $sql->fetch("SELECT `reg` FROM `{prefix_newscomments}` WHERE `id` = ?;",array(($cid = intval($_GET['cid']))),'reg');
                    if ($reg == $userid || permission('news')) {
                        $sql->delete("DELETE FROM `{prefix_newscomments}` WHERE `id` = ?;",array($cid));
                        $notification_p = notification::add_success(_comment_deleted);
                    } else {
                        $notification_p = notification::add_error(_error_wrong_permissions);
                    }

                    notification::set_global(true);
                    break;
                case 'editcom':
                    notification::set_global(false);
                    javascript::set('AnchorMove', 'notification-box');
                    $reg = $sql->fetch("SELECT `reg` FROM `{prefix_newscomments}` WHERE `id` = ?;",array(($cid = intval($_GET['cid']))),'reg');
                    if ($sql->rowCount() && !empty($_POST['comment'])) {
                        if ($reg == $userid || permission('news')) {
                            $editedby = show(_edited_by, array("autor" => autor($userid), "time" => date("d.m.Y H:i", time()) . _uhr));
                            $sql->update("UPDATE `{prefix_newscomments}` SET `nick` = ?, `email` = ?, `hp` = ?, `comment` = ?, `editby` = ?
                                          WHERE `id` = ?;",array((isset($_POST['nick']) ? stringParser::encode($_POST['nick']) : ''),
                                          (isset($_POST['email']) ? stringParser::encode($_POST['email']) : ''),
                                          (isset($_POST['hp']) ? links($_POST['hp']) : ''),
                                          (isset($_POST['comment']) ? stringParser::encode($_POST['comment']) : ''),
                                          stringParser::encode($editedby),$cid));

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
                    $get = $sql->fetch("SELECT `reg`,`comment`,`hp`,`email`,`nick` FROM `{prefix_newscomments}` WHERE `id` = ?;",array(intval($_GET['cid'])));
                    if ($get['reg'] == $userid || permission('news')) {
                        javascript::set('AnchorMove', 'comForm');
                        if ($get['reg'] != 0) {
                            $form = show("page/editor_regged", array("nick" => autor($get['reg']), "von" => _autor));
                        } else {
                            $form = show("page/editor_notregged", array("posthp" => stringParser::decode($get['hp']), "postemail" => stringParser::decode($get['email']), "postnick" => stringParser::decode($get['nick'])));
                        }

                        $add = show("page/comments_add", array("titel" => _comments_edit,
                                                               "form" => $form,
                                                               "what" => _button_value_edit,
                                                               "prevurl" => '../news/?action=compreview&do=edit&id=' . $_GET['id'] . '&cid=' . $_GET['cid'],
                                                               "action" => '?action=show&amp;do=editcom&amp;id=' . $_GET['id'] . '&amp;cid=' . $_GET['cid'],
                                                               "id" => (isset($_GET['id']) ? $_GET['id'] : '1'),
                                                               "posteintrag" => stringParser::decode($get['comment'])));
                    } else {
                        javascript::set('AnchorMove', 'notification-box');
                        notification::set_global(false);
                        $notification_p = notification::add_error(_error_edit_post);
                        notification::set_global(true);
                    }

                    break;
            }

            /************************
             * View News
             ************************/
            //Update viewed
            if (count_clicks('news', $news_id)) {
                $sql->update("UPDATE `{prefix_news}` SET `viewed` = (viewed+1) WHERE `id` = ?;",array($news_id));
            }

            $klapp = ($get_news['klapptext'] ? show(_news_klapplink, array("klapplink" => stringParser::decode($get_news['klapplink']), 
                                                                           "which" => "expand", 
                                                                           "id" => $get_news['id'])) : '');
            $viewed = show(_news_viewed, array("viewed" => $get_news['viewed']));
            $links1 = ""; $rel = "";
            if (!empty($get_news['url1'])) {
                $rel = _related_links;
                $links1 = show(_news_link, array("link" => stringParser::decode($get_news['link1']),
                                                 "url" => $get_news['url1']));
            }

            $links2 = "";
            if (!empty($get_news['url2'])) {
                $rel = _related_links;
                $links2 = show(_news_link, array("link" => stringParser::decode($get_news['link2']),
                                                 "url" => $get_news['url2']));
            }

            $links3 = "";
            if (!empty($get_news['url3'])) {
                $rel = _related_links;
                $links3 = show(_news_link, array("link" => stringParser::decode($get_news['link3']),
                                                 "url" => $get_news['url3']));
            }

            $links = "";
            if (!empty($links1) || !empty($links2) || !empty($links3)) {
                $links = show(_news_links, array("link1" => $links1,
                                                 "link2" => $links2,
                                                 "link3" => $links3,
                                                 "rel" => $rel));
            }

            //News Comments
            $qryc = $sql->select("SELECT * FROM `{prefix_newscomments}` WHERE `news` = ? "
                                ."ORDER BY `datum` DESC LIMIT ".($page - 1)*settings::get('m_comments').",".settings::get('m_comments').";",
                                array($news_id));
            
            $entrys = cnt('{prefix_newscomments}', " WHERE `news` = ".$news_id);
            $i = ($entrys - ($page - 1) * settings::get('m_comments')); $comments = '';
            foreach($qryc as $getc) {
                $edit = ""; $delete = "";
                if (($chkMe >= 1 && $getc['reg'] == $userid) || permission("news")) {
                    $edit = show("page/button_edit_single", array("id" => $get_news['id'],
                                                                  "action" => "action=show&amp;do=edit&amp;cid=" . $getc['id'],
                                                                  "title" => _button_title_edit));

                    $delete = show("page/button_delete_single", array("id" => $get_news['id'],
                                                                      "action" => "action=show&amp;do=delete&amp;cid=" . $getc['id'],
                                                                      "title" => _button_title_del,
                                                                      "del" => convSpace(_confirm_del_entry)));
                }

                $email = ""; $hp = ""; $avatar = ""; $onoff = "";
                if (!$getc['reg']) {
                    if ($getc['hp']) {
                        $hp = show(_hpicon_forum, array("hp" => links(stringParser::decode($getc['hp']))));
                    }

                    if ($getc['email']) {
                        $email = '<br />' . CryptMailto(stringParser::decode($getc['email']), _emailicon_forum);
                    }

                    $nick = show(_link_mailto, array("nick" => stringParser::decode($getc['nick']), "email" => $email));
                } else {
                    $onoff = onlinecheck($getc['reg']);
                    $nick = autor($getc['reg']);
                }

                $titel = show(_eintrag_titel, array("postid" => $i,
                                                    "datum" => date("d.m.Y", $getc['datum']),
                                                    "zeit" => date("H:i", $getc['datum']) . _uhr,
                                                    "edit" => $edit,
                                                    "delete" => $delete));

                $posted_ip = ($chkMe == 4 || permission('ipban') ? $getc['ip'] : _logged);
                $comments .= show("page/comments_show", array("titel" => $titel,
                                                              "comment" => bbcode::parse_html($getc['comment']),
                                                              "nick" => $nick,
                                                              "hp" => $hp,
                                                              "editby" => bbcode::parse_html($getc['editby']),
                                                              "email" => $email,
                                                              "avatar" => useravatar($getc['reg']),
                                                              "onoff" => $onoff,
                                                              "rank" => getrank($getc['reg']),
                                                              "ip" => $posted_ip));
                $i--;
            }

            if (settings::get("reg_newscomments") && !$chkMe) {
                $add = _error_unregistered_nc;
            } else {
                if (empty($form)) {
                    if ($userid >= 1) {
                        $form = show("page/editor_regged", array("nick" => autor($userid)));
                    } else {
                        $form = show("page/editor_notregged", array("postnick" => '', "postemail" => '', "posthp" => ''));
                    }
                }

                if (!ipcheck("ncid(".$_GET['id'].")", settings::get('f_newscom')) && empty($add)) {
                    $add = show("page/comments_add", array("titel" => _news_comments_write_head,
                                                           "form" => $form,
                                                           "what" => _button_value_add,
                                                           "action" => '?action=show&amp;do=add&amp;id=' . (isset($_GET['id']) ? $_GET['id'] : '1'),
                                                           "prevurl" => '../news/?action=compreview&id=' . (isset($_GET['id']) ? $_GET['id'] : '1'),
                                                           "id" => (isset($_GET['id']) ? $_GET['id'] : '1'),
                                                           "posteintrag" => (isset($_POST['comment']) ? $_POST['comment'] : '')));
                }
            }

            $seiten = nav($entrys, settings::get('m_comments'), "?action=show&amp;id=" . $_GET['id'] . "");
            $showmore = show($dir . "/comments", array("head" => _comments_head,
                                                       "show" => $comments,
                                                       "seiten" => $seiten,
                                                       "add" => $add));

            $intern = $get_news['intern'] ? _votes_intern : "";
            $newsimage = '../inc/images/newskat/'.$sql->fetch("SELECT `katimg` FROM `{prefix_newskat}` WHERE `id` = ?;",array($get_news['kat']),'katimg');
            foreach ($picformat as $tmpendung) {
                if (file_exists(basePath . "/inc/images/uploads/news/".$get_news['id'].".".$tmpendung)) {
                    $newsimage = '../inc/images/uploads/news/'.$get_news['id'].'.'.$tmpendung;
                    break;
                }
            }

            $where = $where." - ".stringParser::decode($get_news['titel']);
            $index = show($dir."/news_show_full", array("titel" => stringParser::decode($get_news['titel']),
                                                        "kat" => $newsimage,
                                                        "id" => $get_news['id'],
                                                        "comments" => "",
                                                        "dp" => "compact",
                                                        "notification_page" => notification::get($notification_p),
                                                        "dir" => $designpath,
                                                        "rel" => $rel,
                                                        "sticky" => "",
                                                        "intern" => $intern,
                                                        "ncomments" => "",
                                                        "showmore" => $showmore,
                                                        "klapp" => $klapp,
                                                        "more" => bbcode::parse_html($get_news['klapptext']),
                                                        "viewed" => $viewed,
                                                        "text" => bbcode::parse_html($get_news['text']),
                                                        "datum" => date("j.m.y H:i", (empty($get_news['datum']) ? time() : $get_news['datum']))._uhr,
                                                        "links" => $links,
                                                        "autor" => autor($get_news['autor'])));
        }
    }
}