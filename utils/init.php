<?php
header('Access-Control-Allow-Origin:*');
header('content-type: application/json;charset=UTF-8');
function maria($link,$sql){
    $res = mysqli_query($link,$sql);
    if(!$res){
        echo json_encode(['code'=>1]);
        echo '错误编号：'.mysqli_errno($link).'<br>';
        echo '错误信息：'.mysqli_error($link).'<br>';
        exit;
    }
    return $res;
}