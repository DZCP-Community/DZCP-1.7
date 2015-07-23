<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

//-> Informationen ueber die mySQL-Datenbank
function dbinfo() {
    global $sql;
    $info = array(); $entrys = 0;
    $sum = 0; $rows = 0;
    $qry = $sql->show("Show table status;");
    foreach($qry as $get) {
        $allRows = $get["Rows"];
        $dataLength  = $get["Data_length"];
        $indexLength = $get["Index_length"];
        $tableSum    = $dataLength + $indexLength;

        $sum += $tableSum;
        $rows += $allRows;
        $entrys++;
    }

    $info["entrys"] = $entrys;
    $info["rows"] = $rows;
    $info["size"] = @round($sum/1048576,2);
    return $info;
}