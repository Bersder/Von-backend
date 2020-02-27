<?php //这是文章/笔记页content-main初始化接口,包括trivial的content-aside
require 'utils/init.php';
require 'links/public_link.php';

if(isset($_GET['_'])&&in_array($_GET['_'],['anime','code','game','trivial','note'])){
    if($_GET['_']=='note'){
        $catMap = [];$notes = [];
        $headerInfo = mysqli_fetch_assoc(maria($link,"select imgSrc,title,description from Page.header_area where type='note' limit 1"));
        $res = maria($link,"
        select cid,catName,catName_en
        from Note.note_category
        where cid in (select distinct catID from Note.note_info)
        ");
        while($each =  mysqli_fetch_assoc($res))$catMap[$each['cid']] = ['catName'=>$each['catName'],'catName_en'=>$each['catName_en']];

        $res = maria($link,"
        select tmp.*,ifnull(ttt.tags,'') as tags
        from (
            select nid,title,preview,imgSrc,catID,catName,catName_en,time
            from Note.note_info as ni,Note.note_category as nc
            where ni.catID=nc.cid
        ) as tmp left join (select xid,type,group_concat(tagName) as tags from Tag.tm_tc group by concat(xid,type)) as ttt on nid=xid and ttt.type='note'
        order by time desc
        ");
        while($each =  mysqli_fetch_assoc($res))$notes[] = $each;
        $notice = mysqli_fetch_row(maria($link,"select content from Page.notice where type='note' order by time desc limit 1"))[0];
        echo json_encode(['code'=>0,'data'=>['catMap'=>$catMap,'notes'=>$notes,'headerInfo'=>$headerInfo,'notice'=>$notice]]);
    }
    elseif ($_GET['_']=='trivial'){
        $artsNew=[];$artsHot=[];$artNum=0;
        $artNum = mysqli_fetch_row(maria($link,"select count(aid) as count from Article.article_info where type='trivial'"))[0];
        $headerInfo = mysqli_fetch_assoc(maria($link,"select imgSrc,title,description from Page.header_area where type='trivial' limit 1"));
        $res = maria($link,"select aid,author,title,preview,imgSrc,commentCount,readCount,time,type from Article.article_info where type='trivial' order by time desc limit 8");
        while ($each = mysqli_fetch_assoc($res))$artsNew[] = $each;
        $res = maria($link,"select aid,author,title,preview,imgSrc,commentCount,readCount,time,type from Article.article_info where type='trivial' order by readCount desc limit 8");
        while ($each = mysqli_fetch_assoc($res))$artsHot[] = $each;
        echo json_encode(['code'=>0,'data'=>['artNum'=>$artNum,'artsNew'=>$artsNew,'artsHot'=>$artsHot,'headerInfo'=>$headerInfo]]);

    }
    else{
        $type = mysqli_real_escape_string($link,$_GET['_']);
        $artsNew=[];$artsHot=[];$artNum=0;
        $artNum = mysqli_fetch_row(maria($link,"select count(aid) as count from Article.article_info where type='$type'"))[0];

        $res = maria($link,"select aid,author,title,preview,imgSrc,commentCount,readCount,time,type from Article.article_info where type='$type' order by time desc limit 6");
        while ($each = mysqli_fetch_assoc($res))$artsNew[] = $each;
        $res = maria($link,"select aid,author,title,preview,imgSrc,commentCount,readCount,time,type from Article.article_info where type='$type' order by readCount desc limit 6");
        while ($each = mysqli_fetch_assoc($res))$artsHot[] = $each;
        //array_multisort(array_column($artsNew,'readCount'),SORT_DESC,$artsNew);
        echo json_encode(['code'=>0,'data'=>['artNum'=>$artNum,'artsNew'=>$artsNew,'artsHot'=>$artsHot,]]);
    }
}

