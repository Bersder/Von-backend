<?php //这是文章/笔记页content-main初始化接口,包括trivial的content-aside
require 'utils/init.php';
$link = mysqli_connect('127.0.0.1','root','awsl') or die('数据库连接失败');

if(isset($_GET['_'])&&in_array($_GET['_'],['anime','code','game','trivial','note'])){
    if($_GET['_']=='note'){
//        $catCount = [];
        $catMap = [];$notes = [];
//        $catCount['all'] = mysqli_fetch_row(maria($link,"select count(type) from Note.note_info"))[0];
//        $catMap['all'] = '所有';
//        $res = maria($link,"select category,count(nid) as count from (select * from Note.note_info where type is not null) as son group by category");
//        while($each =  mysqli_fetch_assoc($res))$catCount[$each['category']] = $each['count'];
        $headerInfo = mysqli_fetch_assoc(maria($link,"select imgSrc,title,description from Page.header_area where type='note' limit 1"));
        $res = maria($link,"select catName_en,catName from Note.note_category limit 100");
        while($each =  mysqli_fetch_assoc($res))$catMap[$each['catName_en']] = $each['catName'];
        $res = maria($link,"select nid,title,preview,imgSrc,category,time,tags from Note.note_info where type is not null order by time desc limit 500");
        while($each =  mysqli_fetch_assoc($res))$notes[] = $each;
//        $notes['tags'] = explode(',',$notes['tags']);
        echo json_encode(['code'=>0,'data'=>['catMap'=>$catMap,'notes'=>$notes,'headerInfo'=>$headerInfo]]);
    }
    elseif ($_GET['_']=='trivial'){
        $artsNew=[];$artsHot=[];$artNum=0;
        $artNum = mysqli_fetch_row(maria($link,"select count(aid) as count from Article.article_info where type='trivial'"))[0];
        $headerInfo = mysqli_fetch_assoc(maria($link,"select imgSrc,title,description from Page.header_area where type='trivial' limit 1"));
        $res = maria($link,"select aid,author,title,preview,imgSrc,commentCount,readCount,time,type from Article.article_info where type='trivial' order by time desc limit 8");
        while ($each = mysqli_fetch_assoc($res))$artsNew[] = $each;
        $res = maria($link,"select aid,author,title,preview,imgSrc,commentCount,readCount,time,type from Article.article_info where type='trivial' order by readCount desc limit 8");
        while ($each = mysqli_fetch_assoc($res))$artsHot[] = $each;
        $album = [];
        $res = maria($link,"select imgSrc,description,time from Page.album where type='trivial' order by time desc limit 15");
        while ($each = mysqli_fetch_assoc($res))$album[] = $each;
        $gossip = mysqli_fetch_assoc(maria($link,"select content,time from Page.gossip where type='trivial' order by time desc limit 1"));
        echo json_encode(['code'=>0,'data'=>['artNum'=>$artNum,'artsNew'=>$artsNew,'artsHot'=>$artsHot,'album'=>$album,'gossip'=>$gossip,'headerInfo'=>$headerInfo]]);

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

