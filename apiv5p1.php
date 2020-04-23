<?php //标签查询接口
require 'utils/init.php';
require 'utils/filters.php';
require 'links/public_link.php';
if (isset($_GET['tag'])&&($tag=maria_str_notnull_filter($_GET['tag'],$link))){
    $e3 = mysqli_fetch_row(maria($link,"select 1 from Tag.tag_cloud where tagName=$tag limit 1"))[0];
    $exist = $e3?1:0;
    $results = [];
    if ($exist){
        $res = maria($link,"
        select aid as id,ai.type,title,time,ttt.tags
        from Article.article_info as ai left join (select xid,type,group_concat(tagName) as tags from Tag.tm_tc group by concat(xid,type)) as ttt on aid=xid and locate($tag,ttt.tags)>0 and ttt.type<>'note'
        where ttt.tags is not null
        union
        select nid as id,ni.type,title,time,ttt.tags
        from Note.note_info as ni left join (select xid,type,group_concat(tagName) as tags from Tag.tm_tc group by concat(xid,type)) as ttt on nid=xid and locate($tag,ttt.tags)>0 and ttt.type='note'
        where ttt.tags is not null
        order by time desc
        ");
        while ($each = mysqli_fetch_assoc($res))$results[] = $each;
    }
    echo json_encode(['code'=>0,'data'=>['results'=>$results],'exist'=>$exist]);
}
else{
    $rand = [];
    $res = maria($link,"
    select aid as id,ai.type,title,time,ifnull(ttt.tags,'') as tags
    from Article.article_info as ai left join (select xid,type,group_concat(tagName) as tags from Tag.tm_tc group by concat(xid,type)) as ttt on aid=xid and ttt.type<>'note'
    where aid >= floor(((select max(aid) from Article.article_info)-(select min(aid) from Article.article_info)) * rand() + (select min(aid) from Article.article_info))
    limit 5
    ");
    while ($each = mysqli_fetch_assoc($res))$rand[] = $each;
    $res = maria($link,"
    select nid as id,ni.type,title,time,ifnull(ttt.tags,'') as tags
    from Note.note_info as ni left join (select xid,type,group_concat(tagName) as tags from Tag.tm_tc group by concat(xid,type)) as ttt on nid=xid and ttt.type='note'
    where nid >= floor(((select max(nid) from Note.note_info)-(select min(nid) from Note.note_info)) * rand() + (select min(nid) from Note.note_info)) 
    limit 5
    ");
    while ($each = mysqli_fetch_assoc($res))$rand[] = $each;
    shuffle($rand);
    echo json_encode(['code'=>0,'data'=>['rand'=>$rand]]);
}