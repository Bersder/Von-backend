<?php
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Methods: POST,OPTIONS,GET');
header('content-type: application/json;charset=UTF-8');
define('C_DOMAIN','localhost'); //cookie生效范围, 如".oshinonya.com"
define('MY_MAIL','---'); // 发送邮箱（目前只支持qq邮箱）
define('MY_MAIL_PASSWORD','---'); // 发送邮箱密码或授权码（目前只支持qq授权码）
define('MY_MAIL_NAME','忍野喵'); // 发送者名字
define('RECEIVE_MAIL','1747322151@qq.com'); // 接收邮箱(有人回复了你的文章)
define('BLOG_DOMAIN','https://www.oshinonya.com'); // 博客网址
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
function maria_scalar($link,$sql){
    return mysqli_fetch_row(maria($link,$sql))[0];
}