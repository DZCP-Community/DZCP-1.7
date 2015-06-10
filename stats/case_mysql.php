<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Stats')) exit();

$info = array(); $entrys = 0;
$sum = 0; $rows = 0;
$qry = $sql->show("Show table status;");
foreach($qry as $data) {
    $allRows = $data["Rows"];
    $dataLength  = $data["Data_length"];
    $indexLength = $data["Index_length"];
    $tableSum    = ($dataLength + $indexLength);

    $sum += $tableSum;
    $rows += $allRows;
    $entrys++;
}

$info["entrys"] = $entrys;
$info["rows"] = $rows;
$info["size"] = @round($sum/1048576,2);

$stats = show($dir."/mysql", array("nsize" => $info["size"],
                                   "nentrys" => $info["entrys"],
                                   "nrows" => $info["rows"]));