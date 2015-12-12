<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Away')) {
    $where = $where.' - '._away_new;
    if(!$chkMe || $chkMe < 2) {
        $index = error(_error_wrong_permissions, 1);
    } else {
        $date1 = show(_dropdown_date, array("day" => dropdown("day",date("d",time())),
                                            "month" => dropdown("month",date("m",time())),
                                            "year" => dropdown("year",date("Y",time()))));

        $date2 = show(_dropdown_date2, array("tag" => dropdown("day",date("d",time())),
                                             "monat" => dropdown("month",date("m",time())),
                                             "jahr" => dropdown("year",date("Y",time()))));

        $index = show($dir."/form_away", array("head" => _away_new_head,
                                               "action" => "new&amp;do=set",
                                               "error" => "",
                                               "date1" => $date1,
                                               "date2" => $date2,
                                               "titel" => "",
                                               "text" => "",
                                               "submit" => _button_value_add));

        if($do == "set") {
            $abdata = mktime(0,0,0,$_POST['m'],$_POST['t'],$_POST['j']);
            $bisdata = mktime(0,0,0,$_POST['monat'],$_POST['tag'],$_POST['jahr']);

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

            $index = show($dir."/form_away", array("head" => _away_new_head,
                                                   "action" => "new&amp;do=set",
                                                   "error" => $error,
                                                   "date1" => $date1,
                                                   "date2" => $date2,
                                                   "titel" => $_POST['titel'],
                                                   "text" => $_POST['reason'],
                                                   "submit" => _button_value_add));
            } else {
                $time = mktime(23,59,59,$_POST['monat'],$_POST['tag'],$_POST['jahr']);
                $sql->insert("INSERT INTO `{prefix_away}` SET `userid`= ?,`start`= ?,`end`= ?,"
                . "`titel`= ?,`reason`= ?,`date`= ?;",
                array(intval($userid),intval($abdata),intval($time),stringParser::encode($_POST['titel']),stringParser::encode($_POST['reason']),time()));
                $index = info(_away_successful_added, "../away/");
            }
        }
    }
}