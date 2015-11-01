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
$dir = "user";
$where = _site_user;
define('_UserMenu', true);

function custom_content($kid=1) {
    global $sql;
    $custom_content = ''; $i = 0;
    $qrycustom = $sql->select("SELECT `feldname`,`type`,`name` FROM `{prefix_profile}` WHERE `kid` = ? AND `shown` = 1 ORDER BY id ASC;",array($kid));
    if($sql->rowCount()) {
        foreach($qrycustom as $getcustom) {
            $getcontent = $sql->fetch("SELECT `".$getcustom['feldname']."` FROM `{prefix_users}` WHERE `id` = ? LIMIT 1;",array(intval($_GET['id'])));
            if(!empty($getcontent[$getcustom['feldname']])) {
                switch($getcustom['type']) {
                    case 2:
                        $custom_content .= show(_profil_custom_url, array("name" => re(pfields_name($getcustom['name'])), "value" => re($getcontent[$getcustom['feldname']])));
                        break;
                    case 3:
                        $custom_content .= show(_profil_custom_mail, array("name" => re(pfields_name($getcustom['name'])), "value" => CryptMailto(re($getcontent[$getcustom['feldname']]),_link_mailto)));
                        break;
                    default:
                        $custom_content .= show(_profil_custom, array("name" => re(pfields_name($getcustom['name'])), "value" => re($getcontent[$getcustom['feldname']])));
                        break;
                }

                $i++;
            }
        }
    }

    return array('count' => $i, 'content' => $custom_content);
}

function getcustom($kid=1,$user=0) {
    global $sql,$userid;
    if (!$kid) { return ""; }
    $set_id = ($user != 0 ? intval($user) : $userid);
    $qrycustom = $sql->select("SELECT `feldname`,`name` FROM `{prefix_profile}` WHERE `kid` = ? AND `shown` = 1 ORDER BY id ASC",array($kid)); $custom = "";
    foreach($qrycustom as $getcustom) {
        $getcontent = $sql->fetch("SELECT `".$getcustom['feldname']."` FROM `{prefix_users}` WHERE `id` = ? LIMIT 1;",array($set_id));
        $custom .= show(_profil_edit_custom, array("name" => re(pfields_name($getcustom['name'])) . ":",
                                                   "feldname" => $getcustom['feldname'],
                                                   "value" => re($getcontent[$getcustom['feldname']])));
    }
                            
    return $custom;
}

//Setzt die IDs der Messages Tabelle auf 1 zuruck, wenn alle Nachrichten geloscht wurden.
function msg_truncate() {
    if(auto_db_optimize) {
        $sql->select("SELECT `id` FROM `{prefix_messages}`;");
        if(!$sql->rowCount()) {
            $sql->query("TRUNCATE `{prefix_messages}`;");
        }
    }
}

//-> Prueft ob ein User schon in der Buddyliste vorhanden ist
function check_buddy($buddy) {
    global $sql,$userid;
    return !$sql->rows("SELECT `buddy` FROM `{prefix_userbuddys}` WHERE `user` = ? AND `buddy` = ?;",
            array(intval($userid),intval($buddy))) ? true : false;
}

//Load Index
if (file_exists(basePath . "/user/case_" . $action . ".php")) {
    require_once(basePath . "/user/case_" . $action . ".php");
}

## INDEX OUTPUT ##
$whereami = preg_replace_callback("#autor_(.*?)$#",create_function('$id', 'return re(data("nick","$id[1]"));'),$where);
$title = $pagetitle." - ".$whereami."";
page($index, $title, $where);