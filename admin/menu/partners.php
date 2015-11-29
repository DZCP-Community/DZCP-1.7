<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._partners_head;

      if($do == "add")
      {
        $files = get_files(basePath.'/banner/partners/',false,true);
        for($i=0; $i<count($files); $i++)
        {
          $banners .= show(_partners_select_icons, array("icon" => $files[$i],
                                                         "sel" => ""));
        }
        $show = show($dir."/form_partners", array("do" => "addbutton",
                                                  "head" => _partners_add_head,
                                                  "nothing" => "",
                                                  "banner" => _partners_button,
                                                  "link" => _link,
                                                  "e_link" => "",
                                                  "e_textlink" => "",
                                                  "or" => _or,
                                                  "textlink" => _partnerbuttons_textlink,
                                                  "banners" => $banners,
                                                  "what" => _button_value_add));
      } elseif($do == "addbutton") {
        if(empty($_POST['link']))
        {
          $show = error(_empty_url, 1);
        } else {
          $sql->insert("INSERT INTO `{prefix_partners}` SET `link` = ?, `banner`   = ?, `textlink` = ?;",
                  array(up(links($_POST['link'])),up(empty($_POST['textlink']) ? $_POST['banner'] : $_POST['textlink']),intval(empty($_POST['textlink']) ? 0 : 1)));

          $show = info(_partners_added, "?admin=partners");
        }
      } elseif($do == "edit") {
        $get = $sql->fetch("SELECT * FROM `{prefix_partners}` WHERE `id` = ?;",array(intval($_GET['id'])));

        $files = get_files(basePath.'/banner/partners/',false,true);
        for($i=0; $i<count($files); $i++)
        {
          if(re($get['banner']) == $files[$i]) $sel = 'selected="selected"';
          else $sel = "";

          $banners .= show(_partners_select_icons, array("icon" => $files[$i],
                                                         "sel" => $sel));
        }
        $show = show($dir."/form_partners", array("do" => "editbutton&amp;id=".$get['id']."",
                                                  "head" => _partners_edit_head,
                                                  "nothing" => "",
                                                  "banner" => _partners_button,
                                                  "link" => _link,
                                                  "e_link" => re($get['link']),
                                                  "e_textlink" => (empty($get['textlink']) ? '' : re($get['banner'])),
                                                  "or" => _or,
                                                  "textlink" => _partnerbuttons_textlink,
                                                  "banners" => $banners,
                                                  "what" => _button_value_edit));
      } elseif($do == "editbutton") {
        if(empty($_POST['link'])) {
          $show = error(_empty_url, 1);
        } else {
          $sql->update("UPDATE `{prefix_partners}` SET `link` = ?, `banner` = ?, `textlink` = ? WHERE `id` = ?;",
                  array(up(links($_POST['link'])),up(empty($_POST['textlink']) ? $_POST['banner'] : $_POST['textlink']),intval(empty($_POST['textlink']) ? 0 : 1),intval($_GET['id'])));
          $show = info(_partners_edited, "?admin=partners");
        }
      } elseif($do == "delete") {
        $sql->delete("DELETE FROM `{prefix_partners}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $show = info(_partners_deleted,"?admin=partners");
      } else {
        $qry = $sql->select("SELECT * FROM `{prefix_partners}` ORDER BY id;");
        while($get = _fetch($qry)) {
          $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                        "action" => "admin=partners&amp;do=edit",
                                                        "title" => _button_title_edit));
          $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                            "action" => "admin=partners&amp;do=delete",
                                                            "title" => _button_title_del,
                                                            "del" => convSpace(_confirm_del_entry)));

          $rlink = links(re($get['link']));
          $button = '<img src="../banner/partners/'.re($get['banner']).'" alt="'.$rlink.'" title="'.$rlink.'" />';
          $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
          $show .= show($dir."/partners_show", array("class" => $class,
                                                      "button" => (empty($get['textlink']) ? $button : '<center>'._partnerbuttons_textlink.': <b>'.re($get['banner']).'</b></center>'),
                                                      "link" => re($get['link']),
                                                      "id" => $get['id'],
                                                      "edit" => $edit,
                                                      "delete" => $delete));
        }

        $show = show($dir."/partners", array("head" => _partners_head,
                                             "add" => _partners_link_add,
                                             "show" => $show,
                                             "edit" => _editicon_blank,
                                             "del" =>_deleteicon_blank,
                                             "link" => _link,
                                             "button" => _partners_button));
      }