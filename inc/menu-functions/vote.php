<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: Votes
 */

function vote($ajax = false) {
    global $sql,$chkMe;
    
    $get = $sql->fetch("SELECT `id`,`closed`,`titel`,`intern` FROM `{prefix_votes}` WHERE `menu` = 1 AND `forum` = 0;"); $vote = '';
    if($sql->rowCount()) {
        if(!$get['intern'] || $chkMe >= 1) {
            $qryv = $sql->select("SELECT `id`,`stimmen`,`sel` FROM `{prefix_vote_results}` WHERE `vid` = ? ORDER BY `what`;",array($get['id']));
            $results = '';
            foreach($qryv as $getv) {
                $ipcheck = !count_clicks('vote',$get['id'],0,false);
                $stimmen = sum("{prefix_vote_results}", " WHERE `vid` = '".$get['id']."'", "stimmen",array($get['id']));
                if($stimmen != 0) {
                    if($ipcheck || cookie::get('vid_'.$get['id']) != false || $get['closed']) {
                        $percent = round($getv['stimmen']/$stimmen*100,1);
                        $rawpercent = round($getv['stimmen']/$stimmen*100,0);
                        $balken = show(_votes_balken, array("width" => $rawpercent));

                        $votebutton = "";
                        $results .= show("menu/vote_results", array("answer" => stringParser::decode($getv['sel']),
                                                                    "percent" => $percent,
                                                                    "stimmen" => $getv['stimmen'],
                                                                    "balken" => $balken));
                    } else {
                        $votebutton = '<input id="contentSubmitVote" type="submit" value="'._button_value_vote.'" class="voteSubmit" />';
                        $results .= show("menu/vote_vote", array("id" => $getv['id'], "answer" => stringParser::decode($getv['sel'])));
                    }
                } else {
                    $votebutton = '<input id="contentSubmitVote" type="submit" value="'._button_value_vote.'" class="voteSubmit" />';
                    $results .= show("menu/vote_vote", array("id" => $getv['id'], "answer" => stringParser::decode($getv['sel'])));
                }
            }

            $vote = show("menu/vote", array("titel" => stringParser::decode($get['titel']),
                                            "vid" => $get['id'],
                                            "results" => $results,
                                            "votebutton" => $votebutton,
                                            "stimmen" => $stimmen));
        }
    }

    return empty($vote) ? '<center style="margin:2px 0">'._vote_menu_no_vote.'</center>' : ($ajax ? $vote : '<div id="navVote">'.$vote.'</div>');
}