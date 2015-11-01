<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Ck')) {
    if(permission("clankasse")) {
        switch ($do) {
            case 'new':
                $qry = $sql->select("SELECT kat FROM `{prefix_clankasse_kats}`;"); $trans = '';
                foreach($qry as $get) {
                    $trans .= show(_select_field, array("value" => re($get['kat']),
                                                        "sel" => "",
                                                        "what" => re($get['kat'])));
                }

                $dropdown_date = show(_dropdown_date, array("day" => dropdown("day",date("d",time())),
                                                            "month" => dropdown("month",date("m",time())),
                                                            "year" => dropdown("year",date("Y",time()))));

                $index = show($dir."/new", array("thisyear" => date("Y"),
                                                 "dropdown_date" => $dropdown_date,
                                                 "trans" => $trans,
                                                 "w" => str_replace(array('EUR','USD'), array('&euro;','$'),re(settings::get("k_waehrung")))));
            break;
            case 'add':
                if(!$_POST['t'] OR !$_POST['m'])
                    $index = error(_error_clankasse_empty_datum, 1);
                elseif($_POST['transaktion'] == "lazy")
                    $index = error(_error_clankasse_empty_transaktion, 1);
                elseif(!$_POST['betrag'])
                    $index = error(_error_clankasse_empty_betrag, 1);
                else {
                    $betrag = preg_replace("#,#iUs",".",$_POST['betrag']);
                    $datum = mktime(0,0,0,$_POST['m'],$_POST['t'],$_POST['j']);
                    $sql->insert("INSERT INTO `{prefix_clankasse}` SET `datum` = ?, "
                        . "`member` = ?,`transaktion` = ?,`pm` = ?,`betrag` = ?;",
                        array(intval($datum),up($_POST['member']),up($_POST['transaktion']),
                            intval($_POST['pm']),up($betrag)));

                    $index = info(_clankasse_saved, "../clankasse/");
                }
            break;
            case 'delete':
                if(isset($_GET['id'])) {
                    $sql->delete("DELETE FROM `{prefix_clankasse}` WHERE `id` = ?;",array(intval($_GET['id'])));
                    $index = info(_clankasse_deleted, "../clankasse/");
                }
            break;
            case 'update':
                if(isset($_GET['id'])) {
                    if(!$_POST['datum'])
                        $index = error(_error_clankasse_empty_datum, 1);
                    elseif(!$_POST['betrag'])
                        $index = error(_error_clankasse_empty_betrag, 1);
                    elseif(!$_POST['transaktion'])
                        $index = error(_error_clankasse_empty_transaktion, 1);
                    else {
                        $sql->update("UPDATE `{prefix_clankasse}` SET `datum` = ?, `transaktion` = ?, `pm` = ?,`betrag` = ? WHERE `id` = ?;",
                            array(intval($_POST['datum']),up($_POST['transaktion']),intval($_POST['pm']),up($_POST['betrag']),intval($_POST['id'])));
                        $index = info(_clankasse_edited, "../clankasse/");
                    }
                }
            break;
            case 'edit':
                if(isset($_GET['id'])) {
                    $get = $sql->fetch("SELECT * FROM `{prefix_clankasse}` WHERE `id` = ?;",array(intval($_GET['id'])));
                    $dropdown_date = show(_dropdown_date, array("day" => dropdown("day",date("d",$get['datum'])),
                                                                "month" => dropdown("month",date("m",$get['datum'])),
                                                                "year" => dropdown("year",date("Y",$get['datum']))));

                    $qryk = $sql->select("SELECT * FROM `{prefix_clankasse_kats}`;"); $trans = '';
                    foreach($qryk as $getk) {
                        $sel = ($getk['kat'] == $get['transaktion'] ? 'selected="selected"' : '');
                        $trans .= show(_select_field, array("value" => re($getk['kat']),
                                                            "sel" => $sel,
                                                            "what" => re($getk['kat'])));
                    }

                    $index = show($dir."/edit", array("dropdown_date" => $dropdown_date,
                                                      "id" => $_GET['id'],
                                                      "psel" => (!$get['pm'] ? 'selected="selected"' : ''),
                                                      "msel" => ($get['pm'] == 1 ? 'selected="selected"' : ''),
                                                      "trans" => $trans,
                                                      "w" => str_replace(array('EUR','USD'), array('&euro;','$'),re(settings::get("k_waehrung"))),
                                                      "evonan" => re($get['member']),
                                                      "sum" => re($get['betrag'])));
                }
            break;
            case 'editck':
                if(!$_POST['t'] || !$_POST['m'])
                    $index = error(_error_clankasse_empty_datum, 1);
                elseif($_POST['transaktion'] == "lazy")
                    $index = error(_error_clankasse_empty_transaktion, 1);
                elseif(!$_POST['betrag'])
                    $index = error(_error_clankasse_empty_betrag, 1);
                else {
                    $betrag = preg_replace("#,#iUs",".",$_POST['betrag']);
                    $datum = mktime(0,0,0,$_POST['m'],$_POST['t'],$_POST['j']);
                    $sql->update("UPDATE `{prefix_clankasse}` SET `datum` = ?, `member` = ?,`transaktion` = ?, `pm` = ?, `betrag` = ? WHERE `id` = ?;",
                        array(intval($datum),up($_POST['member']),up($_POST['transaktion']),intval($_POST['pm']),up($betrag),intval($_GET['id'])));

                    $index = info(_clankasse_edited, "../clankasse/");
                }
            break;
            case 'paycheck':
                if(isset($_GET['id'])) {
                    $get = $sql->fetch("SELECT `payed` "
                            . "FROM `{prefix_clankasse_payed}` "
                            . "WHERE `user` = ?;",
                            array(intval($_GET['id'])));
                    if($sql->rowCount()) {
                        $tag = date("d", $get['payed']);
                        $monat = date("m", $get['payed']);
                        $jahr = date("Y", $get['payed']);
                    } else {
                        $tag = date("d", time());
                        $monat = date("m", time());
                        $jahr = date("Y", time());
                    }

                    $index = show($dir."/paycheck", array("id" => $_GET['id'],
                                                          "puser" => autor($_GET['id']),
                                                          "t" => $tag,
                                                          "m" => $monat,
                                                          "j" => $jahr));
                }
            break;
            case 'editpaycheck':
                if(isset($_GET['id'])) {
                    $datum = mktime(0,0,0,$_POST['m'],$_POST['t'],$_POST['j']);
                    if($sql->rows("SELECT payed FROM `{prefix_clankasse_payed}` WHERE `user` = ?;",array(intval($_GET['id'])))) {
                        $sql->update("UPDATE `{prefix_clankasse_payed}` SET `payed` = ? WHERE user = ?;",
                            array(intval($datum),intval($_GET['id'])));
                    } else {
                        $sql->insert("INSERT INTO `{prefix_clankasse_payed}` SET `user` = ?, `payed` = ?;",
                            array(intval($_GET['id']),intval($datum)));
                    }

                    $index = info(_info_clankass_status_edited, "../clankasse/");
                }
            break;
        }
    } else {
        $index = error(_error_wrong_permissions, 1);
    }
}