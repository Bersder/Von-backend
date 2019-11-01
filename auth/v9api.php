<?php //删除图片/上传图片
require '../utils/init.php';
require '../utils/filters.php';
require '../links/secret_link.php';
$DISK_ROOT = $_SERVER['DOCUMENT_ROOT'];
if (isset($_POST['token'])&&($auth = token_authorize($_POST['token']))){
    if (isset($_GET['delete']) && ($delId=positive_int_filter($_GET['delete']))){
        if($path = mysqli_fetch_row(maria($link,"select imgSrc from Page.album where id=$delId limit 1"))[0])
            unlink($DISK_ROOT.$path);
        maria($link,"delete from Page.album where id=$delId limit 1");
        echo json_encode(['code'=>0]);
    }
    else if (($imgInfo=file_filter('img',10000000))&&($type=in_array_filter($_POST['type'],['anime','game','trivial']))&&($des=maria_str_notnull_filter($_POST['des'],$link))){
        $path = '/uploads/album/'.substr($imgInfo[2],0,16).substr(md5(time()),0,16).$imgInfo[1];
        move_uploaded_file($imgInfo[3],$DISK_ROOT.$path);
        maria($link,"insert into Page.album (type, imgSrc, description, time) values ('$type','$path',$des,now())");
        echo json_encode(['code'=>0]);
    }

    else
        echo json_encode(['code'=>1]);
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}
