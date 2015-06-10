<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Votes')) {
    $whereIntern = ' AND `intern` = 0';
    if(permission('votes')) {
        $whereIntern = '';
    }

    $fvote = '';
    if(!settings('forum_vote'))
        $fvote = empty($whereIntern) ? ' AND `forum` = 0' : ' AND `forum` = 0';

    $qry = $sql->select('SELECT votes.*,sum(votes_result.`stimmen`) as `ges_stimmen` FROM `{prefix_votes}` as votes, `{prefix_vote_results}` as `votes_result`'
            . ' WHERE votes.`id` = votes_result.`vid` '.$whereIntern.$fvote.''
            . ' GROUP by votes.`id` '.orderby_sql(array('titel','datum','von','ges_stimmen'), 'ORDER BY `datum`;'));
    foreach($qry as $get) {
        $qryv = $sql->select('SELECT * FROM `{prefix_vote_results}` '
                           . 'WHERE `vid` = '.$get['id'].' ORDER BY `id`;');

        $check = ''; $ipcheck = false; $intern = '';
        $stimmen = $get['ges_stimmen'];
        $vid = 'vid_'.$get['id'];
        if($get['intern']) {
            $showVoted = '';
            $intern = _votes_intern;
        }

        $results = ''; $color2 = 0;
        $ipcheck = !count_clicks('vote',$get['id'],0,false);
        foreach($qryv as $getv) {
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            if($ipcheck || cookie::get('vid_'.$get['id']) != false || $get['closed']) {
                $percent = @round($getv['stimmen']/$stimmen*100,2);
                $rawpercent = @round($getv['stimmen']/$stimmen*100,0);
                $balken = show(_votes_balken, array("width" => $rawpercent));
                $result_head = _votes_results_head;
                $votebutton = "";
                $results .= show($dir."/votes_results", array("answer" => re($getv['sel']),
                                                              "percent" => $percent,
                                                              "class" => $class,
                                                              "stimmen" => $getv['stimmen'],
                                                              "balken" => $balken));
            } else {
                $result_head = _votes_results_head_vote;
                $votebutton = '<input id="voteSubmit_'.$get['id'].'" type="submit" value="'._button_value_vote.'" class="submit" />';
                $results .= show($dir."/votes_vote", array("id" => $getv['id'],
                                                           "answer" => re($getv['sel']),
                                                           "class" => $class));
            }
        }

        $showVoted = '';
        if($get['intern'] && $stimmen != 0 && ($get['von'] == $userid || permission('votes'))) {
            $showVoted = ' <a href="?action=showvote&amp;id='.$get['id'].'"><img src="../inc/images/lupe.gif" alt="" title="'.
            _show_who_voted.'" class="icon" /></a>';
        }

        if(isset($_GET['show']) && $_GET['show'] == $get['id']) {
            $moreicon = "collapse";
            $display = "";
        } else {
            $moreicon = "expand";
            $display = "none";
        }

        $ftitel = $get['forum'] ? re($get['titel']).' (Forum)' : re($get['titel']);
        $titel = show(_votes_titel, array("titel" => $ftitel,
                                          "vid" => $get['id'],
                                          "icon" => $moreicon,
                                          "intern" => $intern));

        $closed = $get['closed'] ? _closedicon_votes : '';
        $class = ($color2 % 2) ? "contentMainSecond" : "contentMainFirst"; $color2++;
        $show .= show($dir."/votes_show", array("datum" => date("d.m.Y", $get['datum']),
                                                "titel" => $titel,
                                                "vid" => $get['id'],
                                                "display" => $display,
                                                "result_head" => $result_head,
                                                "results" => $results,
                                                "show" => $showVoted,
                                                "closed" => $closed,
                                                "autor" => autor($get['von']),
                                                "class" => $class,
                                                "votebutton" => $votebutton,
                                                "stimmen" => $stimmen));
    }

    if(empty($show)) {
        $show = show(_no_entrys_yet, array("colspan" => "4"));
    }
    
    $index = show($dir."/votes", array("show" => $show,
                                       "order_titel" => orderby('titel'),
                                       "order_autor" => orderby('von'),
                                       "order_datum" => orderby('datum'),
                                       "order_stimmen" => orderby('ges_stimmen')));
}