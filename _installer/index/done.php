<?php
if($_COOKIE['agb'] =! true)
    $index = show("/msg/agb_error");
else
{
    $index = show("done");
    $_SESSION['db_install'] = false;
}