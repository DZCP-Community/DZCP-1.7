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
        $qry = db("INSERT INTO `{prefix_users}` SET `user` = '".up($_POST['user'])."',`nick` = '".up($_POST['nick'])."', `email` = '".up($_POST['email'])."',`pwd` = '".up($pwd)."', `rlname` = '".up($_POST['rlname'])."', `sex` = '".intval($_POST['sex'])."', `bday`     = '".(!$bday ? 0 : strtotime($bday))."',
                                 `city`     = '".up($_POST['city'])."',
                                 `country`  = '".up($_POST['land'])."',
                                 `regdatum` = '".time()."',
                                 `level`    = '".intval($_POST['level'])."',
                                 `time`     = '".time()."',
                                 `gmaps_koord`  = '".up($_POST['gmaps_koord'])."',
                                 `status`   = '1'");

      $insert_id = _insert_id();
      setIpcheck("createuser(".$_SESSION['id']."_".$insert_id.")");

    // permissions
      if(!empty($_POST['perm']))
      {
        foreach($_POST['perm'] AS $v => $k) $p .= "`".substr($v, 2)."` = '".intval($k)."',";
                         if(!empty($p)) $p = ', '.substr($p, 0, strlen($p) - 1);

        db("INSERT INTO ".$db['permissions']." SET `user` = '".intval($insert_id)."'".$p);
      }
    ////////////////////

    // internal boardpermissions
      if(!empty($_POST['board']))
      {
          foreach($_POST['board'] AS $v)
            db("INSERT INTO ".$db['f_access']." SET `user` = '".intval($insert_id)."', `forum` = '".$v."'");
      }
    ////////////////////

      $sq = db("SELECT * FROM ".$db['squads']."");
      while($getsq = _fetch($sq))
      {
        if(isset($_POST['squad'.$getsq['id']]))
        {
          $qry = db("INSERT INTO ".$db['squaduser']."
                     SET `user`  = '".intval($insert_id)."',
                         `squad` = '".intval($_POST['squad'.$getsq['id']])."'");
        }

        if(isset($_POST['squad'.$getsq['id']]))
        {
          $qry = db("INSERT INTO ".$db['userpos']."
                     SET `user`   = '".intval($insert_id)."',
                         `posi`   = '".intval($_POST['sqpos'.$getsq['id']])."',
                         `squad`  = '".intval($getsq['id'])."'");
        }
      }

    //Profilfoto
    if(!empty($_FILES['file']))
    {
        $tmpname = $_FILES['file']['tmp_name'];
        $name = $_FILES['file']['name'];
        $type = $_FILES['file']['type'];
        $size = $_FILES['file']['size'];

        $endung = explode(".", $_FILES['file']['name']);
        $endung = strtolower($endung[count($endung)-1]);

        if($tmpname)
        {
            $imageinfo = getimagesize($tmpname);
            foreach($picformat as $tmpendung)
            {
                if(file_exists(basePath."/inc/images/uploads/userpics/".$insert_id.".".$tmpendung)) {
                    @unlink(basePath."/inc/images/uploads/userpics/".$insert_id.".".$tmpendung);
                }
            }
            copy($tmpname, basePath."/inc/images/uploads/userpics/".$insert_id.".".strtolower($endung)."");
            @unlink($_FILES['file']['tmp_name']);
        }
    }

    //Avatar
    if(!empty($_FILES['file_avatar']))
    {
        $tmpname = $_FILES['file_avatar']['tmp_name'];
        $name = $_FILES['file_avatar']['name'];
        $type = $_FILES['file_avatar']['type'];
        $size = $_FILES['file_avatar']['size'];

        $endung = explode(".", $_FILES['file_avatar']['name']);
        $endung = strtolower($endung[count($endung)-1]);

        if($tmpname)
        {
            $imageinfo = getimagesize($tmpname);
            foreach($picformat as $tmpendung)
            {
                if(file_exists(basePath."/inc/images/uploads/useravatare/".$insert_id.".".$tmpendung)) {
                @unlink(basePath."/inc/images/uploads/useravatare/".$insert_id.".".$tmpendung);
                }
            }

            copy($tmpname, basePath."/inc/images/uploads/useravatare/".$insert_id.".".strtolower($endung)."");
            @unlink($_FILES['file_avatar']['tmp_name']);
        }
    }

      db("INSERT INTO ".$db['userstats']."
                       SET `user`       = '".intval($insert_id)."',
                   `lastvisit`    = '".time()."'");

      $show = info(_uderadd_info, "../admin/");

      }
}

if(empty($show)) {
    $dropdown_age = show(_dropdown_date, array("day" => dropdown("day",$bdayday,1),
                                               "month" => dropdown("month",$bdaymonth,1),
                                               "year" => dropdown("year",$bdayyear,1)));

    $gmaps = show('membermap/geocoder', array('form' => 'adduser'));

    $qrysq = $sql->select("SELECT `id`,`name` FROM `{prefix_squads}` ORDER BY `pos`;");
    foreach($qrysq as $getsq) {
            $qrypos = $sql->select("SELECT id,position FROM ".$db['pos']." ORDER BY pid"); $posi = "";
            foreach($qrypos as $getpos) {
                $check = $sql->rows("SELECT * FROM `{prefix_userposis}` WHERE `posi` = ? AND `squad` = ? AND `user` = ?;",
                    array($getpos['id'],$getsq['id'],intval($_GET['edit'])));

                $sel = $check ? 'selected="selected"' : "";
                $posi .= show(_select_field_posis, array("value" => $getpos['id'], "sel" => $sel, "what" => re($getpos['position'])));
            }

            $check = $sql->rows("SELECT squad FROM `{prefix_userposis}` WHERE `user` = ? AND `squad` = ?;",array(intval($_GET['edit']),$getsq['id'])) ? 'checked="checked"' : '';
            $esquads .= show(_checkfield_squads, array("id" => $getsq['id'], "check" => $check,"eposi" => $posi,"squad" => re($getsq['name'])));
        }

        $show = show($dir."/register", array("esquad" => $esquads,
                                             "getpermissions" => getPermissions(),
                                             "getboardpermissions" => getBoardPermissions(),
                                             "dropdown_age" => $dropdown_age,
                                             "country" => show_countrys($get['country']),
                                             "gmaps" => $gmaps,
                                             "alvl" => ""));
}