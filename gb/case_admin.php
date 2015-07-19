<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_GB')) exit();

  if(!permission("gb"))
  {
    $index = error(_error_wrong_permissions, 1);
  } else {
    if($do == "addcomment")
    {
      $qry = db("SELECT * FROM ".$db['gb']." WHERE id = '".intval($_GET['id'])."'");
      $get = _fetch($qry);

      if($get['hp']) $gbhp = show(_hpicon, array("hp" => $get['hp']));
      else $gbhp = "";

      if($get_email) $gbemail = CryptMailto(re($get['email']));
      else $gbemail = "";

      if(permission("gb")) $comment = show(_gb_commenticon, array("id" => $get['id']));
      else $comment = "";

          if($get['reg'] == "0")
          {
              $gbtitel = show(_gb_titel_noreg, array("postid" => "?",
                                                                                           "nick" => re($get['nick']),
                                               "edit" => "",
                                               "delete" => "",
                                               "comment" => "",
                                               "public" => "",
                                               "uhr" => _uhr,
                                                                                           "email" => $gbemail,
                                                                                           "datum" => date("d.m.Y", $get['datum']),
                                                                                           "zeit" => date("H:i", $get['datum']),
                                                                                           "hp" => $gbhp));
          } else {
              $gbtitel = show(_gb_titel, array("postid" => "?",
                                                                               "nick" => data("nick",$get['reg']),
                                         "edit" => "",
                                         "public" => "",
                                         "delete" => "",
                                         "uhr" => _uhr,
                                         "comment" => "",
                                                                               "id" => $get['reg'],
                                                                                 "email" => $gbemail,
                                                                                 "datum" => date("d.m.Y", $get['datum']),
                                                                                 "zeit" => date("H:i", $get['datum']),
                                                                                "hp" => $gbhp));
          }

          $entry = show($dir."/gb_show", array("gbtitel" => $gbtitel,
                                                                             "nachricht" => bbcode($get['nachricht']),
                                           "editby" => bbcode($get['editby']),
                                           "ip" => $get['ip']));

      $index = show($dir."/gb_addcomment", array("head" => _gb_addcomment_head,
                                                 "entry" => $entry,
                                                 "what" => _button_value_add,
                                                 "id" => $_GET['id'],
                                                 "head_gb" => _gb_addcomment_headgb));
    } elseif($do == "postcomment") {
      $qry = db("SELECT * FROM ".$db['gb']."
                 WHERE id = '".intval($_GET['id'])."'");
      $get = _fetch($qry);

      $comment = show($dir."/commentlayout", array("nick" => autor($userid),
                                                   "datum" => date("d.m.Y H:i", time())._uhr,
                                                   "comment" => up($_POST['comment']),
                                                   "nachricht" => $get['nachricht']));

      $upd = db("UPDATE ".$db['gb']."
                 SET `nachricht` = '".$comment."'
                 WHERE id = '".intval($_GET['id'])."'");

      $index = info(_gb_comment_added, "../gb/");
    }
  }