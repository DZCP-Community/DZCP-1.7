<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: Forum Vote
 */

function fvote($id, $ajax=false) {
    global $sql;
    
    $get = $sql->fetch("SELECT `id`,`closed`,`titel` FROM `{prefix_votes}` WHERE `id` = ? ".(permission("votes") ? ";" : " AND `intern` = 0;"),array(intval($id)));
    if($sql->rowCount()) {
        $results = ''; $votebutton = '';
        $qryv = $sql->select("SELECT `id`,`stimmen`,`sel` FROM `{prefix_vote_results}` WHERE `vid` = ? ORDER BY `id` ASC;",array($get['id']));
        if($sql->rowCount()) {
            foreach($qryv as $getv) {
                $stimmen = sum('{prefix_vote_results}', " WHERE `vid` = ?", "stimmen",array($get['id']));
                if($stimmen != 0) {
                    if(ipcheck("vid_".$get['id']) || cookie::get('vid_'.$get['id']) != false || $get['closed']) {
                        $percent = round($getv['stimmen']/$stimmen*100,1);
                        $rawpercent = round($getv['stimmen']/$stimmen*100,0);
                        $balken = show(_votes_balken, array("width" => $rawpercent));

                        $votebutton = "";
                        $results .= show("forum/vote_results", array("answer" => re($getv['sel']),
                                                                     "percent" => $percent,
                                                                     "stimmen" => $getv['stimmen'],
                                                                     "balken" => $balken));
                    } else {
                        $votebutton = '<input id="contentSubmitFVote" type="submit" value="'._button_value_vote.'" class="voteSubmit" />';
                        $results .= show("forum/vote_vote", array("id" => $getv['id'], "answer" => re($getv['sel'])));
                    }
                } else {
                    $votebutton = '<input id="contentSubmitFVote" type="submit" value="'._button_value_vote.'" class="voteSubmit" />';
                    $results .= show("forum/vote_vote", array("id" => $getv['id'], "answer" => re($getv['sel'])));
                }
            }
        }

        $getf = $sql->fetch("SELECT `id`,`kid` FROM `{prefix_forumthreads}` WHERE `vote` = ?;",array($get['id']));
        $vote = show("forum/vote", array("titel" => re($get['titel']),
                                         "vid" => $get['id'],
                                         "fid" => $getf['id'],
                                         "kid" => $getf['kid'],
                                         "results" => $results,
                                         "votebutton" => $votebutton,
                                         "stimmen" => $stimmen));
    }

    return empty($vote) ? '<center style="margin:2px 0">'._no_entrys.'</center>' : ($ajax ? $vote : '<div id="navFVote">'.$vote.'</div>');
}