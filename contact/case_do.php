<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Contact')) exit();

switch($_GET['what']) {
    case 'contact':
        if(checkme() == "unlogged" && !$securimage->check($_POST['secure']))
            $index = error((captcha_mathematic ? _error_invalid_regcode_mathematic : _error_invalid_regcode),1);
        elseif(empty($_POST['text']))
            $index = error(_error_empty_nachricht, 1);
        elseif(empty($_POST['email']))
            $index = error(_empty_email, 1);
        elseif(!check_email($_POST['email']))
            $index = error(_error_invalid_email, 1);
        elseif(empty($_POST['nick']))
            $index = error(_empty_nick, 1);
        else {
            $icq = preg_replace("=-=Uis","",$_POST['icq']);
            $email = show(_email_mailto, array("email" => $_POST['email']));
            $text = show(_contact_text, array("icq" => $icq,
                                              "skype" => $_POST['skype'],
                                              "steam" => $_POST['steam'],
                                              "email" => $email,
                                              "text" => $_POST['text'],
                                              "nick" => $_POST['nick']));

            $qry = $sql->select("SELECT s1.`id` FROM `{prefix_users}` AS `s1` "
                              . "LEFT JOIN `{prefix_permissions}` AS `s2` "
                              . "ON s1.`id` = s2.`user` WHERE s2.`contact` = 1 "
                              . "AND s1.`user` != 0 GROUP BY s1.`id`;");

            $sqlAnd = ''; $params = array();
            foreach($qry as $get) {
                $sqlAnd .= " AND s2.`user` != ?";
                $params = array_merge($params,array(intval($get['id'])));
                $sql->insert("INSERT INTO `{prefix_messages}` "
                           . "SET `datum` = ?, "
                           . "`von` = 0, "
                           . "`an` = ?, "
                           . "`titel` = ?, "
                           . "`nachricht` = ?;",
                        array(time(),intval($get['id']),stringParser::encode(_contact_title),stringParser::encode($text)));
            }

            $qry = $sql->select("SELECT s2.`user` FROM `{prefix_permissions}` AS `s1` "
                    . "LEFT JOIN `{prefix_userposis}` AS `s2` ON s1.`pos` = s2.`posi` "
                    . "WHERE s1.`contact` = 1 AND s2.`posi` != 0".$sqlAnd." GROUP BY s2.`user`;",$params);

            foreach($qry as $get) {
                $sql->insert("INSERT INTO `{prefix_messages}` "
                           . "SET `datum` = ?, "
                           . "`von` = 0, "
                           . "`an` = ?, "
                           . "`titel` = ?, "
                           . "`nachricht` = ?;",
                        array(time(),intval($get['user']),stringParser::decode(_contact_title),stringParser::encode($text)));
            }

            $index = info(_contact_sended, "../news/");
        }
    break;
    case 'joinus':
        if(checkme() == "unlogged" && !$securimage->check($_POST['secure']))
            $index = error((captcha_mathematic ? _error_invalid_regcode_mathematic : _error_invalid_regcode),1);
        elseif(empty($_POST['text']))
            $index = error(_error_empty_nachricht, 1);
        elseif(empty($_POST['age']))
            $index = error(_error_empty_age, 1);
        elseif(empty($_POST['email']))
            $index = error(_empty_email, 1);
        elseif(!check_email($_POST['email']))
            $index = error(_error_invalid_email, 1);
        elseif(empty($_POST['nick']))
            $index = error(_empty_nick, 1);
        else {
            if (intval($_POST['squad']) != 0) {
                $qrysquads = $sql->fetch("SELECT `name` FROM `{prefix_squads}` WHERE `id` = ?;",array(intval($_POST['squad'])));
            } else {
                $qrysquads = array('name' => stringParser::encode(_contact_joinus_no_squad_aviable));
            }

            $icq = preg_replace("=-=Uis","",$_POST['icq']);
            $email = show(_email_mailto, array("email" => $_POST['email']));
            $text = show(_contact_text_joinus, array("icq" => $icq,
                                                     "skype" => $_POST['skype'],
                                                     "steam" => $_POST['steam'],
                                                     "email" => $email,
                                                     "age" => $_POST['age'],
                                                     "text" => $_POST['text'],
                                                     "squad" => stringParser::decode($qrysquads['name']),
                                                     "nick" => $_POST['nick']));

            $qry = $sql->select("SELECT s1.id FROM `{prefix_users}` AS `s1` "
                              . "LEFT JOIN `{prefix_permissions}` AS `s2` "
                              . "ON s1.`id` = s2.`user` "
                              . "WHERE s2.`joinus` = 1 AND s1.`user` != 0 GROUP BY s1.`id`;");
            
            $sqlAnd = ''; $params = array();
            foreach($qry as $get) {
                $sqlAnd .= " AND s2.`user` != ?";
                $params = array_merge($params,array(intval($get['id'])));
                $sql->insert("INSERT INTO `{prefix_messages}` "
                        . "SET `datum` = ?, "
                        . "`von` = 0, "
                        . "`an` = ?, "
                        . "`titel` = ?, "
                        . "`nachricht` = ?;",
                array(time(),intval($get['id']),stringParser::encode(_contact_title_joinus),stringParser::encode($text)));
          }

          $qry = $sql->select("SELECT s2.`user` FROM `{prefix_permissions}` AS `s1` "
                            . "LEFT JOIN `{prefix_userposis}` AS `s2` "
                            . "ON s1.`pos` = s2.`posi` "
                            . "WHERE s1.`joinus` = 1 AND s2.`posi` != 0".$sqlAnd." "
                            . "GROUP BY s2.`user`;");
          foreach($qry as $get) {
                $sql->insert("INSERT INTO `{prefix_messages}` "
                        . "SET `datum` = ?, "
                        . "`von` = 0, "
                        . "`an` = ?, "
                        . "`titel` = ?, "
                        . "`nachricht` = ?;",
                array(time(),intval($get['user']),stringParser::encode(_contact_title_joinus),stringParser::encode($text)));
          }

          $index = info(_contact_joinus_sended, "../news/");
        }
    break;
    case 'fightus':
        if(checkme() == "unlogged" && !$securimage->check($_POST['secure']))
            $index = error((captcha_mathematic ? _error_invalid_regcode_mathematic : _error_invalid_regcode),1);
        elseif(empty($_POST['clan']))
            $index = error(_error_empty_clanname, 1);
        elseif(empty($_POST['email']))
            $index = error(_empty_email, 1);
        elseif(empty($_POST['maps']))
            $index = error(_empty_fightus_map, 1);
        elseif(!check_email($_POST['email']))
            $index = error(_error_invalid_email, 1);
        elseif(empty($_POST['nick']))
            $index = error(_empty_nick, 1);
        else {
            $icq = preg_replace("=-=Uis","",$_POST['icq']);
            $email = show(_email_mailto, array("email" => $_POST['email']));
            $hp = show(_contact_hp, array("hp" => links($_POST['hp'])));

            if(!empty($_POST['t']) && $_POST['j'] == date("Y", time())) {
                $date = $_POST['t'].".".$_POST['m'].".".$_POST['j']."&nbsp;".$_POST['h'].":".$_POST['min']._uhr;
            }

            $get = $sql->fetch("SELECT `name` FROM `{prefix_squads}` WHERE `id` = ?;",array(intval($_POST['squad'])));
            $msg = show(_contact_text_fightus, array("icq" => $icq,
                                                     "skype" => $_POST['skype'],
                                                     "steam" => $_POST['steam'],
                                                     "email" => $email,
                                                     "text" => $_POST['text'],
                                                     "clan" => $_POST['clan'],
                                                     "hp" => $hp,
                                                     "squad" => stringParser::decode($get['name']),
                                                     "game" => $_POST['game'],
                                                     "us" => $_POST['us'],
                                                     "to" => $_POST['to'],
                                                     "date" => $date,
                                                     "map" => $_POST['maps'],
                                                     "nick" => $_POST['nick']));

            $params = array();
            if($chkMe != 4) {
                $params = array(intval($_POST['squad']));
                $add = " AND s2.`squad` = ?";
            }
          
            $who = $sql->select("SELECT s1.`user` FROM `{prefix_permissions}` AS `s1` "
                    . "LEFT JOIN `{prefix_squaduser}` AS `s2` "
                    . "ON s1.`user` = s2.`user` "
                    . "WHERE s1.`receivecws` = 1 AND s1.`user` != 0 ".$add." GROUP BY s1.`user`;",$params);
          
            $sqlAnd = '';
            foreach($qry as $get) {
                $sqlAnd .= " AND s2.`user` != ?";
                $params = array_merge($params,array(intval($get['user'])));
                $sql->insert("INSERT INTO `{prefix_messages}` "
                        . "SET `datum` = ?, "
                        . "`von` = 0, "
                        . "`an` = ?, "
                        . "`titel` = ?, "
                        . "`nachricht` = ?;",
                array(time(),intval($get['user']),stringParser::encode(_contact_title_fightus),stringParser::encode($msg)));
            }

            $qry = $sql->select("SELECT s3.`user` FROM `{prefix_permissions}` AS `s1` "
                              . "LEFT JOIN `{prefix_userposis}` AS `s2` "
                              . "ON s1.`pos` = s2.`posi` "
                              . "LEFT JOIN `{prefix_squaduser}` AS `s3` "
                              . "ON s2.`user` = s3.`user` "
                              . "WHERE s1.`receivecws` = 1 AND s2.`posi` != 0".$add.$sqlAnd." "
                              . "GROUP BY s2.`user`;");
            foreach($qry as $get) {
                $sql->insert("INSERT INTO `{prefix_messages}` "
                        . "SET `datum` = ?, "
                        . "`von` = 0, "
                        . "`an` = ?, "
                        . "`titel` = ?, "
                        . "`nachricht` = ?;",
                array(time(),intval($get['user']),stringParser::encode(_contact_title_fightus),stringParser::encode($msg)));
          }
          
          $index = info(_contact_fightus_sended, "../news/");
        }
    break;
}