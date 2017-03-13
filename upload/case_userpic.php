<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Upload')) {
    if($chkMe >= 1 && $userid) {
        switch($do) {
            case 'upload':
                $tmpname = $_FILES['file']['tmp_name'];
                if(!$tmpname) {
                    $index = error(_upload_no_data, 1);
                } else {
                    $file_info = getimagesize($tmpname);
                    if(!$file_info) {
                        $index = error(_upload_error, 1);
                    } else {
                        $file_info['width']  = $file_info[0];
                        $file_info['height'] = $file_info[1];
                        $file_info['mime']   = $file_info[2];
                        unset($file_info[3],$file_info['bits'],$file_info['channels'],
                            $file_info[0],$file_info[1],$file_info[2]);

                        if(!array_key_exists($file_info['mime'], $extensions)) {
                           $error = show(_upload_usergallery_info, array('userpicsize' => settings::get('upicsize')));
                           $index = error($error, 1);
                        } else {
                            if($_FILES['file']['size'] > (settings::get('upicsize')*1000)) {
                                $index = error(_upload_wrong_size, 1);
                            } else {
                                foreach($picformat as $tmpendung) {
                                    if(file_exists(basePath."/inc/images/uploads/userpics/".$userid.".".$tmpendung))
                                        unlink(basePath."/inc/images/uploads/userpics/".$userid.".".$tmpendung);
                                }
                                
                                if(!move_uploaded_file($tmpname, basePath."/inc/images/uploads/userpics/".$userid.".".$extensions[$file_info['mime']])) {
                                    $index = error(_upload_error, 1);
                                } else {
                                    $index = info(_info_upload_success, "../user/?action=editprofile");
                                }
                            }
                        }
                    }
                }
            break;
            case 'deletepic':
                foreach($picformat as $tmpendung) {
                    if(file_exists(basePath."/inc/images/uploads/userpics/".$userid.".".$tmpendung))
                        unlink(basePath."/inc/images/uploads/userpics/".$userid.".".$tmpendung);
                }

                $index = info(_delete_pic_successful, "../user/?action=editprofile");
            break;
            default:
                $infos = show(_upload_userpic_info, array("userpicsize" => settings::get('upicsize')));
                $index = show($dir."/upload", array("uploadhead" => _upload_head,
                                                    "name" => "file",
                                                    "action" => "?action=userpic&amp;do=upload",
                                                    "infos" => $infos));
            break;
        }
    }
}