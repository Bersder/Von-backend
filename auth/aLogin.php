<?php
require '../utils/init.php';
require '../utils/filters.php';
$link = mysqli_connect('127.0.0.1','root','awsl') or die('数据库连接失败');
if (isset($_POST['token'])){
    if($auth = token_authorize($_POST['token'])){
        //通过验证返回用户信息
        $uid = $auth['uid'];
        if ($info = mysqli_fetch_assoc(maria($link,"select id as uid,name,avatar from User.me where id=$uid limit 1"))){
            echo json_encode(['code'=>0,'data'=>['info'=>$info]]);
        }
        else
            echo json_encode(['code'=>1]);

    }
    else
        echo json_encode(['code'=>1]);
}
else
    echo json_encode(['code'=>1]);