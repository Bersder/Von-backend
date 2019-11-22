<?php //管理页面返回所有文章信息
require '../utils/init.php';
require '../utils/filters.php';
require '../links/secret_link.php';
if (isset($_COOKIE['utk'])&&($auth = token_authorize($_COOKIE['utk']))){
    $articles=[];
    $notes = [];
    $res = maria($link,"
    select aid as id,type,title,seriesName as series,readCount,commentCount,time,lut,topped
    from Article.article_info left join Article.series_link on seriesID=sid
    order by time desc;
    ");
    while ($each = mysqli_fetch_assoc($res))$articles[] = $each;
    $res = maria($link,"select nid as id,type,title,readCount,commentCount,time,lut from Note.note_info order by time desc");
    while ($each = mysqli_fetch_assoc($res))$notes[] = $each;
    echo json_encode(['code'=>0,'data'=>['notes'=>$notes,'articles'=>$articles]]);
}
else{
    http_response_code(401);
    echo json_encode(['code'=>1]);
}