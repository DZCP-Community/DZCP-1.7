<?php
/**
 * DZCP - deV!L`z ClanPortal 1.7.0
 * http://www.dzcp.de
 * Menu: Begr��ung nach Tageszeit
 */
function welcome() {
    global $chkMe,$userid;

    $return = "<script language=\"javascript\" type=\"text/javascript\">
               <!--
                 date = new Date();
                 hour = date.getHours();
                 if(hour>=18)      document.write('"._welcome_18."');
                 else if(hour>=13) document.write('"._welcome_13."');
                 else if(hour>=11) document.write('"._welcome_11."');
                 else if(hour>=5)  document.write('"._welcome_5."');
                 else if(hour>=0)  document.write('"._welcome_0."');
               //-->
             </script>";

    $demo = (dzcp_demo ? ' - <font color="#FF0004"><b>Demo Version: '._version.' | Build: '._build.' | Date: '._release.'</b></font>' : '');
    if(!$chkMe)
        return $return.' '._welcome_guest.$demo;

    return $return.' '.autor($userid, "welcome").$demo;
}