<?php //这是文章content-main加载更多接口
require 'utils/init.php';
$link = mysqli_connect('127.0.0.1','root','awsllswa') or die('数据库连接失败');

if(isset($_GET['_'])&&in_array($_GET['_'],['a','c','g','t','n'])){
//    if($_GET['_']=='n'){
//        $filter = isset($_GET['f'])?$_GET['f']:false;
//        $offset = isset($_GET['o'])?intval($_GET['o']):0;
//        $data = [];
//        if($filter=='all')$res = maria($link,"select nid,title,preview,imgSrc,category,time from (select * from Note.note_info where type is not null) as son limit $offset,10");
//        else $res = maria($link,"select nid,title,preview,imgSrc,category,time from (select * from Note.note_info where type is not null) as son where category='$filter' limit $offset,10");
//        while($each =  mysqli_fetch_assoc($res))$data[] = $each;
//        echo json_encode(['code'=>0,'data'=>$data]);
//    }
    if ($_GET['_']=='t'){

        $filter = isset($_GET['f'])?$_GET['f']:false;
        $offset = isset($_GET['o'])?intval($_GET['o']):0;
        $data = [];
    }
    elseif (isset($_GET['pn'])&&preg_match('/^[1-9]\\d*$/',$_GET['pn'])){
        $offset = ($_GET['pn'] - 1)*6;
        $type = $_GET['_']=='a'?'anime':($_GET['_']=='c'?'code':'game');
        $order = intval($_GET['order'])?'readCount':'time';
        $data = [];
        $res = maria($link,"select aid,author,title,preview,imgSrc,commentCount,readCount,time,type from Article.article_info where type='$type' order by $order desc limit $offset,6");
        while($each =  mysqli_fetch_assoc($res))$data[] = $each;
        echo json_encode(['code'=>0,'data'=>['arts'=>$data]]);


    }
}

