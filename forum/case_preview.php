<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Forum')) {
  header("Content-type: text/html; charset=utf-8");
  if($_GET['what'] == 'thread')
  {
    if($do == 'editthread')
    {
      $get = $sql->fetch("SELECT * FROM `{prefix_forumthreads}` WHERE id = '".intval($_GET['id'])."'");

      $get_datum = $get['t_date'];

      if($get['t_reg'] == 0) $guestCheck = false;
      else {
        $guestCheck = true;
        $pUId = $get['t_reg'];
      }
      $editedby = show(_edited_by, array("autor" => cleanautor($userid),
                                         "time" => date("d.m.Y H:i", time())._uhr));
      $tID = $get['id'];
    } else {
      $get_datum = time();

      if(!$chkMe) $guestCheck = false;
      else {
        $guestCheck = true;
        $pUId = $userid;
      }
      $tID = $_GET['kid'];
    }

    $titel = show(_eintrag_titel_forum, array("postid" => "1",
                                                                            "datum" => date("d.m.Y", $get_datum),
                                                                              "zeit" => date("H:i", $get_datum)._uhr,
                                        "url" => '#',
                                        "edit" => "",
                                        "delete" => ""));
    if($guestCheck)
    {
      $getu = $sql->fetch("SELECT nick,icq,hp,email FROM `{prefix_users}` WHERE id = '".$pUId."'");

      $email = CryptMailto(stringParser::decode($getu['email']),_emailicon_forum);
      $pn = _forum_pn_preview;
      if(empty($getu['icq']) || $getu['icq'] == 0) $icq = "";
      else {
        $uin = show(_icqstatus_forum, array("uin" => $getu['icq']));
        $icq = '<a href="http://www.icq.com/whitepages/about_me.php?uin='.$getu['icq'].'" target="_blank">'.$uin.'</a>';
        }

      if(empty($getu['hp'])) $hp = "";
      else $hp = show(_hpicon_forum, array("hp" => $getu['hp']));
      if(data("signatur",$pUId)) $sig = _sig.bbcode::parse_html(data("signatur",$pUId),true);
      else $sig = "";
      $onoff = onlinecheck($userid);
      $userposts = show(_forum_user_posts, array("posts" => userstats("forumposts",$pUId)+1));
    } else {
        $pn = "";
        $icq = "";
        $email = CryptMailto($_POST['email'],_emailicon_forum);
        if(empty($_POST['hp'])) $hp = "";
        else $hp = show(_hpicon_forum, array("hp" => links($_POST['hp'])));
      }

    $getw = $sql->fetch("SELECT s1.kid,s1.topic,s2.kattopic,s2.sid
                FROM `{prefix_forumthreads}` AS s1
                LEFT JOIN `{prefix_forumsubkats}` AS s2
                ON s1.kid = s2.id
                WHERE s1.id = '".intval($tID)."'");

    $kat = $sql->fetch("SELECT name FROM `{prefix_forumkats}` WHERE id = '".$getw['sid']."'");

    $wheres = show(_forum_post_where_preview, array("wherepost" => stringParser::decode($_POST['topic']),
                                                    "wherekat" => stringParser::decode($getw['kattopic']),
                                                    "mainkat" => stringParser::decode($kat['name']),
                                                    "tid" => $_GET['id'],
                                                    "kid" => $getw['kid']));

    if(empty($get['vote'])) $vote = "";
      else $vote = '<tr><td>'.fvote($get['vote']).'</td></tr>';

    if(!empty($_POST['question '])) $vote = _forum_vote_preview;
    else $vote = "";

    $index = show($dir."/forum_posts", array("head" => _forum_head,
                                             "where" => $wheres,
                                             "admin" => "",
                                             "class" => 'class="commentsRight"',
                                             "nick" => cleanautor($pUId, '', $_POST['nick'], $_POST['email']),
                                             "threadhead" => stringParser::decode($_POST['topic']),
                                             "titel" => $titel,
                                             "postnr" => "1",
                                             "pn" => $pn,
                                             "icq" => $icq,
                                             "hp" => $hp,
                                             "email" => $email,
                                             "posts" => $userposts,
                                             "text" =>  bbcode::parse_html($_POST['eintrag'],true).$editedby,
                                             "status" => getrank($pUId),
                                             "avatar" => useravatar($pUId),
                                             "edited" => $get['edited'],
                                             "signatur" => $sig,
                                             "date" => _posted_by.date("d.m.y H:i", time())._uhr,
                                             "zitat" => _forum_zitat_preview,
                                             "onoff" => $onoff,
                                             "ip" => $userip.'<br />'._only_for_admins,
                                             "top" => _topicon,
                                             "lpost" => $lpost,
                                             "lp" => "",
                                             "add" => "",
                                             "nav" => "",
                                             "vote" => $vote,
                                             "f_abo" => "",
                                             "show" => $show));
    update_user_status_preview();
    exit(utf8_encode('<table class="mainContent" cellspacing="1" style="margin-top:17px">'.$index.'</table>'));
  } else {
    if($do == 'editpost')
    {
      $get = $sql->fetch("SELECT * FROM `{prefix_forumposts}` WHERE id = '".intval($_GET['id'])."'");
      $get_datum = $get['date'];

      if($get['reg'] == 0) $guestCheck = false;
      else {
        $guestCheck = true;
        $pUId = $get['reg'];
      }
      $editedby = show(_edited_by, array("autor" => cleanautor($userid),
                                         "time" => date("d.m.Y H:i", time())._uhr));
      $tID = $get['sid'];
      $cnt = "?";
    } else {
      $get_datum = time();

      if(!$chkMe) $guestCheck = false;
      else {
        $guestCheck = true;
        $pUId = $userid;
      }
      $tID = $_GET['id'];
      $cnt = cnt("{prefix_forumposts}", " WHERE sid = '".intval($_GET['id'])."'")+2;
    }

    $titel = show(_eintrag_titel_forum, array("postid" => $cnt,
                                                                          "datum" => date("d.m.Y",$get_datum),
                                                                             "zeit" => date("H:i",$get_datum)._uhr,
                                        "url" => '#',
                                        "edit" => "",
                                        "delete" => ""));
    if($guestCheck)
    {
      $getu = $sql->fetch("SELECT nick,icq,hp,email FROM `{prefix_users}` WHERE id = '".intval($pUId)."'");

      $email = CryptMailto(stringParser::decode($getu['email']),_emailicon_forum);
      $pn = _forum_pn_preview;
      if(empty($getu['icq']) || $getu['icq'] == 0) $icq = "";
      else {
       $uin = show(_icqstatus_forum, array("uin" => $getu['icq']));
       $icq = '<a href="http://www.icq.com/whitepages/about_me.php?uin='.$getu['icq'].'" target="_blank">'.$uin.'</a>';
      }

      if(empty($getu['hp'])) $hp = "";
      else $hp = show(_hpicon_forum, array("hp" => $getu['hp']));
      if(data("signatur",$pUId)) $sig = _sig.bbcode::parse_html(data("signatur",$pUId),true);
      else $sig = "";
    } else {
      $icq = "";
      $pn = "";
      $email = CryptMailto($_POST['email'],_emailicon_forum);
      if(empty($_POST['hp'])) $hp = "";
      else $hp = show(_hpicon_forum, array("hp" => links($_POST['hp'])));
    }

    $index = show($dir."/forum_posts_show", array("nick" => cleanautor($pUId, '', $_POST['nick'], $_POST['email']),
                                                  "postnr" => "#".($i+($page-1)*settings::get('m_fposts')),
                                                  "p" => ($i+($page-1)*settings::get('m_fposts')),
                                                  "class" => 'class="commentsRight"',
                                                  "text" => bbcode::parse_html($_POST['eintrag'],false).$editedby,
                                                  "pn" => $pn,
                                                  "icq" => $icq,
                                                  "hp" => $hp,
                                                  "email" => $email,
                                                  "status" => getrank($pUId),
                                                  "avatar" => useravatar($pUId),
                                                  "ip" => $userip.'<br />'._only_for_admins,
                                                  "edited" => "",
                                                  "posts" => $userposts,
                                                  "titel" => $titel,
                                                  "signatur" => $sig,
                                                  "zitat" => _forum_zitat_preview,
                                                  "onoff" => $onoff,
                                                  "p" => ""));

    update_user_status_preview();
    exit(utf8_encode('<table class="mainContent" cellspacing="1" style="margin-top:17px">'.$index.'</table>'));
  }
}