<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Forum')) {
  $checks = $sql->fetch("SELECT s3.name,s3.intern,s2.sid,s1.kid,s2.id
               FROM `{prefix_forumkats}` s3, `{prefix_forumsubkats}` s2, `{prefix_forumthreads}` s1
               WHERE s1.kid = s2.id
               AND s2.sid = s3.id
               AND s1.id = '".intval($_GET['id'])."'");

  if($sql->rows("SELECT * FROM `{prefix_forumthreads}` WHERE id = '".intval($_GET['id'])."' AND kid = '".$checks['kid']."'"))
  {
    if($checks['intern'] == 1 && !permission("intforum") && !fintern($checks['id']))
    {
      $index = error(_error_wrong_permissions, 1);
    } else {
      $sql->update("UPDATE `{prefix_forumthreads}`
                    SET `hits` = hits+1
                    WHERE id = '".intval($_GET['id'])."'");

      $qryp = $sql->select("SELECT * FROM `{prefix_forumposts}`
                  WHERE sid = '".intval($_GET['id'])."'
                  ORDER BY id
                  LIMIT ".($page - 1)*settings::get('m_fposts').",".settings::get('m_fposts')."");

      $entrys = cnt("{prefix_forumposts}", " WHERE sid = ".intval($_GET['id']));
      $i = 2;

      if($entrys == 0) $pagenr = "1";
      else $pagenr = ceil($entrys/settings::get('m_fposts'));

      if(!empty($_GET['hl'])) $hL = '&amp;hl='.$_GET['hl'];
      else                    $hL = '';

      $lpost = show(_forum_lastpost, array("id" => $entrys+1,
                                           "tid" => $_GET['id'],
                                           "page" => $pagenr.$hL));

      foreach($qryp as $getp) {
        if(data("signatur",$getp['reg'])) $sig = _sig.bbcode::parse_html(data("signatur",$getp['reg']));
        else                               $sig = "";

        if($getp['reg'] != 0) $userposts = show(_forum_user_posts, array("posts" => userstats("forumposts",$getp['reg'])));
        else                  $userposts = "";

        if($getp['reg'] == 0) $onoff = "";
        else                  $onoff = onlinecheck($getp['reg']);

        $zitat = show("page/button_zitat", array("id" => $_GET['id'],
                                                 "action" => "action=post&amp;do=add&amp;kid=".$getp['kid']."&amp;zitat=".$getp['id'],
                                                 "title" => _button_title_zitat));

        if($getp['reg'] == $userid || permission("forum"))
        {
          $edit = show("page/button_edit_single", array("id" => $getp['id'],
                                                       "action" => "action=post&amp;do=edit",
                                                       "title" => _button_title_edit));

          $delete = show("page/button_delete_single", array("id" => $getp['id'],
                                                           "action" => "action=post&amp;do=delete",
                                                           "title" => _button_title_del,
                                                           "del" => _confirm_del_entry));
        } else {
          $delete = "";
          $edit = "";
        }

        $ftxt = hl($getp['text'], (isset($_GET['hl']) ? $_GET['hl'] : ''));
        if(isset($_GET['hl'])) $text = bbcode::parse_html($ftxt['text']);
        else $text = bbcode::parse_html($getp['text']);

        if($chkMe == 4 || permission('ipban')) $posted_ip = $getp['ip'];
        else $posted_ip = _logged;

        $titel = show(_eintrag_titel_forum, array("postid" => $i+($page-1)*settings::get('m_fposts'),
                                                                                    "datum" => date("d.m.Y", $getp['date']),
                                                                                    "zeit" => date("H:i", $getp['date'])._uhr,
                                            "url" => '?action=showthread&amp;id='.intval($_GET['id']).'&amp;page='.$page.'#p'.($i+($page-1)*settings::get('m_fposts')),
                                            "edit" => $edit,
                                            "delete" => $delete));

        if($getp['reg'] != 0)
        {
          $getu = $sql->fetch("SELECT nick,icq,hp,email FROM `{prefix_users}` WHERE id = '".$getp['reg']."'");
          $email = CryptMailto(stringParser::decode($getu['email']),_emailicon_forum);
          $pn = show(_pn_write_forum, array("id" => $getp['reg'],"nick" => $getu['nick']));
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
          $email = CryptMailto(stringParser::decode($getp['email']),_emailicon_forum);
          if(empty($getp['hp'])) $hp = "";
          else $hp = show(_hpicon_forum, array("hp" => $getp['hp']));
        }

        $nick = autor($getp['reg'], '', $getp['nick'], stringParser::decode($getp['email']));
        if(!empty($_GET['hl']) && $_SESSION['search_type'] == 'autor')
        {
          if(preg_match("#".$_GET['hl']."#i",$nick)) $ftxt['class'] = 'class="highlightSearchTarget"';
        }

        $show .= show($dir."/forum_posts_show", array("nick" => $nick,
                                                      "postnr" => "#".($i+($page-1)*settings::get('m_fposts')),
                                                      "p" => ($i+($page-1)*settings::get('m_fposts')),
                                                      "text" => $text,
                                                      "pn" => $pn,
                                                      "class" => $ftxt['class'],
                                                      "icq" => $icq,
                                                      "hp" => $hp,
                                                      "email" => $email,
                                                      "status" => getrank($getp['reg']),
                                                      "avatar" => useravatar($getp['reg']),
                                                      "ip" => $posted_ip,
                                                      "edited" => stringParser::decode($getp['edited']),
                                                      "posts" => $userposts,
                                                      "titel" => $titel,
                                                      "signatur" => $sig,
                                                      "zitat" => $zitat,
                                                      "onoff" => $onoff,
                                                      "top" => _topicon,
                                                      "lp" => cnt("{prefix_forumposts}", " WHERE sid = '".intval($_GET['id'])."'")+1));
        $i++;
      }

      $get = $sql->fetch("SELECT * FROM `{prefix_forumthreads}` WHERE id = '".intval($_GET['id'])."'");

      $getw = $sql->fetch("SELECT s1.kid,s1.topic,s2.kattopic,s2.sid
                  FROM `{prefix_forumthreads}` AS s1
                  LEFT JOIN `{prefix_forumsubkats}` AS s2
                  ON s1.kid = s2.id
                  WHERE s1.id = '".intval($_GET['id'])."'");

      $kat = $sql->fetch("SELECT name FROM `{prefix_forumkats}`
                    WHERE id = '".$getw['sid']."'");

      $wheres = show(_forum_post_where, array("wherepost" => stringParser::decode($getw['topic']),
                                              "wherekat" => stringParser::decode($getw['kattopic']),
                                              "mainkat" => stringParser::decode($kat['name']),
                                              "tid" => $_GET['id'],
                                              "kid" => $getw['kid']));
      if($get['t_reg'] == "0")
      {
        $userposts = "";
        $onoff = "";
      } else {
        $onoff = onlinecheck($get['t_reg']);
        $userposts = show(_forum_user_posts, array("posts" => userstats("forumposts",$get['t_reg'])));
      }

      $zitat = show("page/button_zitat", array("id" => $_GET['id'],
                                               "action" => "action=post&amp;do=add&amp;kid=".$getw['kid']."&amp;zitatt=".$get['id'],
                                               "title" => _button_title_zitat));
      if($get['closed'] == 1)
      {
        $add = show("page/button_closed", array());
      } else {
        $add = show(_forum_addpost, array("id" => $_GET['id'],
                                          "kid" => $getw['kid']));
      }

      $nav = nav($entrys,settings::get('m_fposts'),"?action=showthread&amp;id=".$_GET['id'].$hL);

      if(data("signatur",$get['t_reg'])) $sig = _sig.bbcode::parse_html(data("signatur",$get['t_reg']));
      else $sig = "";

      $editt = '';
      if($get['t_reg'] == $userid || permission("forum"))
        $editt = show("page/button_edit_single", array("id" => $get['id'],
                                                      "action" => "action=thread&amp;do=edit",
                                                      "title" => _button_title_edit));

      $admin = '';
      if(permission("forum"))
      {
        $sticky = $get['sticky'] ? 'checked="checked"' : "";
        $global = $get['global'] ? 'checked="checked"' : "";

        if($get['closed'] == "1")
        {
          $closed = 'checked="checked"';
          $opened = "";
        } else {
          $opened = 'checked="checked"';
          $closed = "";
        }

        $qryok = $sql->select("SELECT * FROM `{prefix_forumkats}` ORDER BY kid");
        $move = '';
        foreach($qryok as $getok) {
          $skat = "";
          $qryo = $sql->select("SELECT * FROM `{prefix_forumsubkats}` WHERE sid = '".$getok['id']."' ORDER BY kattopic");
          foreach($qryo as $geto) {
            $skat .= show(_forum_select_field_skat, array("value" => $geto['id'],"what" => stringParser::decode($geto['kattopic'])));
          }

          $move .= show(_forum_select_field_kat, array("value" => "lazy",
                                                       "what" => stringParser::decode($getok['name']),
                                                       "skat" => $skat));
        }

        $admin = show($dir."/admin", array("admin" => _admin,
                                           "id" => $get['id'],
                                           "open" => _forum_admin_open,
                                           "close" => _forum_admin_close,
                                           "asticky" => _forum_admin_addsticky,
                                           "delete" => _forum_admin_delete,
                                           "moveto" => _forum_admin_moveto,
                                           "aglobal" => _forum_admin_global,
                                           "move" => $move,
                                           "closed" => $closed,
                                           "opened" => $opened,
                                           "global" => $global,
                                           "sticky" => $sticky));
      }

      $hl = isset($_GET['hl']) ? $_GET['hl'] : '';
      $ftxt = hl($get['t_text'], $hl);
      if(isset($_GET['hl'])) $text = stringParser::decode($ftxt['text']);
      else $text = bbcode::parse_html($get['t_text']);

      if($chkMe == 4 || permission('ipban')) $posted_ip = $get['ip'];
      else $posted_ip = _logged;

      $titel = show(_eintrag_titel_forum, array("postid" => "1",
                                                "datum" => date("d.m.Y", $get['t_date']),
                                                "zeit" => date("H:i", $get['t_date'])._uhr,
                                                "url" => '?action=showthread&amp;id='.intval($_GET['id']).'&amp;page=1#p1',
                                                "edit" => $editt,
                                                "delete" => ""));


      if($get['t_reg'] != 0)
      {
        $getu = $sql->fetch("SELECT nick,icq,hp,email FROM `{prefix_users}` WHERE id = '".$get['t_reg']."'");
        $email = CryptMailto(stringParser::decode($getu['email']),_emailicon_forum);
        $pn = show(_pn_write_forum, array("id" => $get['t_reg'],"nick" => $getu['nick']));
        if(empty($getu['icq']) || $getu['icq'] == 0) $icq = "";
            else {
          $uin = show(_icqstatus_forum, array("uin" => $getu['icq']));
          $icq = '<a href="http://www.icq.com/whitepages/about_me.php?uin='.$getu['icq'].'" target="_blank">'.$uin.'</a>';
            }

        if(empty($getu['hp'])) $hp = "";
        else $hp = show(_hpicon_forum, array("hp" => $getu['hp']));
      } else {
        $pn = "";
        $icq = "";
        $email = CryptMailto(stringParser::decode($get['t_email']),_emailicon_forum);
        if(empty($get['t_hp'])) $hp = "";
        else $hp = show(_hpicon_forum, array("hp" => $get['t_hp']));
      }

      $nick = autor($get['t_reg'], '', $get['t_nick'], $get['t_email']);
      if(!empty($_GET['hl']) && $_SESSION['search_type'] == 'autor')
      {
        if(preg_match("#".$_GET['hl']."#i",$nick)) $ftxt['class'] = 'class="highlightSearchTarget"';
      }

      $abo = $sql->rows("SELECT user FROM `{prefix_f_abo}`
                 WHERE user = '".$userid."'
                 AND fid = '".intval($_GET['id'])."'") ? 'checked="checked"' : '';
      if(!$chkMe) {
          $f_abo = '';
      } else {
          $f_abo = show($dir."/forum_abo", array("id" => intval($_GET['id']),
                                             "abo" => $abo,
                                             "abo_info" => _foum_fabo_checkbox,
                                             "abo_title" => _forum_abo_title,
                                             "submit" => _button_value_save));
        }

      $vote = "";
      if(!empty($get['vote'])) {
        include_once(basePath.'/inc/menu-functions/fvote.php');
        $vote = '<tr><td>'.fvote($get['vote']).'</td></tr>';
      }

      $where = $where.' - '.stringParser::decode($getw['topic']);
      $index = show($dir."/forum_posts", array("head" => _forum_head,
                                               "where" => $wheres,
                                               "admin" => $admin,
                                               "nick" => $nick,
                                               "threadhead" => stringParser::decode($getw['topic']),
                                               "titel" => $titel,
                                               "postnr" => "1",
                                               "class" => $ftxt['class'],
                                               "pn" => $pn,
                                               "icq" => $icq,
                                               "hp" => $hp,
                                               "email" => $email,
                                               "posts" => $userposts,
                                               "text" => $text,
                                               "status" => getrank($get['t_reg']),
                                               "avatar" => useravatar($get['t_reg']),
                                               "edited" => stringParser::decode($get['edited']),
                                               "signatur" => $sig,
                                               "date" => _posted_by.date("d.m.y H:i", $get['t_date'])._uhr,
                                               "zitat" => $zitat,
                                               "onoff" => $onoff,
                                               "ip" => $posted_ip,
                                               "top" => _topicon,
                                               "lpost" => $lpost,
                                               "lp" => cnt("{prefix_forumposts}", " WHERE sid = '".intval($_GET['id'])."'")+1,
                                               "add" => $add,
                                               "nav" => $nav,
                                               "vote" => $vote,
                                               "f_abo" => $f_abo,
                                               "show" => $show));
    }
  } else {
    $index = error(_error_wrong_permissions, 1);
  }
}