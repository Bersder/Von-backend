<?php //标签页面初始化& 相关筛选
require 'utils/init.php';
require 'links/public_link.php';

if(isset($_GET['tag'])){ //带实际标签的访问

    $tag = mysqli_real_escape_string($link,$_GET['tag']);
    $e1 = mysqli_fetch_row(maria($link,"select ifnull(group_concat(xid),'') from Tag.tm_tc where tagName='$tag' and type<>'note'"))[0];
    $e2 = mysqli_fetch_row(maria($link,"select ifnull(group_concat(xid),'') from Tag.tm_tc where tagName='$tag' and type='note'"))[0];
    $e3 = mysqli_fetch_row(maria($link,"select 1 from Tag.tag_cloud where tagName='$tag' limit 1"))[0];
    $exist = $e3?1:0;
    if (isset($_GET['init'])){
        $artInfos = [];
        $res = maria($link,"
        select aid as id,ai.type,title,time,ifnull(ttt.tags,'') as tags
        from Article.article_info as ai left join (select xid,type,group_concat(tagName) as tags from Tag.tm_tc group by concat(xid,type)) as ttt on aid=xid and ttt.type<>'note'
        order by time desc
        ");
        while ($each = mysqli_fetch_assoc($res))array_push($artInfos,$each);
        $noteInfos = [];
        $res = maria($link,"
        select nid as id,ni.type,title,time,ifnull(ttt.tags,'') as tags
        from Note.note_info as ni left join (select xid,type,group_concat(tagName) as tags from Tag.tm_tc group by concat(xid,type)) as ttt on nid=xid and ttt.type='note'
        order by time desc
        ");
        while ($each = mysqli_fetch_assoc($res))array_push($noteInfos,$each);

        $tagCountList = [];
        $res = maria($link,"
        select tid as id,tagName,ifnull(count,0) as count
        from Tag.tag_cloud as tc left join (select count(tid) as count,tid from Tag.tag_map group by tid) as tmp
        using(tid)
        order by tagName asc;
        ");
        while ($each = mysqli_fetch_assoc($res))$tagCountList[] = $each;
        echo json_encode(['code'=>0,'data'=>['artInfos'=>$artInfos,'noteInfos'=>$noteInfos,'tagRelate'=>['arts'=>$e1,'notes'=>$e2],'tagCountList'=>$tagCountList],'exist'=>$exist]);
    }
    else{
        echo json_encode(['code'=>0,'data'=>['tagRelate'=>['arts'=>$e1,'notes'=>$e2]],'exist'=>$exist]);
    }

}
else{ //直接访问/tags
    $artInfos = [];
    $res = maria($link,"
    select aid as id,ai.type,title,time,ifnull(ttt.tags,'') as tags
    from Article.article_info as ai left join (select xid,type,group_concat(tagName) as tags from Tag.tm_tc group by concat(xid,type)) as ttt on aid=xid and ttt.type<>'note'
    order by time desc
    ");
    while ($each=mysqli_fetch_assoc($res))array_push($artInfos,$each);
    $noteInfos = [];
    $res = maria($link,"
    select nid as id,ni.type,title,time,ifnull(ttt.tags,'') as tags
    from Note.note_info as ni left join (select xid,type,group_concat(tagName) as tags from Tag.tm_tc group by concat(xid,type)) as ttt on nid=xid and ttt.type='note'
    order by time desc
    ");
    while ($each=mysqli_fetch_assoc($res))array_push($noteInfos,$each);

    $tagCountList = [];
    $res = maria($link,"
    select tid as id,tagName,ifnull(count,0) as count
    from Tag.tag_cloud as tc left join (select count(tid) as count,tid from Tag.tag_map group by tid) as tmp
    using(tid)
    order by tagName asc;
    ");
    while ($each = mysqli_fetch_assoc($res))$tagCountList[] = $each;

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
    echo json_encode(['code'=>0,'data'=>['artInfos'=>$artInfos,'noteInfos'=>$noteInfos,'tagCountList'=>$tagCountList,'rand'=>$rand]]);
}
