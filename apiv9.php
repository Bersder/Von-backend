<?php //首页初始化&加载更多
require 'utils/init.php';
require 'links/public_link.php';
if (isset($_GET['offset'])){
    $offset = intval($_GET['offset']);
    $arts = [];
    $res = maria($link,"select aid,title,preview,imgSrc,commentCount,readCount,time,type from Article.article_info order by time desc limit $offset,8");
    while ($each = mysqli_fetch_assoc($res))$arts[] = $each;
    echo json_encode(['code'=>0,'data'=>['arts'=>$arts]]);
}
else{
    $gossip = mysqli_fetch_assoc(maria($link,"select content,time from Page.gossip  order by time desc limit 1"));
    $latestUpdate = [];
    $res = maria($link,"select aid,title,type,lut from Article.article_info order by lut desc limit 5");
    while ($each = mysqli_fetch_assoc($res))$latestUpdate[] = $each;
    $hits = [];
    $res = maria($link,"select aid,type,title,time,readCount from Article.article_info order by readCount desc limit 5");
    while ($each = mysqli_fetch_assoc($res))$hits[] = $each;
    $arts = [];
    $res = maria($link,"select aid,title,preview,imgSrc,commentCount,readCount,time,type from Article.article_info order by time desc limit 8");
    while ($each = mysqli_fetch_assoc($res))$arts[] = $each;
    $topped = [];
    $res = maria($link,"select aid,title,preview,imgSrc,type from Article.article_info where topped=1 limit 6");
    while ($each = mysqli_fetch_assoc($res))$topped[] = $each;
    $notice = mysqli_fetch_row(maria($link,"select content from Page.notice where type='home' order by time desc limit 1"))[0];
    echo json_encode(['code'=>0,'data'=>['latestUpdate'=>$latestUpdate,'gossip'=>$gossip,'hits'=>$hits,'arts'=>$arts,'topped'=>$topped,'notice'=>$notice]]);
}

