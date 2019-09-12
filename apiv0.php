<?php //侧边导航栏初始化
require 'utils/init.php';
$link = mysqli_connect('127.0.0.1','root','awsllswa') or die('数据库连接失败');
$info =  mysqli_fetch_assoc(maria($link,"select sign,status from User.me limit 1"));
echo json_encode(['code'=>0,'data'=>['info'=>$info]],JSON_NUMERIC_CHECK);