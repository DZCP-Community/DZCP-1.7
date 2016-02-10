<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
switch ($do) {
    case 'step2':
        if(empty($_POST['gallery']))
            $show = error(_error_gallery,1);
        else {
            $addfile = '';
            for($i=1;$i<=$_POST['anzahl'];$i++) {
                $addfile .= show($dir."/form_gallery_addfile", array("file" => _gallery_image, "i" => $i));
            }

            $sql->insert("INSERT INTO `{prefix_gallery}` SET `kat` = ?,`intern` = ?,`beschreibung` = ?,`datum` = ?;",
                array(stringParser::encode($_POST['gallery']),intval($_POST['intern']),stringParser::encode($_POST['beschreibung']),time()));

            $show = show($dir."/form_gallery_step2", array("head" => _gallery_admin_head,
                                                           "what" => stringParser::decode($_POST['gallery']),
                                                           "addfile" => $addfile,
                                                           "id" => $sql->lastInsertId(),
                                                           "do" => "add",
                                                           "dowhat" => _button_value_add,
                                                           "anzahl" => $_POST['anzahl'],
                                                           "gal" => _subgallery_head));
        }
    break;
    case 'add':
        $galid = $_GET['id'];
        $anzahl = $_POST['anzahl'];
        for($i=1;$i<=$anzahl;$i++) {
            $tmp = $_FILES['file'.$i]['tmp_name'];
            $type = $_FILES['file'.$i]['type'];
            $end = explode(".", $_FILES['file'.$i]['name']);
            $end = $end[count($end)-1];
            $imginfo = getimagesize($tmp);

            if($_FILES['file'.$i]) {
                if(($type == "image/gif" || $type == "image/pjpeg" || $type == "image/jpeg" || $type == "image/png") && $imginfo[0])
                    move_uploaded_file($tmp, basePath."/gallery/images/".$galid."_".str_pad($i, 3, '0', STR_PAD_LEFT).".".strtolower($end));
            }
        }

        $show = info(_gallery_added, "?admin=gallery");
    break;
    case 'delgal':
        $sql->delete("DELETE FROM `{prefix_gallery}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $files = get_files(basePath."/gallery/images/",false,true,$picformat);
        foreach ($files as $file) {
            if(preg_match("#".$_GET['id']."_(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!= FALSE) {
                $res = preg_match("#".$_GET['id']."_(.*)#",$file,$match);
                if(file_exists(basePath."/gallery/images/".$_GET['id']."_".$match[1]))
                    @unlink(basePath."/gallery/images/".$_GET['id']."_".$match[1]);
            }
        }

        $show = info(_gallery_deleted, "?admin=gallery");
    break;
    case 'delete':
        $pic = $_GET['pic'];
        $file_d = explode('.',$pic);
        $files = get_files(basePath."/gallery/images/",false,true,$picformat);
        foreach ($files as $file) {
            $file_exp_minimize = explode('_minimize_',$file);
            $file_exp = explode('.',$file);
            if($file_exp_minimize[0] == $file_d[0] || $file_exp[0] == $file_d[0])
                @unlink(basePath."/gallery/images/".$file);
        }

        $res = preg_match("#(.*)_(.*?).(gif|GIF|JPG|jpg|JPEG|jpeg|png)#",$pic,$pid);
        $show = info(_gallery_pic_deleted, "../gallery/?action=show&amp;id=".$pid[1]."");
    break;
    case 'edit':
        $get = $sql->fetch("SELECT * FROM `{prefix_gallery}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $show = show($dir."/form_gallery_edit", array("head" => _gallery_admin_edit,
                                                      "value" => _button_value_edit,
                                                      "id" => $get['id'],
                                                      "e_gal" => stringParser::decode($get['kat']),
                                                      "e_intern" => $get['intern'] ? 'checked="checked"' : '',
                                                      "e_beschr" => stringParser::decode($get['beschreibung'])));
    break;
    case 'editgallery':
        $sql->update("UPDATE `{prefix_gallery}` SET `kat` = ?, `intern` = ?, `beschreibung` = ? WHERE `id` = ?;",
            array(stringParser::encode($_POST['gallery']),intval($_POST['intern']),stringParser::encode($_POST['beschreibung']),intval($_GET['id'])));

        $show = info(_gallery_edited, "?admin=gallery");
    break;
    case 'new':
        $get = $sql->fetch("SELECT * FROM `{prefix_gallery}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $option = '';
        for($i=1;$i<=100;$i++) {
            $option .= "<option value=\"".$i."\">".$i."</option>";
        }

        $show = show($dir."/form_gallery_new", array("head" => _gallery_admin_head,
                                                     "value" => _error_fwd,
                                                     "gal" => stringParser::decode($get['kat']),
                                                     "id" => $get['id'],
                                                     "option" => $option));
    break;
    case 'editstep2':
        $get = $sql->fetch("SELECT `id`,`kat` FROM `{prefix_gallery}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $addfile = '';
        for($i=1;$i<=$_POST['anzahl'];$i++) {
            $addfile .= show($dir."/form_gallery_addfile", array("file" => _gallery_image, "i" => $i));
        }

        $show = show($dir."/form_gallery_step2", array("head" => _gallery_admin_edit,
                                                       "what" => stringParser::decode($get['kat']),
                                                       "do" => "editpics",
                                                       "addfile" => $addfile,
                                                       "id" => $get['id'],
                                                       "dowhat" => _button_value_edit,
                                                       "anzahl" => $_POST['anzahl'],
                                                       "gal" => _subgallery_head));
    break;
    case 'editpics':
        $galid = $_GET['id'];
        $anzahl = $_POST['anzahl'];
        $files = get_files(basePath."/gallery/images/",false,true,$picformat); $cnt = 0;
        foreach ($files as $file) {
            if(preg_match("#".$galid."_(.*?).(gif|GIF|JPG|jpg|JPEG|jpeg|png)#",$file)!=FALSE)
                $cnt++;
        }

        for($i=1;$i<=$anzahl;$i++) {
            $tmp = $_FILES['file'.$i]['tmp_name'];
            $type = $_FILES['file'.$i]['type'];
            $end = explode(".", $_FILES['file'.$i]['name']);
            $end = $end[count($end)-1];
            $imginfo = getimagesize($tmp);

            if($_FILES['file'.$i]) {
                if(($type == "image/gif" || $type == "image/pjpeg" || $type == "image/jpeg" || $type == "image/png") && $imginfo[0])
                    move_uploaded_file($tmp, basePath."/gallery/images/".$galid."_".str_pad($i+$cnt, 3, '0', STR_PAD_LEFT).".".strtolower($end));
            }
        }

        $show = info(_gallery_new, "?admin=gallery");
    break;
    case 'addnew':
        $option ='';
        for($i=1;$i<=100;$i++) {
            $option .= "<option value=\"".$i."\">".$i."</option>";
        }

        $show = show($dir."/form_gallery", array("option" => $option));
    break;
    default:
        $qry = $sql->select("SELECT * FROM `{prefix_gallery}` ORDER BY id DESC;");
        foreach($qry as $get) {
            $files = get_files(basePath."/gallery/images/",false,true,$picformat,false,array(),'minimize'); $cnt = 0;
            foreach ($files as $file) {
                if(preg_match("#^".$get['id']."_(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!=FALSE)
                    $cnt++;
            }

            $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                          "action" => "admin=gallery&amp;do=edit",
                                                          "title" => _button_title_edit));

            $del = show("page/button_delete_single", array("id" => $get['id'],
                                                           "action" => "admin=gallery&amp;do=delgal",
                                                           "title" => _button_title_del,
                                                           "del" => convSpace(_confirm_del_gallery)));

            $new = show(_gal_newicon, array("id" => $get['id'], "titel" => _button_value_newgal));
            $cntpics = $cnt == 1 ? _gallery_image : _gallery_images;

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show .= show($dir."/gallery_show", array("link" => stringParser::decode($get['kat']),
                                                     "class" => $class,
                                                     "del" => $del,
                                                     "edit" => $edit,
                                                     "new" => $new,
                                                     "images" => $cntpics,
                                                     "id" => $get['id'],
                                                     "beschreibung" => bbcode::parse_html($get['beschreibung']),
                                                     "cnt" => $cnt));
        }

        if(empty($show))
            $show = '<tr><td class="contentMainSecond">'._no_entrys.'</td></tr>';

        $show = show($dir."/gallery",array("show" => $show));
    break;
}