<?php
define('basePath', dirname(__FILE__));
ob_start();
$ajaxJob = true;
include(basePath."/inc/common.php");
$time = round(generatetime() - $time_start,4);
echo 'Common core Time: '.$time;
ob_end_flush();