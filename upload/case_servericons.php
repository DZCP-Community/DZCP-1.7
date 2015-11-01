<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Upload')) {
    if(permission("editserver")) {
        $set_action = isset($_GET['id']) ? "&amp;edit=1&amp;id=".$_GET['id'] : "";
        $infos = show(_upload_usergallery_info, array("userpicsize" => settings::get('upicsize')));
        $index = show($dir."/upload", array("uploadhead" => _upload_icons_head,
                                            "name" => "file",
                                            "action" => "?action=servericons&amp;do=upload".$set_action,
                                            "infos" => $infos));

        if($do == "upload") {
            $tmpname = $_FILES['file']['tmp_name'];
            $name = $_FILES['file']['name'];
            $type = $_FILES['file']['type'];
            $size = $_FILES['file']['size'];

            if(!$tmpname)
                $index = error(_upload_no_data, 1);
            else if($size > settings::get('upicsize')."000")
                $index = error(_upload_wrong_size, 1);
            else {
                if(move_uploaded_file($tmpname, basePath."/inc/images/gameicons/custom/".$_FILES['file']['name'])) {
                    $link_to = isset($_GET['edit']) && isset($_GET['id']) ? "edit&id=".$_GET['id'] : "new";
                    $index = info(_info_upload_success, "../admin/?admin=server&amp;do=".$link_to);
                }
                else
                    $index = error(_upload_error, 1);
            }
        }
    }
}