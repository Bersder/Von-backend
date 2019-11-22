<?php //随机背景接口
require 'utils/init.php';
$DISK_ROOT = $_SERVER['DOCUMENT_ROOT'];
$bgs_info = json_decode(file_get_contents($DISK_ROOT.'/site/bg/bg.json'),true);
$randIndex = mt_rand(0,sizeof($bgs_info)-1);
echo json_encode($bgs_info[$randIndex]);

