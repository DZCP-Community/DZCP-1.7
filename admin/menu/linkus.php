<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._config_linkus;

switch ($do) {
    case 'new':
        $show = show($dir."/form_linkus", array("head" => _linkus_admin_head,
                                                "link" => _linkus_link,
                                                "beschreibung" => _linkus_beschreibung,
                                                "bchecked" => 'checked="checked"',
                                                "tchecked" => "",
                                                "llink" => _linkus_bsp_target,
                                                "lbeschreibung" => _linkus_bsp_desc,
                                                "btext" => _linkus_text,
                                                "ltext" => _linkus_bsp_bannerurl,
                                                "what" => _button_value_add,
                                                "do" => "add"));
    break;
    case 'add':
        if(empty($_POST['link']) || empty($_POST['beschreibung']) || empty($_POST['text'])) {
            if(empty($_POST['link']))             
                $show = error(_linkus_empty_link, 1);
            elseif(empty($_POST['beschreibung'])) 
                $show = error(_linkus_empty_beschreibung, 1);
            elseif(empty($_POST['text']))         
                $show = error(_linkus_empty_text, 1);
        } else {
            $sql->insert("INSERT INTO `{prefix_linkus}` SET `url` = ?, `text` = ?, `banner` = ?, `beschreibung` = ?;",
            array(up(links($_POST['link'])),up($_POST['text']),up($_POST['banner']),up($_POST['beschreibung'])));
            
            $show = info(_linkus_added, "?admin=linkus");
        }
    break;
    case 'edit':
        $get = $sql->fetch("SELECT * FROM `{prefix_linkus}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $show = show($dir."/form_linkus", array("head" => _linkus_admin_edit,
                                                "link" => _linkus_link,
                                                "beschreibung" => _linkus_beschreibung,
                                                "art" => _linkus_art,
                                                "text" => _linkus_admin_textlink,
                                                "banner" => _linkus_admin_bannerlink,
                                                "llink" => links(re($get['url'])),
                                                "lbeschreibung" => re($get['beschreibung']),
                                                "btext" => _linkus_text,
                                                "ltext" => re($get['text']),
                                                "what" => _button_value_edit,
                                                "do" => "editlink&amp;id=".$_GET['id'].""));
    break;
    case 'editlink':
        if(empty($_POST['link']) || empty($_POST['beschreibung']) || empty($_POST['text'])) {
          if(empty($_POST['link']))             
              $show = error(_linkus_empty_link, 1);
          elseif(empty($_POST['beschreibung'])) 
              $show = error(_linkus_empty_beschreibung, 1);
          elseif(empty($_POST['text']))         
              $show = error(_linkus_empty_text, 1);
        } else {
            $sql->update("UPDATE `{prefix_linkus}` SET `url` = ?, `text` = ?, `banner` = ?, `beschreibung` = ? WHERE `id` = ?;",
                    array(up(links($_POST['link'])),up($_POST['text']),up($_POST['banner']),up($_POST['beschreibung']),intval($_GET['id'])));
            $show = info(_linkus_edited, "?admin=linkus");
        }
    break;
    case 'delete':
        $sql->delete("DELETE FROM `{prefix_linkus}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $show = info(_linkus_deleted, "?admin=linkus");
    break;
    default:
        $qry = $sql->select("SELECT * FROM `{prefix_linkus}` ORDER BY banner DESC;"); $cnt = 1;
        foreach($qry as $get) {
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;

            $banner = show(_linkus_bannerlink, array("id" => $get['id'],
                                                     "banner" => re($get['text'])));

            $edit = show("page/button_edit", array("id" => $get['id'],
                                                   "action" => "admin=linkus&amp;do=edit",
                                                   "title" => _button_title_edit));

            $delete = show("page/button_delete", array("id" => $get['id'],
                                                       "action" => "admin=linkus&amp;do=delete",
                                                       "title" => _button_title_del));

            $show .= show($dir."/linkus_show", array("class" => $class,
                                                     "beschreibung" => re($get['beschreibung']),
                                                     "edit" => $edit,
                                                     "delete" => $delete,
                                                     "cnt" => $cnt,
                                                     "banner" => $banner,
                                                     "besch" => re($get['beschreibung']),
                                                     "url" => $get['url']));
            $cnt++;
        }

        if(empty($show))
            $show = '<tr><td class="contentMainSecond">'._no_entrys.'</td></tr>';

        $show = show($dir."/linkus", array("show" => $show));
    break;
}