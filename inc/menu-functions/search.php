<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: Globale Suche
 */

function search() {
    return show("menu/search", array("searchword" => (empty($_GET['searchword']) ? _search_word : $_GET['searchword'])));
}