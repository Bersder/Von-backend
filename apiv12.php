<?php //about页面初始化
require 'utils/init.php';
require 'links/public_link.php';
$headerInfo = mysqli_fetch_assoc(maria($link,"select imgSrc,title,description from Page.header_area where type='about' limit 1"));
echo json_encode(['code'=>0,'data'=>['headerInfo'=>$headerInfo]]);
