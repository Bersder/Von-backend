<?php
require '../utils/filters.php';
require '../utils/utils.php';
header('Access-Control-Allow-Origin:*');
header('content-type: application/json;charset=UTF-8');
$DISK_ROOT = $_SERVER['DOCUMENT_ROOT'];

if (isset($_COOKIE['utk'])&&($auth = token_authorize($_COOKIE['utk']))){
    if(($imgInfo=file_filter('hi',5000000,['png','jpg','jpeg','gif','bmp']))&&(isset($_GET['aid'])||isset($_GET['nid']))){ //来的是header-img
        $xid = isset($_GET['aid'])?$_GET['aid']:$_GET['nid'];
        $isACGN = isset($_GET['aid']);
        $path = '/uploads/'.date('Y/m/d');
        if(!file_exists($DISK_ROOT.$path))mkdir($DISK_ROOT.$path,0777,true);
        $dst = $isACGN?$path.'/HI-'.$xid.$imgInfo[1]:$path.'/HIN-'.$xid.$imgInfo[1];
        move_uploaded_file($imgInfo[3],$DISK_ROOT.$dst);
        if (thumb_img($DISK_ROOT.$dst)){}//添加了缩略图
        else
            copy($DISK_ROOT.$dst,$DISK_ROOT.$dst.'.thumb');
        echo json_encode(['code'=>0,'imgSrc'=>$dst]);
    }
    elseif($imgInfo=file_filter('img',5000000)){ //来的是mdimg
        $md5 = substr($imgInfo[2],0,16).substr(md5(time()),0,16);
        $path = '/uploads/'.date('Y/m/d');
        if(!file_exists($DISK_ROOT.$path))mkdir($DISK_ROOT.$path,0777,true);
        if(!file_exists($DISK_ROOT.$path.'/'.$md5.$imgInfo[1]))move_uploaded_file($imgInfo[3],$DISK_ROOT.$path.'/'.$md5.$imgInfo[1]);
        $imgSrc = $path.'/'.$md5.$imgInfo[1];
        echo json_encode(['code'=>0,'imgSrc'=>$imgSrc]);
    }
    elseif(isset($_POST['delImg'])){
        $delImg = $_POST['delImg'];
        if (unlink($DISK_ROOT.$delImg))
            echo json_encode(['code'=>0]);
        else
            echo json_encode(['code'=>1]);
    }
    else
        echo json_encode(['code'=>1,'imgSrc'=>'/static/images/nodata.png']);
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}







