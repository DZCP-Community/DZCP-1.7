<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_Shout')) exit();

$securimage->namespace = 'menu_shout';
if(!ipcheck("shout", settings::get('f_shout'))) {
    if(($_POST['protect'] != 'nospam' || !$securimage->check($_POST['secure']) || empty($_POST['spam'])) && !$userid)
        $index = error(html_entity_decode(_error_invalid_regcode, ENT_COMPAT | ENT_HTML401,'ISO-8859-1'),1);
    elseif(!$userid && (empty($_POST['name']) || trim($_POST['name']) == '') || $_POST['name'] == "Nick")
        $index = error(_empty_nick, 1);
    elseif(!$userid && empty($_POST['email']) || $_POST['email'] == "E-Mail")
        $index = error(_empty_email, 1);
    elseif(!$userid && !check_email($_POST['email']))
        $index = error(_error_invalid_email, 1);
    elseif(empty($_POST['eintrag']))
        $index = error(_error_empty_shout, 1);
    elseif(settings::get('reg_shout') && !$chkMe)                       
        $index = error(_error_unregistered, 1);
    else {
        $reg = !$userid ? $_POST['email'] : $userid;
        $sql->insert("INSERT INTO `{prefix_shoutbox}` SET `datum` = ?,`nick` = ?,`email` = ?,`text` = ?,`ip` = ?;",
            array(time(),stringParser::encode($_POST['name']),stringParser::encode($reg),stringParser::encode(substr(str_replace("\n", ' ', $_POST['eintrag']),0,settings::get('shout_max_zeichen'))),$userip));

        setIpcheck("shout");
        
        if(!isset($_GET['ajax'])) 
            header("Location: ".GetServerVars('HTTP_REFERER').'#shoutbox');
    }
} else
    $index = error(show(_error_flood_post, array("sek" => settings::get('f_shout'))), 1);

if(isset($_GET['ajax'])) {
    exit(str_replace("\n", '', html_entity_decode(strip_tags($index))));
}