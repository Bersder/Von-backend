<?php
header('Access-Control-Allow-Origin:*');
header('content-type: application/json;charset=UTF-8');
$DISK_ROOT = $_SERVER['DOCUMENT_ROOT'];
$bgs_info = json_decode(file_get_contents($DISK_ROOT.'/site/bg/bg.json'),true);
$randIndex = mt_rand(0,sizeof($bgs_info)-1);
echo json_encode($bgs_info[$randIndex]);

