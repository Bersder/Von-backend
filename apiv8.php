<?php //acgt页面content-primary初始化
require 'utils/init.php';
require 'links/public_link.php';
if(isset($_GET['_'])&&in_array($_GET['_'],['anime','code','game','trivial'])){
    $type = $_GET['_'];
    $headerInfo = mysqli_fetch_assoc(maria($link,"select imgSrc,title,description from Page.header_area where type='$type' limit 1"));
    echo json_encode(['code'=>0,'data'=>['headerInfo'=>$headerInfo]]);
}