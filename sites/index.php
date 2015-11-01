<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

## OUTPUT BUFFER START ##
include("../inc/buffer.php");

## INCLUDES ##
include(basePath."/inc/common.php");

## SETTINGS ##
$dir = "sites";

## SECTIONS ##
switch ($action):
default:
    $qry = $sql->fetch("SELECT s1.*,s2.`internal` "
                            . "FROM `{prefix_sites}` AS `s1` "
                            . "LEFT JOIN `{prefix_navi}` AS `s2` "
                            . "ON s1.`id` = s2.`editor` "
                            . "WHERE s1.`id` = ?;",array(intval($_GET['show'])));
    if($sql->rowCount()) {
        if($get['internal'] && !$chkMe)
          $index = error(_error_wrong_permissions, 1);
        else {
          $where = re($get['titel']);

          if($get['html']) 
              $inhalt = bbcode_html(re($get['text']));
          else 
              $inhalt = bbcode(re($get['text']));

          $index = show($dir."/sites", array("titel" => re($get['titel']), "inhalt" => $inhalt));
        }
    } else 
        $index = error(_sites_not_available,1);
break;
case 'preview';
    header("Content-type: text/html; charset=utf-8");
    if($_POST['html']) 
        $inhalt = bbcode_html($_POST['inhalt'],true);
    else 
        $inhalt = bbcode($_POST['inhalt'],true);

    $index = show($dir."/sites", array("titel" => re($_POST['titel']), "inhalt" => $inhalt));
    exit(utf8_encode('<table class="mainContent" cellspacing="1"'.$index.'</table>'));
break;
endswitch;

## INDEX OUTPUT ##
$title = $pagetitle." - ".$where;
page($index, $title, $where);