<?php //这是文章/笔记获取接口
require 'utils/init.php';
$link = mysqli_connect('127.0.0.1','root','awsllswa') or die('数据库连接失败');

if(isset($_GET['_'])&&in_array($_GET['_'],['a','c','g','t','n'])){
    if($_GET['_']=='n'&&preg_match('/^[1-9]\\d*$/',$_GET['xid'])){
        $nid = $_GET['xid'];
        maria($link,"update Note.note_info set readCount=readCount+1 where nid=$nid limit 1");
        if($info = mysqli_fetch_assoc(maria($link,"
        select title,preview,imgSrc,author,time,lut,commentCount,readCount,liked,ifnull(ttt.tags,'') as tags
        from (select * from Note.note_info where nid=$nid limit 1) as ni 
        left join (select xid,type,group_concat(tagName)as tags from Tag.tm_tc group by concat(xid,type)) as ttt on nid=xid and ttt.type='note'
        "))){
            $info['tags'] = !$info['tags']?[]:explode(',',$info['tags']);
            $rawContent = mysqli_fetch_row(maria($link,"select rawContent from Note.note_content where nid=$nid limit 1"))[0];
            $curTime = $info['time'];
            $pre = mysqli_fetch_assoc(maria($link,"select nid as id,type,title,imgSrc from Note.note_info where time=(select max(time) from Note.note_info where time<'$curTime') limit 1"));
            $next = mysqli_fetch_assoc(maria($link,"select nid as id,type,title,imgSrc from Note.note_info where time=(select min(time) from Note.note_info where time>'$curTime') limit 1"));
            echo json_encode(['code'=>0,'data'=>['info'=>$info,'rawContent'=>$rawContent,'pre'=>$pre,'next'=>$next]]);
        }
        else
            echo json_encode(['code'=>1]);

    }
    elseif(preg_match('/^[1-9]\\d*$/',$_GET['xid'])){
        switch ($_GET['_']){
            case 'a':$type = 'anime';break;
            case 'c':$type = 'code';break;
            case 'g':$type = 'game';break;
            case 't':$type = 'trivial';break;
            default:$type = null;
        }
        if (!$type){
            echo json_encode(['code'=>1]);
            die();
        }

        $aid = $_GET['xid'];
        maria($link, "update Article.article_info set readCount=readCount+1 where aid=$aid limit 1");
        if($info = mysqli_fetch_assoc(maria($link,"
        select title,preview,imgSrc,author,time,lut,seriesName as series,commentCount,readCount,liked,ifnull(ttt.tags,'') as tags
        from (select * from Article.article_info where aid=$aid and type='$type' limit 1) as ai 
        left join (select xid,type,group_concat(tagName)as tags from Tag.tm_tc group by concat(xid,type)) as ttt on aid=xid and ttt.type<>'note'
        left join Article.series_link on seriesID=sid
        "))){
            $info['tags'] = !$info['tags']?[]:explode(',',$info['tags']);
            $rawContent = mysqli_fetch_row(maria($link,"select rawContent from Article.article_content where aid=$aid limit 1"))[0];
            $curTime = $info['time'];
            $pre = mysqli_fetch_assoc(maria($link,"select aid as id,type,title,imgSrc from Article.article_info where time=(select max(time) from Article.article_info where time<'$curTime') limit 1"));
            $next = mysqli_fetch_assoc(maria($link,"select aid as id,type,title,imgSrc from Article.article_info where time=(select min(time) from Article.article_info where time>'$curTime') limit 1"));
            echo json_encode(['code'=>0,'data'=>['info'=>$info,'rawContent'=>$rawContent,'pre'=>$pre,'next'=>$next]]);
        }
        else
            echo json_encode(['code'=>1]);


    }
    else
        echo json_encode(['code'=>1]);
}