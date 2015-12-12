<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Forum')) {
  if(permission("forum"))
  {
    if($do == "mod")
    {
      if(isset($_POST['delete']))
      {
         $getv = $sql->fetch("SELECT * FROM `{prefix_forumthreads}` WHERE id = '".intval($_GET['id'])."'");
        
        $userPostReduction = array();
		    $userPostReduction[$getv['t_reg']] = 1;

        if(!empty($getv['vote']))
        {
        $sql->delete("DELETE FROM `{prefix_votes}` WHERE id = '".$getv['vote']."'");

        $sql->delete("DELETE FROM `{prefix_vote_results}` WHERE vid = '".$getv['vote']."'");

        setIpcheck("vid_".$getv['vote']);
        }
            $sql->delete("DELETE FROM `{prefix_forumthreads}` WHERE id = '".intval($_GET['id'])."'");

        // grab user to reduce post count
        $tmpSid = intval($_GET['id']);
        $userPosts = $sql->select('SELECT p.`reg` FROM `{prefix_forumposts}` p WHERE sid = ' . $tmpSid . ' AND p.`reg` != 0');
        foreach($userPosts as $get) {
            if(!isset($userPostReduction[$get['reg']])) {
                $userPostReduction[$get['reg']] = 1;
            } else {
                $userPostReduction[$get['reg']] = $userPostReduction[$get['reg']] + 1;
            }
        }
        
        foreach($userPostReduction as $key_id => $value_postDecrement) {
            $sql->update('UPDATE {prefix_userstats}'.
                 ' SET `forumposts` = `forumposts` - '. $value_postDecrement .
                 ' WHERE user = ' . $key_id);
        }
        
        $sql->delete("DELETE FROM `{prefix_forumposts}` WHERE sid = '" . $tmpSid . "'");
        $sql->delete("DELETE FROM {prefix_f_abo} WHERE fid = '".intval($_GET['id'])."'");
        
        $index = info(_forum_admin_thread_deleted, "../forum/");
      } else {
        if($_POST['closed'] == "0")
        {
          $sql->update("UPDATE `{prefix_forumthreads}`
                      SET `closed` = '0'
                      WHERE id = '".intval($_GET['id'])."'");
        } elseif($_POST['closed'] == "1") {
          $sql->update("UPDATE `{prefix_forumthreads}`
                       SET `closed` = '1'
                       WHERE id = '".intval($_GET['id'])."'");
        }

        if(isset($_POST['sticky']))
        {
          $sql->update("UPDATE `{prefix_forumthreads}`
                        SET `sticky` = '1'
                        WHERE id = '".intval($_GET['id'])."'");
        } else {
          $sql->update("UPDATE `{prefix_forumthreads}`
                        SET `sticky` = '0'
                        WHERE id = '".intval($_GET['id'])."'");
        }

        if(isset($_POST['global']))
        {
          $sql->update("UPDATE `{prefix_forumthreads}`
                        SET `global` = '1'
                        WHERE id = '".intval($_GET['id'])."'");
        } else {
          $sql->update("UPDATE `{prefix_forumthreads}`
                        SET `global` = '0'
                        WHERE id = '".intval($_GET['id'])."'");
        }

        if($_POST['move'] == "lazy")
        {
          $index = info(_forum_admin_modded, "?action=showthread&amp;id=".$_GET['id']."");
        } else {
          $sql->update("UPDATE `{prefix_forumthreads}`
                      SET `kid` = '".$_POST['move']."'
                      WHERE id = '".intval($_GET['id'])."'");

          $sql->update("UPDATE `{prefix_forumposts}`
                      SET `kid` = '".$_POST['move']."'
                      WHERE sid = '".intval($_GET['id'])."'");

          $getm = $sql->fetch("SELECT s1.kid,s2.kattopic,s2.id
                      FROM `{prefix_forumthreads}` AS s1
                      LEFT JOIN `{prefix_forumsubkats}` AS s2
                      ON s1.kid = s2.id
                      WHERE s1.id = '".intval($_GET['id'])."'");

          $i_move = show(_forum_admin_do_move, array("kat" => stringParser::decode($getm['kattopic'])));
          $index = info($i_move, "?action=showthread&amp;id=".$_GET['id']."");
        }
      }
    }
  } else {
    $index = error(_error_wrong_permissions, 1);
  }
}
