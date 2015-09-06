<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(defined('_Away')) {
    $where = $where.' - '._away_list;
    if(!$chkMe || $chkMe < 2) {
        $index = error(_error_wrong_permissions, 1);
    } else {
        $show = "";
        $entrys = cnt('{prefix_away}');
        $qry = $sql->select("SELECT * FROM `{prefix_away}` ".orderby_sql(array("userid","start","end"), 'ORDER BY `id` DESC')." "
                . "LIMIT ".($page - 1)*settings('m_away').",".settings('m_away').";");
        foreach($qry as $get) {
            if($get['start'] > time()) 
                $status = _away_status_new;
            
            if($get['start'] <= time() && $get['end'] >= time()) 
                $status = _away_status_now;
            
            if($get['end'] < time()) 
                $status = _away_status_done;
            
            if($userid == $get['userid'] || $chkMe == 4) {
                $value = show("page/button_edit_single", array("id" => $get['id'],
                                                               "action" => "action=edit",
                                                               "title" => _button_title_edit));
            } else {
                $value = "&nbsp;";
            }

            if($get['end'] < time()) 
                $value = "&nbsp;";

            $chkMe == 4 ? $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                                            "action" => "action=del",
                                                                            "title" => _button_title_del,
                                                                            "del" => convSpace(_confirm_del_entry))) : $delete = "&nbsp;";

            $info = show($dir."/button_info", array("id" => $get['id'],
                                                    "action" => "action=info",
                                                    "title" =>"Info"));

            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $show .= show($dir."/away_show", array("class" => $class,
                                                   "id" => $get["id"],
                                                   "status" => $status,
                                                   "von" => date("d.m.y",$get['start']),
                                                   "bis" => date("d.m.y",$get['end']),
                                                   "grund" => $get["titel"],
                                                   "value" => $value,
                                                   "del" => $delete,
                                                   "nick" => autor($get['userid']),
                                                   "details" => $info));
        }

        if(empty($show)) 
            $show = _away_no_entry;
        
        $nav= nav($entrys,settings('m_away'),"?".(isset($_GET['show']) ? $_GET['show'] : "").orderby_nav());
        $index = show($dir."/away", array("order_user" => orderby('userid'),
                                          "order_status" => orderby('end'),
                                          "order_from" => orderby('start'),
                                          "order_to" => orderby('end'),
                                          "show" => $show,
                                          "nav" => $nav));
    }
}