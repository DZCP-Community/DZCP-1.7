<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Downloads')) exit();

$qry = $sql->select("SELECT * FROM `{prefix_download_kat}` ORDER BY `name`;");
$t = 1; $cnt = 0; $kats = '';
foreach($qry as $get) {
    $intern =  permission('dlintern') ? "" : "AND `intern` = 0 "; $show = "";
    $qrydl = $sql->select("SELECT * FROM `{prefix_downloads}` WHERE `kat` = ? ".$intern."ORDER BY `download`;",array($get['id']));
    if($sql->rowCount()) {
        $display = "none"; $img = "expand";
        foreach($qrydl as $getdl) {
            if(isset($_GET['hl']) && intval($_GET['hl']) == $getdl['id']) {
                $display = "";
                $img = "collapse";
                $download = highlight(stringParser::decode($getdl['download']));
            } else
                $download = stringParser::decode($getdl['download']);

            $link = show(_downloads_link, array("id" => $getdl['id'],
                                                "download" => $download,
                                                "titel" => stringParser::decode($getdl['download'])));

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show .= show($dir."/downloads_show", array("class" => $class,
                                                        "link" => $link,
                                                        "kid" => $get['id'],
                                                        "display" => $display,
                                                        "beschreibung" => bbcode::parse_html($getdl['beschreibung']),
                                                        "hits" => $getdl['hits']));
        }

        $cntKat = cnt("{prefix_downloads}", " WHERE `kat` = ?","id",array($get['id']));
        $dltitel = ($cntKat == 1 ? _dl_file : _site_stats_files);
        $kat = show(_dl_titel, array("id" => $get['id'],
                                     "file" => $dltitel,
                                     "cnt" => $cntKat,
                                     "name" => stringParser::decode($get['name'])));

        $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
        $kats .= show($dir."/download_kats", array("kat" => $kat,
                                                   "class" => $class,
                                                   "kid" => $get['id'],
                                                   "img" => $img,
                                                   "show" => $show,
                                                   "display" => $display));
    }
}

$index = show($dir."/downloads", array("kats" => $kats));