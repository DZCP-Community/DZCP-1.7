<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_News')) {
    if(!($kat = isset($_GET['kat']) ? intval($_GET['kat']) : 0)) {
        $navKat = 'lazy';
        $n_kat = '';
        $navWhere = "WHERE `public` = 1 ".(!permission("intnews") ? "AND `intern` = 0" : '')."";
    } else {
        $n_kat = "AND `kat` = ".$kat;
        $navKat = $kat;
        $navWhere = "WHERE `kat` = '".$kat."' AND public = 1 ".(!permission("intnews") ? "AND `intern` = 0" : '')."";
    }

    //Sticky News
    $qry = $sql->select("SELECT * FROM `{prefix_news}` WHERE `sticky` >= ? AND `datum` <= ? AND "
            . "`public` = 1 ".(permission("intnews") ? "" : "AND `intern` = 0")." ".$n_kat." "
            . "ORDER BY `datum` DESC LIMIT ".(($page - 1)*settings('m_news')).",".settings('m_news').";",
            array(($time=time()),$time));

    $show_sticky = '';
    if($sql->rowCount()) {
        foreach($qry as $get) {
            $count = cnt('{prefix_newscomments}', " WHERE `news` = ".intval($get['id']));
            $comments = show(_news_comments, array("comments" => '0', "id" => $get['id']));
            if ($count >= 2) {
                $comments = show(_news_comments, array("comments" => $count, "id" => $get['id']));
            } else if ($count == 1) {
                $comments = show(_news_comment, array("comments" => "1", "id" => $get['id']));
            }

            $klapp = "";
            if ($get['klapptext']) {
                $klapp = show(_news_klapplink, array("klapplink" => re($get['klapplink']),
                                                     "which" => "expand",
                                                     "id" => $get['id']));
            }

            $viewed = show(_news_viewed, array("viewed" => $get['viewed']));

            $links1 = "";
            if(!empty($get['url1'])) {
                $rel = _related_links;
                $links1 = show(_news_link, array("link" => re($get['link1']),
                                                 "url" => $get['url1']));
            }

            $links2 = "";
            if(!empty($get['url2'])) {
              $rel = _related_links;
              $links2 = show(_news_link, array("link" => re($get['link2']),
                                               "url" => $get['url2']));
            }

            $links3 = "";
            if(!empty($get['url3'])) {
                $rel = _related_links;
                $links3 = show(_news_link, array("link" => re($get['link3']),
                                                 "url" => $get['url3']));
            }

            $links = "";
            if (!empty($links1) || !empty($links2) || !empty($links3)) {
                $links = show(_news_links, array("link1" => $links1,
                    "link2" => $links2,
                    "link3" => $links3,
                    "rel" => $rel));
            }

            $intern = $get['intern'] ? _votes_intern : "";
            $newsimage = '../inc/images/newskat/'.re($sql->selectSingle("SELECT `katimg` FROM `{prefix_newskat}` WHERE `id` = ?;",array($get['kat']),'katimg'));
            foreach($picformat as $tmpendung) {
                if(file_exists(basePath."/inc/images/uploads/news/".$get['id'].".".$tmpendung)) {
                    $newsimage = '../inc/images/uploads/news/'.$get['id'].'.'.$tmpendung;
                    break;
                }
            }

            $show_sticky .= show($dir."/news_show", array("titel" => re($get['titel']),
                                                          "kat" => $newsimage,
                                                          "id" => $get['id'],
                                                          "comments" => $comments,
                                                          "showmore" => "",
                                                          "dp" => "none",
                                                          "dir" => $designpath,
                                                          "nautor" => _autor,
                                                          "intern" => $intern,
                                                          "sticky" => _news_sticky,
                                                          "ndatum" => _datum,
                                                          "ncomments" => _news_kommentare.":",
                                                          "klapp" => $klapp,
                                                          "more" => bbcode($get['klapptext']),
                                                          "viewed" => $viewed,
                                                          "text" => bbcode(re($get['text'])),
                                                          "datum" => date("d.m.y H:i", $get['datum'])._uhr,
                                                          "links" => $links,
                                                          "autor" => autor($get['autor'])));
        }
    }

    //News
    $qry = $sql->select("SELECT * FROM `{prefix_news}` WHERE `sticky` < ? AND `datum` <= ? "
            . "AND `public` = 1 ".(permission("intnews") ? "" : "AND `intern` = 0")." ".$n_kat." "
            . "ORDER BY `datum` DESC LIMIT ".($page - 1)*settings('m_news').",".settings('m_news').";",
            array(($time=time()),$time));
    if($sql->rowCount()) {
        foreach($qry as $get) {
            $count = cnt('{prefix_newscomments}', " WHERE `news` = ".$get['id']);
            $comments = show(_news_comments, array("comments" => '0', "id" => $get['id']));
            if ($count >= 2) {
                $comments = show(_news_comments, array("comments" => $count, "id" => $get['id']));
            } else if ($count == 1) {
                $comments = show(_news_comment, array("comments" => "1", "id" => $get['id']));
            }

            $klapp = "";
            if ($get['klapptext']) {
                $klapp = show(_news_klapplink, array("klapplink" => re($get['klapplink']),
                    "which" => "expand",
                    "id" => $get['id']));
            }

            $viewed = show(_news_viewed, array("viewed" => $get['viewed']));
            $links1 = "";
            if(!empty($get['url1'])) {
                $rel = _related_links;
                $links1 = show(_news_link, array("link" => re($get['link1']),
                                                 "url" => $get['url1']));
            }

            $links2 = "";
            if(!empty($get['url2'])) {
              $rel = _related_links;
              $links2 = show(_news_link, array("link" => re($get['link2']),
                                               "url" => $get['url2']));
            }

            $links3 = "";
            if(!empty($get['url3'])) {
                $rel = _related_links;
                $links3 = show(_news_link, array("link" => re($get['link3']),
                                                 "url" => $get['url3']));
            }

            $links = "";
            if (!empty($links1) || !empty($links2) || !empty($links3)) {
                $links = show(_news_links, array("link1" => $links1,
                                                 "link2" => $links2,
                                                 "link3" => $links3,
                                                 "rel" => $rel));
            }

            $intern = $get['intern'] ? _votes_intern : "";
            $newsimage = '../inc/images/newskat/'.re($sql->selectSingle("SELECT `katimg` FROM `{prefix_newskat}` WHERE `id` = ?;",array($get['kat']),'katimg'));
            foreach($picformat as $tmpendung) {
                if(file_exists(basePath."/inc/images/uploads/news/".$get['id'].".".$tmpendung)) {
                    $newsimage = '../inc/images/uploads/news/'.$get['id'].'.'.$tmpendung;
                    break;
                }
            }

            $show .= show($dir."/news_show", array("titel" => re($get['titel']),
                                                   "kat" => $newsimage,
                                                   "id" => $get['id'],
                                                   "comments" => $comments,
                                                   "showmore" => "",
                                                   "dp" => "none",
                                                   "nautor" => _autor,
                                                   "dir" => $designpath,
                                                   "intern" => $intern,
                                                   "sticky" => "",
                                                   "ndatum" => _datum,
                                                   "ncomments" => _news_kommentare.":",
                                                   "klapp" => $klapp,
                                                   "more" => bbcode($get['klapptext']),
                                                   "viewed" => $viewed,
                                                   "text" => bbcode($get['text']),
                                                   "datum" => date("d.m.y H:i", $get['datum'])._uhr,
                                                   "links" => $links,
                                                   "autor" => autor($get['autor'])));
        }
    }

    $qrykat = $sql->select("SELECT `id`,`kategorie` FROM `{prefix_newskat}`;");
    $kategorien = '';
    if($sql->rowCount()) {
        foreach($qrykat as $getkat) {
            $sel = (isset($_GET['kat']) && intval($_GET['kat']) == $getkat['id'] ? 'selected' : '');
            $kategorien .= "<option value='".$getkat['id']."' ".$sel.">".$getkat['kategorie']."</option>";
        }
    }

    $index = show($dir."/news", array("show" => $show,
                                      "show_sticky" => $show_sticky,
                                      "nav" => nav(cnt('{prefix_news}',$navWhere),settings('m_news'),"?kat=".$navKat),
                                      "kategorien" => $kategorien,
                                      "choose" => _news_kat_choose,
                                      "archiv" => _news_archiv));
}