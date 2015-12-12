<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: Slideshow
 */

function slideshow() {
    global $sql,$picformat;
    
    $qry = $sql->select("SELECT `id`,`desc`,`showbez`,`bez`,`url`,`target` FROM `{prefix_slideshow}` ORDER BY `pos` ASC LIMIT 4;");
    if($sql->rowCount()) {
        $pic = ''; $tabs = '';
        foreach($qry as $get) {
            if(empty($get['desc']) && !$get['showbez'])
                $slideroverlay = '';
            else if(!empty($get['desc']) && !$get['showbez'])
                $slideroverlay = '<div class="slideroverlay"><span>'.bbcode::parse_html(wrap(stringParser::decode($get['desc']))).'</span></div>';
            else
                $slideroverlay = '<div class="slideroverlay"><h2>'.bbcode::parse_html(wrap(stringParser::decode($get['bez']))).'</h2><span>'.bbcode::parse_html(wrap(stringParser::decode($get['desc']))).'</span></div>';

            $image = '';
            foreach($picformat as $endung) {
                if(file_exists(basePath."/inc/images/slideshow/".$get['id'].".".$endung)) {
                    $image = "../inc/images/slideshow/".$get['id'].".".$endung;
                    break;
                }
            }

            $pic .= show("menu/slideshowbild", array("image" => "<img src=\"".$image."\" alt=\"\" />",
                                                     "link" => "'".$get['url']."'",
                                                     "bez" => cut(stringParser::decode($get['bez']),32),
                                                     "text" => $slideroverlay,
                                                     "target" => $get['target']));

            $tabs .= '<a href="#" class="slidertabs" id="slider'.$get['id'].'"></a>';
        }

        return show("menu/slideshow", array("pic" => $pic, "tabs" => $tabs));
    }

    return '';
}