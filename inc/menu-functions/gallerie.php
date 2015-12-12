<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: Image Gallerie
 */

function gallerie() {
    global $sql,$picformat;
    
    $get = $sql->fetch("SELECT `id`,`kat` FROM `{prefix_gallery}` ".(permission('galleryintern') ? "" : " WHERE `intern` = 0")." ORDER BY RAND();");
    $files = get_files(basePath.'/gallery/images/',false,true,$picformat,"#^".$get['id']."_(.*)#",array(),'minimize');
    $cnt = count($files);
    $gallery = '';
    if($files && $cnt >= 1) {
        shuffle($files); $files = limited_array($files,1,4);
        foreach($files as $file) {
            if(!empty($file)) {
                $info = 'onmouseover="DZCP.showInfo(\''.jsconvert(stringParser::decode($get['kat'])).'\', \''._gal_pics.'\', \''.$cnt.'\')" onmouseout="DZCP.hideInfo()"';
                $gallery .= show("menu/gallerie", array("info" => '<p><b>'.jsconvert(stringParser::decode($get['kat'])).'</b></p><p>'._gal_pics.$cnt.'</p>',
                                                       "image" => $file,
                                                       "kat" => stringParser::decode($get['kat']),
                                                       "info" => $info,
                                                       "id" => $get['id']));
            }
        }
    }

    return empty($gallery) ? '<center>No Pictures Added</center>' : $gallery;
}