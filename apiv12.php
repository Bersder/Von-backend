<?php //about页面初始化
require 'utils/init.php';
$link = mysqli_connect('127.0.0.1','root','awsl') or die('数据库连接失败');
$headerInfo = mysqli_fetch_assoc(maria($link,"select imgSrc,title,description from Page.header_area where type='about' limit 1"));
echo json_encode(['code'=>0,'data'=>['headerInfo'=>$headerInfo]]);
