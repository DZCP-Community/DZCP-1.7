<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._config_links;

switch ($do) {
    case 'new':
        $show = show($dir."/form_links", array("head" => _links_admin_head,
                                               "bchecked" => 'checked="checked"',
                                               "bnone" => "",
                                               "tchecked" => "",
                                               "llink" => "",
                                               "lbeschreibung" => "",
                                               "ltext" => "",
                                               "what" => _button_value_add,
                                               "do" => "add"));
    break;
    case 'add':
        if(empty($_POST['link']) || empty($_POST['beschreibung']) || (isset($_POST['banner']) && empty($_POST['text']))) {
            if(empty($_POST['link']))             
                $show = error(_links_empty_link, 1);
            elseif(empty($_POST['beschreibung'])) 
                $show = error(_links_empty_beschreibung, 1);
            elseif(empty($_POST['text']))         
                $show = error(_links_empty_text, 1);
        } else {
            $sql->insert("INSERT INTO `{prefix_links}` SET `url` = ?, `text` = ?, `banner` = ?, `beschreibung` = ?;",
                  array(links($_POST['link']),stringParser::encode($_POST['text']),stringParser::encode($_POST['banner']),stringParser::encode($_POST['beschreibung'])));
            $show = info(_link_added, "?admin=links");
        }
    break;
    case 'edit':
        $get = $sql->fetch("SELECT * FROM `{prefix_links}` WHERE `id` = ?;",array(intval($_GET['id'])));
        
        $tchecked = (!$get['banner'] ? 'checked="checked"' : '');
        $bchecked = ($get['banner'] ? 'checked="checked"' : '');
        $bnone = ($get['banner'] ? '' : "display:none");

        $show = show($dir."/form_links", array("head" => _links_admin_head_edit,
                                               "bchecked" => $bchecked,
                                               "tchecked" => $tchecked,
                                               "bnone" => $bnone,
                                               "llink" => links(stringParser::decode($get['url'])),
                                               "lbeschreibung" => stringParser::decode($get['beschreibung']),
                                               "ltext" => stringParser::decode($get['text']),
                                               "what" => _button_value_edit,
                                               "do" => "editlink&amp;id=".$_GET['id'].""));
    break;
    case 'editlink':
        if(empty($_POST['link']) || empty($_POST['beschreibung']) || (isset($_POST['banner']) && empty($_POST['text']))) {
          if(empty($_POST['link']))             
              $show = error(_links_empty_link, 1);
          elseif(empty($_POST['beschreibung'])) 
              $show = error(_links_empty_beschreibung, 1);
          elseif(empty($_POST['text']))         
              $show = error(_links_empty_text, 1);
        } else {
            $sql->update("UPDATE `{prefix_links}` SET `url` = ?, `text` = ?, `banner` = ?, `beschreibung` = ? WHERE id = ?;",
                    array(stringParser::encode(links($_POST['link'])),stringParser::encode($_POST['text']),stringParser::encode($_POST['banner']),stringParser::encode($_POST['beschreibung']),intval($_GET['id'])));
            $show = info(_link_edited, "?admin=links");
        }
    break;
    case 'delete':
        $sql->delete("DELETE FROM `{prefix_links}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $show = info(_link_deleted, "?admin=links");
    break;
    default:
        $qry = $sql->select("SELECT * FROM `{prefix_links}` ORDER BY `banner` DESC;");
        foreach($qry as $get) {
            $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                          "action" => "admin=links&amp;do=edit",
                                                          "title" => _button_title_edit));
          
            $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                              "action" => "admin=links&amp;do=delete",
                                                              "title" => _button_title_del,
                                                              "del" => _confirm_del_link));

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show .= show($dir."/links_show", array("link" => cut(stringParser::decode($get['url']),40),
                                                    "class" => $class,
                                                    "edit" => $edit,
                                                    "delete" => $delete));
        }

        if(empty($show)) {
            $show = '<tr><td colspan="3" class="contentMainSecond">'._no_entrys.'</td></tr>';
        }

        $show = show($dir."/links", array("show" => $show));
    break;
}