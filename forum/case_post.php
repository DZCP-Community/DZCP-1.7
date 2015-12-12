<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Forum')) {
  if($do == "edit")
  {
    $get = $sql->fetch("SELECT * FROM `{prefix_forumposts}` WHERE id = '".intval($_GET['id'])."'");

    if($get['reg'] == $userid || permission("forum"))
    {
      if($get['reg'] != 0)
        {
            $form = show("page/editor_regged", array("nick" => autor($get['reg']),
                                                 "von" => _autor));
        } else {
        $form = show("page/editor_notregged", array("nickhead" => _nick,
                                                    "emailhead" => _email,
                                                    "hphead" => _hp,
                                                    "postemail" => stringParser::decode($get['email']),
                                                                                    "posthp" => stringParser::decode($get['hp']),
                                                                                    "postnick" => stringParser::decode($get['nick'])));
      }

      $dowhat = show(_forum_dowhat_edit_post, array("id" => $_GET['id']));
      $index = show($dir."/post", array("titel" => _forum_edit_post_head,
                                        "nickhead" => _nick,
                                        "emailhead" => _email,
                                        "kid" => "",
                                        "id" => $_GET['id'],
                                        "ip" => _iplog_info,
                                        "dowhat" => $dowhat,
                                        "form" => $form,
                                        "zitat" => $zitat,
                                        "preview" => _preview,
                                        "br1" => "<!--",
                                        "br2" => "-->",
                                        "security" => _register_confirm,
                                        "lastpost" => "",
                                        "last_post" => _forum_no_last_post,
                                        "bbcodehead" => _bbcode,
                                        "eintraghead" => _eintrag,
                                        "error" => "",
                                        "what" => _button_value_edit,
                                        "posteintrag" => stringParser::decode($get['text'])));
    } else {
      $index = error(_error_wrong_permissions, 1);
    }
  } elseif($do == "editpost") {
    $get = $sql->fetch("SELECT reg FROM `{prefix_forumposts}` WHERE id = '".intval($_GET['id'])."'");
    if($get['reg'] == $userid || permission("forum"))
    {
      if($get['reg'] != 0 || permission('forum'))
      {
        $toCheck = empty($_POST['eintrag']);
      } else {
        $toCheck = empty($_POST['nick']) || empty($_POST['email']) || empty($_POST['eintrag']) || !$securimage->check($_POST['secure']);
      }

      if($toCheck)
        {
        if($get['reg'] != 0)
          {
          if(empty($_POST['eintrag'])) $error = _empty_eintrag;
              $form = show("page/editor_regged", array("nick" => autor($userid),
                                                   "von" => _autor));
          } else {
          if(!$securimage->check($_POST['secure']) && !userid()) $error = captcha_mathematic ? _error_invalid_regcode_mathematic : _error_invalid_regcode;
          elseif(empty($_POST['nick'])) $error = _empty_nick;
          elseif(empty($_POST['email'])) $error = _empty_email;
          elseif(!check_email($_POST['email'])) $error = _error_invalid_email;
          elseif(empty($_POST['eintrag']))$error = _empty_eintrag;
          $form = show("page/editor_notregged", array("nickhead" => _nick,
                                                      "emailhead" => _email,
                                                      "hphead" => _hp));
        }

        $error = show("errors/errortable", array("error" => $error));
        $dowhat = show(_forum_dowhat_edit_post, array("id" => $_GET['id']));
        $index = show($dir."/post", array("titel" => _forum_edit_post_head,
                                                                          "nickhead" => _nick,
                                                                            "bbcodehead" => _bbcode,
                                          "preview" => _preview,
                                                                            "emailhead" => _email,
                                          "zitat" => $zitat,
                                          "form" => $form,
                                          "dowhat" => $dowhat,
                                          "security" => _register_confirm,
                                          "what" => _button_value_edit,
                                          "ip" => _iplog_info,
                                                                            "id" => $_GET['id'],
                                          "kid" => $_GET['kid'],
                                          "br1" => "<!--",
                                          "br2" => "-->",
                                                                            "postemail" => stringParser::decode($get['email']),
                                                                            "postnick" => stringParser::decode($get['nick']),
                                                                              "posteintrag" => stringParser::decode($_POST['eintrag']),
                                                                               "error" => $error,
                                                                           "eintraghead" => _eintrag));
      } else {
        $getp = $sql->fetch("SELECT * FROM `{prefix_forumposts}` WHERE id = '".intval($_GET['id'])."'");

        $editedby = show(_edited_by, array("autor" => autor($userid),
                                           "time" => date("d.m.Y H:i", time())._uhr));

        $sql->update("UPDATE `{prefix_forumposts}`
                   SET `nick`   = '".stringParser::encode($_POST['nick'])."',
                       `email`  = '".stringParser::encode($_POST['email'])."',
                       `text`   = '".stringParser::encode($_POST['eintrag'])."',
                       `hp`     = '".links($_POST['hp'])."',
                       `edited` = '".stringParser::encode($editedby)."'
                   WHERE id = '".intval($_GET['id'])."'");

      $checkabo = $sql->select("SELECT s1.user,s1.fid,s2.nick,s2.id,s2.email FROM {prefix_f_abo} AS s1
                        LEFT JOIN `{prefix_users}` AS s2 ON s2.id = s1.user
                      WHERE s1.fid = '".$getp['sid']."'");
      foreach($checkabo as $getabo) {
        if($userid != $getabo['user'])
        {
          $gettopic = $sql->fetch("SELECT topic FROM `{prefix_forumthreads}` WHERE id = '".$getp['sid']."'");

            $entrys = cnt("{prefix_forumposts}", " WHERE `sid` = ".$getp['sid']);

            if($entrys == "0") $pagenr = "1";
            else $pagenr = ceil($entrys/settings::get('m_fposts'));

          $subj = show(stringParser::decode(settings::get('eml_fabo_pedit_subj')), array("titel" => $title));

           $message = show(bbcode_email(settings::get('eml_fabo_pedit')), array("nick" => stringParser::decode($getabo['nick']),
                                                               "postuser" => fabo_autor($userid),
                                                            "topic" => $gettopic['topic'],
                                                            "titel" => $title,
                                                            "domain" => $httphost,
                                                            "id" => $getp['sid'],
                                                            "entrys" => $entrys+1,
                                                            "page" => $pagenr,
                                                            "text" => bbcode::parse_html($_POST['eintrag']),
                                                            "clan" => settings::get('clanname')));

          sendMail(stringParser::decode($getabo['email']),$subj,$message);
        }
      }
        $entrys = cnt("{prefix_forumposts}", " WHERE `sid` = ".$getp['sid']);

        if($entrys == "0") $pagenr = "1";
        else $pagenr = ceil($entrys/settings::get('m_fposts'));

        $lpost = show(_forum_add_lastpost, array("id" => $entrys+1,
                                                 "tid" => $getp['sid'],
                                                 "page" => $pagenr));

        $index = info(_forum_editpost_successful, $lpost);
      }
    } else {
      $index = error(_error_wrong_permissions, 1);
    }
  } elseif($do == "add") {
    if(settings::get("reg_forum") && !$chkMe)
    {
      $index = error(_error_unregistered,1);
    } else {
      if(!ipcheck("fid(".$_GET['kid'].")", settings::get('f_forum')))
      {
        $checks = $sql->fetch("SELECT s2.id,s1.intern FROM `{prefix_forumkats}` AS s1
                     LEFT JOIN `{prefix_forumsubkats}` AS s2
                     ON s2.sid = s1.id
                     WHERE s2.id = '".intval($_GET['kid'])."'");
        if(forumcheck($_GET['id'], "closed"))
        {
          $index = error(_error_forum_closed, 1);
        } elseif($checks['intern'] == 1 && !permission("intforum") && !fintern($checks['id'])) {
          $index = error(_error_no_access, 1);
        } else {
          if($userid >= 1)
          {
              $postnick = data("nick");
              $postemail = data("email");
          } else {
              $postnick = "";
              $postemail = "";
          }
          if(isset($_GET['zitat']))
          {
            $getzitat = $sql->fetch("SELECT nick,reg,text FROM `{prefix_forumposts}` WHERE id = '".intval($_GET['zitat'])."'");

            if($getzitat['reg'] == "0") $nick = $getzitat['nick'];
            else                        $nick = autor($getzitat['reg']);

            $zitat = bbcode::zitat($nick, $getzitat['text']);
          } elseif(isset($_GET['zitatt'])) {
            $getzitat = $sql->fetch("SELECT t_nick,t_reg,t_text FROM `{prefix_forumthreads}` WHERE id = '".intval($_GET['zitatt'])."'");

            if($getzitat['t_reg'] == "0") $nick = $getzitat['t_nick'];
            else                          $nick = data("nick",$getzitat['t_reg']);

            $zitat = bbcode::zitat($nick, $getzitat['t_text']);
          } else {
            $zitat = "";
          }

          $dowhat = show(_forum_dowhat_add_post, array("id" => $_GET['id'],
                                                       "kid" => $_GET['kid']));

          $getl = $sql->fetch("SELECT * FROM `{prefix_forumposts}`
                      WHERE kid = '".intval($_GET['kid'])."'
                      AND sid = '".intval($_GET['id'])."'
                      ORDER BY date DESC");
          if($sql->rowCount())
          {
            if(data("signatur",$getl['reg'])) $sig = _sig.bbcode::parse_html(data("signatur",$getl['reg']));
            else                               $sig = "";

            if($getl['reg'] != "0") $userposts = show(_forum_user_posts, array("posts" => userstats("forumposts",$getl['reg'])));
            else                    $userposts = "";

            if($getl['reg'] == "0") $onoff = "";
            else                    $onoff = onlinecheck($getl['reg']);

            $text = bbcode::parse_html($getl['text']);

            if($chkMe == 4 || permission('ipban')) $posted_ip = $getl['ip'];
            else              $posted_ip = _logged;

            $titel = show(_eintrag_titel_forum, array("postid" => (cnt("{prefix_forumposts}", " WHERE sid =".intval($_GET['id']))+1),
                                                                                        "datum" => date("d.m.Y", $getl['date']),
                                                                                        "zeit" => date("H:i", $getl['date'])._uhr,
                                                "url" => '#',
                                                "edit" => "",
                                                "delete" => ""));
            if($getl['reg'] != 0)
            {
              $getu = $sql->fetch("SELECT nick,icq,hp,email FROM `{prefix_users}` WHERE id = '".$getl['reg']."'");

              $email = CryptMailto(stringParser::decode($getu['email']),_emailicon_forum);
              $pn = _forum_pn_preview;
              if(empty($getu['icq']) || $getu['icq'] == 0) $icq = "";
                  else {
                $uin = show(_icqstatus_forum, array("uin" => $getu['icq']));
                $icq = '<a href="http://www.icq.com/whitepages/about_me.php?uin='.$getu['icq'].'" target="_blank">'.$uin.'</a>';
                  }

              if(empty($getu['hp'])) $hp = "";
              else $hp = show(_hpicon_forum, array("hp" => $getu['hp']));
            } else {
              $icq = "";
              $pn = "";
              $email = CryptMailto(stringParser::decode($getl['email']),_emailicon_forum);
              if(empty($getl['hp'])) $hp = "";
              else $hp = show(_hpicon_forum, array("hp" => $getl['hp']));
            }

            $lastpost = show($dir."/forum_posts_show", array("nick" => cleanautor($getl['reg'], '', $getl['nick'], stringParser::decode($getl['email'])),
                                                             "postnr" => "",
                                                             "text" => $text,
                                                             "status" => getrank($getl['reg']),
                                                             "avatar" => useravatar($getl['reg']),
                                                             "pn" => $pn,
                                                             "icq" => $icq,
                                                             "hp" => $hp,
                                                             "class" => 'class="commentsRight"',
                                                             "email" => $email,
                                                             "titel" => $titel,
                                                             "p" => ($page-1*settings::get('m_fposts')),
                                                             "ip" => $posted_ip,
                                                             "edited" => $getl['edited'],
                                                             "posts" => $userposts,
                                                             "date" => _posted_by.date("d.m.y H:i", $getl['date'])._uhr,
                                                             "signatur" => $sig,
                                                             "zitat" => _forum_zitat_preview,
                                                             "onoff" => $onoff,
                                                             "top" => "",
                                                             "lp" => cnt("{prefix_forumposts}", " WHERE sid = '".intval($_GET['id'])."'")+1));
          } else {
            $gett = $sql->fetch("SELECT * FROM `{prefix_forumthreads}`
                        WHERE kid = '".intval($_GET['kid'])."'
                        AND id = '".intval($_GET['id'])."'");

            if(data("signatur",$gett['t_reg'])) $sig = _sig.bbcode::parse_html(data("signatur",$gett['t_reg']));
            else $sig = "";

            if($gett['t_reg'] != "0")
              $userposts = show(_forum_user_posts, array("posts" => userstats("forumposts",$gett['t_reg'])));
            else $userposts = "";

            if($gett['t_reg'] == "0") $onoff = "";
            else                      $onoff = onlinecheck($gett['t_reg']);

            $ftxt = hl($gett['t_text'], (isset($_GET['hl']) ? $_GET['hl'] : ''));
            if(isset($_GET['hl'])) $text = bbcode::parse_html($ftxt['text']);
            else $text = bbcode::parse_html($gett['t_text']);

            if($chkMe == 4 || permission('ipban')) $posted_ip = $gett['ip'];
            else                 $posted_ip = _logged;

            $titel = show(_eintrag_titel_forum, array("postid" => "1",
                                                                                        "datum" => date("d.m.Y", $gett['t_date']),
                                                                                        "zeit" => date("H:i", $gett['t_date'])._uhr,
                                                "url" => '#',
                                                "edit" => "",
                                                "delete" => ""));
            if($gett['t_reg'] != 0)
            {
              $getu = $sql->fetch("SELECT nick,icq,hp,email FROM `{prefix_users}`
                          WHERE id = '".$gett['t_reg']."'");

              $email = CryptMailto(stringParser::decode($getu['email']),_emailicon_forum);
              $pn = show(_pn_write_forum, array("id" => $gett['t_reg'],
                                                                              "nick" => $getu['nick']));
              if(empty($getu['icq']) || $getu['icq'] == 0) $icq = "";
                  else {
                $uin = show(_icqstatus_forum, array("uin" => $getu['icq']));
                $icq = '<a href="http://www.icq.com/whitepages/about_me.php?uin='.$getu['icq'].'" target="_blank">'.$uin.'</a>';
                  }

              if(empty($getu['hp'])) $hp = "";
              else $hp = show(_hpicon_forum, array("hp" => $getu['hp']));
            } else {
              $icq = "";
              $pn = "";

              $email = CryptMailto(stringParser::decode($gett['email']),_emailicon_forum);
              if(empty($gett['t_hp'])) $hp = "";
              else $hp = show(_hpicon_forum, array("hp" => $gett['t_hp']));
            }

            $lastpost = show($dir."/forum_posts_show", array("nick" => cleanautor($gett['t_reg'], '', $gett['t_nick'], $gett['t_email']),
                                                             "postnr" => "",
                                                             "text" => $text,
                                                             "status" => getrank($gett['t_reg']),
                                                             "avatar" => useravatar($gett['t_reg']),
                                                             "pn" => $pn,
                                                             "icq" => $icq,
                                                             "class" => $ftxt['class'],
                                                             "hp" => $hp,
                                                             "email" => $email,
                                                             "titel" => $titel,
                                                             "ip" => $posted_ip,
                                                             "p" => ($page-1*settings::get('m_fposts')),
                                                             "edited" => $gett['edited'],
                                                             "posts" => $userposts,
                                                             "date" => _posted_by.date("d.m.y H:i", $gett['t_date'])._uhr,
                                                             "signatur" => $sig,
                                                             "zitat" => "",
                                                             "onoff" => $onoff,
                                                             "top" => "",
                                                             "lp" => cnt("{prefix_forumposts}", " WHERE sid = '".intval($_GET['id'])."'")+1));
          }

          if($userid >= 1)
            {
                $form = show("page/editor_regged", array("nick" => autor($userid),
                                                     "von" => _autor));
            } else {
            $form = show("page/editor_notregged", array("nickhead" => _nick,
                                                        "emailhead" => _email,
                                                        "hphead" => _hp));
          }

          $title = stringParser::decode($gett['topic']).' - '.$title;
          $index = show($dir."/post", array("titel" => _forum_new_post_head,
                                            "nickhead" => _nick,
                                            "emailhead" => _email,
                                            "id" => $_GET['id'],
                                            "kid" => $_GET['kid'],
                                            "zitat" => $zitat,
                                            "last_post" => _forum_lp_head,
                                            "preview" => _preview,
                                            "lastpost" => $lastpost,
                                            "bbcodehead" => _bbcode,
                                            "form" => $form,
                                            "br1" => "",
                                            "security" => _register_confirm,
                                            "ip" => _iplog_info,
                                            "br2" => "",
                                            "what" => _button_value_add,
                                            "kid" => $_GET['kid'],
                                            "id" => $_GET['id'],
                                            "dowhat" => $dowhat,
                                            "eintraghead" => _eintrag,
                                            "error" => "",
                                            "postnick" => $postnick,
                                            "postemail" => $postemail,
                                            "posthp" => '',
                                            "posteintrag" => ""));
        }
      } else {
        $index = error(show(_error_flood_post, array("sek" => settings::get('f_forum'))), 1);
      }
    }
  } elseif($do == "addpost") {
        $get_threadkid = $sql->fetch("SELECT `id`,`kid` FROM `{prefix_forumthreads}` WHERE `id` = '".(int)$_GET['id']."'");
        if(!$sql->rowCount())
        {
            $index = error(_id_dont_exist,1);
        } else {
            if(settings::get("reg_forum") && !$chkMe)
            {
                $index = error(_error_unregistered,1);
            } else {
                $checks = $sql->fetch("SELECT s2.id,s1.intern FROM `{prefix_forumkats}` AS s1
                                         LEFT JOIN `{prefix_forumsubkats}` AS s2
                                         ON s2.sid = s1.id
                                         WHERE s2.id = '".intval($_GET['kid'])."'");

                if($checks['intern'] == 1 && !permission("intforum") && !fintern($checks['id'])) {
                    exit();
                }

                if($userid >= 1) $toCheck = empty($_POST['eintrag']);
                else $toCheck = empty($_POST['nick']) || empty($_POST['email']) || empty($_POST['eintrag']) || !check_email($_POST['email']) || !$securimage->check($_POST['secure']);

                if($toCheck)
                {
                    if($userid >= 1)
                    {
                        if(empty($_POST['eintrag'])) $error = _empty_eintrag;
                        $form = show("page/editor_regged", array("nick" => autor($userid),
                                                                                                         "von" => _autor));
                    } else {
                        if(!$securimage->check($_POST['secure'])) $error = captcha_mathematic ? _error_invalid_regcode_mathematic : _error_invalid_regcode;
                        elseif(empty($_POST['nick'])) $error = _empty_nick;
                        elseif(empty($_POST['email'])) $error = _empty_email;
                        elseif(!check_email($_POST['email'])) $error = _error_invalid_email;
                        elseif(empty($_POST['eintrag'])) $error = _empty_eintrag;
                        $form = show("page/editor_notregged", array("nickhead" => _nick,
                                                                                                                "emailhead" => _email,
                                                                                                                "hphead" => _hp));
                    }

                    $error = show("errors/errortable", array("error" => $error));
                    $dowhat = show(_forum_dowhat_add_post, array("id" => $_GET['id'],
                                                                                                             "kid" => $get_threadkid['kid']));
                    $getl = $sql->fetch("SELECT * FROM `{prefix_forumposts}`
                                            WHERE kid = '".intval($get_threadkid['kid'])."'
                                            AND sid = '".intval($_GET['id'])."'
                                            ORDER BY date DESC");
                    if($sql->rowCount())
                    {
                        if(data("signatur",$getl['reg'])) $sig = _sig.bbcode::parse_html(data("signatur",$getl['reg']));
                        else $sig = "";

                        if($getl['reg'] != "0") $userposts = show(_forum_user_posts, array("posts" => userstats("forumposts",$getl['reg'])));
                        else $userposts = "";

                        if($getl['reg'] == "0") $onoff = "";
                        else $onoff = onlinecheck($getl['reg']);

                        $ftxt = hl($getl['text'], $_GET['hl']);
                        if($_GET['hl']) $text = bbcode::parse_html($ftxt['text']);
                        else $text = bbcode::parse_html($getl['text']);

                        if($chkMe == 4 || permission('ipban')) $posted_ip = $getl['ip'];
                        else $posted_ip = _logged;

                        $titel = show(_eintrag_titel_forum, array("postid" => (cnt("{prefix_forumposts}", " WHERE sid = ".intval($_GET['id']))+1),
                                                                                                "datum" => date("d.m.Y", $getl['date']),
                                                                                                "zeit" => date("H:i", $getl['date'])._uhr,
                                                                                                "url" => '#',
                                                                                                "edit" => "",
                                                                                                "delete" => ""));

                        if($getl['reg'] != 0)
                        {
                            $getu = $sql->fetch("SELECT nick,icq,hp,email FROM `{prefix_users}` WHERE id = '".$getl['reg']."'");
                            
                            $email = CryptMailto(stringParser::decode($getu['email']),_emailicon_forum);
                            $pn = show(_pn_write_forum, array("id" => $getl['reg'],
                                                                                                "nick" => $getu['nick']));
                            if(empty($getu['icq']) || $getu['icq'] == 0) $icq = "";
                            else {
                                $uin = show(_icqstatus_forum, array("uin" => $getu['icq']));
                                $icq = '<a href="http://www.icq.com/whitepages/about_me.php?uin='.$getu['icq'].'" target="_blank">'.$uin.'</a>';
                            }

                            if(empty($getu['hp'])) $hp = "";
                            else $hp = show(_hpicon_forum, array("hp" => $getu['hp']));
                        } else {
                            $icq = "";
                            $pn = "";
                            $email = CryptMailto(stringParser::decode($getl['email']),_emailicon_forum);
                            if(empty($getl['hp'])) $hp = "";
                            else $hp = show(_hpicon_forum, array("hp" => $getl['hp']));
                        }

                        $nick = autor($getl['reg'], '', $getl['nick'], stringParser::decode($getl['email']));
                        if(!empty($_GET['hl']) && $_SESSION['search_type'] == 'autor')
                        {
                            if(preg_match("#".$_GET['hl']."#i",$nick)) $ftxt['class'] = 'class="highlightSearchTarget"';
                        }

                        $lastpost = show($dir."/forum_posts_show", array("nick" => $nick,
                                                                                                                         "postnr" => "",
                                                                                                                         "text" => $text,
                                                                                                                         "status" => getrank($getl['reg']),
                                                                                                                         "avatar" => useravatar($getl['reg']),
                                                                                                                         "titel" => $titel,
                                                                                                                         "pn" => $pn,
                                                                                                                         "icq" => $icq,
                                                                                                                         "hp" => $hp,
                                                                                                                         "class" => $ftxt['class'],
                                                                                                                         "email" => $email,
                                                                                                                         "ip" => $posted_ip,
                                                                                                                         "p" => ($i+($page-1)*settings::get('m_fposts')),
                                                                                                                         "edited" => $getl['edited'],
                                                                                                                         "posts" => $userposts,
                                                                                                                         "signatur" => $sig,
                                                                                                                         "zitat" => "",
                                                                                                                         "onoff" => $onoff,
                                                                                                                         "top" => "",
                                                                                                                         "lp" => cnt("{prefix_forumposts}", " WHERE sid = '".intval($_GET['id'])."'")+1));
                    } else {
                        $gett = $sql->fetch("SELECT * FROM `{prefix_forumthreads}`
                                                WHERE kid = '".intval($get_threadkid['kid'])."'
                                                AND id = '".intval($_GET['id'])."'");

                        if(data("signatur",$gett['t_reg'])) $sig = _sig.bbcode::parse_html(data("signatur",$gett['t_reg']));
                        else $sig = "";

                        if($gett['t_reg'] != "0") $userposts = show(_forum_user_posts, array("posts" => userstats("forumposts",$gett['t_reg'])));
                        else $userposts = "";

                        if($gett['t_reg'] == "0") $onoff = "";
                        else $onoff = onlinecheck($gett['t_reg']);

                        $ftxt = hl($gett['t_text'], $_GET['hl']);
                        if($_GET['hl']) $text = bbcode::parse_html($ftxt['text']);
                        else $text = bbcode::parse_html($gett['t_text']);

                        if($chkMe == 4 || permission('ipban')) $posted_ip = $gett['ip'];
                        else $posted_ip = _logged;

                        if($gett['t_reg'] != 0)
                        {
                            $getu = $sql->fetch("SELECT nick,icq,hp,email FROM `{prefix_users}` WHERE id = '".$gett['t_reg']."'");

                            $email = CryptMailto(stringParser::decode($getu['email']),_emailicon_forum);
                            $pn = show(_pn_write_forum, array("id" => $gett['t_reg'], "nick" => $getu['nick']));
                            if(empty($getu['icq']) || $getu['icq'] == 0) $icq = "";
                            else {
                                $uin = show(_icqstatus_forum, array("uin" => $getu['icq']));
                                $icq = '<a href="http://www.icq.com/whitepages/about_me.php?uin='.$getu['icq'].'" target="_blank">'.$uin.'</a>';
                            }

                            if(empty($getu['hp'])) $hp = "";
                            else $hp = show(_hpicon_forum, array("hp" => $getu['hp']));
                        } else {
                            $icq = "";
                            $pn = "";
                            $email = CryptMailto(stringParser::decode($gett['t_email']),_emailicon_forum);
                            if(empty($gett['t_hp'])) $hp = "";
                            else $hp = show(_hpicon_forum, array("hp" => $gett['t_hp']));
                        }

                        $nick = autor($gett['t_reg'], '', $gett['t_nick'], $gett['t_email']);
                        if(!empty($_GET['hl']) && $_SESSION['search_type'] == 'autor')
                        {
                            if(preg_match("#".$_GET['hl']."#i",$nick)) $ftxt['class'] = 'class="highlightSearchTarget"';
                        }

                        $lastpost = show($dir."/forum_posts_show", array("nick" => $nick,
                                                                                                                         "postnr" => "",
                                                                                                                         "text" => $text,
                                                                                                                         "status" => getrank($gett['t_reg']),
                                                                                                                         "avatar" => useravatar($gett['t_reg']),
                                                                                                                         "ip" => $posted_ip,
                                                                                                                         "pn" => $pn,
                                                                                                                         "class" => $ftxt['class'],
                                                                                                                         "icq" => $icq,
                                                                                                                         "hp" => $hp,
                                                                                                                         "email" => $email,
                                                                                                                         "edit" => "",
                                                                                                                         "p" => ($i+($page-1)*settings::get('m_fposts')),
                                                                                                                         "delete" => "",
                                                                                                                         "edited" => $gett['edited'],
                                                                                                                         "posts" => $userposts,
                                                                                                                         "date" => _posted_by.date("d.m.y H:i", $gett['t_date'])._uhr,
                                                                                                                         "signatur" => $sig,
                                                                                                                         "zitat" => "",
                                                                                                                         "onoff" => $onoff,
                                                                                                                         "top" => "",
                                                                                                                         "lp" => cnt("{prefix_forumposts}", " WHERE sid = '".intval($_GET['id'])."'")+1));
                    }

                    $index = show($dir."/post", array("titel" => _forum_new_post_head,
                                                                                        "nickhead" => _nick,
                                                                                        "bbcodehead" => _bbcode,
                                                                                        "emailhead" => _email,
                                                                                        "zitat" => $zitat,
                                                                                        "what" => _button_value_add,
                                                                                        "preview" => _preview,
                                                                                        "form" => $form,
                                                                                        "br1" => "",
                                                                                        "br2" => "",
                                                                                        "security" => _register_confirm,
                                                                                        "lastpost" => $lastpost,
                                                                                        "last_post" => _forum_lp_head,
                                                                                        "dowhat" => $dowhat,
                                                                                        "id" => $_GET['id'],
                                                                                        "ip" => _iplog_info,
                                                                                        "kid" => $_GET['kid'],
                                                                                        "postemail" => $_POST['email'],
                                                                                        "posthp" => $_POST['hp'],
                                                                                        "postnick" => stringParser::decode($_POST['nick']),
                                                                                        "posteintrag" => stringParser::decode($_POST['eintrag']),
                                                                                        "error" => $error,
                                                                                        "eintraghead" => _eintrag));
                } else {
                    $spam = 0;
                    $getdp = $sql->fetch("SELECT * FROM `{prefix_forumposts}`
                                             WHERE kid = '".intval($get_threadkid['kid'])."'
                                             AND sid = '".intval($_GET['id'])."'
                                             ORDER BY date DESC
                                             LIMIT 1");
                    if($sql->rowCount())
                    {
                        if($userid >= 1)
                        {
                            if($userid == $getdp['reg'] && settings::get('double_post')) $spam = 1;
                            else $spam = 0;
                        } else {
                            if($_POST['nick'] == $getdp['nick'] && settings::get('double_post')) $spam = 1;
                            else $spam = 0;
                        }
                    } else {

                        $gettdp = $sql->fetch("SELECT * FROM `{prefix_forumthreads}`
                                    WHERE kid = '".intval($get_threadkid['kid'])."'
                                    AND id = '".intval($_GET['id'])."'");

                        if($userid >= 1)
                        {
                            if($userid == $gettdp['t_reg'] && settings::get('double_post')) $spam = 2;
                            else $spam = 0;
                        } else {
                            if($_POST['nick'] == $gettdp['t_nick'] && settings::get('double_post')) $spam = 2;
                            else $spam = 0;
                        }
                    }

                    if($spam == 1)
                    {
                        if($userid >= 1) $fautor = autor($userid);
                        else $fautor = autor('', '', $_POST['nick'], $_POST['email']);

                            $text = show(_forum_spam_text, array("autor" => $fautor,
                                                                                                     "ltext" => addslashes($getdp['text']),
                                                                                                     "ntext" => stringParser::encode($_POST['eintrag'])));

                                                    $sql->update("UPDATE `{prefix_forumthreads}`
                                                                                         SET `lp` = '".time()."'
                                    WHERE kid = '".intval($_GET['kid'])."'
                                    AND id = '".intval($_GET['id'])."'");

                            $sql->update("UPDATE `{prefix_forumposts}`
                                                 SET `date`   = '".time()."',
                                                         `text`   = '".$text."'
                                                 WHERE id = '".$getdp['id']."'");
                    } elseif($spam == 2) {
                        if($userid >= 1) $fautor = autor($userid);
                        else $fautor = autor('', '', $_POST['nick'], $_POST['email']);

                            $text = show(_forum_spam_text, array("autor" => $fautor,
                                                                                                     "ltext" => addslashes($gettdp['t_text']),
                                                                                                     "ntext" => stringParser::encode($_POST['eintrag'])));

                            $sql->update("UPDATE `{prefix_forumthreads}`
                                                 SET `lp`   = '".time()."',
                                                 `t_text`   = '".$text."'
                                                 WHERE id = '".$gettdp['id']."'");
                } else {
                    $sql->insert("INSERT INTO `{prefix_forumposts}`
                                         SET `kid`   = '".intval($get_threadkid['kid'])."',
                                                 `sid`   = '".intval($_GET['id'])."',
                                                 `date`  = '".time()."',
                                                 `nick`  = '".stringParser::encode($_POST['nick'])."',
                                                 `email` = '".stringParser::encode($_POST['email'])."',
                                                 `hp`    = '".links($_POST['hp'])."',
                                                 `reg`   = '".stringParser::encode($userid)."',
                                                 `text`  = '".stringParser::encode($_POST['eintrag'])."',
                                                 `ip`    = '".$userip."'");

                    $sql->update("UPDATE `{prefix_forumthreads}`
                                                SET `lp`    = '".time()."',
                                                        `first` = '0'
                                                WHERE id    = '".intval($_GET['id'])."'");
                }

                setIpcheck("fid(".$get_threadkid['kid'].")");

                    $sql->update("UPDATE `{prefix_userstats}`
                                                SET `forumposts` = forumposts+1
                                                WHERE `user`       = '".$userid."'");

                    $checkabo = $sql->select("SELECT s1.user,s1.fid,s2.nick,s2.id,s2.email FROM {prefix_f_abo} AS s1
                                    LEFT JOIN `{prefix_users}` AS s2 ON s2.id = s1.user
                                                    WHERE s1.fid = '".intval($_GET['id'])."'");
                    
                    foreach($checkabo as $getabo) {
                        if($userid != $getabo['user'])
                        {
                            $gettopic = $sql->fetch("SELECT topic FROM `{prefix_forumthreads}` WHERE id = '".intval($_GET['id'])."'");

                            $entrys = cnt("{prefix_forumposts}", " WHERE `sid` = ".intval($_GET['id']));

                            if($entrys == "0") $pagenr = "1";
                            else $pagenr = ceil($entrys/settings::get('m_fposts'));

                            $subj = show(settings::get('eml_fabo_npost_subj'), array("titel" => $title));

                            $message = show(bbcode_email(settings::get('eml_fabo_npost')), array("nick" => stringParser::decode($getabo['nick']),
                                                                            "postuser" => fabo_autor($userid),
                                                                            "topic" => $gettopic['topic'],
                                                                            "titel" => $title,
                                                                            "domain" => $httphost,
                                                                            "id" => intval($_GET['id']),
                                                                            "entrys" => $entrys+1,
                                                                            "page" => $pagenr,
                                                                            "text" => bbcode::parse_html($_POST['eintrag']),
                                                                            "clan" => settings::get('clanname')));

                            sendMail(stringParser::decode($getabo['email']),$subj,$message);
                        }
                    }

                    $entrys = cnt("{prefix_forumposts}", " WHERE `sid` = ".intval($_GET['id']));

                    if($entrys == "0") $pagenr = "1";
                    else $pagenr = ceil($entrys/settings::get('m_fposts'));

                    $lpost = show(_forum_add_lastpost, array("id" => $entrys+1,
                                                                                                     "tid" => $_GET['id'],
                                                                                                     "page" => $pagenr));

                    $index = info(_forum_newpost_successful, $lpost);
                }
            }
        }
  } elseif($do == "delete") {
    $get = $sql->fetch("SELECT * FROM `{prefix_forumposts}` WHERE id = '".intval($_GET['id'])."'");
    if($get['reg'] == $userid OR permission("forum"))
    {
      $sql->delete("DELETE FROM `{prefix_forumposts}`
                 WHERE id = '".intval($_GET['id'])."'");

      $fposts = userstats("forumposts",$get['reg'])-1;
      $sql->update("UPDATE `{prefix_userstats}`
                 SET `forumposts` = '".intval($fposts)."'
                 WHERE user = '".$get['reg']."'");

      $entrys = cnt("{prefix_forumposts}", " WHERE `sid` = ".$get['sid']);

      if($entrys == "0")
      {
        $pagenr = "1";
        $sql->update("UPDATE `{prefix_forumthreads}`
                      SET `first` = '1'
                      WHERE kid = '".$get['kid']."'");
      } else {
        $pagenr = ceil($entrys/settings::get('m_fposts'));
      }

      $lpost = show(_forum_add_lastpost, array("id" => $entrys+1,
                                               "tid" => $get['sid'],
                                               "page" => $pagenr));

      $index = info(_forum_delpost_successful, $lpost);
    }
  }
}