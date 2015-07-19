<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if (!defined('_GB')) exit();

  if(isset($_GET['what']) && $_GET['what'] == "addgb")
  {
    if($userid >= 1)
    {
      $toCheck = empty($_POST['eintrag']);
    } else {
      $toCheck = empty($_POST['nick']) || empty($_POST['email']) || empty($_POST['eintrag']) || !check_email($_POST['email']) || !$securimage->check($_POST['secure']);
    }
      if($toCheck)
        {
      if($userid >= 1)
        {
        if(empty($_POST['eintrag'])) $error = _empty_eintrag;
            $form = show("page/editor_regged", array("nick" => autor($userid), "von" => _autor));
        } else {
            if (!$securimage->check($_POST['secure']))
                $error = captcha_mathematic ? _error_invalid_regcode_mathematic : _error_invalid_regcode;
            elseif(empty($_POST['nick']))
                $error = _empty_nick;
            elseif(empty($_POST['email']))
                $error = _empty_email;
            elseif(!check_email($_POST['email']))
                $error = _error_invalid_email;
            elseif(empty($_POST['eintrag']))
                $error = _empty_eintrag;

            $form = show("page/editor_notregged", array("nickhead" => _nick,
                                                        "emailhead" => _email,
                                                        "hphead" => _hp));
      }

          $error = show("errors/errortable", array("error" => $error));

          $index = show($dir."/add", array("titel" => _eintragen_titel,
                                                                           "nickhead" => _nick,
                                                                           "bbcodehead" => _bbcode,
                                                                           "emailhead" => _email,
                                                                           "hphead" => _hp,
                                       "preview" => _preview,
                                       "security" => _register_confirm,
                                       "add_head" => _gb_add_head,
                                       "ed" => "",
                                       "whaturl" => "addgb",
                                       "what" => _button_value_add,
                                       "form" => $form,
                                       "reg" => "",
                                       "ip" => _iplog_info,
                                                                           "id" => isset($_GET['id']) ? $_GET['id'] : '0',
                                                                           "postemail" => $_POST['email'],
                                                                           "posthp" => links($_POST['hp']),
                                                                           "postnick" => $_POST['nick'],
                                                                           "posteintrag" => re($_POST["eintrag"]),
                                                                           "error" => $error,
                                                                           "eintraghead" => _eintrag));
      } else {
          $qry = db("INSERT INTO ".$db['gb']."
                 SET `datum`      = '".time()."',
                     `nick`       = '".up($_POST['nick'])."',
                     `email`      = '".up($_POST['email'])."',
                     `hp`         = '".links($_POST['hp'])."',
                     `reg`        = '".intval($userid)."',
                     `nachricht`  = '".up($_POST['eintrag'])."',
                     `ip`         = '".$userip."'");

        setIpcheck("gb");
        $index = info(_gb_entry_successful, "../gb/");
      }
  }
  elseif(isset($_GET['what']) && $_GET['what'] == 'set')
  {
          if(permission('gb'))
        {
            db("UPDATE ".$db['gb']." SET `public` = '1' WHERE id = '".intval($_GET['id'])."'");
            header("Location: ../gb/");
        }
        else
        $index = error(_error_edit_post,1);
    }
    elseif($_GET['what'] == 'unset')
    {
        if(permission('gb'))
        {
               db("UPDATE ".$db['gb']." SET `public` = '0' WHERE id = '".intval($_GET['id'])."'");
               header("Location: ../gb/");
        }
        else
        $index = error(_error_edit_post,1);
    }
    elseif(isset($_GET['what']) && $_GET['what'] == "delete")
    {
        $qry = db("SELECT * FROM ".$db['gb']." WHERE id = '".intval($_GET['id'])."'");
        $get = _fetch($qry);

    if($get['reg'] == $userid && $chkMe >= 1 or permission('gb'))
    {
      db("DELETE FROM ".$db['gb']." WHERE id = '".intval($_GET['id'])."'");
      $index = info(_gb_delete_successful, "../gb/");
    }
    else
        $index = error(_error_edit_post,1);

    }
    elseif(isset($_GET['what']) && $_GET['what'] == "edit")
    {
    $qry = db("SELECT * FROM ".$db['gb']."  WHERE id = '".intval($_GET['id'])."'");
    $get = _fetch($qry);

    if($get['reg'] == $userid && $chkMe >= 1 or permission('gb'))
    {
      if($get['reg'] != 0)
        {
            $form = show("page/editor_regged", array("nick" => autor($get['reg']),
                                                 "von" => _autor));
        } else {
        $form = show("page/editor_notregged", array("nickhead" => _nick,
                                                    "emailhead" => _email,
                                                    "hphead" => _hp,
                                                    "postemail" => re($get['email']),
                                                                                    "posthp" => re($get['hp']),
                                                                                    "postnick" => re($get['nick'])));
      }

      $index = show($dir."/add", array("titel" => _eintragen_titel,
                                                                          "nickhead" => _nick,
                                                                          "bbcodehead" => _bbcode,
                                       "add_head" => _gb_edit_head,
                                                                       "emailhead" => _email,
                                       "what" => _button_value_edit,
                                       "security" => _register_confirm,
                                       "reg" => $get['reg'],
                                       "whaturl" => "editgb&amp;id=".$get['id'],
                                                                       "hphead" => _hp,
                                       "ed" => "&edit=".$get['id'],
                                       "preview" => _preview,
                                                                       "id" => $get['id'],
                                       "form" => $form,
                                                                       "posteintrag" => re($get['nachricht']),
                                       "ip" => _iplog_info,
                                                                       "error" => "",
                                                                   "eintraghead" => _eintrag));
      } else {
        $index = error(_error_edit_post,1);
      }
    } elseif(isset($_GET['what']) && $_GET['what'] == 'editgb') {
      if($_POST['reg'] == $userid || permission('gb'))
      {
        if($_POST['reg'] == 0)
        {
           $addme = "`nick`       = '".up($_POST['nick'])."',
                     `email`      = '".up($_POST['email'])."',
                     `hp`         = '".links($_POST['hp'])."',";
        }

        $editedby = show(_edited_by, array("autor" => autor($userid),
                                           "time" => date("d.m.Y H:i", time())._uhr));

        $upd = db("UPDATE ".$db['gb']."
                   SET ".$addme."
                       `nachricht`  = '".up($_POST['eintrag'])."',
                       `reg`        = '".intval($_POST['reg'])."',
                       `editby`     = '".up($editedby)."'
                   WHERE id = '".intval($_GET['id'])."'");

        $index = info(_gb_edited, "../gb/");
      } else {
        $index = error(_error_edit_post,1);
      }
    }