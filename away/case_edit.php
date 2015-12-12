<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Away')) {
    if(!$chkMe || $chkMe < 2) {
        $index = error(_error_wrong_permissions, 1);
    } else {
        $get = $sql->fetch("SELECT * FROM `{prefix_away}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $date1 = show(_dropdown_date, array("day" => dropdown("day",date("d",$get['start'])),
                                            "month" => dropdown("month",date("m",$get['start'])),
                                            "year" => dropdown("year",date("Y",$get['start']))));

        $date2 = show(_dropdown_date2, array("tag" => dropdown("day",date("d",$get['end'])),
                                             "monat" => dropdown("month",date("m",$get['end'])),
                                             "jahr" => dropdown("year",date("Y",$get['end']))));

        $index = show($dir."/form_away", array("head" => _away_edit_head,
                                               "action" => "edit&amp;do=set&amp;id=".$get['id'],
                                               "error" => "",
                                               "date1" => $date1,
                                               "date2" => $date2,
                                               "titel" => stringParser::decode($get['titel']),
                                               "text" => stringParser::decode($get['reason']),
                                               "submit" => _button_value_edit));

        $abdata = mktime(0,0,0,$_POST['m'],$_POST['t'],$_POST['j']);
        $bisdata = mktime(0,0,0,$_POST['monat'],$_POST['tag'],$_POST['jahr']);
        if($do == "set") {
            if(empty($_POST['titel']) || empty($_POST['reason']) || $bisdata == $abdata || $abdata > $bisdata) {
                if(empty($_POST['titel'])) 
                    $error = show("errors/errortable", array("error" => _away_empty_titel));

                if(empty($_POST['reason'])) 
                    $error = show("errors/errortable", array("error" => _away_empty_reason));

                if($bisdata == $abdata) 
                    $error = show("errors/errortable", array("error" => _away_error_1));

                if($abdata > $bisdata) 
                    $error = show("errors/errortable", array("error" => _away_error_2));

                $date1 = show(_dropdown_date, array("day" => dropdown("day",$_POST['t']),
                                                    "month" => dropdown("month",$_POST['m']),
                                                    "year" => dropdown("year",$_POST['j'])));

                $date2 = show(_dropdown_date2, array("tag" => dropdown("day",$_POST['tag']),
                                                     "monat" => dropdown("month",$_POST['monat']),
                                                     "jahr" => dropdown("year",$_POST['jahr'])));

                $index = show($dir."/form_away", array("head" => _away_edit_head,
                                                       "action" => "edit&amp;do=set&amp;id=".$get['id'],
                                                       "error" => $error,
                                                       "date1" => $date1,
                                                       "date2" => $date2,
                                                       "titel" => $_POST['titel'],
                                                       "text" => $_POST['reason'],
                                                       "submit" => _button_value_edit));
            } else {
                $time = mktime(23,59,59,$_POST['monat'],$_POST['tag'],$_POST['jahr']);
                $editedby = show(_edited_by, array("autor" => autor($userid),"time" => date("d.m.Y H:i", time())._uhr));
                $sql->update("UPDATE `{prefix_away}` SET `start`= ?,`end`= ?,`titel`= ?,`reason`= ?,`lastedit`= ? WHERE `id` = ?;",
                    array(intval($abdata),intval($time),stringParser::encode($_POST['titel']),stringParser::encode($_POST['reason']),stringParser::encode($editedby),intval($_GET['id'])));

                $index = info(_away_successful_edit, "../away/");
            }
        }
    }
}