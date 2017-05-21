<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_UserMenu')) {
    $where = _site_msg;
    if(!$chkMe) {
        $index = error(_error_have_to_be_logged, 1);
    } else {
        switch ($do) {
            case 'show':
                $get = $sql->fetch("SELECT * FROM `{prefix_messages}` WHERE `id` = ? LIMIT 1;",array(intval($_GET['id'])));
                if($get['von'] == $userid || $get['an'] == $userid) {
                    $sql->update("UPDATE `{prefix_messages}` SET `readed` = 1 WHERE `id` = ?;",array($get['id']));
                    $delete = show(_delete, array("id" => $get['id']));

                    if(!$get['von']) {
                        $answermsg = show(_msg_answer_msg, array("nick" => "MsgBot"));
                        $answer = "&nbsp;";
                    } else {
                        $answermsg = show(_msg_answer_msg, array("nick" => autor($get['von'])));
                        $answer = show(_msg_answer, array("id" => $get['id']));
                    }

                    if($get['sendnews'] == 1 || $get['sendnews'] == 2) {
                        $sendnews = show(_msg_sendnews_user, array("id" => $get['id'], "datum" => $get['datum']));
                    } elseif($get['sendnews'] == 3) {
                        $sendnews = show(_msg_sendnews_done, array("user" => autor($get['sendnewsuser'])));
                    } else {
                        $sendnews = '';
                    }

                    $index = show($dir."/msg_show", array("answermsg" => $answermsg,
                                                          "titel" => stringParser::decode($get['titel']),
                                                          "nachricht" => bbcode::parse_html($get['nachricht']),
                                                          "answer" => $answer,
                                                          "sendnews" => $sendnews,
                                                          "delete" => $delete));
                }
            break;
            case 'sendnewsdone':
                $get = $sql->fetch("SELECT `id` FROM `{prefix_messages}` WHERE `id` = ? LIMIT 1;",array(intval($_GET['id'])));
                if($sql->rowCount()) {
                    $sql->update("UPDATE `{prefix_messages}` "
                               . "SET `sendnews` = 3, `sendnewsuser` = ?, `readed`= 1 "
                               . "WHERE `id` = ?;",array($userid,$get['id']));
                    $index = info(_send_news_done, "?action=msg&do=show&id=".$get['id']."");
                }
            break;
            case 'showsended':
                $get = $sql->fetch("SELECT `von`,`an`,`titel`,`nachricht` "
                                        . "FROM `{prefix_messages}` "
                                        . "WHERE `id` = ? LIMIT 1;",array(intval($_GET['id'])));
                if($get['von'] == $userid || $get['an'] == $userid) {
                    $answermsg = show(_msg_sended_msg, array("nick" => autor($get['an'])));
                    $answer = _back;
                    $index = show($dir."/msg_show", array("answermsg" => $answermsg,
                                                          "titel" => stringParser::decode($get['titel']),
                                                          "nachricht" => bbcode::parse_html($get['nachricht']),
                                                          "answer" => $answer,
                                                          "sendnews" => "",
                                                          "delete" => ""));
                }
            break;
            case 'answer':
                $get = $sql->fetch("SELECT * FROM `{prefix_messages}` WHERE `id` = ? LIMIT 1;",array(intval($_GET['id'])));
                if($get['von'] == $userid || $get['an'] == $userid) {
                    $titel = (preg_match("#RE:#is",stringParser::decode($get['titel'])) ? stringParser::decode($get['titel']) : "RE: ".stringParser::decode($get['titel']));
                    $index = show($dir."/answer", array("von" => $userid,
                                                        "an" => $get['von'],
                                                        "titel" => $titel,
                                                        "nick" => autor($get['von']),
                                                        "zitat" => bbcode::zitat(autor($get['von']),stringParser::decode($get['nachricht']))));
                }
            break;
            case 'pn':
                $uid = (isset($_GET['id']) && !empty($_GET['id']) ? intval($_GET['id']) : $userid);
                if (!$chkMe) {
                    $index = error(_error_have_to_be_logged);
                } elseif ($uid == $userid) {
                    $index = error(_error_msg_self, 1);
                } else {
                    $titel = show(_msg_from_nick, array("nick" => stringParser::decode(data("nick"))));
                    $index = show($dir . "/answer", array("von" => $userid,
                                                          "an" => $uid,
                                                          "titel" => $titel,
                                                          "nick" => autor($uid),
                                                          "zitat" => ""));
                }
            break;
            case 'sendanswer':
                if(empty($_POST['titel'])) {
                    $index = error(_empty_titel, 1);
                } elseif(empty($_POST['eintrag'])) {
                    $index = error(_empty_eintrag, 1);
                } elseif (intval($_POST['an']) == $userid) {
                    $index = error(_error_msg_self, 1);
                } else {
                    $sql->insert("INSERT INTO `{prefix_messages}` "
                               . "SET `datum`  = ".time().","
                               . "`von`        = ?,"
                               . "`an`         = ?,"
                               . "`titel`      = ?,"
                               . "`nachricht`  = ?,"
                               . "`see`        = 1;",
                    array($userid,intval($_POST['an']),stringParser::encode($_POST['titel']),stringParser::encode($_POST['eintrag'])));
                    $sql->update("UPDATE `{prefix_userstats}` SET `writtenmsg` = (writtenmsg+1) WHERE `user` = ?;",array($userid));

                    //benachrichtigungs email senden
                    if(data('pnmail',intval($_POST['an']))) {
                        //E-Mail an empfänger senden
                        $message = show(bbcode_email(stringParser::decode(settings::get('eml_pn'))),
                            array('nick' => stringParser::decode(data('nick',intval($_POST['an']))),
                                'titel' => stringParser::encode($_POST['titel']),
                                'clan' => $pagetitle));
                        //subj
                        $subj = show(stringParser::decode(settings::get('eml_pn_subj')),
                            array('domain' => $httphost));

                        //send e-mail
                        sendMail(stringParser::decode(data('email',intval($_POST['an']))), $subj, $message);
                    }

                    $index = info(_msg_answer_done, "?action=msg");
                }
            break;
            case 'delete':
                if(!empty($_POST)) {
                    foreach ($_POST as $key => $id) {
                        if(strpos($key, 'posteingang_') !== false) {
                            $get = $sql->fetch("SELECT `id`,`see` FROM `{prefix_messages}` WHERE `id` = ? LIMIT 1;",array(intval($id)));
                            if(!$get['see']) {
                                $sql->delete("DELETE FROM `{prefix_messages}` WHERE `id` = ?;",array($get['id']));
                            } else {
                                $sql->update("UPDATE `{prefix_messages}` SET `see_u` = 1 WHERE `id` = ?;",array($get['id']));
                            }
                        }
                    }
                    
                    msg_truncate();
                }
                header("Location: ?action=msg");
            break;
            case 'deletethis':
                $get = $sql->fetch("SELECT `id`,`see` FROM `{prefix_messages}` WHERE `id` = ? LIMIT 1;",array(intval($_GET['id'])));
                if($sql->rowCount()) {
                    if(!$get['see']) {
                        $sql->delete("DELETE FROM `{prefix_messages}` WHERE `id` = ?;",array($get['id']));
                    } else {
                        $sql->update("UPDATE `{prefix_messages}` SET `see_u` = 1 WHERE `id` = ?;",array($get['id']));
                    }

                    msg_truncate();
                }
                
                $index = info(_msg_deleted, "?action=msg");
            break;
            case 'deletesended':
                if(!empty($_POST)) {
                    foreach ($_POST as $key => $id) {
                        if(strpos($key, 'postausgang_') !== false) {
                            $sql->delete("DELETE FROM `{prefix_messages}` WHERE `id` = ?;",array(intval($id)));
                        }
                    }
                    
                    msg_truncate();
                }
                header("Location: ?action=msg");
            break;
            case 'new':
                $qry = $sql->select("SELECT `id`,`nick` "
                                  . "FROM `{prefix_users}` "
                                  . "WHERE `id` != ? "
                                  . "ORDER BY `nick`;",array($userid)); 
                $users = ''; $buddys = '';
                foreach($qry as $get) {
                    $users .= show(_to_users, array("id" => $get['id'],
                                                    "selected" => "",
                                                    "nick" => stringParser::decode($get['nick'])));
                }

                $qry = $sql->select("SELECT userbuddy.`buddy`,user.`nick` "
                                  . "FROM `dzcp_userbuddys` AS `userbuddy` "
                                  . "LEFT JOIN `dzcp_users` AS `user` "
                                  . "ON (user.`id` = userbuddy.`buddy`) "
                                  . "WHERE userbuddy.`user` = ? "
                                  . "ORDER BY userbuddy.`user`;",array($userid));
                foreach($qry as $get) {
                    $buddys .= show(_to_buddys, array("id" => $get['buddy'],
                                                      "selected" => "",
                                                      "nick" => stringParser::decode($get['nick'])));
                }

                $index = show($dir."/new", array("von" => $userid,
                                                 "buddys" => $buddys,
                                                 "users" => $users,
                                                 "posttitel" => "",
                                                 "error" => "",
                                                 "posteintrag" => ""));
            break;
            case 'send':
                if(empty($_POST['titel']) || empty($_POST['eintrag']) || $_POST['buddys'] == "-" && $_POST['users'] == "-" || $_POST['buddys'] != "-"
                   && $_POST['users'] != "-" || $_POST['users'] == $userid || $_POST['buddys'] == $userid) {
                    if (empty($_POST['titel'])) {
                        $error = _empty_titel;
                    } elseif (empty($_POST['eintrag'])) {
                        $error = _empty_eintrag;
                    } elseif ($_POST['buddys'] == "-" AND $_POST['users'] == "-") {
                        $error = _empty_to;
                    } elseif ($_POST['buddys'] != "-" AND $_POST['users'] != "-") {
                        $error = _msg_to_just_1;
                    } elseif ($_POST['buddys'] == $userid || $_POST['users'] == $userid) {
                        $error = _msg_not_to_me;
                    }

                    $error = show("errors/errortable", array("error" => $error));
                    $qry = $sql->select("SELECT `id`,`nick` "
                                      . "FROM `{prefix_users}` "
                                      . "WHERE `id` != ? "
                                      . "ORDER BY `nick`;",array($userid)); 
                    $users = ''; $buddys = '';
                    foreach($qry as $get) {
                        $selected = isset($_POST['users']) && $get['id'] == $_POST['users'] ? 'selected="selected"' : '';
                        $users .= show(_to_users, array("id" => $get['id'],
                                                        "nick" => stringParser::decode($get['nick']),
                                                        "selected" => $selected));
                    }

                    $qry = $sql->select("SELECT userbuddy.`buddy`,user.`nick` "
                            . "FROM `dzcp_userbuddys` AS `userbuddy` "
                            . "LEFT JOIN `dzcp_users` AS `user` "
                            . "ON (user.`id` = userbuddy.`buddy`) "
                            . "WHERE userbuddy.`user` = ? "
                            . "ORDER BY userbuddy.`user`;",array($userid));
                    foreach($qry as $get) {
                        $selected = isset($_POST['buddys']) && $get['buddy'] == $_POST['buddys'] ? 'selected="selected"' : '';
                        $buddys .= show(_to_buddys, array("id" => $get['buddy'],
                                                          "nick" => stringParser::decode($get['nick']),
                                                          "selected" => $selected));
                    }

                    $index = show($dir."/new", array("von" => $userid,
                                                     "posttitel" => stringParser::decode($_POST['titel']),
                                                     "posteintrag" => stringParser::decode($_POST['eintrag']),
                                                     "postto" => $_POST['buddys']."".$_POST['users'],
                                                     "buddys" => $buddys,
                                                     "users" => $users,
                                                     "error" => $error));
                } else {
                    $to = ($_POST['buddys'] == "-" ? $_POST['users'] : $_POST['buddys']);
                    $sql->insert("INSERT INTO `{prefix_messages}` "
                               . "SET `datum` = ".time().", "
                               . "`von` = ?, "
                               . "`an` = ?, "
                               . "`titel` = ?, "
                               . "`nachricht` = ?,"
                               . "`see` = 1;",array($userid,$to,stringParser::encode($_POST['titel']),stringParser::encode($_POST['eintrag'])));

                    //benachrichtigungs email senden
                    if(data('pnmail',(int)$to)) {
                        //E-Mail an empfänger senden
                        $message = show(bbcode_email(stringParser::decode(settings::get('eml_pn'))),
                            array('nick' => stringParser::decode(data('nick',(int)$to)),
                                'titel' => stringParser::encode($_POST['titel']),
                                'clan' => $pagetitle));
                        //subj
                        $subj = show(stringParser::decode(settings::get('eml_pn_subj')),
                            array('domain' => $httphost));

                        //send e-mail
                        sendMail(stringParser::decode(data('email',(int)$to)), $subj, $message);
                    }

                    $sql->update("UPDATE `{prefix_userstats}` SET `writtenmsg` = (writtenmsg+1) WHERE `user` = ?;",array($userid));
                    $index = info(_msg_answer_done, "?action=msg");
                }
            break;
            default:
                $qry = $sql->select("SELECT `von`,`titel`,`datum`,`readed`,`see_u`,`id` "
                                  . "FROM `{prefix_messages}` "
                                  . "WHERE `an` = ? AND `see_u` = 0 "
                                  . "ORDER BY datum DESC;",array($userid));
                $posteingang = "";
                if($sql->rowCount()) {
                    foreach($qry as $get) {
                        $absender = !$get['von'] ? _msg_bot : autor($get['von']);
                        $titel = show(_msg_in_title, array("titel" => stringParser::decode($get['titel'])));
                        $delete = _delete;
                        $date = date("d.m.Y H:i", $get['datum'])._uhr;
                        $new = !$get['readed'] && !$get['see_u'] ? _newicon : '';
                        $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
                        $posteingang.= show($dir."/posteingang", array("titel" => $titel,
                                                                       "absender" => $absender,
                                                                       "datum" => $date,
                                                                       "class" => $class,
                                                                       "delete" => $delete,
                                                                       "new" => $new,
                                                                       "id" => $get['id']));
                    }
                }
                
                if(empty($posteingang)) {
                    $posteingang = show(_no_entrys_found, array("colspan" => "4"));
                }
                
                $qry = $sql->select("SELECT `titel`,`datum`,`readed`,`an`,`id` "
                                  . "FROM `{prefix_messages}` "
                                  . "WHERE `von` = ? AND `see` = 1 "
                                  . "ORDER BY datum DESC;", array($userid));
                $postausgang = "";
                foreach($qry as $get) {
                    $titel = show(_msg_out_title, array("titel" => stringParser::decode($get['titel'])));
                    $delete = _msg_delete_sended;
                    $date = date("d.m.Y H:i", $get['datum'])._uhr;
                    $readed = !$get['readed'] ? _noicon : _yesicon;
                    $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
                    $postausgang.= show($dir."/postausgang", array("titel" => $titel,
                                                                   "empfaenger" => autor($get['an']),
                                                                   "datum" => $date,
                                                                   "class" => $class,
                                                                   "readed" => $readed,
                                                                   "delete" => $delete,
                                                                   "id" => $get['id']));
                }

                if (empty($postausgang)) {
                    $postausgang = show(_no_entrys_found, array("colspan" => "4"));
                }

                $msghead = show(_msghead, array("nick" => autor($userid)));
                $index = show($dir."/msg", array("msghead" => $msghead,
                                                 "showincoming" => $posteingang,
                                                 "showsended" => $postausgang));
            break;
        }
    }
}