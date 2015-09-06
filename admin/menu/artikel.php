<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._artikel;

switch($do) {
    case 'add':
        $qryk = $sql->select("SELECT `id`,`kategorie` FROM `{prefix_newskat}`;"); $kat = '';
        foreach($qryk as $getk) {
            $kat .= show(_select_field, array("value" => $getk['id'],"sel" => "","what" => re($getk['kategorie'])));
        }

        $show = show($dir."/artikel_form", array("head" => _artikel_add,
                                                 "autor" => autor($userid),
                                                 "kat" => $kat,
                                                 "do" => "insert",
                                                 "error" => "",
                                                 "titel" => "",
                                                 "artikeltext" => "",
                                                 "link1" => "",
                                                 "link2" => "",
                                                 "link3" => "",
                                                 "url1" => "",
                                                 "url2" => "",
                                                 "url3" => "",
                                                 "button" => _button_value_add,
                                                 "n_artikelpic" => '',
                                                 "delartikelpic" => ''));
    break;
    case 'insert':
        if(empty($_POST['titel']) || empty($_POST['artikel'])) {
            $error = _empty_artikel;
            if(empty($_POST['titel']))
                $error = _empty_artikel_title;

            $qryk = $sql->select("SELECT `id`,`kategorie` FROM `{prefix_newskat}`;"); $kat = '';
            foreach($getk as $getk) {
                $sel = ($_POST['kat'] == $getk['id'] ? 'selected="selected"' : '');
                $kat .= show(_select_field, array("value" => $getk['id'],
                                                  "sel" => $sel,
                                                  "what" => re($getk['kategorie'])));
            }

            $error = show("errors/errortable", array("error" => $error));
            $show = show($dir."/artikel_form", array("head" => _artikel_add,
                                                     "autor" => autor($userid),
                                                     "kat" => $kat,
                                                     "do" => "insert",
                                                     "titel" => re($_POST['titel']),
                                                     "artikeltext" => re($_POST['artikel']),
                                                     "link1" => re($_POST['link1']),
                                                     "link2" => re($_POST['link2']),
                                                     "link3" => re($_POST['link3']),
                                                     "url1" => $_POST['url1'],
                                                     "url2" => $_POST['url2'],
                                                     "url3" => $_POST['url3'],
                                                     "button" => _button_value_add,
                                                     "error" => $error,
                                                     "n_artikelpic" => '',
                                                     "delartikelpic" => ''));
        } else {
            if(isset($_POST)) {
                $sql->insert("INSERT INTO `{prefix_artikel}` SET `autor` = ?, `kat` = ?, `titel` = ?, `text` = ?, "
                            ."`link1`  = ?, `link2`  = ?, `link3`  = ?, `url1`   = ?, `url2`   = ?, `url3`   = ?;",
                array(intval($userid),intval($_POST['kat']),up($_POST['titel']),up($_POST['artikel']),up($_POST['link1']),
                        up($_POST['link2']),up($_POST['link3']),up(links($_POST['url1'])),up(links($_POST['url2'])),up(links($_POST['url3']))));

                if(isset($_FILES['artikelpic']['tmp_name']) && !empty($_FILES['artikelpic']['tmp_name'])) {
                    $endung = explode(".", $_FILES['artikelpic']['name']);
                    $endung = strtolower($endung[count($endung)-1]);
                    move_uploaded_file($_FILES['artikelpic']['tmp_name'], basePath."/inc/images/uploads/artikel/"._insert_id().".".strtolower($endung));
                }
            }
            
            $show = info(_artikel_added, "?admin=artikel");
        }
    break;
    case 'edit':
        $get = $sql->selectSingle("SELECT * FROM `{prefix_artikel}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $qryk = $sql->select("SELECT `id`,`kategorie` FROM `{prefix_newskat}`;"); $kat = '';
        foreach($qryk as $getk) {
            $sel = ($get['kat'] == $getk['id'] ? 'selected="selected"' : '');
            $kat .= show(_select_field, array("value" => $getk['id'], "sel" => $sel, "what" => re($getk['kategorie'])));
        }

        $artikelimage = ""; $delartikelpic = "";
        foreach($picformat as $tmpendung) {
            if(file_exists(basePath."/inc/images/uploads/artikel/".intval($_GET['id']).".".$tmpendung)) {
                $artikelimage = img_size('inc/images/uploads/artikel/'.intval($_GET['id']).'.'.$tmpendung)."<br /><br />";
                $delartikelpic = '<a href="?admin=artikel&do=delartikelpic&id='.$_GET['id'].'">'._artikelpic_del.'</a><br /><br />';
            }
        }

        $do = show(_artikel_edit_link, array("id" => $_GET['id']));
        $show = show($dir."/artikel_form", array("head" => _artikel_edit,
                                                 "nautor" => _autor,
                                                 "autor" => autor($userid),
                                                 "nkat" => _news_admin_kat,
                                                 "preview" => _preview,
                                                 "kat" => $kat,
                                                 "do" => $do,
                                                 "ntitel" => _titel,
                                                 "titel" => re($get['titel']),
                                                 "artikeltext" => re($get['text']),
                                                 "link1" => re($get['link1']),
                                                 "link2" => re($get['link2']),
                                                 "link3" => re($get['link3']),
                                                 "url1" => re($get['url1']),
                                                 "url2" => re($get['url2']),
                                                 "url3" => re($get['url3']),
                                                 "ntext" => _eintrag,
                                                 "error" => "",
                                                 "button" => _button_value_edit,
                                                 "linkname" => _linkname,
                                                 "aimage" => _artikel_userimage,
                                                 "n_artikelpic" => $artikelimage,
                                                 "delartikelpic" => $delartikelpic,
                                                 "nurl" => _url));
    break;
    case 'editartikel':
        if(isset($_POST)) {
            $sql->update("UPDATE `{prefix_artikel}` SET `kat` = ?, `titel` = ?, `text` = ?, `link1` = ?, "
            . "`link2` = ?, `link3` = ?, `url1` = ?, `url2` = ?, `url3` = ? WHERE `id` = ?;",
            array(intval($_POST['kat']),up($_POST['titel']),up($_POST['artikel']),up($_POST['link1']),
                up($_POST['link2']),up($_POST['link3']),up(links($_POST['url1'])),
                up(links($_POST['url2'])),up(links($_POST['url3'])),intval($_GET['id'])));

            if(isset($_FILES['artikelpic']['tmp_name']) && !empty($_FILES['artikelpic']['tmp_name'])) {
                foreach($picformat as $tmpendung) {
                    if(file_exists(basePath."/inc/images/uploads/artikel/".intval($_GET['id']).".".$tmpendung))
                        @unlink(basePath."/inc/images/uploads/artikel/".intval($_GET['id']).".".$tmpendung);
                }

                //Remove minimize
                $files = get_files(basePath."/inc/images/uploads/artikel/",false,true,$picformat);
                if($files) {
                    foreach ($files as $file) {
                        if(preg_match("#".intval($_GET['id'])."(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!= FALSE) {
                            $res = preg_match("#".intval($_GET['id'])."_(.*)#",$file,$match);
                            if(file_exists(basePath."/inc/images/uploads/artikel/".intval($_GET['id'])."_".$match[1]))
                                @unlink(basePath."/inc/images/uploads/artikel/".intval($_GET['id'])."_".$match[1]);
                        }
                    }
                }

                $endung = explode(".", $_FILES['artikelpic']['name']);
                $endung = strtolower($endung[count($endung)-1]);
                move_uploaded_file($_FILES['artikelpic']['tmp_name'], basePath."/inc/images/uploads/artikel/".intval($_GET['id']).".".strtolower($endung));
            }

            $show = info(_artikel_edited, "?admin=artikel");
        }
    break;
    case 'delete':
        $sql->delete("DELETE FROM `{prefix_artikel}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $sql->delete("DELETE FROM `{prefix_acomments}` WHERE `artikel` = ?;",array(intval($_GET['id'])));

        //Remove Pic
        foreach($picformat as $tmpendung) {
            if(file_exists(basePath."/inc/images/uploads/artikel/".intval($_GET['id']).".".$tmpendung))
                @unlink(basePath."/inc/images/uploads/artikel/".intval($_GET['id']).".".$tmpendung);
        }

        //Remove minimize
        $files = get_files(basePath."/inc/images/uploads/artikel/",false,true,$picformat);
        if($files) {
            foreach ($files as $file) {
                if(preg_match("#".intval($_GET['id'])."(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!= FALSE) {
                    $res = preg_match("#".intval($_GET['id'])."_(.*)#",$file,$match);
                    if(file_exists(basePath."/inc/images/uploads/artikel/".intval($_GET['id'])."_".$match[1]))
                        @unlink(basePath."/inc/images/uploads/artikel/".intval($_GET['id'])."_".$match[1]);
                }
            }
        }

        $show = info(_artikel_deleted, "?admin=artikel");
    break;
    case 'delartikelpic':
        //Remove Pic
        foreach($picformat as $tmpendung) {
            if(file_exists(basePath."/inc/images/uploads/artikel/".intval($_GET['id']).".".$tmpendung))
                @unlink(basePath."/inc/images/uploads/artikel/".intval($_GET['id']).".".$tmpendung);
        }

        //Remove minimize
        $files = get_files(basePath."/inc/images/uploads/artikel/",false,true,$picformat);
        if($files) {
            foreach ($files as $file) {
                if(preg_match("#".intval($_GET['id'])."(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!= FALSE) {
                    $res = preg_match("#".intval($_GET['id'])."_(.*)#",$file,$match);
                    if(file_exists(basePath."/inc/images/uploads/artikel/".intval($_GET['id'])."_".$match[1]))
                        @unlink(basePath."/inc/images/uploads/artikel/".intval($_GET['id'])."_".$match[1]);
                }
            }
        }

        $show = info(_newspic_deleted, "?admin=artikel&do=edit&id=".intval($_GET['id'])."");
    break;
    case 'public':
        if(isset($_GET['what']) && $_GET['what'] == 'set')
            $sql->update("UPDATE `{prefix_artikel}` SET `public` = 1, `datum`  = ? WHERE `id` = ?",array(time(),intval($_GET['id'])));
        else
            $sql->update("UPDATE `{prefix_artikel}` SET `public` = 0 WHERE `id` = ?;",array(intval($_GET['id'])));

        header("Location: ?admin=artikel");
    break;
    default:
        $qry = $sql->select("SELECT * FROM `{prefix_artikel}` ".orderby_sql(array("titel","datum","autor"),'ORDER BY `public` ASC, `datum` DESC')." LIMIT ".($page - 1)*settings('m_adminartikel').",".settings('m_adminartikel').";");
        foreach($qry as $get) {
            $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                          "action" => "admin=artikel&amp;do=edit",
                                                          "title" => _button_title_edit));

            $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                              "action" => "admin=artikel&amp;do=delete",
                                                              "title" => _button_title_del,
                                                              "del" => convSpace(_confirm_del_artikel)));

            $titel = show(_artikel_show_link, array("titel" => cut(re($get['titel']),settings('l_newsadmin')), "id" => $get['id']));
            $public = ($get['public'] ? '<a href="?admin=artikel&amp;do=public&amp;id='.$get['id'].'&amp;what=unset"><img src="../inc/images/public.gif" alt="" title="'._non_public.'" /></a>'
                    : '<a href="?admin=artikel&amp;do=public&amp;id='.$get['id'].'&amp;what=set"><img src="../inc/images/nonpublic.gif" alt="" title="'._public.'" /></a>');

            $datum = empty($get['datum']) ? _no_public : date("d.m.y H:i", $get['datum'])._uhr;
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show .= show($dir."/admin_show", array("date" => $datum,
                                                    "titel" => $titel,
                                                    "class" => $class,
                                                    "autor" => autor($get['autor']),
                                                    "intnews" => "",
                                                    "sticky" => "",
                                                    "public" => $public,
                                                    "edit" => $edit,
                                                    "delete" => $delete));
        }

        if(empty($show))
            $show = '<tr><td colspan="6" class="contentMainSecond">'._no_entrys.'</td></tr>';

        $entrys = cnt('{prefix_artikel}');
        $nav = nav($entrys,settings('m_adminnews'),"?admin=artikel".(isset($_GET['show']) ? $_GET['show'] : '').orderby_nav());
        $show = show($dir."/admin_news", array("head" => _artikel,
                                               "nav" => $nav,
                                               "order_autor" => orderby('autor'),
                                               "order_date" => orderby('datum'),
                                               "order_titel" => orderby('titel'),
                                               "show" => $show,
                                               "val" => "artikel",
                                               "add" => _artikel_add));
    break;
}