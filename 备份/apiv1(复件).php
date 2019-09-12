<?php //这是文章/笔记页content-main初始化接口
require 'utils/init.php';
$link = mysqli_connect('127.0.0.1','root','awsllswa') or die('数据库连接失败');

if(isset($_GET['_'])&&in_array($_GET['_'],['a','c','g','t','n'])){
    if($_GET['_']=='n'){
//        $catCount = [];
        $catMap = [];$notes = [];
//        $catCount['all'] = mysqli_fetch_row(maria($link,"select count(type) from Note.note_info"))[0];
//        $catMap['all'] = '所有';
//        $res = maria($link,"select category,count(nid) as count from (select * from Note.note_info where type is not null) as son group by category");
//        while($each =  mysqli_fetch_assoc($res))$catCount[$each['category']] = $each['count'];
        $res = maria($link,"select catName_en,catName from Note.note_category limit 100");
        while($each =  mysqli_fetch_assoc($res))$catMap[$each['catName_en']] = $each['catName'];
        $res = maria($link,"select nid,title,preview,imgSrc,category,time,tags from Note.note_info where type is not null order by time desc limit 500");
        while($each =  mysqli_fetch_assoc($res))$notes[] = $each;
//        $notes['tags'] = explode(',',$notes['tags']);
        echo json_encode(['code'=>0,'data'=>['catMap'=>$catMap,'notes'=>$notes]]);
    }
    elseif($_GET['_']=='t'){
        $artsNew=[];$artsHot=[];$artNum=0;
        $artNum = mysqli_fetch_row(maria($link,"select count(type) as count from Article.article_info where type='trivial'"))[0];

        $res = maria($link,"select aid,author,title,preview,imgSrc,commentCount,readCount,time,type from Article.article_info where type='trivial' order by time desc limit 10");
        while ($each = mysqli_fetch_assoc($res))$artsNew[] = $each;
        $res = maria($link,"select aid,author,title,preview,imgSrc,commentCount,readCount,time,type from Article.article_info where type='trivial' order by readCount desc limit 10");
        while ($each = mysqli_fetch_assoc($res))$artsHot[] = $each;
        echo json_encode(['code'=>0,'data'=>['artNum'=>$artNum,'artsNew'=>$artsNew,'artsHot'=>$artsHot,]]);

    }
    elseif($_GET['_']=='c'){
        $artsNew=[];$artsHot=[];$artNum=0;
        $artNum = mysqli_fetch_row(maria($link,"select count(type) as count from Article.article_info where type='code'"))[0];

        $res = maria($link,"select aid,author,title,preview,imgSrc,commentCount,readCount,time,type from Article.article_info where type='code' order by time desc limit 6");
        while ($each = mysqli_fetch_assoc($res))$artsNew[] = $each;
        $res = maria($link,"select aid,author,title,preview,imgSrc,commentCount,readCount,time,type from Article.article_info where type='code' order by readCount desc limit 6");
        while ($each = mysqli_fetch_assoc($res))$artsHot[] = $each;
        //array_multisort(array_column($artsNew,'readCount'),SORT_DESC,$artsNew);
        echo json_encode(['code'=>0,'data'=>['artNum'=>$artNum,'artsNew'=>$artsNew,'artsHot'=>$artsHot,]]);
    }
    elseif($_GET['_']=='g'){

    }
    else{

    }
}
//if(isset($_GET['_'])&&in_array($_GET['_'],['a','c','g','t','n'])){
//    if($_GET['_']=='n'){
//        $offset = isset($_GET['p'])?(intval($_GET['p'])-1)*10:0;
//        $filter = isset($_GET['f'])?$_GET['f']:false;
//        maria($link,"use Note");
//        $data = [];
//        if($filter)$res = maria($link,"select nid,title,preview,imgSrc,category,time from note_info where category='$filter' limit $offset,10");
//        else $res = maria($link,"select nid,title,preview,imgSrc,category,time from note_info limit $offset,10");
//        while($each =  mysqli_fetch_assoc($res))$data[] = $each;
//        echo json_encode(['code'=>0,'data'=>$data]);
//        //var_dump(json_encode($data));
//    }
//}
