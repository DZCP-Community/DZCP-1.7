<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._config_newskats_edit_head;

switch($do) {
    case 'delete':
        $get = fetch("SELECT `id`,`katimg` FROM `{prefix_newskat}` WHERE `id` = ?;",array(intval($_GET['id'])));
        if($sql->rowCount()) {
            if(file_exists(basePath."/inc/images/newskat/".stringParser::decode($get['katimg']))) {
                unlink(basePath."/inc/images/newskat/".stringParser::decode($get['katimg']));
            }
            $sql->delete("DELETE FROM `{prefix_newskat}` WHERE `id` = ?;",array(intval($get['id'])));
            $show = info(_config_newskat_deleted, "?admin=news");
        }
    break;
    case 'add':
        $files = get_files(basePath.'/inc/images/newskat/',false,true); $img = "";
        for($i=0; $i<count($files); $i++) {
            $img .= show(_select_field, array("value" => $files[$i],
                                              "sel" => "",
                                              "what" => $files[$i]));
        }

        $show = show($dir."/newskatform", array("head" => _config_newskats_add_head,
                                                "kat" => "",
                                                "value" => _button_value_add,
                                                "nothing" => "",
                                                "do" => "addnewskat",
                                                "upload" => _config_neskats_katbild_upload,
                                                "img" => $img));
    break;
    case 'addnewskat':
        if(empty($_POST['kat'])) {
            $show = error(_config_empty_katname,1);
        } else {
            $sql->insert("INSERT INTO `{prefix_newskat}` SET `katimg` = ?, `kategorie` = ?;",
                    array(stringParser::encode($_POST['img']),stringParser::encode($_POST['kat'])));
            $show = info(_config_newskats_added, "?admin=news");
        }
    break;
    case 'edit':
        $get = $sql->fetch("SELECT * FROM `{prefix_newskat}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $files = get_files(basePath.'/inc/images/newskat/',false,true); $img = '';
        for($i=0; $i<count($files); $i++) {
            $sel = ($get['katimg'] == $files[$i] ? 'selected="selected"' : '');
            $img .= show(_select_field, array("value" => $files[$i],
                                              "sel" => $sel,
                                              "what" => $files[$i]));
        }

        $upload = show(_config_neskats_katbild_upload_edit, array("id" => $_GET['id']));
        $do = show(_config_newskats_editid, array("id" => $_GET['id']));
        $show = show($dir."/newskatform", array("head" => _config_newskats_edit_head,
                                                "kat" => stringParser::decode($get['kategorie']),
                                                "value" => _button_value_edit,
                                                "id" => intval($_GET['id']),
                                                "do" => $do,
                                                "upload" => $upload,
                                                "img" => $img));
    break;
    case 'editnewskat':
        if(empty($_POST['kat'])) {
            $show = error(_config_empty_katname,1);
        } else {
            $katimg = ($_POST['img'] == "lazy" ? "" : "`katimg` = '".stringParser::encode($_POST['img'])."',");
            $sql->update("UPDATE `{prefix_newskat}` SET ".$katimg." `kategorie` = ? WHERE id = ?;",
                    array(stringParser::encode($_POST['kat']),intval($_GET['id'])));
            $show = info(_config_newskats_edited, "?admin=news");
        }
    break;
    default:
        $qry = $sql->select("SELECT `id`,`katimg`,`kategorie` FROM `{prefix_newskat}` ORDER BY `kategorie`;"); $kats = '';
        foreach($qry as $get) {
            $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                          "action" => "admin=news&amp;do=edit",
                                                          "title" => _button_title_edit));

            $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                              "action" => "admin=news&amp;do=delete",
                                                              "title" => _button_title_del,
                                                              "del" => convSpace(_confirm_del_kat)));

            $img = show(_config_newskats_img, array("img" => stringParser::decode($get['katimg'])));
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $kats .= show($dir."/newskats_show", array("mainkat" => stringParser::decode($get['kategorie']),
                                                       "class" => $class,
                                                       "img" => $img,
                                                       "delete" => $delete,
                                                       "edit" => $edit));
        }

        $show = show($dir."/newskats", array("kats" => $kats));
    break;
}