<?php
require 'utils/init.php';
require 'utils/utils.php';
$ip = get_ip();
$loc = get_ip_loc($ip);
$ua = $_SERVER['HTTP_USER_AGENT'];
$test = '';
echo json_encode(['ip'=>$ip,'loc'=>$loc,'test'=>$test]);