<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._dl;
switch ($do) {
    case 'new':
        $qry = $sql->select("SELECT `id`,`name` FROM `{prefix_download_kat}` ORDER BY `name`;"); $kats = '';
        foreach($qry as $get) {
            $kats .= show(_select_field, array("value" => $get['id'],
                                               "what" => re($get['name']),
                                               "sel" => ""));
        }

        $files = get_files(basePath.'/downloads/files/',false,true); $dl = '';
        foreach ($files as $file) {
            $dl .= show(_downloads_files_exists, array("dl" => $file, "sel" => ""));
        }

        $show = show($dir."/form_dl", array("admin_head" => _downloads_admin_head,
                                            "ddownload" => "",
                                            "dintern" => "",
                                            "durl" => "",
                                            "file" => $dl,
                                            "nothing" => "",
                                            "what" => _button_value_add,
                                            "do" => "add",
                                            "dbeschreibung" => "",
                                            "kats" => $kats));
    break;
    case 'add':
        if(empty($_POST['download']) || empty($_POST['url'])) {
            if (empty($_POST['download'])) {
                $show = error(_downloads_empty_download, 1);
            } else if (empty($_POST['url'])) {
                $show = error(_downloads_empty_url, 1);
            }
        } else {
            $dl = (preg_match("#^www#i",$_POST['url']) ? links($_POST['url']) : up($_POST['url']));
            $sql->insert("INSERT INTO `{prefix_downloads}` SET `download` = ?, "
                    . "`url` = ?, "
                    . "`date` = ?, "
                    . "`beschreibung` = ?, "
                    . "`kat` = ?, "
                    . "`intern` = ?;",
                    array(up($_POST['download']),$dl,time(),up($_POST['beschreibung']),
                        intval($_POST['kat']),intval($_POST['intern']),intval($_POST['intern'])));

            $show = info(_downloads_added, "?admin=dladmin");
        }
    break;
    case 'edit':
        $get  = $sql->fetch("SELECT `download`,`intern`,`url`,`kat`,`beschreibung` FROM `{prefix_downloads}` WHERE `id` = ?;",
                array(intval($_GET['id'])));
        $qryk = $sql->select("SELECT `id`,`name` FROM `{prefix_download_kat}` ORDER BY `name`;"); $kats = '';
        foreach($qryk as $getk) {
            $sel = ($getk['id'] == $get['kat'] ? 'selected="selected"' : '');
            $kats .= show(_select_field, array("value" => $getk['id'],
                                               "what" => re($getk['name']),
                                               "sel" => $sel));
        }

        $show = show($dir."/form_dl", array("admin_head" => _downloads_admin_head_edit,
                                            "ddownload" => re($get['download']),
                                            "dintern" => $get['intern'] ? 'checked="checked"' : '',
                                            "durl" => re($get['url']),
                                            "dbeschreibung" => re($get['beschreibung']),
                                            "what" => _button_value_edit,
                                            "do" => "editdl&amp;id=".$_GET['id']."",
                                            "kats" => $kats));
    break;
    case 'editdl':
        if(empty($_POST['download']) || empty($_POST['url'])) {
            if(empty($_POST['download'])) 
                $show = error(_downloads_empty_download, 1);
            elseif(empty($_POST['url']))  
                $show = error(_downloads_empty_url, 1);
        } else {
            $dl = preg_match("#^www#i",$_POST['url']) ? up(links($_POST['url'])) : up($_POST['url']);
            $sql->update("UPDATE `{prefix_downloads}` SET `download` = ?, "
                    . "`url` = ?, "
                    . "`beschreibung` = ?, "
                    . "`kat` = ?, "
                    . "`intern` = ? "
                    . "WHERE id = ?;",
                array(up($_POST['download']),$dl,up($_POST['beschreibung']),intval($_POST['kat']),
                    intval($_POST['intern']),intval($_GET['id'])));

            $show = info(_downloads_edited, "?admin=dladmin");
        }
    break;
    case 'delete':
        $sql->delete("DELETE FROM `{prefix_downloads}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $show = info(_downloads_deleted, "?admin=dladmin");
    break;
    default:
        $qry = $sql->select("SELECT `id`,`download` FROM `{prefix_downloads}` ORDER BY id");
        foreach($qry as $get) {
            $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                          "action" => "admin=dladmin&amp;do=edit",
                                                          "title" => _button_title_edit));
          
            $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                              "action" => "admin=dladmin&amp;do=delete",
                                                              "title" => _button_title_del,
                                                              "del" => convSpace(_confirm_del_dl)));

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show .= show($dir."/downloads_show", array("id" => $get['id'],
                                                        "dl" => re($get['download']),
                                                        "class" => $class,
                                                        "edit" => $edit,
                                                        "delete" => $delete));
        }

        if (empty($show)) {
            $show = '<tr><td colspan="3" class="contentMainSecond">'._no_entrys.'</td></tr>';
        }

        $show = show($dir."/downloads", array("show" => $show));
    break;
}