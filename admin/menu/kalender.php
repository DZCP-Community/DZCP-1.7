<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;

$where = $where.': '._kalender_head;
switch ($do) {
    case 'add':
        if(isset($_POST)) {
            if(empty($_POST['title']) || empty($_POST['event']))
            {
              if(empty($_POST['title']))     $show = error(_kalender_error_no_title,1);
              elseif(empty($_POST['event'])) $show = error(_kalender_error_no_event,1);
            } else {
              $time = mktime($_POST['h'],$_POST['min'],0,$_POST['m'],$_POST['t'],$_POST['j']);
              $sql->insert("INSERT INTO `{prefix_events}` SET `datum` = ?, `title` = ?, `event` = ?;",
                      array(intval($time),up($_POST['title']),up($_POST['event'])));

              $show = info(_kalender_successful_added,"?admin=kalender");
            }
        } else {
        
        $dropdown_date = show(_dropdown_date, array("day" => dropdown("day",date("d",time())),
                                                    "month" => dropdown("month",date("m",time())),
                                                    "year" => dropdown("year",date("Y",time()))));

        $dropdown_time = show(_dropdown_time, array("hour" => dropdown("hour",date("H",time())),
                                                    "minute" => dropdown("minute",date("i",time())),
                                                    "uhr" => _uhr));
        
        $show = show($dir."/form_kalender", array("datum" => _datum,
                                                  "event" => _kalender_event,
                                                  "dropdown_time" => $dropdown_time,
                                                  "dropdown_date" => $dropdown_date,
                                                  "beschreibung" => _beschreibung,
                                                  "what" => _button_value_add,
                                                  "do" => "addevent",
                                                  "k_event" => "",
                                                  "k_beschreibung" => "",
                                                  "head" => _kalender_admin_head));
        }
    break;
    case 'edit':
        $get = $sql->fetch("SELECT `datum`,`title`,`event` FROM `{prefix_events}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $dropdown_date = show(_dropdown_date, array("day" => dropdown("day",date("d",$get['datum'])),
                                                    "month" => dropdown("month",date("m",$get['datum'])),
                                                    "year" => dropdown("year",date("Y",$get['datum']))));

        $dropdown_time = show(_dropdown_time, array("hour" => dropdown("hour",date("H",$get['datum'])),
                                                    "minute" => dropdown("minute",date("i",$get['datum'])),
                                                    "uhr" => _uhr));
      
        $show = show($dir."/form_kalender", array("datum" => _datum,
                                                  "event" => _kalender_event,
                                                  "dropdown_time" => $dropdown_time,
                                                  "dropdown_date" => $dropdown_date,
                                                  "beschreibung" => _beschreibung,
                                                  "what" => _button_value_edit,
                                                  "do" => "editevent&amp;id=".$_GET['id'],
                                                  "k_event" => re($get['title']),
                                                  "k_beschreibung" => re($get['event']),
                                                  "head" => _kalender_admin_head_edit));
    break;
    case 'editevent':
      if(empty($_POST['title']) || empty($_POST['event']))
      {
        if(empty($_POST['title']))     $show = error(_kalender_error_no_title,1);
        elseif(empty($_POST['event'])) $show = error(_kalender_error_no_event,1);
      } else {
        $time = mktime($_POST['h'],$_POST['min'],0,$_POST['m'],$_POST['t'],$_POST['j']);
        $sql->update("UPDATE `{prefix_events}` SET `datum` = ?, `title` = ?, `event` = ? WHERE `id` = ?;",
                array(intval($time),up($_POST['title']),up($_POST['event']),intval($_GET['id'])));

        $show = info(_kalender_successful_edited,"?admin=kalender");
      }
    break;
    case 'delete':
        $sql->delete("DELETE FROM `{prefix_events}` WHERE `id` = ?;",array(intval($_GET['id'])));
        $show = info(_kalender_deleted,"?admin=kalender");
    break;
    default:
        $qry = $sql->select("SELECT * FROM `{prefix_events}` ".orderby_sql(array("event","datum"),'ORDER BY `datum` DESC').";");
        foreach($qry as $get) {
          $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                        "action" => "admin=kalender&amp;do=edit",
                                                        "title" => _button_title_edit));

          $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                            "action" => "admin=kalender&amp;do=delete",
                                                            "title" => _button_title_del,
                                                            "del" => convSpace(_confirm_del_kalender)));

          $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
          $show .= show($dir."/kalender_show", array("datum" => date("d.m.y H:i", $get['datum'])._uhr,
                                                      "event" => re($get['title']),
                                                      "time" => $get['datum'],
                                                      "class" => $class,
                                                      "edit" => $edit,
                                                      "delete" => $delete));
        }

        if(empty($show))
            $show = '<tr><td colspan="4" class="contentMainSecond">'._no_entrys.'</td></tr>';

        $show = show($dir."/kalender", array("head" => _kalender_admin_head,
                                             "date" => _datum,
                                             "titel" => _kalender_event,
                                             "show" => $show,
                                             "order_date" => orderby('datum'),
                                             "order_titel" => orderby('event'),
                                             "add" => _kalender_admin_head_add));
    break;
}