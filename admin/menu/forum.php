<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._config_forum_head;

        if($_GET['show'] == "subkats")
        {
          $qryk = $sql->select("SELECT s1.`name`,s2.`id`,s2.`kattopic`,s2.`subtopic`,s2.`pos` "
                  . "FROM `{prefix_forumkats}` AS `s1` "
                  . "LEFT JOIN `{prefix_forumsubkats}` AS `s2` "
                  . "ON s1.`id` = s2.`sid` "
                  . "WHERE s1.`id` = ? ORDER BY s2.`pos`;",
                  array(intval($_GET['id'])));
          foreach($qryk as $getk) {
            if(!empty($getk['kattopic']))
            {
              $subkat = show(_config_forum_subkats, array("topic" => stringParser::decode($getk['kattopic']),
                                                          "subtopic" => stringParser::decode($getk['subtopic']),
                                                          "id" => $getk['id']));

              $edit = show("page/button_edit_single", array("id" => $getk['id'],
                                                            "action" => "admin=forum&amp;do=editsubkat",
                                                            "title" => _button_title_edit));
              
              $delete = show("page/button_delete_single", array("id" => $getk['id'],
                                                                "action" => "admin=forum&amp;do=deletesubkat",
                                                                "title" => _button_title_del,
                                                                "del" => convSpace(_confirm_del_entry)));

              $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
              $subkats .= show($dir."/forum_show_subkats_show", array("subkat" => $subkat,
                                                                      "delete" => $delete,
                                                                      "class" => $class,
                                                                      "edit" => $edit));
            }

            $skathead = show(_config_forum_subkathead, array("kat" => stringParser::decode($getk['name'])));
            $add = show(_config_forum_subkats_add, array("id" => $_GET['id']));

            $show = show($dir."/forum_show_subkats", array("head" => _config_forum_head,
                                                           "subkathead" => $skathead,
                                                           "subkats" => $subkats,
                                                           "add" => $add,
                                                           "subkat" => _config_forum_subkat,
                                                           "delete" => _deleteicon_blank,
                                                           "edit" => _editicon_blank));
          }
        } else {
          $qry = $sql->select("SELECT * FROM `{prefix_forumkats}` ORDER BY `kid`;");
            foreach($qry as $get) {
          $kat = show(_config_forum_kats_titel, array("kat" => stringParser::decode($get['name']),
                                                      "id" => $get['id']));

          $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                        "action" => "admin=".$_GET['admin']."&amp;do=edit",
                                                        "title" => _button_title_edit));
          
          $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                            "action" => "admin=".$_GET['admin']."&amp;do=delete",
                                                            "title" => _button_title_del,
                                                            "del" => convSpace(_confirm_del_entry)));
          if($get['intern'] == 1)
          {
            $status = _config_forum_intern;
          } else {
            $status = _config_forum_public;
          }

          $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
          $kats .= show($dir."/forum_show_kats", array("class" => $class,
                                                       "kat" => $kat,
                                                       "status" => $status,
                                                       "skats" => cnt('{prefix_forumsubkats}', " WHERE sid = '".intval($get['id'])."'"),
                                                       "edit" => $edit,
                                                       "delete" => $delete));
        }
        $show = show($dir."/forum", array("head" => _config_forum_head,
                                          "mainkat" => _config_forum_mainkat,
                                          "edit" => _editicon_blank,
                                          "skats" => _cnt,
                                          "status" => _config_forum_status,
                                          "delete" => _deleteicon_blank,
                                          "add" => _config_forum_kats_add,
                                          "kats" => $kats));
        if($do == "newkat")
        {
          $qry = $sql->select("SELECT * FROM `{prefix_forumkats}` ORDER BY `kid`;");
            foreach($qry as $get) {
            $positions .= show(_select_field, array("value" => $get['kid']+1,
                                                    "what" => _nach.' '.stringParser::decode($get['name']),
                                                    "sel" => ""));
          }

          $show = show($dir."/katform", array("fkat" => _config_katname,
                                              "head" => _config_forum_kat_head,
                                              "fkid" => _position,
                                              "fart" => _kind,
                                              "positions" => $positions,
                                              "public" => _config_forum_public,
                                              "intern" => _config_forum_intern,
                                              "value" => _button_value_add,
                                              "kat" => ""));
        } elseif($do == "addkat") {
          if(!empty($_POST['kat']))
          {
            if($_POST['kid'] == "1" || "2") $sign = ">= ";
            else  $sign = "> ";

            $sql->update("UPDATE `{prefix_forumkats}` SET `kid` = kid+1 WHERE kid ".$sign." ?;",array(intval($_POST['kid'])));
            $sql->insert("INSERT INTO `{prefix_forumkats}` SET `kid` = ?, `name` = ?, `intern` = ?",
                    array(intval($_POST['kid']),stringParser::encode($_POST['kat']),intval($_POST['intern'])));

            $show = info(_config_forum_kat_added, "?admin=forum");
          } else {
            $show = error(_config_empty_katname, 1);
          }
        } elseif($do == "delete") {
          $get = $sql->fetch("SELECT id FROM `{prefix_forumsubkats}` WHERE sid = '".intval($_GET['id'])."'");
          $sql->delete("DELETE FROM `{prefix_forumkats}` WHERE id = '".intval($_GET['id'])."'");
          $sql->delete("DELETE FROM `{prefix_forumthreads}` WHERE kid = '".intval($get['id'])."'");
          $sql->delete("DELETE FROM `{prefix_forumposts}` WHERE kid = '".intval($get['id'])."'");
          $sql->delete("DELETE FROM `{prefix_forumsubkats}` WHERE sid = '".intval($_GET['id'])."'");

          $show = info(_config_forum_kat_deleted, "?admin=forum");
        } elseif($do == "edit") {
            $qry = $sql->select("SELECT * FROM `{prefix_forumkats}` WHERE id = '".intval($_GET['id'])."'");
            foreach($qry as $get) {
                $pos = $sql->select("SELECT * FROM `{prefix_forumkats}` ORDER BY kid;");
                foreach($pos as $getpos) {
                  if($get['name'] != $getpos['name'])
                  {
                    $positions .= show(_select_field, array("value" => $getpos['kid']+1,
                                                            "what" => _nach.' '.stringParser::decode($getpos['name'])));
                  }
                }

            if($get['intern'] == "1") $sel = 'selected="selected"';

            $show = show($dir."/katform_edit", array("fkat" => _config_katname,
                                                     "head" => _config_forum_kat_head_edit,
                                                     "fkid" => _position,
                                                     "fart" => _kind,
                                                     "id" => $get['id'],
                                                     "sel" => $sel,
                                                     "positions" => $positions,
                                                     "public" => _config_forum_public,
                                                     "intern" => _config_forum_intern,
                                                     "value" => _button_value_edit,
                                                     "kat" => stringParser::decode($get['name'])));
          }
        } elseif($do == "editkat") {
          if(empty($_POST['kat']))
          {
            $show = error(_config_empty_katname, 1);
          } else {
            if($_POST['kid'] == "lazy"){
              $kid = "";
            }else{
              $kid = "`kid` = '".intval($_POST['kid'])."',";

              if($_POST['kid'] == "1" || "2") $sign = ">= ";
              else  $sign = "> ";
              $sql->update("UPDATE `{prefix_forumkats}` SET `kid` = kid+1 WHERE `kid` ".$sign." '".intval($_POST['kid'])."'");
            }


            $sql->update("UPDATE `{prefix_forumkats}` SET `name`    = '".stringParser::encode($_POST['kat'])."', ".$kid." `intern`  = '".intval($_POST['intern'])."' WHERE id = '".intval($_GET['id'])."'");

            $show = info(_config_forum_kat_edited, "?admin=forum");
          }
        } elseif($do == "newskat") {
          $qry = $sql->select("SELECT * FROM `{prefix_forumsubkats}` WHERE sid = " . (int) $_GET['id']." ORDER BY pos");
            foreach($qry as $get) {
            $positions .= show(_select_field, array("value" => $get['pos']+1,
                                                    "what" => _nach.' '.stringParser::decode($get['kattopic']),
                                                    "sel" => ""));
          }
          $show = show($dir."/skatform", array("head" => _config_forum_add_skat,
                                               "fkat" => _config_forum_skatname,
                                               "fstopic" => _config_forum_stopic,
                                               "skat" => "",
                                               "what" => "addskat",
                                               "stopic" => "",
                                               "id" => $_GET['id'],
                                               "nothing" => "",
                                               "tposition" => _position,
                                               "position" => $positions,
                                               "value" => _button_value_add));
        } elseif($do== "addskat") {
          if(empty($_POST['skat']))
          {
            $show = error(_config_forum_empty_skat,1);
          } else {
            if($_POST['order'] == "1" || "2") $sign = ">= ";
            else  $sign = "> ";

            $sql->update("UPDATE `{prefix_forumsubkats}` SET `pos` = pos+1 WHERE `pos` ".$sign." '".intval($_POST['order'])."'");
            $sql->insert("INSERT INTO `{prefix_forumsubkats}` SET `sid` = '".intval($_GET['id'])."', `pos` = '".intval($_POST['order'])."', `kattopic` = '".stringParser::encode($_POST['skat'])."', `subtopic` = '".stringParser::encode($_POST['stopic'])."'");

            $show = info(_config_forum_skat_added, "?admin=forum&show=subkats&amp;id=".$_GET['id']."");
          }
        } elseif($do == "editsubkat") {
          $qry = $sql->select("SELECT `sid`,`kattopic` FROM `{prefix_forumsubkats}` WHERE `id` = ?;",array(intval($_GET['id'])));
          foreach($qry as $get) {
            $pos = $sql->select("SELECT `kattopic`,`pos` FROM `{prefix_forumsubkats}` WHERE `sid` = ? ORDER BY `pos`;",array($get['sid']));
            foreach($pos as $getpos) {
              if($get['kattopic'] != $getpos['kattopic'])
              {
                $positions .= show(_select_field, array("value" => $getpos['pos']+1,
                                                        "what" => _nach.' '.stringParser::decode($getpos['kattopic'])));
              }
            }

          $show = show($dir."/skatform", array("head" => _config_forum_edit_skat,
                                               "fkat" => _config_forum_skatname,
                                               "fstopic" => _config_forum_stopic,
                                               "skat" => stringParser::decode($get['kattopic']),
                                               "what" => "editskat",
                                               "stopic" => stringParser::decode($get['subtopic']),
                                               "id" => $_GET['id'],
                                               "sid" => $get['sid'],
                                               "tposition" => _position,
                                               "position" => $positions,
                                               "value" => _button_value_edit));
            } //--> End while subkat sort
        } elseif($do == "editskat") {
          if(empty($_POST['skat']))
          {
            $show = error(_config_forum_empty_skat,1);
          } else {

            if($_POST['order'] == "lazy"){
              $order = "";
            }else{
              $order = "`pos` = '".intval($_POST['order'])."',";

              if($_POST['order'] == "1" || "2") $sign = ">= ";
              else  $sign = "> ";
              $sql->update("UPDATE `{prefix_forumsubkats}`
                        SET `pos` = pos+1
                        WHERE `pos` ".$sign." '".intval($_POST['order'])."'");
            }

            $sql->update("UPDATE `{prefix_forumsubkats}`
                       SET `kattopic` = '".stringParser::encode($_POST['skat'])."',
                           ".$order."
                           `subtopic` = '".stringParser::encode($_POST['stopic'])."'
                       WHERE id = '".intval($_GET['id'])."'");

            $show = info(_config_forum_skat_edited, "?admin=forum&show=subkats&amp;id=".$_POST['sid']."");
          }
        } elseif($do == "deletesubkat") {
          $get = $sql->fetch("SELECT `id`,`sid` FROM `{prefix_forumsubkats}` WHERE id = ?;",array(intval($_GET['id'])));
          $sql->delete("DELETE FROM `{prefix_forumsubkats}` WHERE `id` = ?;",array(intval($get['id'])));
          $sql->delete("DELETE FROM `{prefix_forumthreads}` WHERE `kid` = ?;",array(intval($get['id'])));
          $sql->delete("DELETE FROM `{prefix_forumposts}` WHERE `kid` = ?;",array(intval($get['id'])));
          $show = info(_config_forum_skat_deleted, "?admin=forum&show=subkats&amp;id=".$get['sid']."");
        }
      }