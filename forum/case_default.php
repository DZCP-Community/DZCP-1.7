<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Forum')) {
    $qry = $sql->select("SELECT * FROM `{prefix_forumkats}` ORDER BY `kid` ASC;");
    foreach($qry as $get) {
        $showt = "";
        $qrys = $sql->select("SELECT * FROM `{prefix_forumsubkats}` WHERE `sid` = ? ORDER BY pos;",array($get['id']));
        foreach($qrys as $gets) {
            if($get['intern'] == 0 || ($get['intern'] == 1 && fintern($gets['id']))) {
                unset($lpost);
		$getlt = $sql->fetch("SELECT `id`,`kid`,`t_date`,`t_nick`,`t_email`,`t_reg`,`lp`,`first`,`topic` "
                        . "FROM `{prefix_forumthreads}` "
                        . "WHERE `kid` = ? "
                        . "ORDER BY `lp` DESC;",
                        array($gets['id']));
                
		$getlp = $sql->fetch("SELECT s1.`kid`,s1.`id`,s1.`date`,s1.`nick`,s1.`reg`,s1.`email`,s2.`kid`,s2.`id`,s2.`t_date`,s2.`lp`,s2.`first` "
                        . "FROM `{prefix_forumposts}` AS `s1` "
                        . "LEFT JOIN `{prefix_forumthreads}` AS `s2` "
                        . "ON s2.`lp` = s1.`date` "
                        . "WHERE s2.`kid` = ? "
                        . "ORDER BY s1.`date` DESC;",array($gets['id']));

                $lpost = "-"; $lpdate = "";
                if(cnt('{prefix_forumthreads}', " WHERE `kid` = ?","id",array($gets['id']))) {
                   $lpost = "";
                   if($getlt['first'] == 1) {
                        //Check Unreaded
                        $iconpic = "icon_topic_latest.gif";
                        if($userid >= 1 && $_SESSION['lastvisit']) {
                            //Check in Threads
                            if($sql->rows("SELECT `id` FROM `{prefix_forumthreads}` "
                                    . "WHERE (`t_date` >= ? || `lp` >= ?) AND `t_reg` != ? AND `id` = ?;",
                                    array($_SESSION['lastvisit'],$_SESSION['lastvisit'],$userid,$getlt['id']))) {
                                $iconpic = "icon_topic_newest.gif";
                            }
                        }
                       
                        $lpost .= show(_forum_thread_lpost, array("nick" => _from.' '.autor($getlt['t_reg'], '', 
                                                                    stringParser::decode($getlt['t_nick']), 
                                                                    stringParser::decode($getlt['t_email'])).' ',
                                                                  "post_link" => '?action=showthread&kid='.$getlt['kid'].'&id='.$getlt['id'],
                                                                  "img" => $iconpic,
                                                                  "date" => date("F j, Y, g:i a", $getlt['t_date'])));

                      $lpdate = $getlt['t_date'];
                    } elseif(!$getlt['first']) {
                        //Check Unreaded
                        $iconpic = "icon_topic_latest.gif";
                        if($userid >= 1 && $_SESSION['lastvisit']) {
                            //Check in Posts
                            if($sql->rows("SELECT `id` FROM `{prefix_forumposts}` "
                                    . "WHERE `date` >= ? AND `reg` != ? AND `id` = ?;",
                                    array($_SESSION['lastvisit'],$userid,$getlp['id']))) {
                                $iconpic = "icon_topic_newest.gif";
                            }
                        }
                        
                        $lpost .= show(_forum_thread_lpost, array("nick" => _from.' '.autor($getlp['reg'], '', 
                                                                    stringParser::decode($getlp['nick']), 
                                                                    stringParser::decode($getlp['email'])).' ',
                                                                  "post_link" => '?action=showthread&kid='.$getlt['kid'].'&id='.$getlt['id'],
                                                                  "img" => $iconpic,
                                                                  "date" => date("F j, Y, g:i a", $getlp['date'])));
                        $lpdate = $getlp['date'];
                    }
                }

                $threads = cnt('{prefix_forumthreads}', " WHERE `kid` = ?","id",array($gets['id']));
                $posts = cnt('{prefix_forumposts}', " WHERE `kid` = ?","id",array($gets['id']));
                $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
                
                //Unreaded
                $frompic = "read";
                if($userid >= 1 && $_SESSION['lastvisit']) {
                    //Check new Threads
                    if($sql->rows("SELECT `id` FROM `{prefix_forumthreads}` "
                            . "WHERE (`t_date` >= ? || `lp` >= ?) AND `t_reg` != ? AND `kid` = ?;",
                            array($_SESSION['lastvisit'],$_SESSION['lastvisit'],$userid,$gets['id']))) {
                        $frompic = "unread";
                    }
                    
                    //Check new Posts
                    if($sql->rows("SELECT `id` FROM `{prefix_forumposts}` "
                            . "WHERE `date` >= ? AND `reg` != ? AND `kid` = ?;",
                            array($_SESSION['lastvisit'],$userid,$gets['id']))) {
                        $frompic = "unread";
                    }
                }

                //Show
                $showt .= show($dir."/kats_show", array("topic" => stringParser::decode($gets['kattopic']),
                                                        "subtopic" => stringParser::decode($gets['subtopic']),
                                                        "lpost" => $lpost,
                                                        "frompic" => $frompic,
                                                        "subforum" => "",
                                                        "new" => check_new($lpdate),
                                                        "threads" => $threads,
                                                        "posts" => $posts+$threads,
                                                        "class" => $class,
                                                        "kid" => $gets['sid'],
                                                        "id" => $gets['id']));
            }
        } //end while

        if($get['intern'] == 1) $katname =  show(_forum_katname_intern, array("katname" => stringParser::decode($get['name'])));
        else $katname = stringParser::decode($get['name']);

        if(!empty($showt)) {
            $show .= show($dir."/kats", array("katname" => $katname, "showt" => $showt));
        }
    }
    
    /* Stats */
    $stats = show($dir."/forum_stats", array("total_posts" => strval(cnt("{prefix_forumposts}")),
                                             "total_topics" => strval(cnt("{prefix_forumthreads}")), 
                                             "total_members" => strval(cnt("{prefix_users}","WHERE `banned` = 0 AND `level` >= 1")), 
                                             "newest_member" => autor($sql->fetch("SELECT `id` FROM `{prefix_users}` WHERE `level` >= 1 AND "
                                                     . "`banned` = 0 ORDER BY `regdatum` DESC;",array(),"id"))));
    
    $threads = show(_forum_cnt_threads, array("threads" => cnt("{prefix_forumthreads}")));
    $posts = show(_forum_cnt_posts, array("posts" => cnt("{prefix_forumposts}")+cnt("{prefix_forumthreads}")));

    $qrytp = $sql->select("SELECT `id`,`user`,`forumposts` FROM `{prefix_userstats}` ORDER BY `forumposts` DESC LIMIT 5;");
    $show_top = '';
    foreach($qrytp as $gettp) {
        if($gettp['forumposts'] >= 1) {
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show_top .= show($dir."/top_posts_show", array("nick" => autor($gettp['user']),
                                                            "posts" => $gettp['forumposts'],
                                                            "class" => $class));
        }
    } //end while
    
    $top_posts = show($dir."/top_posts", array("head" => _forum_top_posts,
                                               "show" => $show_top,
                                               "nick" => _nick,
                                               "posts" => _forum_posts));

    /* Wer ist online */
    $qry = $sql->select('SELECT `position`,`color` FROM `{prefix_positions}`;'); $team_groups = '';
    foreach($qry as $get) {
        $team_groups .= show(_forum_team_groups, array('color' => stringParser::decode($get['color']), 'group' => stringParser::decode($get['position'])));
    }

    update_online($where); //Update Where
    $hash = md5($where);
    if(!$cache->isExisting($hash)) {
        $qryo = $sql->select("SELECT `id` FROM `{prefix_users}` WHERE `whereami` LIKE ? AND (time+".intval($useronline).") > ".time().";",array("%".$where."%"));
        if($sql->rowCount()) {
            $i=0; $check = 1; $nick = '';
            $cnto = cnt('{prefix_users}', " WHERE (time+".intval($useronline).") > ".time()." AND `whereami` LIKE ?;",'id',array("%".$where."%"));
            foreach($qryo as $geto) {
                if($i == 5) {
                    $end = "<br />";
                    $i=0;
                }  else  {
                    $end = ($cnto == $check ? "" : ", ");
                }

                $nick .= autorcolerd($geto['id']).$end;
                $i++; $check++;
            } //end while
        } else {
            $nick = _forum_nobody_is_online;
        }
        
        $counter_users = online_reg($where,true); 
        $counter_gast = online_guests($where,true);
        $cache->set($hash, array('user'=>$counter_users,
                                 'gast'=>$counter_gast,
                                 'nick'=>$nick), 30);
    } else {
         $statsc = $cache->get($hash);
         $counter_gast = $statsc['gast'];
         $counter_users = $statsc['user'];
         $nick = $statsc['nick'];
         unset($statsc);
    }
    
    $counter_users = online_reg($where,true); $counter_gast = online_guests($where,true);
    $total_users=($counter_users+$counter_gast);
    $forum_user_stats = show(_forum_online_info0,array('users' => strval($total_users),
                                                       't_gast' => ($counter_gast == 1 ? _forum_gast : _forum_gaste),
                                                       'regs'  => strval($counter_users), 
                                                       't_regs' => ($counter_users == 1 ? _forum_reg : _forum_regs),
                                                       'gast'  => strval($counter_gast),
                                                       't_is' => ($total_users == 1 ? _forum_ist : _forum_sind),
                                                       'timer' => strval(($useronline/60/60))));
    
    $online = show($dir."/online", array("nick" => $nick, "forum_online_info0" => $forum_user_stats, 'groups' => $team_groups));

    /* Index */
    $index = show($dir."/forum", array("head" => _forum_head,
                                       "threads" => $threads,
                                       "stats" => $stats,
                                       "search" => _forum_searchlink,
                                       "posts" => $posts,
                                       "show" => $show,
                                       "online" => $online,
                                       "top_posts" => $top_posts));
}