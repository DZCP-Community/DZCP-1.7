<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._server_admin_head;

switch ($do) {
    case 'add':
        $show = show($dir."/form_glossar", array("head" => _admin_glossar_add,
                                                 "llink" => "",
                                                 "lbeschreibung" => "",
                                                 "do" => "insert",
                                                 "value" => _button_value_add));
    break;
    case 'insert':
        if(empty($_POST['link']) || empty($_POST['beschreibung']) || preg_match("#[[:punct:]]]#is",$_POST['link'])) {
            if (empty($_POST['link'])) {
                $show = error(_admin_error_glossar_word);
            } else if($_POST['beschreibung']) {
                $show = error(_admin_error_glossar_desc);
            } else if(preg_match("#[[:punct:]]#is", $_POST['link'])) {
                $show = error(_glossar_specialchar);
            }
        } else {
            $sql->insert("INSERT INTO `{prefix_glossar}` SET `word` = ?, `glossar` = ?;",
                    array(up($_POST['link']),up($_POST['beschreibung'])));
            $show = info(_admin_glossar_added,'?admin=glossar');
        }
    break;
    case 'edit':
        $get = $sql->selectSingle("SELECT `id`,`word`,`glossar` FROM `{prefix_glossar}` WHERE `id` = ?;",
                array(intval($_GET['id'])));
        $show = show($dir."/form_glossar", array("head" => _admin_glossar_edit,
                                                 "llink" => re($get['word']),
                                                 "lbeschreibung" => re($get['glossar']),
                                                 "do" => "update&amp;id=".$get['id'],
                                                 "value" => _button_value_edit));
    break;
    case 'update':
        if(empty($_POST['link']) || empty($_POST['beschreibung']) || preg_match("#[[:punct:]]]#is",$_POST['link'])) {
            if(empty($_POST['link'])) {
                $show = error(_admin_error_glossar_word);
            } else if($_POST['beschreibung']) {
                $show = error(_admin_error_glossar_desc);
            } else if(preg_match("#[[:punct:]]#is", $_POST['link'])) {
                $show = error(_glossar_specialchar);
            }
        } else {
          $sql->update("UPDATE `{prefix_glossar}` SET `word` = ?, `glossar` = ? WHERE `id` = ?;",
                  array(up($_POST['link']),up($_POST['beschreibung']),intval($_GET['id'])));
          $show = info(_admin_glossar_edited,'?admin=glossar');
        }
    break;
    case 'delete':
        $sql->delete("DELETE FROM `{prefix_glossar}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $show = info(_admin_glossar_deleted,'?admin=glossar');
    break;
    default:
        $maxglossar = 20; $entrys = cnt('{prefix_glossar}');
        $qry = $sql->select("SELECT `id`,`word`,`glossar` FROM `{prefix_glossar}` ORDER BY `word` LIMIT ".
                ($page - 1)*$maxglossar.",".$maxglossar.";");
        foreach($qry as $get) {
            $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                          "action" => "admin=glossar&amp;do=edit",
                                                          "title" => _button_title_edit));
            
            $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                              "action" => "admin=glossar&amp;do=delete",
                                                              "title" => _button_title_del,
                                                              "del" => convSpace(_confirm_del_entry)));

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show .= show($dir."/glossar_show", array("word" => re($get['word']),
                                                       "class" => $class,
                                                       "edit" => $edit,
                                                       "delete" => $delete,
                                                       "glossar" => bbcode(re($get['glossar']))));
        }

        if (empty($show)) {
            $show = '<tr><td colspan="5" class="contentMainSecond">'._no_entrys.'</td></tr>';
        }

        $show = show($dir."/glossar", array("show" => $show,
                                            "cnt" => $entrys,
                                            "nav" => nav($entrys,$maxglossar,"?admin=glossar")));
    break;
}