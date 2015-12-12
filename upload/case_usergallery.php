<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Upload')) {
    if($chkMe >= 1) {
        switch ($do) {
            case 'upload':
                $tmpname = $_FILES['file']['tmp_name'];
                $name = $_FILES['file']['name'];
                $type = $_FILES['file']['type'];
                $size = $_FILES['file']['size'];

                if(!$tmpname)
                    $index = error(_upload_no_data, 1);
                elseif($size > settings::get('upicsize')."000")
                    $index = error(_upload_wrong_size, 1);
                elseif(cnt('{prefix_usergallery}', " WHERE user = ".$userid) == settings::get('m_gallerypics'))
                    $index = error(_upload_over_limit, 2);
                elseif(file_exists(basePath."/inc/images/uploads/usergallery/".$userid."_".$_FILES['file']['name']))
                    $index = error(_upload_file_exists, 1);
                else {
                    if(move_uploaded_file($tmpname, basePath."/inc/images/uploads/usergallery/".$userid."_".strtolower($_FILES['file']['name']))) {
                        $sql->insert("INSERT INTO `{prefix_usergallery}` "
                                . "SET `user` = ?, "
                                . "`beschreibung` = ?, "
                                . "`pic`          = ?;",
                                array(intval($userid),stringParser::encode($_POST['beschreibung']),stringParser::encode(strtolower($_FILES['file']['name']))));

                        $index = info(_info_upload_success, "../user/?action=editprofile&show=gallery");
                    } else
                        $index = error(_upload_error, 1);
                }
            break;
            case 'edit':
                $get = $sql->fetch("SELECT `id`,`user`,`pic`,`beschreibung` FROM `{prefix_usergallery}` WHERE `id` = ?;",array(intval($_GET['gid'])));
                if($get['user'] == $userid) {
                    $infos = show(_upload_usergallery_info, array("userpicsize" => settings::get('upicsize')));
                    $index = show($dir."/usergallery_edit", array("showpic" => img_size("inc/images/uploads/usergallery/".$get['user']."_".$get['pic']),
                                                                  "id" => $get['id'],
                                                                  "showbeschreibung" => stringParser::decode($get['beschreibung']),
                                                                  "name" => "file",
                                                                  "infos" => $infos));
                }
                else
                    $index = error(_error_wrong_permissions, 1);
            break;
            case 'editfile':
                $tmpname = $_FILES['file']['tmp_name'];
                $name = $_FILES['file']['name'];
                $type = $_FILES['file']['type'];
                $size = $_FILES['file']['size'];

                $endung = explode(".", $_FILES['file']['name']);
                $endung = strtolower($endung[count($endung)-1]);

                $get = $sql->fetch("SELECT `pic` FROM `{prefix_usergallery}` WHERE `id` = ?;",array(intval($_POST['id']))); $pic = '';
                if(!empty($_FILES['file']['size'])) {
                    if(file_exists(basePath."/inc/images/uploads/usergallery/".$userid."_".$get['pic']))
                        @unlink(basePath."/inc/images/uploads/usergallery/".$userid."_".$get['pic']);

                    @unlink(show(_gallery_edit_unlink, array("img" => $get['pic'], "user" => $userid)));
                    if(!move_uploaded_file($tmpname, basePath."/inc/images/uploads/usergallery/".$userid."_".$_FILES['file']['name'])) {
                        $index = error(_upload_error, 1);
                        break;
                    }
                    
                    if(empty($index)) {
                        $pic = "`pic` = ?, ";
                        $params = array(stringParser::encode($_FILES['file']['name']));
                    }
                }

                if(empty($index)) {
                    $params = array_merge($params,array(stringParser::encode($_POST['beschreibung']),intval($_POST['id']),intval($userid)));
                    $sql->update("UPDATE `{prefix_usergallery}` SET ".$pic."`beschreibung` = ? WHERE `id` = ? AND `user` = ?;",$params);
                    $index = info(_edit_gallery_done, "../user/?action=editprofile&show=gallery");
                }
            break;
            default:
                $infos = show(_upload_usergallery_info, array("userpicsize" => settings::get('upicsize')));
                $index = show($dir."/usergallery", array("infos" => $infos));
            break;
        }
    }
}