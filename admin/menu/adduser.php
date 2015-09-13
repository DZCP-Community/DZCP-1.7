<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;
$where = $where.': '._config_useradd_head;

if(isset($_POST['user'])) {
    $check_user = $sql->rows("SELECT `id` FROM `{prefix_users}` WHERE `user`= ?;", array(up($_POST['user'])));
    $check_nick = $sql->rows("SELECT `id` FROM `{prefix_users}` WHERE `nick`= ?;", array(up($_POST['nick'])));
    $check_email = $sql->rows("SELECT `id` FROM `{prefix_users}` WHERE `email`= ?;", array(up($_POST['email'])));

    if(empty($_POST['user'])) {
        $show = error(_empty_user, 1);
    } elseif(empty($_POST['nick'])) {
        $show = error(_empty_nick, 1);
    } elseif(empty($_POST['email'])) {
        $show = error(_empty_email, 1);
    } elseif(!check_email($_POST['email'])) {
        $show = error(_error_invalid_email, 1);
    } elseif($check_user) {
        $show = error(_error_user_exists, 1);
    } elseif($check_nick) {
        $show = error(_error_nick_exists, 1);
    } elseif($check_email) {
        $show = error(_error_email_exists, 1);
    } else {
        $mkpwd = empty($_POST['pwd']) ? mkpwd() : $_POST['pwd'];
        $pwd = md5($mkpwd);
        $bday = ($_POST['t'] && $_POST['m'] && $_POST['j'] ? cal($_POST['t']).".".cal($_POST['m']).".".$_POST['j'] : 0);
        $qry = $sql->insert("INSERT INTO `{prefix_users}` "
                          . "SET `user` = ?,"
                          . "`nick` = ?, "
                          . "`email` = ?,"
                          . "`pwd` = ?, "
                          . "`rlname` = ?, "
                          . "`sex` = ?, "
                          . "`bday` = ?, "
                          . "`city` = ?, "
                          . "`country` = ?, "
                          . "`regdatum` = ?, "
                          . "`level` = ?, "
                          . "`time` = ?, "
                          . "`gmaps_koord` = ?, "
                          . "`status` = 1;",
                array(up($_POST['user']),up($_POST['nick']),up($_POST['email']),up($pwd),up($_POST['rlname']),intval($_POST['sex']),
                (!$bday ? 0 : strtotime($bday)),up($_POST['city']),up($_POST['land']),$time=time(),intval($_POST['level']),$time,up($_POST['gmaps_koord'])));

        $insert_id = $sql->lastInsertId();
        setIpcheck("createuser(".$_SESSION['id']."_".$insert_id.")");

        //Insert Permissions
        $permissions = "";
        foreach($_POST['perm'] AS $v => $k) {
            $permissions .= "`".substr($v, 2)."` = ".intval($k).", ";
        }

        if(!empty($permissions)) {
            $permissions = ', '.substr($permissions, 0, -2);
        }

        $sql->insert("INSERT INTO `{prefix_permissions}` SET `user` = ?".$permissions.";",array($insert_id));

        // internal boardpermissions
        if(!empty($_POST['board'])) {
            foreach ($_POST['board'] AS $boardname) {
                $sql->insert("INSERT INTO `{prefix_f_access}` SET `user` = ?, `forum` = ?;",array($insert_id,$boardname));
            }
        }

        $squads = $sql->select("SELECT * FROM `{prefix_squads}`;");
        foreach($squads as $getsq) {
            if(isset($_POST['squad'.$getsq['id']])) {
                $sql->insert("INSERT INTO `{prefix_squaduser}` SET `user`  = ?, `squad` = ?;",array($insert_id,intval($_POST['squad'.$getsq['id']])));
            }

            if(isset($_POST['squad'.$getsq['id']])) {
                $sql->insert("INSERT INTO `{prefix_userposis}` SET `user` = ?, `posi` = ?, `squad` = ?;",array($insert_id,intval($_POST['sqpos'.$getsq['id']]),$getsq['id']));
            }
        }

        //Profilfoto
        if(!empty($_FILES['file'])) {
            $tmpname = $_FILES['file']['tmp_name'];
            $name = $_FILES['file']['name'];
            $type = $_FILES['file']['type'];
            $size = $_FILES['file']['size'];

            $endung = explode(".", $_FILES['file']['name']);
            $endung = strtolower($endung[count($endung)-1]);

            if($tmpname) {
                $imageinfo = getimagesize($tmpname);
                foreach($picformat as $tmpendung) {
                    if(file_exists(basePath."/inc/images/uploads/userpics/".$insert_id.".".$tmpendung)) {
                        @unlink(basePath."/inc/images/uploads/userpics/".$insert_id.".".$tmpendung);
                    }
                }
                copy($tmpname, basePath."/inc/images/uploads/userpics/".$insert_id.".".strtolower($endung)."");
                @unlink($_FILES['file']['tmp_name']);
            }
        }

        //Avatar
        if(!empty($_FILES['file_avatar'])) {
            $tmpname = $_FILES['file_avatar']['tmp_name'];
            $name = $_FILES['file_avatar']['name'];
            $type = $_FILES['file_avatar']['type'];
            $size = $_FILES['file_avatar']['size'];

            $endung = explode(".", $_FILES['file_avatar']['name']);
            $endung = strtolower($endung[count($endung)-1]);

            if($tmpname) {
                $imageinfo = getimagesize($tmpname);
                foreach($picformat as $tmpendung) {
                    if(file_exists(basePath."/inc/images/uploads/useravatare/".$insert_id.".".$tmpendung)) {
                        @unlink(basePath."/inc/images/uploads/useravatare/".$insert_id.".".$tmpendung);
                    }
                }

                copy($tmpname, basePath."/inc/images/uploads/useravatare/".$insert_id.".".strtolower($endung)."");
                @unlink($_FILES['file_avatar']['tmp_name']);
            }
        }

        $sql->insert("INSERT INTO `{prefix_userstats}` SET `user` = ?, `lastvisit` = ?;",array($insert_id,time()));
        $show = info(_uderadd_info, "../admin/");
    }
}

if(empty($show)) {
    $dropdown_age = show(_dropdown_date, array("day" => dropdown("day",0,1),
                                               "month" => dropdown("month",0,1),
                                               "year" => dropdown("year",0,1)));

    $qrysq = $sql->select("SELECT `id`,`name` FROM `{prefix_squads}` ORDER BY `pos`;"); $esquads = "";
    foreach($qrysq as $getsq) {
        $qrypos = $sql->select("SELECT `id`,`position` FROM `{prefix_positions}` ORDER BY `pid`;"); $posi = "";
        foreach($qrypos as $getpos) {
            $posi .= show(_select_field_posis, array("value" => $getpos['id'], "sel" => "", "what" => re($getpos['position'])));
        }

        $esquads .= show(_checkfield_squads, array("id" => $getsq['id'], "check" => "","eposi" => $posi,"squad" => re($getsq['name'])));
    }

    $gmaps = show('membermap/geocoder', array('form' => 'adduser'));
    $show = show($dir."/register", array("esquad" => $esquads,
                                         "getpermissions" => getPermissions(),
                                         "getboardpermissions" => getBoardPermissions(),
                                         "dropdown_age" => $dropdown_age,
                                         "country" => show_countrys(),
                                         "gmaps" => $gmaps,
                                         "alvl" => ""));
}