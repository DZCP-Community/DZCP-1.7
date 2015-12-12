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
$dir = "glossar";
$where = _glossar;
$use_glossar = false; //Disable Glossar Tags in Glossar

## SECTIONS ##
$a = '';
if(!empty($_GET['word'])) {
    $_GET['word'] = trim($_GET['word']);
    $a = substr($_GET['word'],0,1);
    $qry = $sql->select("SELECT * "
            . "FROM `{prefix_glossar}` "
            . "WHERE `word` = ? OR `word` LIKE ? "
            . "ORDER BY `word`;",
            array(stringParser::encode($_GET['word']),'%'.stringParser::encode($a).'%'));
} else if(!empty($_GET['bst']) && $_GET['bst'] != 'all') {
    $_GET['bst'] = trim($_GET['bst']);
    $a = $_GET['bst'];
    $qry = $sql->select("SELECT * "
            . "FROM `{prefix_glossar}` "
            . "WHERE `word` LIKE ? "
            . "ORDER BY `word`;",
            array('%'.stringParser::encode($a).'%'));
} else {
    $qry = $sql->select("SELECT * "
            . "FROM `{prefix_glossar}` "
            . "ORDER BY `word`;");
}

foreach($qry as $get) {
    $class = ($color % 2) ? "contentMainSecond" : "contentMainFirst"; $color++;
    if(isset($_GET['word']) && $_GET['word'] == $get['word']) {
        $class = 'highlightSearchTarget';
    }

    $show .= show($dir."/glossar_show", array("word" => stringParser::decode($get['word']),
                                              "class" => $class,
                                              "glossar" => bbcode::parse_html($get['glossar'])));
}

$show = (empty($show) ? show(_no_entrys_found, array("colspan" => "2")) : $show); //No Entrys

$bst = array(_all,"A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"); $abc = ''; $i=0;
foreach ($bst as $bst_s) {
    $bclass = (empty($a) && ($bst_s) == _all || strtolower($bst_s) == strtolower($a)) ? 'active' : '';
    $ret = ($bst_s == _all) ? '?bst=all' : "?bst=".$bst_s; $i++;
    $abc .= "<a href=\"".$ret."\" title=\"".$bst_s."\"><span class=\"pagination ".$bclass."\">".$bst_s."</span></a> ";
    if($i == 20) {
        $abc .= '<p>';
    }
}

$index = show($dir."/glossar", array("show" => $show, "abc" => $abc));

## INDEX OUTPUT ##
$title = $pagetitle." - ".$where;
page($index, $title, $where);