<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Gallery')) exit();

$get = $sql->selectSingle("SELECT `intern`,`kat`,`beschreibung` FROM `{prefix_gallery}` WHERE `id` = ?;",array(intval($_GET['id'])));
if(!permission('galleryintern') && $get['intern']) {
    $index = error(_error_no_access);
} else {
    $files = get_files("images/",false,true,$picformat,false,array(),'minimize');
    $t = 1; $cnt = 0;
    foreach ($files as $file) {
        if(preg_match("#^".$_GET['id']."_(.*?).(gif|jpg|jpeg|png)#",strtolower($file))!=FALSE) {
            $tr1 = ""; $tr2 = "";
            if($t == 0 || $t == 1) {
                $tr1 = "<tr>";
            }

            if($t == config('gallery')) {
                $tr2 = "</tr>";
                $t = 0;
            }

            $del = "";
            if(permission("gallery")) {
                $del = show("page/button_delete_gallery", array("id" => "",
                                                                "action" => "admin=gallery&amp;do=delete&amp;pic=".$file,
                                                                "del" => convSpace(_confirm_del_galpic)));
            }

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show .= show($dir."/show_gallery", array("img" => gallery_size($file),
                                                      "tr1" => $tr1,
                                                      "max" => config('gallery'),
                                                      "width" => intval(round(100/config('gallery'))),
                                                      "del" => $del,
                                                      "tr2" => $tr2));
            $t++; $cnt++;
        }
    }

    $end = '';
    if(is_float($cnt/config('gallery'))) {
        for($e=$t; $e<=config('gallery'); $e++) {
            $end .= '<td class="contentMainFirst"></td>';
        }

        $end = $end."</tr>";
    }

    $index = show($dir."/show", array("gallery" => re($get['kat']),
                                      "show" => $show,
                                      "beschreibung" => bbcode(re($get['beschreibung'])),
                                      "end" => $end));
}