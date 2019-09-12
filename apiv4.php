<?php //归档页面使用
require 'utils/init.php';
$link = mysqli_connect('127.0.0.1','root','awsl') or die('数据库连接失败');
$headerInfo = mysqli_fetch_assoc(maria($link,"select imgSrc,title,description from Page.header_area where type='archive' limit 1"));
$noteNum = mysqli_fetch_row(maria($link,"select count(nid) from Note.note_info"))[0];
$articles = [];$tags = [];
maria($link,"use Article");
$res = maria($link,"select aid as id,time,title,type,readCount,commentCount,tags from article_info order by time desc limit 500");
while($each = mysqli_fetch_assoc($res))$articles[] = $each;
$res = maria($link,"select nid as id,time,title,type,readCount,commentCount,tags from Note.note_info order by time desc limit 500");
while($each = mysqli_fetch_assoc($res))$articles[] = $each;
array_multisort(array_column($articles,'time'),SORT_DESC,$articles);
$res = maria($link,"select tagName,relateArt,relateNote from Tag.tag_cloud limit 500");
while ($each = mysqli_fetch_assoc($res))$tags[] = $each;
echo json_encode(['code'=>0,'data'=>['articles'=>$articles,'tags'=>$tags,'headerInfo'=>$headerInfo,'noteNum'=>$noteNum]],JSON_NUMERIC_CHECK);
