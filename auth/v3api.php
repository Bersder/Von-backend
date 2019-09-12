<?php //管理页面返回所有文章信息
require '../utils/init.php';
require '../utils/filters.php';
$link = mysqli_connect('127.0.0.1','root','awsl') or die('数据库连接失败');
if (isset($_POST['token'])&&($auth = token_authorize($_POST['token']))){
    $articles=[];
    $notes = [];
    $res = maria($link,"select aid as id,type,title,series,readCount,commentCount,time,lut,topped from Article.article_info order by time desc");
    while ($each = mysqli_fetch_assoc($res))$articles[] = $each;
    $res = maria($link,"select nid as id,type,title,readCount,commentCount,time,lut from Note.note_info order by time desc");
    while ($each = mysqli_fetch_assoc($res))$notes[] = $each;
    echo json_encode(['code'=>0,'data'=>['notes'=>$notes,'articles'=>$articles]]);
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}