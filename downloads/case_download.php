<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Downloads')) exit();

if(settings("reg_dl") && !$chkMe)
    $index = error(_error_unregistered);
else {
    $get = $sql->selectSingle("SELECT * FROM `{prefix_downloads}` WHERE `id` = ?;",array(intval($_GET['id'])));
    if($sql->rowCount()) {
        if(!permission('dlintern') && $get['intern']) {
            $index = error(_error_no_access);
        } else {
            $file = preg_replace("#added...#Uis", "files/", re($get['url']));
            if(strpos(re($get['url']),"../") != 0) 
                $rawfile = @basename($file);
            else 
                $rawfile = re($get['download']);

            $size = 0;
            if(file_exists($file)) {
                $size = filesize($file);
            }

            $size_mb = 0; $size_kb = 0; $speed_modem = 0; $speed_isdn = 0; $speed_dsl256 = 0;
            $speed_dsl512 = 0; $speed_dsl1024 = 0; $speed_dsl2048 = 0; $speed_dsl3072 = 0;
            $speed_dsl6016 = 0; $speed_dsl16128 = 0;
            if($size) {
                $size_mb = @round($size/1048576,2);
                $size_kb = @round($size/1024,2);
                $speed_modem = @round(($size/1024)/(56/8)/60,2);
                $speed_isdn = @round(($size/1024)/(128/8)/60,2);
                $speed_dsl256 = @round(($size/1024)/(256/8)/60,2);
                $speed_dsl512 = @round(($size/1024)/(512/8)/60,2);
                $speed_dsl1024 = @round(($size/1024)/(1024/8)/60,2);
                $speed_dsl2048 = @round(($size/1024)/(2048/8)/60,2);
                $speed_dsl3072 = @round(($size/1024)/(3072/8)/60,2);
                $speed_dsl6016 = @round(($size/1024)/(6016/8)/60,2);
                $speed_dsl16128 = @round(($size/1024)/(16128/8)/60,2);
            }

            if(strlen(@round(($size/1048576)*$get['hits'],0)) >= 4)
                $traffic = @round(($size/1073741824)*$get['hits'],2).' GB';
            else
                $traffic = @round(($size/1048576)*$get['hits'],2).' MB';

            $getfile = show(_dl_getfile, array("file" => $rawfile));

            if(!$size) {
                $dlsize = $traffic = 'n/a';
                $br1 = '<!--';
                $br2 = '-->';
            } else {
                $dlsize = $size_mb.' MB ('.$size_kb.' KB)';
                $br1 = '';
                $br2 = '';
            }

            $date = 'n/a';
            if(empty($get['date']))
                $date = date("d.m.Y H:i",@filemtime($file))._uhr;
            else
                $date = date("d.m.Y H:i",$get['date'])._uhr;

            $lastdate = date("d.m.Y H:i",$get['last_dl'])._uhr;
            $index = show($dir."/info", array("getfile" => $getfile,
                                              "br1" => $br1,
                                              "br2" => $br2,
                                              "date" => $date,
                                              "lastdate" => $lastdate,
                                              "id" => $_GET['id'],
                                              "dlname" => re($get['download']),
                                              "loaded" => $get['hits'],
                                              "traffic" => $traffic,
                                              "speed_modem" => $speed_modem,
                                              "speed_isdn" => $speed_isdn,
                                              "speed_dsl256" => $speed_dsl256,
                                              "speed_dsl512" => $speed_dsl512,
                                              "speed_dsl1024" => $speed_dsl1024,
                                              "speed_dsl2048" => $speed_dsl2048,
                                              "speed_dsl3072" => $speed_dsl3072,
                                              "speed_dsl6016" => $speed_dsl6016,
                                              "speed_dsl16128" => $speed_dsl16128,
                                              "size" => $dlsize,
                                              "besch" => bbcode(re($get['beschreibung'])),
                                              "file" => $rawfile));
        }
    } else
        $index = error(_id_dont_exist,1);
}