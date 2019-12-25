<?php
require 'utils/init.php';
require 'utils/filters.php';
require 'links/public_link.php';
$watching = [];
$watched = [];
$offset = 0;
if (isset($_GET['offset'])){
    $tmp = positive_int_filter($_GET['offset']);
    $offset = $tmp?$tmp:0;
}
if (isset($_GET['type'])&&$_GET['type']=='watching'){
    $res = maria($link,"select * from Page.bangumi where fin=0 order by nameCN asc limit 8 offset $offset");
    while ($each = mysqli_fetch_assoc($res)) $watching[] = $each;
    echo json_encode(['code'=>0,'data'=>['watching'=>$watching]],JSON_NUMERIC_CHECK);
}
else if (isset($_GET['type'])&&$_GET['type']=='watched'){
    $res = maria($link,"select * from Page.bangumi where fin=1 order by finDate desc limit 8 offset $offset");
    while ($each = mysqli_fetch_assoc($res)) $watched[] = $each;
    echo json_encode(['code'=>0,'data'=>['watched'=>$watched]],JSON_NUMERIC_CHECK);
}
else{
    $res = maria($link,"select * from Page.bangumi where fin=0 order by nameCN asc limit 8 offset $offset");
    while ($each = mysqli_fetch_assoc($res)) $watching[] = $each;
    $res = maria($link,"select * from Page.bangumi where fin=1 order by finDate desc limit 8 offset $offset");
    while ($each = mysqli_fetch_assoc($res)) $watched[] = $each;
    $num = mysqli_fetch_row(maria($link,"select count(*) from Page.bangumi"))[0];
    $wingNum = mysqli_fetch_row(maria($link,"select count(*) from Page.bangumi where fin=0"))[0];
    echo json_encode(['code'=>0,'data'=>['watching'=>$watching,'watched'=>$watched,'wingNum'=>$wingNum,'wedNum'=>$num-$wingNum]],JSON_NUMERIC_CHECK);
}