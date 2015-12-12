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
$where = _site_forum;
$dir = "forum";
define('_Forum', true);

//-> Prueft sicherheitsrelevante Gegebenheiten im Forum
function forumcheck($tid, $what) {
    global $sql;
    return $sql->rows("SELECT `".$what."` FROM `{prefix_forumthreads}` WHERE `id` = ? AND ".$what." = 1;",array(intval($tid))) ? true : false;
}

//-> Funktion um Bestimmte Textstellen zu markieren
function hl($text, $word) {
    if(!empty($_GET['hl']) && $_SESSION['search_type'] == 'text') {
        if($_SESSION['search_con'] == 'or') {
            $words = explode(" ",$word);
            for($x=0;$x<count($words);$x++)
                $ret['text'] = preg_replace("#".$words[$x]."#i",'<span class="fontRed" title="'.$words[$x].'">'.$words[$x].'</span>',$text);
        } else
            $ret['text'] = preg_replace("#".$word."#i",'<span class="fontRed" title="'.$word.'">'.$word.'</span>',$text);

        if(!preg_match("#<span class=\"fontRed\" title=\"(.*?)\">#", $ret['text']))
            $ret['class'] = 'class="commentsRight"';
        else
            $ret['class'] = 'class="highlightSearchTarget"';
    } else {
        $ret['text'] = $text;
        $ret['class'] = 'class="commentsRight"';
    }

    return $ret;
}

## SECTIONS
$action = empty($action) ? 'default' : $action;
if (file_exists(basePath . "/forum/case_" . $action . ".php")) {
    require_once(basePath . "/forum/case_" . $action . ".php");
}

## INDEX OUTPUT ##
$title = $pagetitle." - ".$where;
page($index, $title, $where);