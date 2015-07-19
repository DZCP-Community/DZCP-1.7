<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Gallery')) exit();

$intern = !permission('galleryintern') ? "WHERE `intern` = 0 " : "";
$qry = $sql->select("SELECT `id`,`kat`,`beschreibung` FROM `{prefix_gallery}` ".$intern."ORDER BY `id` DESC;");
if($sql->rowCount()) {
    foreach($qry as $get) {
        $imgArr = array();
        $files = get_files("images/",false,true,$picformat,false,array(),'minimize');
        foreach($files AS $file) {
            if(intval($file) == $get['id']) {
                array_push($imgArr, $file);
            }
        }

        $cnt = 0;
        for($i=0; $i<count($files); $i++) {
            if(preg_match("#^".$get['id']."_(.*?).(gif|jpg|jpeg|png)#",strtolower($files[$i]))!=FALSE) {
                $cnt++;
            }
        }

        $cntpics = $cnt == 1 ? _gallery_image : _gallery_images;
        $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
        $show .= show($dir."/gallery_show", array("link" => re($get['kat']),
                                                  "class" => $class,
                                                  "images" => $cntpics,
                                                  "image" => $imgArr[0],
                                                  "id" => $get['id'],
                                                  "beschreibung" => bbcode(re($get['beschreibung'])),
                                                  "cnt" => $cnt));

    }
} else {
    $show = show(_no_entrys_yet, array("colspan" => "10"));
}

$index = show($dir."/gallery",array("show" => $show));