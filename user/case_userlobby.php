<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    $where = _site_user_lobby;
    if ($chkMe) {
        $can_erase = false;
        if(isset($_POST['erase']) && intval($_POST['erase']) == 1) {
            $_SESSION['lastvisit'] = time();
            $sql->update("UPDATE `{prefix_userstats}` "
                       . "SET `lastvisit` = ? "
                       . "WHERE `user` = ?;",
            array(intval($_SESSION['lastvisit']),$userid));
        }

        //Get Userinfos
        $lastvisit = $_SESSION['lastvisit'];

        /** Neue Foreneintraege anzeigen */
        $qrykat = $sql->select("SELECT s1.`id`,s2.`kattopic`,s1.`intern`,s2.`id` FROM `{prefix_forumkats}` AS `s1` "
                             . "LEFT JOIN `{prefix_forumsubkats}` AS `s2` "
                             . "ON s1.`id` = s2.`sid` "
                             . "ORDER BY s1.`kid`,s2.`kattopic`;");

        $forumposts = '';
        if ($sql->rowCount()) {
            foreach($qrykat as $getkat) {
                unset($nthread,$post,$forumposts_show);
                if (fintern($getkat['id'])) {
                    $qrytopic = $sql->select("SELECT `lp`,`id`,`topic`,`first`,`sticky` "
                                           . "FROM `{prefix_forumthreads}` "
                                           . "WHERE `kid` = ? AND `lp` > ? "
                                           . "ORDER BY `lp` DESC LIMIT 150;",
                                array($getkat['id'],$lastvisit));
                    if ($sql->rowCount()) {
                        $forumposts_show = '';
                        foreach($qrytopic as $gettopic) {
                            $count = cnt('{prefix_forumposts}', " WHERE `date` > ? AND `sid` = ?",'id',array($lastvisit,$gettopic['id']));
                            $lp = cnt('{prefix_forumposts}', " WHERE `sid` = ?",'id',array($gettopic['id']));
                            
                            if ($count == 0) {
                                $cnt = 1;
                                $pagenr = 1;
                                $post = "";
                            } elseif ($count == 1) {
                                $cnt = 1;
                                $pagenr = ceil($lp / settings::get('m_fposts'));
                                $post = _new_post_1;
                            } else {
                                $cnt = $count;
                                $pagenr = ceil($lp / settings::get('m_fposts'));
                                $post = _new_post_2;
                            }

                            $nthread = $gettopic['first'] == 1 ? _no_new_thread : _new_thread;
                            if (check_new($gettopic['lp'])) {
                                $intern = ($getkat['intern'] != 1 ? '' : '<span class="fontWichtig">' . _internal . ':</span>&nbsp;&nbsp;&nbsp;');
                                $wichtig = ($gettopic['sticky'] != 1 ? '' : '<span class="fontWichtig">' . _sticky . ':</span> ');
                                $date = (date("d.m.") == date("d.m.", $gettopic['lp'])) ? '[' . date("H:i", $gettopic['lp']) . ']' : date("d.m.", $gettopic['lp']) . ' [' . date("H:i", $gettopic['lp']) . ']';
                                $can_erase = true;
                                $forumposts_show .= "&nbsp;&nbsp;" . $date . show(_user_new_forum, array("cnt" => $cnt,
                                                    "tid" => $gettopic['id'],
                                                    "thread" => stringParser::decode($gettopic['topic']),
                                                    "intern" => $intern,
                                                    "wichtig" => $wichtig,
                                                    "post" => $post,
                                                    "page" => $pagenr,
                                                    "nthread" => $nthread,
                                                    "lp" => ($lp + 1)));
                            }
                        }
                    }

                    if (!empty($forumposts_show)) {
                        $forumposts .= '<div style="padding:4px;padding-left:0"><span class="fontBold">' . $getkat['kattopic'] . '</span></div>' . $forumposts_show;
                    }
                }
            }
        }

        /** Neue Clanwars anzeigen */
        $qrycw = $sql->select("SELECT s1.*,s2.`icon` "
                            . "FROM `{prefix_clanwars}` AS `s1` "
                            . "LEFT JOIN `{prefix_squads}` AS `s2` "
                            . "ON s1.`squad_id` = s2.`id` ORDER BY s1.`datum`;");
        $cws = '';
        if($sql->rowCount()) {
            foreach($qrycw as $getcw) {
                if (!empty($getcw) && check_new($getcw['datum'])) {
                    $check = cnt('{prefix_clanwars}', " WHERE `datum` > ?",'id',array($lastvisit));

                    if ($check == 1) {
                        $cnt = 1;
                        $eintrag = _new_eintrag_1;
                    } else {
                        $cnt = $check;
                        $eintrag = _new_eintrag_2;
                    }

                    $can_erase = true;
                    $cws .= show(_user_new_cw, array("datum" => date("d.m. H:i", $getcw['datum']) . _uhr,
                                                     "id" => $getcw['id'],
                                                     "icon" => $getcw['icon'],
                                                     "gegner" => stringParser::decode($getcw['clantag'])));
                }
            }
        }

        /** Neue Registrierte User anzeigen */
        $getu = $sql->fetch("SELECT `id`,`regdatum` "
                                 . "FROM `{prefix_users}` "
                                 . "ORDER BY `id` DESC;");
        $user = '';
        if (!empty($getu) && check_new($getu['regdatum'])) {
            $check = cnt('{prefix_users}', " WHERE `regdatum` > ?",'id',array($lastvisit));
            if ($check == 1) {
                $cnt = 1;
                $eintrag = _new_users_1;
            } else {
                $cnt = $check;
                $eintrag = _new_users_2;
            }

            $can_erase = true;
            $user = show(_user_new_users, array("cnt" => $cnt, "eintrag" => $eintrag));
        }

        /** Neue Eintruage im Guastebuch anzeigen */
        $permission_gb = permission("gb");
        $activ = (!$permission_gb && settings::get('gb_activ')) ? " WHERE `public` = 1" : ""; $gb = '';
        $getgb = $sql->fetch("SELECT `id`,`datum` "
                                  . "FROM `{prefix_gb}`".$activ." "
                                  . "ORDER BY `id` DESC;");
        if (!empty($getgb) && check_new($getgb['datum'])) {
            $cntgb = (!$permission_gb && settings::get('gb_activ')) ? " AND `public` = 1" : "";
            $check = cnt('{prefix_gb}', " WHERE `datum` > ?".$cntgb,'id',array($lastvisit));
            if ($check == 1) {
                $cnt = 1;
                $eintrag = _new_eintrag_1;
            } else {
                $cnt = $check;
                $eintrag = _new_eintrag_2;
            }

            $can_erase = true;
            $gb = show(_user_new_gb, array("cnt" => $cnt, "eintrag" => $eintrag));
        }

        /** Neue Eintruage im User Guastebuch anzeigen */
        $getmember = $sql->fetch("SELECT `id`,`datum` "
                                      . "FROM `{prefix_usergb}` "
                                      . "WHERE `user` = ? "
                                      . "ORDER BY `datum` DESC;",
                     array($userid));
        $membergb = '';
        if (!empty($getmember) && check_new($getmember['datum'])) {
            $check = cnt('{prefix_usergb}', " WHERE `datum` > ? AND `user` = ?",'id',array($lastvisit,$userid));
            if ($check == 1) {
                $cnt = 1;
                $eintrag = _new_eintrag_1;
            } else {
                $cnt = $check;
                $eintrag = _new_eintrag_2;
            }

            $can_erase = true;
            $membergb = show(_user_new_membergb, array("cnt" => $cnt, "id" => $userid, "eintrag" => $eintrag));
        }

        /** Neue Private Nachrichten anzeigen */
        $getmsg = $sql->fetch("SELECT `id`,`an`,`datum` "
                                   . "FROM `{prefix_messages}` "
                                   . "WHERE `an` = ? AND `readed` = 0 AND `see_u` = 0 "
                                   . "ORDER BY `datum` DESC;",
                  array($userid));
        $check = cnt("{prefix_messages}", " WHERE `an` = ? AND `readed` = 0 AND `see_u` = 0",'id',array($userid));
        if ($check == 1) {
            $mymsg = show(_lobby_mymessage, array("cnt" => 1));
        } else if ($check >= 1) {
            $mymsg = show(_lobby_mymessages, array("cnt" => $check));
        } else {
            $mymsg = show(_lobby_no_mymessages, array());
        }

        /** Neue News anzeigen */
        $qrynews = ($qrycheckn = $sql->select("SELECT `id`,`datum`,`titel` "
                                            . "FROM `{prefix_news}` "
                                            . "WHERE `public` = 1".($chkMe >= 2 ? '' : ' AND `intern` = 0')." AND `datum` <= ".time()." "
                                            . "ORDER BY `id` DESC;"));
        $news = '';
        if ($sql->rowCount()) {
            foreach($qrynews as $getnews) {
                if (check_new($getnews['datum'])) {
                    $check = cnt("{prefix_news}", " WHERE `datum` > ?".($chkMe >= 2 ? '' : ' AND `intern` = 0')." AND `public` = 1",'id',array($lastvisit));
                    $cnt = $check == "1" ? "1" : $check;
                    $can_erase = true;
                    $news = show(_user_new_news, array("cnt" => $cnt, "eintrag" => _lobby_new_news));
                }
            }
        }

         /** Neue News comments anzeigen */
        $newsc = '';
        if ($sql->rowCount()) {
            foreach($qrycheckn as $getcheckn) {
                $getnewsc = $sql->fetch("SELECT `id`,`news`,`datum` "
                                             . "FROM `{prefix_newscomments}` "
                                             . "WHERE `news` = ? "
                                             . "ORDER BY `datum` DESC;",
                            array($getcheckn['id']));
                if (check_new($getnewsc['datum'])) {
                    $check = cnt("{prefix_newscomments}", " WHERE `datum` > ? AND `news` = ?",'id',array($lastvisit,$getnewsc['news']));
                    if ($check == "1") {
                        $cnt = "1";
                        $eintrag = _lobby_new_newsc_1;
                    } else if ($check >= 2) {
                        $cnt = $check;
                        $eintrag = _lobby_new_newsc_2;
                    }

                    if ($check) {
                        $can_erase = true;
                        $newsc .= show(_user_new_newsc, array("cnt" => $cnt,
                                                              "id" => $getnewsc['news'],
                                                              "news" => stringParser::decode($getcheckn['titel']),
                                                              "eintrag" => $eintrag));
                    }
                }
            }
        }

        /** Neue Clanwars comments anzeigen */
        $qrycheckcw = $sql->select("SELECT `id` "
                                 . "FROM `{prefix_clanwars}` "
                                 . "ORDER BY `datum` DESC;"); 
        $cwcom = '';
        if ($sql->rowCount()) {
            foreach($qrycheckcw as $getcheckcw) {
                $getcwc = $sql->fetch("SELECT `id`,`cw`,`datum` "
                                           . "FROM `{prefix_cw_comments}` "
                                           . "WHERE `cw` = ? "
                                           . "ORDER BY `datum` DESC;",
                          array($getcheckcw['id']));
                if (!empty($getcwc) && check_new($getcwc['datum'])) {
                    $check = cnt('{prefix_cw_comments}', " WHERE `datum` > ? AND `cw` = ?",'id',array($lastvisit,$getcwc['cw']));
                    if ($check == 1) {
                        $cnt = 1;
                        $eintrag = _lobby_new_cwc_1;
                    } else {
                        $cnt = $check;
                        $eintrag = _lobby_new_cwc_2;
                    }

                    $can_erase = true;
                    $cwcom .= show(_user_new_clanwar, array("cnt" => $cnt,
                                                            "id" => $getcwc['cw'],
                                                            "eintrag" => $eintrag));
                }
            }
        }

        /** Neue Votes anzeigen */
        $getnewv = $sql->fetch("SELECT `datum` FROM `{prefix_votes}` "
                                    . "WHERE `forum` = 0 ".(permission("votes") ? '' : 'AND `intern` = 0 ').""
                                    . "ORDER BY `datum` DESC;");
        $newv = '';
        if (!empty($getnewv) && check_new($getnewv['datum'])) {
            $check = cnt('{prefix_votes}', " WHERE `datum` > ? AND `forum` = 0",'id',array($lastvisit));
            if ($check == "1") {
                $cnt = "1";
                $eintrag = _new_vote_1;
            } else {
                $cnt = $check;
                $eintrag = _new_vote_2;
            }

            $can_erase = true;
            $newv = show(_user_new_votes, array("cnt" => $cnt, "eintrag" => $eintrag));
        }

        /** Kalender Events anzeigen */
        $getkal = $sql->fetch("SELECT `id`,`datum`,`title` "
                                   . "FROM `{prefix_events}` "
                                   . "WHERE `datum` > ".time()." "
                                   . "ORDER BY `datum`;");
        $nextkal = '';
        if (!empty($getkal) && check_new($getkal['datum'])) {
            if (date("d.m.Y", $getkal['datum']) == date("d.m.Y", time())) {
                $nextkal = show(_userlobby_kal_today, array("time" => mktime(0, 0, 0, date("m", $getkal['datum']), date("d", $getkal['datum']), date("Y", $getkal['datum'])),
                                                            "event" => stringParser::decode($getkal['title'])));
            } else {
                $nextkal = show(_userlobby_kal_not_today, array("time" => mktime(0, 0, 0, date("m", $getkal['datum']), date("d", $getkal['datum']), date("Y", $getkal['datum'])),
                                                                "date" => date("d.m.Y", $getkal['datum']),
                                                                "event" => stringParser::decode($getkal['title'])));
            }
        }

        /** Neue Awards anzeigen */
        $getaw = $sql->fetch("SELECT `id`,`postdate` "
                                  . "FROM `{prefix_awards}` "
                                  . "ORDER BY `id` DESC;");
        $awards = '';
        if (!empty($getaw) && check_new($getaw['postdate'])) {
            $check = cnt('{prefix_awards}', " WHERE `postdate` > ?",'id',array($lastvisit));
            if ($check == "1") {
                $cnt = "1";
                $eintrag = _new_awards_1;
            } else {
                $cnt = $check;
                $eintrag = _new_awards_2;
            }

            $can_erase = true;
            $awards = show(_user_new_awards, array("cnt" => $cnt, "eintrag" => $eintrag));
        }

        /** Neue Rankings anzeigen */
        $getra = $sql->fetch("SELECT `id`,`postdate` "
                                  . "FROM `{prefix_rankings}` "
                                  . "ORDER BY `id` DESC;");
        $rankings = '';
        if (!empty($getra) && check_new($getra['postdate'])) {
            $check = cnt('{prefix_rankings}', " WHERE postdate > ?",'id',array($lastvisit));
            if ($check == "1") {
                $cnt = "1";
                $eintrag = _new_rankings_1;
            } else {
                $cnt = $check;
                $eintrag = _new_rankings_2;
            }

            $can_erase = true;
            $rankings = show(_user_new_rankings, array("cnt" => $cnt, "eintrag" => $eintrag));
        }

        /** Neue Artikel anzeigen */
        $qryart = $sql->select("SELECT `id`,`datum` "
                             . "FROM `{prefix_artikel}` "
                             . "WHERE `public` = 1 "
                             . "ORDER BY `id` DESC;");
        $artikel = '';
        if ($sql->rowCount()) {
            foreach($qryart as $getart) {
                if (check_new($getart['datum'])) {
                    $check = cnt('{prefix_artikel}', " WHERE `datum` > ? AND `public` = 1",'id',array($lastvisit));
                    if ($check == "1") {
                        $cnt = "1";
                        $eintrag = _lobby_new_art_1;
                    } else {
                        $cnt = $check;
                        $eintrag = _lobby_new_art_2;
                    }

                    $can_erase = true;
                    $artikel = show(_user_new_art, array("cnt" => $cnt, "eintrag" => $eintrag));
                }
            }
        }

        /** Neue Artikel Comments anzeigen */
        $qrychecka = $sql->select("SELECT `id` "
                                . "FROM `{prefix_artikel}` "
                                . "WHERE `public` = 1;");
        $artc = '';
        if ($sql->rowCount()) {
            foreach($qrychecka as $getchecka) {
                $getartc = $sql->fetch("SELECT `id`,`artikel`,`datum` "
                                            . "FROM `{prefix_acomments}` "
                                            . "WHERE `artikel` = ? "
                                            . "ORDER BY `datum` DESC;",
                            array($getchecka['id']));
                if (!empty($getartc) && check_new($getartc['datum'])) {
                    $check = cnt('{prefix_acomments}', " WHERE `datum` > ? AND `artikel` = ?",'id',array($lastvisit,$getartc['artikel']));
                    if ($check == "1") {
                        $cnt = "1";
                        $eintrag = _lobby_new_artc_1;
                    } else {
                        $cnt = $check;
                        $eintrag = _lobby_new_artc_2;
                    }

                    $can_erase = true;
                    $artc .= show(_user_new_artc, array("cnt" => $cnt,
                                                        "id" => $getartc['artikel'],
                                                        "eintrag" => $eintrag));
                }
            }
        }

        /** Neue Bilder in der Gallery anzeigen */
        $getgal = $sql->fetch("SELECT `id`,`datum` "
                                   . "FROM `{prefix_gallery}` "
                                   . "ORDER BY `id` DESC;");
        $gal = '';
        if (!empty($getgal) && check_new($getgal['datum'])) {
            $check = cnt('{prefix_gallery}', " WHERE `datum` > ?",'id',array($lastvisit));
            if ($check == "1") {
                $cnt = "1";
                $eintrag = _new_gal_1;
            } else {
                $cnt = $check;
                $eintrag = _new_gal_2;
            }

            $can_erase = true;
            $gal = show(_user_new_gallery, array("cnt" => $cnt, "eintrag" => $eintrag));
        }

        /** Neue Aways anzeigen */
        $qryawayn = $sql->select("SELECT * "
                               . "FROM `{prefix_away}` "
                               . "ORDER BY `id`;");
        $away_new = '';
        if ($sql->rowCount()) {
            $awayn = '';
            foreach($qryawayn as $getawayn) {
                if (check_new($getawayn['date']) && data('level') >= 2) {
                    $awayn .= show(_user_away_new, array("id" => $getawayn['id'],
                                                         "user" => autor($getawayn['userid']),
                                                         "ab" => date("d.m.y", $getawayn['start']),
                                                         "wieder" => date("d.m.y", $getawayn['end']),
                                                         "what" => stringParser::decode($getawayn['titel'])));
                }
            }

            $can_erase = true;
            $away_new = show(_user_away, array("naway" => _lobby_away_new, "away" => $awayn));
        }

        /** Alle Aways anzeigen */
        $qryawaya = $sql->select("SELECT * "
                               . "FROM `{prefix_away}` "
                               . "WHERE `start` <= ? AND `end` >= ? "
                               . "ORDER BY `start`;",
                    array(($time = time()),$time));
        $away_now = "";
        if ($sql->rowCount()) {
            $awaya = "";
            foreach($qryawaya as $getawaya) {
                if (data('level') >= 2) {
                    $wieder = '';
                    if ($getawaya['end'] > $time) {
                        $wieder = _away_to2 . ' <b>' . date("d.m.y", $getawaya['end']) . '</b>';
                    }

                    if (date("d.m.Y", $getawaya['end']) == date("d.m.Y", $time)) {
                        $wieder = _away_today;
                    }

                    $awaya .= show(_user_away_now, array("id" => $getawaya['id'],
                                                         "user" => autor($getawaya['userid']),
                                                         "wieder" => $wieder,
                                                         "what" => stringParser::decode($getawaya['titel'])));
                }
            }

            $away_now = show(_user_away_currently, array("ncaway" => _lobby_away, "caway" => $awaya));
        }

        /** Neue Forum Topics anzeigen */
        $qryft = $sql->select("SELECT s1.`t_text`,s1.`id`,s1.`topic`,s1.`kid`,s2.`kattopic`,s3.`intern`,s1.`sticky` "
                            . "FROM `{prefix_forumthreads}` as `s1`, `{prefix_forumsubkats}` as `s2`, `{prefix_forumkats}` as `s3` "
                            . "WHERE s1.`kid` = s2.`id` AND s2.`sid` = s3.`id` "
                            . "ORDER BY s1.`lp` DESC LIMIT 10;");
        $ftopics = '';
        if ($sql->rowCount()) {
            foreach($qryft as $getft) {
                if (fintern($getft['kid'])) {
                    $lp = cnt('{prefix_forumposts}', " WHERE `sid` = ?",'id',array($getft['id']));
                    $pagenr = ceil($lp / settings::get('m_fposts'));
                    $page = (!$pagenr ? 1 : $pagenr);
                    $getp = $sql->fetch("SELECT `text` "
                                             . "FROM `{prefix_forumposts}` "
                                             . "WHERE `kid` = ? AND `sid` = ? "
                                             . "ORDER BY `date` DESC LIMIT 1;",
                            array($getft['kid'],$getft['id']));

                    $text = strip_tags(!empty($getp) ? stringParser::decode($getp['text']) : stringParser::decode($getft['t_text']));
                    $intern = $getft['intern'] != 1 ? "" : '<span class="fontWichtig">' . _internal . ':</span>';
                    $wichtig = $getft['sticky'] != 1 ? '' : '<span class="fontWichtig">' . _sticky . ':</span> ';
                    $ftopics .= show($dir . "/userlobby_forum", array("id" => $getft['id'],
                                                                      "pagenr" => $page,
                                                                      "p" => ($lp + 1),
                                                                      "intern" => $intern,
                                                                      "wichtig" => $wichtig,
                                                                      "lpost" => cut($text, 100),
                                                                      "kat" => stringParser::decode($getft['kattopic']),
                                                                      "titel" => stringParser::decode($getft['topic']),
                                                                      "kid" => $getft['kid']));
                }
            }
        }

        // Userlevel
        if (($lvl = data("level")) == 1) {
            $mylevel = _status_user;
        } elseif ($lvl == 2) {
            $mylevel = _status_trial;
        } elseif ($lvl == 3) {
            $mylevel = _status_member;
        } elseif ($lvl == 4) {
            $mylevel = _status_admin;
        }

        if (empty($ftopics)) {
            $ftopics = '<tr><td colspan="2" class="contentMainSecond">' . _no_entrys . '</td></tr>';
        }

        $erase = ($can_erase ? _user_new_erase : '');
        $index = show($dir . "/userlobby", array("erase" => $erase,
                                                 "myposts" => userstats("forumposts"),
                                                 "mylogins" => userstats("logins"),
                                                 "myhits" => userstats("hits"),
                                                 "mymsg" => $mymsg,
                                                 "mylevel" => $mylevel,
                                                 "kal" => $nextkal,
                                                 "art" => $artikel,
                                                 "artc" => $artc,
                                                 "rankings" => $rankings,
                                                 "awards" => $awards,
                                                 "ftopics" => $ftopics,
                                                 "forum" => $forumposts,
                                                 "cwcom" => $cwcom,
                                                 "gal" => $gal,
                                                 "votes" => $newv,
                                                 "cws" => $cws,
                                                 "newsc" => $newsc,
                                                 "gb" => $gb,
                                                 "user" => $user,
                                                 "mgb" => $membergb,
                                                 "news" => $news,
                                                 "away_new" => $away_new,
                                                 "away_now" => $away_now));
    } else {
        $index = error(_error_have_to_be_logged, 1);
    }
}