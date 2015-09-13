<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Database Connect & Functions
 */

//#######################################
//OLD Code Adapter
//#######################################

$mysqli = null;
if($db['host'] != '' && $db['user'] != '' && $db['pass'] != '' && $db['db'] != '' && !$thumbgen) {
    $db_host = $db['host'];
    $mysqli = new mysqli($db_host,$db['user'],$db['pass'],$db['db']);
    if ($mysqli->connect_error) { die("<b>Fehler beim Zugriff auf die Datenbank!"); }
}

//MySQLi-Funktionen
function _rows($rows) {
    global $mysqli;
    if ($mysqli instanceof mysqli)
        return array_key_exists('_stmt_rows_', $rows) ? $rows['_stmt_rows_'] : $rows->num_rows;

    return false;
}

function _fetch($fetch) {
    global $mysqli;
    if ($mysqli instanceof mysqli)
        return array_key_exists('_stmt_rows_', $fetch) ? $fetch[0] : $fetch->fetch_assoc();

    return false;
}

function _insert_id() {
    global $mysqli;
    if ($mysqli instanceof mysqli)
        return $mysqli->insert_id;

    return false;
}

function db($query='',$rows=false,$fetch=false) {
    global $mysqli,$clanname,$updater;
    if ($mysqli instanceof mysqli) {
        if(debug_all_sql_querys) DebugConsole::wire_log('debug', 9, 'SQL_Query', $query);
        if($updater) { $qry = $mysqli->query($query); } else {
            if(!$qry = $mysqli->query($query)) {
                $message = DebugConsole::sql_error_Exception($query,$query);
                include_once(basePath."/inc/lang/languages/english.php");
                $message = 'SQL-Debug:<p>'.$message;
                die(show('<b>Upps...</b><br /><br />Entschuldige bitte! Das h&auml;tte nicht passieren d&uuml;rfen. Wir k&uuml;mmern uns so schnell wie m&ouml;glich darum.<br><br>'.$clanname.'<br><br>'.(view_error_reporting ? nl2br($message).'<br><br>' : '').'[lang_back]'));
            }
        }

        if ($rows && !$fetch)
            return _rows($qry);
        else if($fetch && $rows)
            return $qry->fetch_array(MYSQLI_NUM);
        else if($fetch && !$rows)
            return _fetch($qry);

        return $qry;
    }

    return false;
}