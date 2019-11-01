<?php //归档页面使用
require 'utils/init.php';
require 'links/public_link.php';
$headerInfo = mysqli_fetch_assoc(maria($link,"select imgSrc,title,description from Page.header_area where type='archive' limit 1"));
$noteNum = mysqli_fetch_row(maria($link,"select count(nid) from Note.note_info"))[0];
$articles = [];
$res = maria($link,"
select aid as id,time,title,ai.type,readCount,commentCount,ifnull(ttt.tags,'') as tags
from Article.article_info as ai left join (select xid,type,group_concat(tagName) as tags from Tag.tm_tc group by concat(xid,type)) as ttt on aid=xid and ttt.type<>'note'
union 
select nid as id,time,title,ni.type,readCount,commentCount,ifnull(ttt.tags,'') as tags
from Note.note_info as ni left join (select xid,type,group_concat(tagName) as tags from Tag.tm_tc group by concat(xid,type)) as ttt on nid=xid and ttt.type='note'
order by time desc; 
");
while($each = mysqli_fetch_assoc($res))$articles[] = $each;

$tagCountList = [];
$res = maria($link,"
select tid as id,tagName,ifnull(count,0) as count
from Tag.tag_cloud as tc left join (select count(tid) as count,tid from Tag.tag_map group by tid) as tmp
using(tid)
order by tagName asc;
");
while ($each = mysqli_fetch_assoc($res))$tagCountList[] = $each;
echo json_encode(['code'=>0,'data'=>['articles'=>$articles,'tagCountList'=>$tagCountList,'headerInfo'=>$headerInfo,'noteNum'=>$noteNum]],JSON_NUMERIC_CHECK);
