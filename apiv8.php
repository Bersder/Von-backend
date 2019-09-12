<?php //acg页面content-aside初始化
require 'utils/init.php';
$link = mysqli_connect('127.0.0.1','root','awsllswa') or die('数据库连接失败');
if(isset($_GET['_'])&&in_array($_GET['_'],['anime','code','game',])){
    $type = mysqli_real_escape_string($link,$_GET['_']);
    $album = [];
    $gossip = mysqli_fetch_assoc(maria($link,"select content,time from Page.gossip where type='$type' order by time desc limit 1"));
    $headerInfo = mysqli_fetch_assoc(maria($link,"select imgSrc,title,description from Page.header_area where type='$type' limit 1"));
    if ($type!='code'){
        $res = maria($link,"select imgSrc,description,time from Page.album where type='$type' order by time desc limit 15");
        while ($each = mysqli_fetch_assoc($res))$album[] = $each;
    }
    if ($type=='code'){
        $seriesList = [];
        $res = maria($link,"select series as name,count(aid) as count from Article.article_info where series is not null group by series");
        while ($each = mysqli_fetch_assoc($res))$seriesList[] = $each;
        echo json_encode(['code'=>0,'data'=>['album'=>$album,'gossip'=>$gossip,'seriesList'=>$seriesList,'headerInfo'=>$headerInfo]]);

    }
    elseif($type=='anime'){
        // 还有追番列表
        echo json_encode(['code'=>0,'data'=>['album'=>$album,'gossip'=>$gossip,'headerInfo'=>$headerInfo]]);
    }
    else
        echo json_encode(['code'=>0,'data'=>['album'=>$album,'gossip'=>$gossip,'headerInfo'=>$headerInfo]]);
}