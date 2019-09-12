<?php //这是文章content-main加载更多接口
require 'utils/init.php';
$link = mysqli_connect('127.0.0.1','root','awsllswa') or die('数据库连接失败');

if(isset($_GET['_'])&&in_array($_GET['_'],['anime','code','game','trivial'])){
    if (isset($_GET['pn'])&&preg_match('/^[1-9]\\d*$/',$_GET['pn'])){
        if ($_GET['_']=='trivial'){
            $offset = ($_GET['pn'] - 1)*8;
            $order = intval($_GET['order'])?'readCount':'time';
            $data = [];
            $res = maria($link,"select aid,author,title,preview,imgSrc,commentCount,readCount,time,type from Article.article_info where type='trivial' order by $order desc limit $offset,8");
            while($each =  mysqli_fetch_assoc($res))$data[] = $each;
            echo json_encode(['code'=>0,'data'=>['arts'=>$data]]);
        }
        else{
            $offset = ($_GET['pn'] - 1)*6;
            $type = mysqli_real_escape_string($link,$_GET['_']);
            $order = intval($_GET['order'])?'readCount':'time';
            $data = [];
            $res = maria($link,"select aid,author,title,preview,imgSrc,commentCount,readCount,time,type from Article.article_info where type='$type' order by $order desc limit $offset,6");
            while($each =  mysqli_fetch_assoc($res))$data[] = $each;
            echo json_encode(['code'=>0,'data'=>['arts'=>$data]]);
        }
    }

}

