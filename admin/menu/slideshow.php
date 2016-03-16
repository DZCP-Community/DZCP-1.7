<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 */

if(_adminMenu != 'true') exit;

$where = $where.': '._slider;

switch ($do) {
    case 'new':
        $qry = $sql->select("SELECT `pos`,`bez` FROM `{prefix_slideshow}` ORDER BY `pos` ASC;"); $positions = '';
        foreach($qry as $get) {
            $positions .= show(_select_field, array("value" => $get['pos']+1,
                                                    "what" => _nach.': '.stringParser::decode($get['bez']),
                                                    "sel" => ""));
        }

        $show = show($dir."/slideshow_form", array("id" => "",
                                                   "error" => "",
                                                   "do" => "add",
                                                   "head" => _slider_admin_add,
                                                   "value" => _button_value_add,
                                                   "tdesc" => '',
                                                   "v_bezeichnung" => "",
                                                   "v_pos_none" => "",
                                                   "v_position" => $positions,
                                                   "v_url" => "http://",
                                                   "selected" => "",
                                                   "selected_txt" => 'selected="selected"',
                                                   "v_pic" => ""));
    break;
    case 'add':
        if(empty($_FILES['bild']['tmp_name']) || empty($_POST['bez']) || empty($_POST['url']) || $_POST['url'] == "http://") {
            if(!$_FILES['bild']['tmp_name'])
                $error = _slider_admin_error_nopic;
            else if(empty($_POST['bez']))
                $error = _slider_admin_error_empty_bezeichnung;
            else if(empty($_POST['url']) || empty($_POST['url']) || $_POST['url'] == "http://")
                $error = _slider_admin_error_empty_url;

            $error = show("errors/errortable", array("error" => $error));
            $selected = (isset($_POST['target']) && $_POST['target'] ? 'selected="selected"' : '');
            $selected_txt = (isset($_POST['showbez']) && $_POST['showbez'] ? 'selected="selected"' : '');

            $qry = $sql->select("SELECT `pos`,`bez` FROM `{prefix_slideshow}` ORDER BY `pos` ASC;"); $positions = '';
            foreach($qry as $get) {
                $posid = ($get['pos']+1);
                $positions .= show(_select_field, array("value" => $posid,
                        "what" => _nach.': '.stringParser::decode($get['bez']),
                        "sel" => (isset($_POST['position']) && $_POST['position'] == $posid ? 'selected="selected"' : '')));
            }

            $show = show($dir."/slideshow_form", array("id" => "",
                                                       "error" => $error,
                                                       "do" => "add",
                                                       "head" => _slider_admin_add,
                                                       "value" => _button_value_add,
                                                       "tdesc" => $_POST['desc'],
                                                       "v_bezeichnung" => $_POST['bez'],
                                                       "v_pos_none" => "",
                                                       "v_position" => $positions,
                                                       "v_url" => $_POST['url'],
                                                       "selected" => $selected,
                                                       "selected_txt" => $selected_txt,
                                                       "v_pic" => ""));
        } else {
            $sign = ($_POST['position'] == '1' || $_POST['position'] == '2' ? ">= " : "> ");
            $sql->update("UPDATE `{prefix_slideshow}` SET `pos` = pos+1 WHERE `pos` ".$sign." ".intval($_POST['position']));

            if(strpos($_POST['url'], 'www.') !== false)
                $_POST['url'] = links($_POST['url']);

            $sql->insert("INSERT INTO `{prefix_slideshow}` SET `pos` = ".intval($_POST['position']).",
                                                       `bez` = '".stringParser::encode($_POST['bez'])."',
                                                       `showbez` = ".intval($_POST['showbez']).",
                                                       `desc` = '".stringParser::encode($_POST['desc'])."',
                                                       `url`  = '".stringParser::encode($_POST['url'])."',
                                                       `target` = ".intval($_POST['target'])."");


            if(isset($_FILES['bild']['tmp_name']) && !empty($_FILES['bild']['tmp_name'])) {
                $tmpname = $_FILES['bild']['tmp_name'];
                $endung = explode(".", $_FILES['bild']['name']);
                $endung = strtolower($endung[count($endung)-1]);
                @copy($tmpname, basePath."/inc/images/slideshow/".$sql->lastInsertId().".".strtolower($endung));
                @unlink($tmpname);
            }

            $show = info(_slider_admin_add_done, "?admin=slideshow");
        }
    break;
    case 'edit':
        $get = $sql->fetch("SELECT * FROM `{prefix_slideshow}` WHERE `id` = '".intval($_GET['id'])."'");
        $qrypos = $sql->select("SELECT `pos`,`bez` FROM `{prefix_slideshow}` WHERE `id` != '".intval($get['id'])."' ORDER BY `pos` ASC");
        foreach($qrypos as $getpos) {
            $posid = ($getpos['pos']+1);
            $positions .= show(_select_field, array("value" => $posid,
                                                    "what" => _nach.': '.$getpos['bez'],
                                                    "sel" => ($get['position'] == $posid ? 'selected="selected"' : '')));
        }

        $selected = ($get['target'] ? 'selected="selected"' : '');
        $selected_txt = ($get['showbez'] ? 'selected="selected"' : '');

        $image = '';
        foreach($picformat as $endung) {
            if(file_exists(basePath."/inc/images/slideshow/".$get['id'].".".$endung)) {
                $image = "inc/images/slideshow/".$get['id'].".".$endung;
                break;
            }
        }

        $show = show($dir."/slideshow_form", array("id" => stringParser::decode($get['id']),
                                                   "error" => "",
                                                   "do" => "editdo",
                                                   "head" => _slider_admin_edit,
                                                   "value" => _button_value_edit,
                                                   "tdesc" => stringParser::decode($get['desc']),
                                                   "v_bezeichnung" => stringParser::decode($get['bez']),
                                                   "v_pos_none" => _slider_position_lazy,
                                                   "v_position" => $positions,
                                                   "v_url" => stringParser::decode($get['url']),
                                                   "selected" => $selected,
                                                   "selected_txt" => $selected_txt,
                                                   "v_pic" => img_size($image)."<br />"));
    break;
    case 'editdo':
        if(empty($_POST['bez']) || empty($_POST['url']) || $_POST['url'] == "http://") {
            if(empty($_POST['bez']))
                $error = _slider_admin_error_empty_bezeichnung;
            else if(empty($_POST['url']) || $_POST['url'] == "http://")
                $error = _slider_admin_error_empty_url;

            $error = show("errors/errortable", array("error" => $error));
            $selected = ($_POST['target'] ? 'selected="selected"' : '');
            $selected_txt = ($_POST['showbez'] ? 'selected="selected"' : '');

            $image = '';
            foreach($picformat as $endung) {
                if(file_exists(basePath."/inc/images/slideshow/".$_POST['id'].".".$endung)) {
                    $image = "inc/images/slideshow/".$_POST['id'].".".$endung;
                    break;
                }
            }

            $show = show($dir."/slideshow_form", array("id" => stringParser::decode($_POST['id']),
                                                       "error" => $error,
                                                       "do" => "editdo",
                                                       "head" => _slider_admin_edit,
                                                       "value" => _button_value_edit,
                                                       "tdesc" => $_POST['desc'],
                                                       "v_bezeichnung" => $_POST['bez'],
                                                       "v_pos_none" => _slider_position_lazy,
                                                       "v_position" => $positions,
                                                       "v_url" => $_POST['url'],
                                                       "selected" => $selected,
                                                       "selected_txt" => $selected_txt,
                                                       "v_pic" => img_size($image)."<br />"));
        } else {
            $pos = "";
            if($_POST['position'] != "lazy") {
                $sign = ($_POST['position'] == '1' || $_POST['position'] == '2' ? ">= " : "> ");
                $sql->update("UPDATE `{prefix_slideshow}` SET `pos` = pos+1 WHERE `pos` ".$sign." '".intval($_POST['position'])."'");
                $pos = " `pos` = ".intval($_POST['position']).", ";
            }

            if(strpos($_POST['url'], 'www.') !== false)
                $_POST['url'] = links($_POST['url']);

            $sql->update("UPDATE `{prefix_slideshow}` SET".$pos."
                      `bez` = '".stringParser::encode($_POST['bez'])."',
                      `showbez` = ".intval($_POST['showbez']).",
                      `url` = '".stringParser::encode($_POST['url'])."',
                      `desc` = '".stringParser::encode($_POST['desc'])."',
                      `target` = ".intval($_POST['target'])."
                WHERE `id` = ".intval($_POST['id']));

            if(isset($_FILES['bild']['tmp_name']) && !empty($_FILES['bild']['tmp_name'])) {
                $files = get_files(basePath."/inc/images/slideshow/",false,true,$picformat);
                foreach ($files as $file) {
                    $file_exp_minimize = explode('_minimize_',$file);
                    $file_exp = explode('.',$file);
                    if($file_exp_minimize[0] == $_POST['id'] || $file_exp[0] == $_POST['id'])
                        @unlink(basePath."/inc/images/slideshow/".$file);
                }

                $tmpname = $_FILES['bild']['tmp_name'];
                $endung = explode(".", $_FILES['bild']['name']);
                $endung = strtolower($endung[count($endung)-1]);
                @copy($tmpname, basePath."/inc/images/slideshow/".$sql->lastInsertId().".".strtolower($endung));
                @unlink($tmpname);
            }

            $show = info(_slider_admin_edit_done, "?admin=slideshow");
        }
    break;
    case 'delete':
        $sql->delete("DELETE FROM `{prefix_slideshow}` WHERE `id` = ".intval($_GET['id']));
        $files = get_files(basePath."/inc/images/slideshow/",false,true,$picformat);
        foreach ($files as $file) {
            $file_exp_minimize = explode('_minimize_',$file);
            $file_exp = explode('.',$file);
            if($file_exp_minimize[0] == $_GET['id'] || $file_exp[0] == $_GET['id'])
                @unlink(basePath."/inc/images/slideshow/".$file);
        }

        $show = info(_slider_admin_del_done, "?admin=slideshow");
    break;
    default:
        $qry = $sql->select("SELECT `id`,`bez` FROM `{prefix_slideshow}` ORDER BY `pos` ASC"); $entry = '';
        foreach($qry as $get) {
            $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
            $edit = show("page/button_edit_single", array("id" => $get['id'],
                                                          "action" => "admin=slideshow&amp;do=edit",
                                                          "title" => _button_title_edit));

            $delete = show("page/button_delete_single", array("id" => $get['id'],
                                                              "action" => "admin=slideshow&amp;do=delete",
                                                              "title" => _button_title_del,
                                                              "del" => _slider_admin_del));

            $entry .= show($dir."/slideshow_show", array("id" => $get['id'],
                                                         "class" => $class,
                                                         "bez" => $get['bez'],
                                                         "edit" => $edit,
                                                         "del" => $delete));
        }

        if(empty($entry))
            $entry = '<tr><td colspan="3" class="contentMainSecond">'._no_entrys.'</td></tr>';

        $show = show($dir."/slideshow", array("show" => $entry));
    break;
}