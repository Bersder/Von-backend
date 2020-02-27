<?php
require 'utils/init.php';
require 'links/public_link.php';
$dynamic = mysqli_fetch_assoc(maria($link,"select id,content,time from Dynamic.dyn_record order by id desc limit 1"));
$latestUpdate = [];
$res = maria($link,"select aid,title,type,lut from Article.article_info order by lut desc limit 5");
while ($each = mysqli_fetch_assoc($res))$latestUpdate[] = $each;
$hits = [];
$res = maria($link,"select aid,type,title,time,readCount from Article.article_info order by readCount desc limit 5");
while ($each = mysqli_fetch_assoc($res))$hits[] = $each;
echo json_encode(['code'=>0,'data'=>['latestUpdate'=>$latestUpdate,'dynamic'=>$dynamic,'hits'=>$hits]]);