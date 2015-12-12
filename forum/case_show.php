<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Forum')) {
  $checks = $sql->fetch("SELECT s2.id,s1.intern FROM `{prefix_forumkats}` AS s1
               LEFT JOIN `{prefix_forumsubkats}` AS s2
               ON s2.sid = s1.id
               WHERE s2.id = '".intval($_GET['id'])."'");

  if($checks['intern'] == 1 && (!permission("intforum") && !fintern($checks['id'])))
  {
    $index = error(_error_no_access, 1);
  } else {
    if(empty($_POST['suche']))
    {
      $qry = $sql->select("SELECT * FROM `{prefix_forumthreads}`
                 WHERE kid ='".intval($_GET['id'])."'
                 OR global = 1
                 ORDER BY global DESC, sticky DESC, lp DESC, t_date DESC
                 LIMIT ".($page - 1)*settings::get('m_fthreads').",".settings::get('m_fthreads')."");
    } else {
      $qry = $sql->select("SELECT s1.global,s1.topic,s1.subtopic,s1.t_text,s1.t_email,s1.hits,s1.t_reg,s1.t_date,s1.closed,s1.sticky,s1.id
                 FROM `{prefix_forumthreads}` AS s1
                 WHERE s1.topic LIKE '%".$_POST['suche']."%'
                 AND s1.kid = '".intval($_GET['id'])."'
                 OR s1.subtopic LIKE '%".$_POST['suche']."%'
                 AND s1.kid = '".intval($_GET['id'])."'
                 OR s1.t_text LIKE '%".$_POST['suche']."%'
                 AND s1.kid = '".intval($_GET['id'])."'
                 ORDER BY s1.global DESC, s1.sticky DESC, s1.lp DESC, s1.t_date DESC
                 LIMIT ".($page - 1)*settings::get('m_fthreads').",".settings::get('m_fthreads')."");
    }

    $entrys = cnt("{prefix_forumthreads}", " WHERE kid = ".intval($_GET['id']));
    $i = 2;

    $threads = '';
    foreach($qry as $get) {
      if($get['sticky'] == "1") $sticky = _forum_sticky;
      else $sticky = "";

      if($get['global'] == "1") $global = _forum_global;
      else $global = "";

      if($get['closed'] == "1") $closed = show("page/button_closed", array());
      else $closed = "";

      $cntpage = cnt("{prefix_forumposts}", " WHERE sid = ".$get['id']);

      if($cntpage == "0") $pagenr = "1";
      else $pagenr = ceil($cntpage/settings::get('m_fposts'));

      if(empty($_POST['suche']))
      {
        $gets = $sql->fetch("SELECT id FROM `{prefix_forumsubkats}` WHERE id = '".intval($_GET['id'])."'");
        $threadlink = show(_forum_thread_link, array("topic" =>stringParser::decode(cut($get['topic'],settings::get('l_forumtopic'))),
                                                     "id" => $get['id'],
                                                     "kid" => $gets['id'],
                                                     "sticky" => $sticky,
                                                     "global" => $global,
                                                     "closed" => $closed,
                                                     "lpid" => $cntpage+1,
                                                     "page" => $pagenr));
      } else {
        $threadlink = show(_forum_thread_search_link, array("topic" =>stringParser::decode(cut($get['topic'],settings::get('l_forumtopic'))),
                                                            "id" => $get['id'],
                                                            "sticky" => $sticky,
                                                            "hl" => $_POST['suche'],
                                                            "closed" => $closed,
                                                            "lpid" => $cntpage+1,
                                                            "page" => $pagenr));
      }

      $getlp = $sql->fetch("SELECT date,nick,reg,email FROM `{prefix_forumposts}`
                   WHERE sid = '".$get['id']."'
                   ORDER BY date DESC");
      if($sql->rowCount())
      {
        $lpost = show(_forum_thread_lpost, array("nick" => autor($getlp['reg'], '', $getlp['nick'], stringParser::decode($getlp['email'])),
                                                 "date" => date("d.m.y H:i", $getlp['date'])._uhr));
        $lpdate = $getlp['date'];
      } else {
        $lpost = "-";
        $lpdate = "";
      }

      $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
      $threads .= show($dir."/forum_show_threads", array("new" => check_new($get['lp']),
                                                         "topic" => $threadlink,
                                                         "subtopic" =>stringParser::decode(cut($get['subtopic'],settings::get('l_forumsubtopic'))),
                                                         "hits" => $get['hits'],
                                                         "replys" => cnt("{prefix_forumposts}", " WHERE sid = '".$get['id']."'"),
                                                         "class" => $class,
                                                         "lpost" => $lpost,
                                                         "autor" => autor($get['t_reg'], '', $get['t_nick'], $get['t_email'])));
      $i--;
    }

    $gets = $sql->fetch("SELECT id,kattopic FROM `{prefix_forumsubkats}` WHERE id = '".intval($_GET['id'])."'");
    $search = show($dir."/forum_skat_search", array("head_search" => _forum_head_skat_search,
                                                    "id" => $_GET['id'],
                                                    "suchwort" => isset($_POST['suche']) ? stringParser::decode($_POST['suche']) : ''));
    $nav = nav($entrys,settings::get('m_fthreads'),"?action=show&amp;id=".$_GET['id']."");

    if(!empty($_POST['suche']))
    {
      $what = show($dir."/search", array("head" => _forum_search_head,
                                         "thread" => _forum_thread,
                                         "autor" => _autor,
                                         "lpost" => _forum_lpost,
                                         "hits" => _hits,
                                         "replys" => _forum_replys,
                                         "threads" => $threads,
                                         "nav" => $nav));
    } else {
      $new = show(_forum_new_thread, array("id" => $_GET['id']));
      $what = show($dir."/forum_show_thread", array("head_threads" => _forum_head_threads,
                                                    "thread" => _forum_thread,
                                                    "autor" => _autor,
                                                    "lpost" => _forum_lpost,
                                                    "hits" => _hits,
                                                    "replys" => _forum_replys,
                                                    "nav" => $nav,
                                                    "threads" => $threads,
                                                    "new" => $new,));
    }

    $subkat = $sql->fetch("SELECT sid FROM `{prefix_forumsubkats}` WHERE id = '".intval($_GET['id'])."'");
    $kat = $sql->fetch("SELECT name FROM `{prefix_forumkats}` WHERE id = '".$subkat['sid']."'");

    $wheres = show(_forum_subkat_where, array("where" => stringParser::decode($gets['kattopic']),
                                              "id" => $gets['id']));

    $index = show($dir."/forum_show", array("head" => _forum_head,
                                            "where" => $wheres,
                                            "mainkat" => stringParser::decode($kat['name']),
                                            "what" => $what,
                                            "search" => $search));
  }
}